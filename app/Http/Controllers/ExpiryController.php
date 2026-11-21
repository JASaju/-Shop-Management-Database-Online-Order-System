<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpiryController extends Controller
{
    public function index(Request $r)
    {
        $days = (int) $r->input('days', 90);
        $batches = DB::table('v_expiry_alert')
            ->where('quantity_remaining', '>', 0)
            ->where('days_left', '<=', $days)
            ->orderBy('days_left')->paginate(30)->withQueryString();
        return view('expiry.index', compact('batches', 'days'));
    }

    // Batch-er bakī stock 0 kore expiry list o stock theke soriye dey (purchase/memo history thake)
    public function destroy($batch)
    {
        $b = DB::table('purchase_items as pi')
            ->join('medicines as m', 'm.medicine_id', '=', 'pi.medicine_id')
            ->where('pi.batch_id', $batch)
            ->select('pi.batch_id', 'pi.batch_no', 'm.name')
            ->first();
        abort_if(!$b, 404);

        DB::table('purchase_items')->where('batch_id', $batch)->update(['quantity_remaining' => 0]);

        return back()->with('success', "\"{$b->name}\" (ব্যাচ: " . ($b->batch_no ?: '-') . ") তালিকা থেকে মুছে ফেলা হয়েছে।");
    }
}