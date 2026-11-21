<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    private function rules(): array
    {
        return [
            'company_name'     => 'required|string|max:150',
            'contact_person'   => 'nullable|string|max:150',
            'address'          => 'nullable|string|max:255',
            'phone_1'          => 'nullable|string|max:20',
            'phone_2'          => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:150',
            'trade_license_no' => 'nullable|string|max:60',
            'note'             => 'nullable|string|max:255',
        ];
    }

    public function index()
    {
        $companies = DB::table('companies as c')
            ->leftJoin('v_company_due as v', 'v.company_id', '=', 'c.company_id')
            ->select('c.*', 'v.total_purchased', 'v.total_paid', 'v.total_due')
            ->orderBy('c.company_name')->paginate(20);
        return view('companies.index', compact('companies'));
    }

    public function create() { return view('companies.create'); }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules());
        $data['is_active'] = $r->boolean('is_active', true) ? 1 : 0;
        DB::table('companies')->insert($data);
        return redirect()->route('companies.index')->with('success', 'কোম্পানি যোগ করা হয়েছে।');
    }

    public function show($id)
    {
        $company = DB::table('companies')->where('company_id', $id)->first();
        abort_if(!$company, 404);
        $purchases = DB::table('purchases')->where('company_id', $id)->orderByDesc('purchase_date')->get();
        $payments  = DB::table('company_payments')->where('company_id', $id)->orderByDesc('payment_date')->get();
        $due = DB::table('v_company_due')->where('company_id', $id)->first();
        return view('companies.show', compact('company', 'purchases', 'payments', 'due'));
    }

    public function edit($id)
    {
        $company = DB::table('companies')->where('company_id', $id)->first();
        abort_if(!$company, 404);
        return view('companies.edit', compact('company'));
    }

    public function update(Request $r, $id)
    {
        $data = $r->validate($this->rules());
        $data['is_active'] = $r->boolean('is_active') ? 1 : 0;
        DB::table('companies')->where('company_id', $id)->update($data);
        return redirect()->route('companies.show', $id)->with('success', 'হালনাগাদ করা হয়েছে।');
    }

    public function destroy($id)
    {
        if (DB::table('purchases')->where('company_id', $id)->exists()) {
            return back()->withErrors('এই কোম্পানির ক্রয়ের হিসাব আছে, তাই মোছা যাবে না। চাইলে নিষ্ক্রিয় করুন।');
        }
        DB::table('companies')->where('company_id', $id)->delete();
        return redirect()->route('companies.index')->with('success', 'মুছে ফেলা হয়েছে।');
    }
}
