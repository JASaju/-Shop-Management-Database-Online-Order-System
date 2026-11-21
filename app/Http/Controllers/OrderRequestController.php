<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Medicine;
use App\Models\Memo;
use App\Models\MemoItem;
use App\Models\OrderRequest;
use App\Models\PurchaseItem;
use App\Models\Shop;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = OrderRequest::query();
        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query
            ->orderByRaw("FIELD(status, 'pending', 'called', 'confirmed', 'cancelled')")
            ->orderByDesc('order_request_id')
            ->paginate(20)
            ->withQueryString();

        $pending = OrderRequest::where('status', 'pending')->count();

        return view('order_requests.index', compact('orders', 'status', 'pending'));
    }

    public function show(OrderRequest $orderRequest)
    {
        $shops = Shop::orderBy('shop_name')->get();
        $staff = Staff::where('is_active', 1)->orderBy('name')->get();

        return view('order_requests.show', [
            'order' => $orderRequest,
            'shops' => $shops,
            'staff' => $staff,
        ]);
    }

    public function updateStatus(Request $request, OrderRequest $orderRequest)
    {
        $data = $request->validate([
            'status'            => ['required', 'in:pending,called,confirmed,cancelled'],
            'note'              => ['nullable', 'string', 'max:255'],
            'items'             => ['nullable', 'array'],
            'items.*.qty'       => ['nullable', 'numeric', 'min:0.01'],
            'shop_id'           => ['required_if:status,confirmed', 'nullable', 'exists:shops,shop_id'],
            'delivery_staff_id' => ['required_if:status,confirmed', 'nullable', 'exists:staff,staff_id'],
        ], [
            'shop_id.required_if'           => 'Please select a shop to confirm the order.',
            'delivery_staff_id.required_if' => 'Please select a delivery staff to confirm the order.',
        ]);

        $memoId = null;

        DB::transaction(function () use ($request, $data, $orderRequest, &$memoId) {

            // 1) Update quantities and totals on the order itself
            $lines = $orderRequest->items ?? [];
            if (is_string($lines)) {
                $lines = json_decode($lines, true) ?: [];
            }

            $newQty = $request->input('items', []);
            $orderTotal = 0;

            foreach ($lines as $i => $line) {
                $qty = (float) $line['qty'];
                if (isset($newQty[$i]['qty']) && $newQty[$i]['qty'] !== '') {
                    $qty = (float) $newQty[$i]['qty'];
                }
                if ($qty < 0.01) {
                    $qty = 0.01;
                }

                $lines[$i]['qty'] = $qty;
                $lines[$i]['line_total'] = round($qty * (float) $line['price'], 2);
                $orderTotal += $lines[$i]['line_total'];
            }

            $shopId  = $request->input('shop_id');
            $staffId = $request->input('delivery_staff_id');

            $orderRequest->update([
                'items'             => $lines,
                'total_amount'      => round($orderTotal, 2),
                'status'            => $data['status'],
                'note'              => $data['note'] ?? null,
                'shop_id'           => $shopId ? $shopId : $orderRequest->shop_id,
                'delivery_staff_id' => $staffId ? $staffId : $orderRequest->delivery_staff_id,
            ]);

            // 2) Confirmed -> create the memo, or rebuild the one already linked
            if ($data['status'] === 'confirmed') {
                $memoId = $this->saveMemoFromOrder($orderRequest)->memo_id;
            }
        });

        if ($memoId) {
            return redirect()->route('memos.show', $memoId)
                ->with('success', 'Order confirmed, memo created/updated.');
        }

        return back()->with('success', 'Order updated.');
    }

    /**
     * Confirmed order -> memo.
     * - First time: creates a new memo.
     * - Already has a memo: stock is given back, the memo is rebuilt (no duplicate memo).
     * Price auto, stock from earliest-expiry batches.
     */
    private function saveMemoFromOrder(OrderRequest $order)
    {
        $memoCtl = app(MemoController::class);
        $today   = now()->toDateString();

        $lines = $order->items ?? [];
        if (is_string($lines)) {
            $lines = json_decode($lines, true) ?: [];
        }

        // If a memo already exists: put old stock back, remove old items and ledger
        $memo = null;
        if ($order->memo_id) {
            $memo = Memo::find($order->memo_id);
        }

        if ($memo) {
            $oldItems = MemoItem::where('memo_id', $memo->memo_id)->get();
            foreach ($oldItems as $old) {
                if ($old->batch_id) {
                    PurchaseItem::where('batch_id', $old->batch_id)
                        ->increment('quantity_remaining', (float) $old->quantity);
                }
            }
            MemoItem::where('memo_id', $memo->memo_id)->delete();
            CustomerLedger::where('memo_id', $memo->memo_id)->delete();
        }

        // Load medicines used in this order
        $ids = [];
        foreach ($lines as $line) {
            if (!empty($line['medicine_id'])) {
                $ids[] = $line['medicine_id'];
            }
        }
        $meds = Medicine::whereIn('medicine_id', $ids)->get()->keyBy('medicine_id');

        $total = 0;
        $discount = 0;
        $rows = [];

        foreach ($lines as $line) {
            $m = null;
            if (!empty($line['medicine_id']) && isset($meds[$line['medicine_id']])) {
                $m = $meds[$line['medicine_id']];
            }

            $name  = $m ? $m->name : $line['name'];
            $price = $m ? (float) $m->default_price : (float) $line['price'];
            $pct   = $m ? (float) ($m->default_discount_percent ?? 0) : 0;
            $qty   = (float) $line['qty'];

            $gross     = $qty * $price;
            $total    += $gross;
            $discount += $gross * $pct / 100;

            // take stock from soonest-expiry, non-expired batches
            $left = $qty;
            if ($m) {
                $batches = PurchaseItem::where('medicine_id', $m->medicine_id)
                    ->where('quantity_remaining', '>', 0)
                    ->orderBy('expiry_date')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $b) {
                    if ($left <= 0) {
                        break;
                    }
                    // skip expired batches
                    if ($b->expiry_date !== null && $b->expiry_date->toDateString() < $today) {
                        continue;
                    }

                    $take = min($left, (float) $b->quantity_remaining);
                    $rows[] = [
                        'medicine_id' => $m->medicine_id,
                        'name'        => $name,
                        'price'       => $price,
                        'pct'         => $pct,
                        'qty'         => $take,
                        'batch_id'    => $b->batch_id,
                    ];
                    PurchaseItem::where('batch_id', $b->batch_id)->decrement('quantity_remaining', $take);
                    $left -= $take;
                }
            }

            // no stock / not enough stock -> add the remainder without a batch
            if ($left > 0) {
                $rows[] = [
                    'medicine_id' => $m ? $m->medicine_id : null,
                    'name'        => $name,
                    'price'       => $price,
                    'pct'         => $pct,
                    'qty'         => $left,
                    'batch_id'    => null,
                ];
            }
        }

        $discount = round(min($discount, $total), 2);
        $payable  = round($total - $discount, 2);
        $paid     = $payable;               // <-- to keep it as due (COD), use: $paid = 0;
        $due      = round($payable - $paid, 2);

        // Customer
        $name  = trim((string) $order->customer_name);
        $phone = trim((string) $order->phone);
        $customer = Customer::firstOrCreate([
            'name'  => $name !== '' ? $name : 'Online Order',
            'phone' => $phone !== '' ? $phone : null,
        ]);
        $customerId = $customer->customer_id;

        // Shop: selected shop, otherwise the first shop
        $shopId = $order->shop_id;
        if (!$shopId) {
            $shopId = Shop::orderBy('shop_id')->value('shop_id');
        }

        $memoData = [
            'shop_id'           => $shopId,
            'delivery_staff_id' => $order->delivery_staff_id,
            'customer_id'       => $customerId,
            'customer_name'     => $name !== '' ? $name : null,
            'customer_address'  => $phone !== '' ? $phone : null,
            'total_amount'      => $total,
            'discount_amount'   => $discount,
            'paid_amount'       => $paid,
            'due_amount'        => $due,
            'amount_in_words'   => $memoCtl->numberToEnglishWords((int) round($payable)),
        ];

        if ($memo) {
            // keep the old memo_no and memo_date
            $memo->update($memoData);
        } else {
            $memoData['memo_no']   = $memoCtl->nextMemoNoLocked();
            $memoData['memo_date'] = $today;
            $memo = Memo::create($memoData);

            $order->update(['memo_id' => $memo->memo_id]);
        }

        foreach ($rows as $r) {
            MemoItem::create([
                'memo_id'          => $memo->memo_id,
                'medicine_id'      => $r['medicine_id'],
                'medicine_name'    => $r['name'],
                'quantity'         => $r['qty'],
                'unit_price'       => $r['price'],
                'discount_percent' => $r['pct'],
                'batch_id'         => $r['batch_id'],
            ]);
        }

        if ($due > 0) {
            CustomerLedger::create([
                'shop_id'     => $memo->shop_id,
                'customer_id' => $customerId,
                'memo_id'     => $memo->memo_id,
                'entry_date'  => $today,
                'entry_type'  => 'due',
                'amount'      => $due,
                'note'        => 'Memo #' . $memo->memo_no,
            ]);
        }

        return $memo;
    }

    public function destroy(OrderRequest $orderRequest)
    {
        $orderRequest->delete();

        return redirect()->route('order-requests.index')->with('success', 'Order deleted.');
    }
}