<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
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
        $rows = DB::table('staff as t')->join('shops as s', 's.shop_id', '=', 't.shop_id')
            ->select('t.*', 's.shop_name')->orderBy('t.name')->paginate(25);
        return view('staff.index', ['rows' => $rows]);
    }

    public function create() { return view('staff.create', $this->lookups()); }

    public function store(Request $r)
    {
        $data = $r->validate(['shop_id'=>'required|integer|exists:shops,shop_id','name'=>'required|string|max:150','designation'=>'nullable|string|max:100','phone'=>'nullable|string|max:20','nid_no'=>'nullable|string|max:30','address'=>'nullable|string|max:255','salary'=>'nullable|numeric|min:0','join_date'=>'nullable|date']);
        $data['is_active'] = $r->boolean('is_active') ? 1 : 0;
        DB::table('staff')->insert($data);
        return redirect()->route('staff.index')->with('success', 'সংরক্ষণ করা হয়েছে।');
    }

    public function edit($id)
    {
        $row = DB::table('staff')->where('staff_id', $id)->first();
        abort_if(!$row, 404);
        return view('staff.edit', $this->lookups() + ['row' => $row]);
    }

    public function update(Request $r, $id)
    {
        $data = $r->validate(['shop_id'=>'required|integer|exists:shops,shop_id','name'=>'required|string|max:150','designation'=>'nullable|string|max:100','phone'=>'nullable|string|max:20','nid_no'=>'nullable|string|max:30','address'=>'nullable|string|max:255','salary'=>'nullable|numeric|min:0','join_date'=>'nullable|date']);
        $data['is_active'] = $r->boolean('is_active') ? 1 : 0;
        DB::table('staff')->where('staff_id', $id)->update($data);
        return redirect()->route('staff.index')->with('success', 'হালনাগাদ করা হয়েছে।');
    }

    public function destroy($id)
    {
        DB::table('staff')->where('staff_id', $id)->delete();
        return redirect()->route('staff.index')->with('success', 'মুছে ফেলা হয়েছে।');
    }
}
