<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:150'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    /** Customers with their current due balance (ledger: due - payment). */
    private function withBalance()
    {
        return Customer::query()
            ->select('customers.*')
            ->selectRaw("COALESCE((SELECT SUM(CASE entry_type WHEN 'due' THEN amount ELSE -amount END)
                                   FROM customer_ledger WHERE customer_ledger.customer_id = customers.customer_id), 0) AS due_balance");
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $customers = $this->withBalance()
            ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x
                ->where('name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->orWhere('address', 'like', "%{$q}%")))
            ->when($request->boolean('due_only'), fn ($w) => $w->havingRaw('due_balance > 0'))
            ->orderByDesc('customer_id')
            ->paginate(25)->withQueryString();

        return view('customers.index', compact('customers', 'q'));
    }

    public function create()
    {
        return view('customers.form', ['customer' => new Customer()]);
    }

    public function store(Request $request)
    {
        $customer = Customer::create($request->validate($this->rules()));

        return redirect()->route('customers.show', $customer->customer_id)->with('success', 'গ্রাহক যোগ করা হয়েছে');
    }

    public function show(Customer $customer)
    {
        $customer->load(['memos' => fn ($q) => $q->orderByDesc('memo_id')->limit(20)]);
        $ledger  = $customer->ledger()->with('memo')->orderBy('entry_date')->orderBy('ledger_id')->get();
        $balance = $customer->currentDue();

        return view('customers.show', compact('customer', 'ledger', 'balance'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($request->validate($this->rules()));

        return redirect()->route('customers.show', $customer->customer_id)->with('success', 'গ্রাহকের তথ্য আপডেট হয়েছে');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->memos()->exists() || $customer->ledger()->exists()) {
            return back()->withErrors(['delete' => 'এই গ্রাহকের মেমো/হিসাব আছে, তাই মুছে ফেলা যাবে না।']);
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'গ্রাহক মুছে ফেলা হয়েছে');
    }

    /** PDF: all customers with dues (?all=1 = every customer). */
    public function report(Request $request)
    {
        $all = $request->boolean('all');

        $customers = $this->withBalance()
            ->when(! $all, fn ($w) => $w->havingRaw('due_balance > 0'))
            ->orderByDesc('due_balance')->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('customers.report_pdf', [
            'customers'  => $customers,
            'all'        => $all,
            'total'      => $customers->sum('due_balance'),
            'generated'  => now()->format('d-m-Y h:i A'),
        ])->setPaper('a4', 'portrait');

        return $request->boolean('download')
            ? $pdf->download('customer-due-report.pdf')
            : $pdf->stream('customer-due-report.pdf');
    }

    /** PDF: one customer's statement (all due/payment entries with running balance). */
    public function statement(Request $request, Customer $customer)
    {
        $ledger = $customer->ledger()->with('memo')->orderBy('entry_date')->orderBy('ledger_id')->get();

        $pdf = Pdf::loadView('customers.statement_pdf', [
            'customer'  => $customer,
            'ledger'    => $ledger,
            'balance'   => $customer->currentDue(),
            'generated' => now()->format('d-m-Y h:i A'),
        ])->setPaper('a4', 'portrait');

        $file = 'statement-' . $customer->customer_id . '.pdf';

        return $request->boolean('download') ? $pdf->download($file) : $pdf->stream($file);
    }
}
