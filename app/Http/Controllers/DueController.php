<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DueController extends Controller
{
    public function index(Request $r)
    {
        $q = DB::table('v_customer_due');
        if (!$r->boolean('all')) $q->where('balance', '>', 0);
        if ($r->filled('q')) $q->where(fn ($w) => $w->where('name', 'like', "%{$r->q}%")->orWhere('phone', 'like', "%{$r->q}%"));
        $customers = $q->orderByDesc('balance')->paginate(25)->withQueryString();
        $totalDue = DB::table('v_customer_due')->where('balance', '>', 0)->sum('balance');
        return view('dues.index', compact('customers', 'totalDue'));
    }

    public function show($id)
    {
        $customer = DB::table('v_customer_due')->where('customer_id', $id)->first();
        abort_if(!$customer, 404);
        $ledger = DB::table('customer_ledger as l')
            ->leftJoin('memos as m', 'm.memo_id', '=', 'l.memo_id')
            ->where('l.customer_id', $id)
            ->orderByDesc('l.entry_date')->orderByDesc('l.ledger_id')
            ->select('l.*', 'm.memo_no')->get();
        $shops = DB::table('shops')->orderBy('shop_name')->get();
        return view('dues.show', compact('customer', 'ledger', 'shops'));
    }

    public function pay(Request $r, $id)
    {
        $c = DB::table('v_customer_due')->where('customer_id', $id)->first();
        abort_if(!$c, 404);
        $data = $r->validate([
            'shop_id'    => 'required|integer|exists:shops,shop_id',
            'entry_date' => 'required|date',
            'amount'     => 'required|numeric|min:0.01|max:' . max((float) $c->balance, 0.01),
            'method'     => 'required|in:cash,bkash,nagad,bank,other',
            'note'       => 'nullable|string|max:255',
        ]);
        DB::table('customer_ledger')->insert($data + ['customer_id' => $id, 'entry_type' => 'payment']);
        return back()->with('success', 'বাকি আদায় সংরক্ষণ করা হয়েছে।');
    }
}
