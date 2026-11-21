<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $r)
    {
        // leftJoin: company/shop chhara purchase-o list-e thakbe
        $q = DB::table('purchases as p')
            ->leftJoin('companies as c', 'c.company_id', '=', 'p.company_id')
            ->leftJoin('shops as s', 's.shop_id', '=', 'p.shop_id')
            ->select('p.*', 'c.company_name', 's.shop_name')
            ->orderByDesc('p.purchase_date')->orderByDesc('p.purchase_id');
        if ($r->filled('company_id')) $q->where('p.company_id', $r->company_id);
        if ($r->boolean('due_only')) $q->where('p.due_amount', '>', 0);
        $purchases = $q->paginate(20)->withQueryString();
        $companies = DB::table('companies')->orderBy('company_name')->get();
        return view('purchases.index', compact('purchases', 'companies'));
    }

    public function create()
    {
        // Each medicine's latest purchase row (latest batch) -> used to auto-fill purchase price
        $last = '(SELECT p.medicine_id, p.purchase_price, p.discount_percent
                    FROM purchase_items p
                    JOIN (SELECT MAX(batch_id) AS b FROM purchase_items GROUP BY medicine_id) l
                      ON l.b = p.batch_id) lp';

        return view('purchases.create', [
            'shops'      => DB::table('shops')->orderBy('shop_name')->get(),
            'companies'  => DB::table('companies')->where('is_active', 1)->orderBy('company_name')->get(),
            // NEW: category list for the per-line category dropdown
            'categories' => DB::table('product_categories')->where('status', 1)->orderBy('name')->get(),
            'medicines'  => DB::table('medicines as m')
                ->leftJoin(DB::raw($last), 'lp.medicine_id', '=', 'm.medicine_id')
                ->orderBy('m.name')
                ->get([
                    // NEW: m.category_id (auto-select category when a medicine is picked)
                    'm.medicine_id', 'm.name', 'm.company_id', 'm.category_id', 'm.default_price',
                    'lp.purchase_price as last_price', 'lp.discount_percent as last_pct',
                ]),
        ]);
    }

    public function store(Request $r)
    {
        $r->validate([
            'invoice_no'      => 'required|string|max:40',
            'shop_id'         => 'nullable|integer|exists:shops,shop_id',
            'company_id'      => 'nullable|integer|exists:companies,company_id',
            'purchase_date'   => 'required|date',
            'due_date'        => 'nullable|date',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount'     => 'nullable|numeric|min:0',
            'note'            => 'nullable|string|max:255',
            'items'                    => 'required|array|min:1',
            'items.*.medicine_id'      => 'required|integer|exists:medicines,medicine_id',
            'items.*.category_id'      => 'required|integer|exists:product_categories,id', // NEW
            'items.*.batch_no'         => 'nullable|string|max:50',
            'items.*.mfg_date'         => 'nullable|date',
            'items.*.expiry_date'      => 'required|date',
            'items.*.quantity'         => 'required|numeric|min:0.01',
            'items.*.purchase_price'   => 'required|numeric|min:0',
            'items.*.mrp'              => 'nullable|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ], [
            'items.*.medicine_id.required' => 'তালিকা থেকে ঠিকমতো ঔষধ বেছে নিন।',
            'items.*.medicine_id.exists'   => 'তালিকা থেকে ঠিকমতো ঔষধ বেছে নিন।',
            'items.*.category_id.required' => 'প্রতিটি লাইনে ক্যাটাগরি বাছাই করুন।', // NEW
            'items.*.category_id.exists'   => 'ক্যাটাগরি ঠিকমতো বাছাই করুন।',         // NEW
        ]);

        $rows = []; $total = 0;
        $cats = []; // NEW: medicine_id => category_id
        foreach ($r->items as $it) {
            $qty = (float) $it['quantity'];
            $price = (float) $it['purchase_price'];
            $pct = (float) ($it['discount_percent'] ?? 0);
            $disc = round($qty * $price * $pct / 100, 2);
            $total += $qty * $price - $disc;
            $rows[] = [
                'medicine_id' => $it['medicine_id'], 'batch_no' => $it['batch_no'] ?? null,
                'mfg_date' => !empty($it['mfg_date']) ? $it['mfg_date'] : null,
                'expiry_date' => $it['expiry_date'],
                'quantity' => $qty, 'quantity_remaining' => $qty,
                'purchase_price' => $price, 'mrp' => $it['mrp'] ?? 0,
                'discount_percent' => $pct, 'discount_amount' => $disc,
            ];
            // NEW: purchase_items-e category_id column nei, tai alada rakhi
            $cats[$it['medicine_id']] = (int) $it['category_id'];
        }
        $discount = (float) $r->input('discount_amount', 0);
        $paid = (float) $r->input('paid_amount', 0);
        if ($paid > $total - $discount + 0.001) {
            return back()->withInput()->withErrors('পরিশোধের পরিমাণ মোট বিলের চেয়ে বেশি হতে পারে না।');
        }

        $id = DB::transaction(function () use ($r, $rows, $cats, $total, $discount, $paid) {
            $pid = DB::table('purchases')->insertGetId([
                'invoice_no' => $r->invoice_no,
                'shop_id' => $r->shop_id ?: null,
                'company_id' => $r->company_id ?: null,
                'purchase_date' => $r->purchase_date, 'due_date' => $r->due_date ?: null,
                'total_amount' => round($total, 2), 'discount_amount' => $discount,
                'paid_amount' => $paid, 'note' => $r->note,
            ]);
            foreach ($rows as $row) DB::table('purchase_items')->insert($row + ['purchase_id' => $pid]);

            // NEW: purchase-er shomoy medicine-er category save -> welcome page-e category onujayi dekhabe
            foreach ($cats as $medicineId => $catId) {
                DB::table('medicines')->where('medicine_id', $medicineId)->update(['category_id' => $catId]);
            }

            // company_payments.company_id is NOT NULL, so only log a payment when a company is set
            if ($paid > 0 && $r->company_id) {
                DB::table('company_payments')->insert([
                    'company_id' => $r->company_id, 'purchase_id' => $pid,
                    'payment_date' => $r->purchase_date, 'amount' => $paid,
                    'method' => 'cash', 'note' => 'ক্রয়ের সময় পরিশোধ',
                ]);
            }
            return $pid;
        });

        return redirect()->route('purchases.show', $id)->with('success', 'ক্রয় সংরক্ষণ করা হয়েছে।');
    }

    public function show($id)
    {
        $purchase = DB::table('purchases as p')
            ->leftJoin('companies as c', 'c.company_id', '=', 'p.company_id')
            ->leftJoin('shops as s', 's.shop_id', '=', 'p.shop_id')
            ->where('p.purchase_id', $id)
            ->select('p.*', 'c.company_name', 's.shop_name')->first();
        abort_if(!$purchase, 404);

        // NEW: category name join (show blade-e dekhanor jonno)
        $items = DB::table('purchase_items as i')
            ->join('medicines as m', 'm.medicine_id', '=', 'i.medicine_id')
            ->leftJoin('product_categories as cat', 'cat.id', '=', 'm.category_id')
            ->where('i.purchase_id', $id)
            ->select('i.*', 'm.name', 'cat.name as category_name')
            ->get();

        $payments = DB::table('company_payments')->where('purchase_id', $id)->orderBy('payment_date')->get();
        return view('purchases.show', compact('purchase', 'items', 'payments'));
    }

    public function pay(Request $r, $id)
    {
        $p = DB::table('purchases')->where('purchase_id', $id)->first();
        abort_if(!$p, 404);

        if (! $p->company_id) {
            return back()->withErrors('এই ক্রয়ে কোম্পানি নেই, তাই পেমেন্ট এন্ট্রি করা যাবে না।');
        }

        $data = $r->validate([
            'amount'       => 'required|numeric|min:0.01|max:' . max((float) $p->due_amount, 0.01),
            'payment_date' => 'required|date',
            'method'       => 'required|in:cash,bkash,nagad,bank,cheque,other',
            'reference_no' => 'nullable|string|max:80',
            'note'         => 'nullable|string|max:255',
        ]);
        DB::transaction(function () use ($data, $p, $id) {
            DB::table('company_payments')->insert($data + ['company_id' => $p->company_id, 'purchase_id' => $id]);
            DB::table('purchases')->where('purchase_id', $id)->increment('paid_amount', $data['amount']);
        });
        return back()->with('success', 'পেমেন্ট সংরক্ষণ করা হয়েছে।');
    }

    public function destroy($id)
    {
        $sold = DB::table('purchase_items')->where('purchase_id', $id)->whereColumn('quantity', '!=', 'quantity_remaining')->exists();
        if ($sold) return back()->withErrors('এই ক্রয়ের কিছু পণ্য বিক্রি হয়ে গেছে, তাই মোছা যাবে না।');
        DB::transaction(function () use ($id) {
            DB::table('company_payments')->where('purchase_id', $id)->delete();
            DB::table('purchase_items')->where('purchase_id', $id)->delete();
            DB::table('purchases')->where('purchase_id', $id)->delete();
        });
        return redirect()->route('purchases.index')->with('success', 'ক্রয় মুছে ফেলা হয়েছে।');
    }
}