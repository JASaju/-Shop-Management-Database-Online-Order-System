<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstrumentController extends Controller
{
    private function lookups(): array
    {
        return [
            'shops'     => DB::table('shops')->orderBy('shop_name')->get(),
            'companies' => DB::table('companies')->orderBy('company_name')->get(),
        ];
    }

    public function index()
    {
        $rows = DB::table('shop_instruments as t')->join('shops as s', 's.shop_id', '=', 't.shop_id')
            ->select('t.*', 's.shop_name')->orderBy('t.instrument_name')->paginate(25);
        return view('instruments.index', ['rows' => $rows]);
    }

    public function create() { return view('instruments.create', $this->lookups()); }

    public function store(Request $r)
    {
        $data = $r->validate(['shop_id'=>'required|integer|exists:shops,shop_id','instrument_name'=>'required|string|max:150','brand_model'=>'nullable|string|max:150','serial_no'=>'nullable|string|max:80','quantity'=>'required|integer|min:1','purchase_date'=>'nullable|date','purchase_price'=>'nullable|numeric|min:0','company_id'=>'nullable|integer|exists:companies,company_id','warranty_until'=>'nullable|date','condition_status'=>'required|in:good,needs_repair,out_of_order','note'=>'nullable|string|max:255']);
        
        DB::table('shop_instruments')->insert($data);
        return redirect()->route('instruments.index')->with('success', 'সংরক্ষণ করা হয়েছে।');
    }

    public function edit($id)
    {
        $row = DB::table('shop_instruments')->where('instrument_id', $id)->first();
        abort_if(!$row, 404);
        return view('instruments.edit', $this->lookups() + ['row' => $row]);
    }

    public function update(Request $r, $id)
    {
        $data = $r->validate(['shop_id'=>'required|integer|exists:shops,shop_id','instrument_name'=>'required|string|max:150','brand_model'=>'nullable|string|max:150','serial_no'=>'nullable|string|max:80','quantity'=>'required|integer|min:1','purchase_date'=>'nullable|date','purchase_price'=>'nullable|numeric|min:0','company_id'=>'nullable|integer|exists:companies,company_id','warranty_until'=>'nullable|date','condition_status'=>'required|in:good,needs_repair,out_of_order','note'=>'nullable|string|max:255']);
        
        DB::table('shop_instruments')->where('instrument_id', $id)->update($data);
        return redirect()->route('instruments.index')->with('success', 'হালনাগাদ করা হয়েছে।');
    }

    public function destroy($id)
    {
        DB::table('shop_instruments')->where('instrument_id', $id)->delete();
        return redirect()->route('instruments.index')->with('success', 'মুছে ফেলা হয়েছে।');
    }
}
