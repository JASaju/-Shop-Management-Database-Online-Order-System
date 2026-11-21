<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Medicine;
use App\Models\PurchaseItem;
use App\Models\Staff;
use App\Models\Memo;
use App\Models\MemoItem;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MemoController extends Controller
{
    public function index()
    {
        $memos = Memo::with('shop')->orderByDesc('memo_id')->paginate(20);

        return view('memos.index', compact('memos'));
    }

    public function create()
    {
        $shops = Shop::orderBy('shop_name')->get();
        $selectedShop = $shops->first();
        $medicines = Medicine::orderBy('name')->get();
        $nextMemoNo = $this->peekNextMemoNo();
        $staff = Staff::where('is_active', 1)->orderBy('name')->get(['staff_id', 'name']);
        $customers = Customer::orderBy('name')->get(['customer_id', 'name', 'address', 'phone']);

        // In-stock, not-expired batches, soonest expiry first (sell oldest first).
        $today = now()->toDateString();

        $batches = PurchaseItem::where('quantity_remaining', '>', 0)
            ->where(function ($q) use ($today) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', $today);
            })
            ->orderBy('expiry_date')
            ->get(['batch_id', 'medicine_id', 'batch_no', 'expiry_date', 'quantity_remaining', 'mrp']);

        return view('memos.create', compact(
            'shops',
            'selectedShop',
            'medicines',
            'nextMemoNo',
            'staff',
            'customers',
            'batches'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_id'                  => ['required', 'exists:shops,shop_id'],
            'memo_date'                => ['required', 'date'],
            'customer_name'            => ['nullable', 'string', 'max:150'],
            'customer_address'         => ['nullable', 'string', 'max:255'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.medicine_name'    => ['required', 'string', 'max:150'],
            'items.*.quantity'         => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'       => ['required', 'numeric', 'min:0'],
            'items.*.medicine_id'      => ['nullable', 'exists:medicines,medicine_id'],
            // --- new (all optional, so the old form keeps working) ---
            'customer_id'              => ['nullable', 'exists:customers,customer_id'],
            'customer_phone'           => ['nullable', 'string', 'max:20'],
            'staff_id'                 => ['nullable', 'exists:staff,staff_id'],
            'discount_amount'          => ['nullable', 'numeric', 'min:0'],
            'paid_amount'              => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.batch_id'         => ['nullable', 'exists:purchase_items,batch_id'],
        ]);

        $memo = DB::transaction(function () use ($validated) {
            // Generate memo_no server-side inside the locked transaction so two
            // simultaneous submits can never collide (client-posted memo_no is ignored).
            $memoNo = $this->nextMemoNoLocked();

            // Gross total, minus per-item discount % and an optional extra flat discount.
            $total = 0;
            $discount = (float) ($validated['discount_amount'] ?? 0);
            foreach ($validated['items'] as $row) {
                $line = $row['quantity'] * $row['unit_price'];
                $total += $line;
                $discount += $line * ((float) ($row['discount_percent'] ?? 0)) / 100;
            }
            $discount = round(min($discount, $total), 2);
            $payable  = round($total - $discount, 2);
            // No paid amount sent (old form) => treated as fully paid, due = 0.
            $paid = isset($validated['paid_amount']) ? min((float) $validated['paid_amount'], $payable) : $payable;
            $due  = round($payable - $paid, 2);

            // Link the memo to a customer: picked from the dropdown (customer_id),
            // or find/create one when there is a due or a phone number was typed.
            $customerId = $validated['customer_id'] ?? null;
            $name  = trim((string) ($validated['customer_name'] ?? ''));
            $phone = trim((string) ($validated['customer_phone'] ?? ''));

            if ($due > 0 && ! $customerId && $name === '') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'customer_name' => 'Please enter the customer name when there is a due amount.',
                ]);
            }
            if (! $customerId && $name !== '' && ($due > 0 || $phone !== '')) {
                $customerId = Customer::firstOrCreate(
                    ['name' => $name, 'phone' => $phone !== '' ? $phone : null],
                    ['address' => $validated['customer_address'] ?? null]
                )->customer_id;
            }

            // Printed memo shows "address, mobile" in one line (as on the paper pad).
            $addressLine = trim((string) ($validated['customer_address'] ?? ''));
            if ($phone !== '' && ! str_contains($addressLine, $phone)) {
                $addressLine = trim($addressLine . ($addressLine !== '' ? ', ' : '') . $phone);
            }

            $memo = Memo::create([
                'memo_no'          => $memoNo,
                'shop_id'          => $validated['shop_id'],
                'customer_id'      => $customerId,
                'customer_name'    => $validated['customer_name'] ?? null,
                'customer_address' => $addressLine !== '' ? $addressLine : null,
                'memo_date'        => $validated['memo_date'],
                'total_amount'     => $total,
                'discount_amount'  => $discount,
                'paid_amount'      => $paid,
                'due_amount'       => $due,
                'staff_id'         => $validated['staff_id'] ?? null,
                // words show what the customer actually pays/owes in total after discount
                'amount_in_words'  => $this->numberToEnglishWords((int) round($payable)),
            ]);

            if ($due > 0) {
                CustomerLedger::create([
                    'shop_id'     => $validated['shop_id'],
                    'customer_id' => $customerId,
                    'memo_id'     => $memo->memo_id,
                    'entry_date'  => $validated['memo_date'],
                    'entry_type'  => 'due',
                    'amount'      => $due,
                    'note'        => 'Memo #' . $memoNo,
                ]);
            }

            foreach ($validated['items'] as $row) {
                $medicineId   = $row['medicine_id'] ?? null;
                $medicineName = trim($row['medicine_name']);

                // Procedure 1: picked from the datalist -> medicine_id already set, use it.
                // Procedure 2: free-typed name -> medicine_id is empty.
                //   Look it up by exact name (covers a typed-but-not-selected match),
                //   otherwise create a new medicine so it becomes reusable next time.
                if (empty($medicineId)) {
                    $medicine = Medicine::firstOrCreate(
                        ['name' => $medicineName],
                        ['unit' => 'pcs', 'default_price' => $row['unit_price']]
                    );
                    $medicineId = $medicine->medicine_id;
                }

                $batchId = $row['batch_id'] ?? null;

                MemoItem::create([
                    'memo_id'          => $memo->memo_id,
                    'medicine_id'      => $medicineId,
                    'medicine_name'    => $medicineName,
                    'quantity'         => $row['quantity'],
                    'unit_price'       => $row['unit_price'],
                    'discount_percent' => $row['discount_percent'] ?? 0,
                    'batch_id'         => $batchId,
                ]);

                // Selling from a purchased batch reduces its remaining stock.
                if ($batchId) {
                    PurchaseItem::where('batch_id', $batchId)->decrement('quantity_remaining', $row['quantity']);
                }
            }

            return $memo;
        });

        return redirect()->route('memos.show', $memo->memo_id);
    }

    public function show(Memo $memo)
    {
        $memo->load('shop', 'items');

        return view('memos.show', compact('memo'));
    }

    /**
     * Read-only preview shown on the create form. Not trusted at save time.
     */
    private function peekNextMemoNo(): string
    {
        $last = Memo::orderByDesc('memo_id')->value('memo_no');

        if ($last && ctype_digit($last)) {
            return (string) ((int) $last + 1);
        }

        return '1';
    }

    /**
     * Authoritative memo number generator. Must be called inside a DB transaction;
     * lockForUpdate() prevents two concurrent submits from getting the same number.
     */
    public function nextMemoNoLocked(): string
    {
        $last = Memo::lockForUpdate()->orderByDesc('memo_id')->value('memo_no');

        if ($last && ctype_digit($last)) {
            return (string) ((int) $last + 1);
        }

        return '1';
    }

    /**
     * Converts an integer amount to English words using the South Asian
     * grouping (crore / lakh / thousand), e.g. 125000 => "One Lakh Twenty Five Thousand Taka Only".
     */
    public function numberToEnglishWords(int $number): string
    {
        if ($number === 0) {
            return 'Zero Taka Only';
        }

        $ones = [
            '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
            'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
            'Seventeen', 'Eighteen', 'Nineteen',
        ];
        $tens = [
            '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety',
        ];

        // Words for a number from 1 to 99.
        $twoDigits = function (int $n) use ($ones, $tens): string {
            if ($n < 20) {
                return $ones[$n];
            }
            return trim($tens[intdiv($n, 10)] . ' ' . $ones[$n % 10]);
        };

        $parts = [];
        $crore = intdiv($number, 10000000);
        $number %= 10000000;
        $lakh = intdiv($number, 100000);
        $number %= 100000;
        $thousand = intdiv($number, 1000);
        $number %= 1000;
        $hundred = intdiv($number, 100);
        $rest = $number % 100;

        // Crore can exceed 99 for very large amounts, so split it recursively.
        if ($crore) {
            $parts[] = ($crore >= 100
                    ? rtrim(str_replace(' Taka Only', '', $this->numberToEnglishWords($crore)))
                    : $twoDigits($crore)) . ' Crore';
        }
        if ($lakh) {
            $parts[] = $twoDigits($lakh) . ' Lakh';
        }
        if ($thousand) {
            $parts[] = $twoDigits($thousand) . ' Thousand';
        }
        if ($hundred) {
            $parts[] = $ones[$hundred] . ' Hundred';
        }
        if ($rest) {
            $parts[] = $twoDigits($rest);
        }

        return implode(' ', $parts) . ' Taka Only';
    }

    public function destroy(Memo $memo)
    {
        DB::transaction(function () use ($memo) {
            foreach ($memo->items as $it) {
                if ($it->batch_id) {
                    PurchaseItem::where('batch_id', $it->batch_id)->increment('quantity_remaining', $it->quantity);
                }
            }
            CustomerLedger::where('memo_id', $memo->memo_id)->delete();
            $memo->items()->delete();
            $memo->delete();
        });

        return redirect()->route('memos.index')->with('status', 'Memo deleted successfully.');
    }

    public function pdf(Memo $memo)
    {
        $memo->load('shop', 'items');

        // English text uses dompdf's built-in DejaVu Sans, so no custom font registration is needed.
        $pdf = Pdf::loadView('memos.pdf', compact('memo'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('memo-' . $memo->memo_no . '.pdf');
    }
}