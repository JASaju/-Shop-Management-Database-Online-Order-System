<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerReportController extends Controller
{
    public function index()
    {
        return view('ledgers.index', [
            'customers' => DB::table('customers')->whereNotNull('name')->orderBy('name')->get(['customer_id', 'name', 'phone']),
            'companies' => DB::table('companies')->orderBy('company_name')->get(['company_id', 'company_name']),
        ]);
    }

    /* ------------------------------------------------------------ Customers */

    public function customers(Request $r)
    {
        $r->validate([
            'mode'        => 'required|in:summary,single,all',
            'customer_id' => 'required_if:mode,single|nullable|integer|exists:customers,customer_id',
            'from'        => 'nullable|date',
            'to'          => 'nullable|date|after_or_equal:from',
        ]);
        $from = $r->from;
        $to   = $r->to;

        if ($r->mode === 'summary') {
            $query = DB::table('v_customer_due');
            if ($r->boolean('due_only')) {
                $query->where('balance', '>', 0);
            }
            $rows = $query->orderByDesc('balance')->get();

            $list = [];
            foreach ($rows as $c) {
                $list[] = [
                    trim($c->name ?? '-'),
                    $c->phone ?: '-',
                    $c->total_due,
                    $c->total_paid,
                    $c->balance,
                ];
            }

            return $this->pdf('ledgers.summary_pdf', [
                'heading' => 'Customer Ledger Summary',
                'period'  => 'As on ' . now()->format('d-m-Y'),
                'cols'    => ['Customer', 'Phone', 'Total Due (Tk)', 'Received (Tk)', 'Balance (Tk)'],
                'rows'    => $list,
                'totals'  => ['Total', '', $rows->sum('total_due'), $rows->sum('total_paid'), $rows->sum('balance')],
                'numFrom' => 2,
            ], 'customer-ledger-summary', $r);
        }

        if ($r->mode === 'single') {
            $ids = [(int) $r->customer_id];
        } else {
            $ids = DB::table('customer_ledger')->distinct()->pluck('customer_id')->all();
        }

        $sections  = [];
        $customers = DB::table('customers')->whereIn('customer_id', $ids)->orderBy('name')->get();
        foreach ($customers as $c) {
            $s = $this->customerSection($c, $from, $to);
            if ($r->mode === 'all' && $r->boolean('due_only') && $s['closing'] <= 0) {
                continue;
            }
            $sections[] = $s;
        }

        return $this->pdf('ledgers.statement_pdf', [
            'heading'  => $r->mode === 'single' ? 'Customer Ledger' : 'All Customer Ledgers',
            'period'   => $this->period($from, $to),
            'colA'     => 'Due (Tk)',
            'colB'     => 'Received (Tk)',
            'balLabel' => 'Balance (Tk)',
            'sections' => $sections,
        ], $r->mode === 'single' ? 'customer-ledger-' . $r->customer_id : 'all-customer-ledgers', $r);
    }

    private function customerSection($c, $from, $to): array
    {
        $opening = 0.0;
        if ($from) {
            $opening = (float) DB::table('customer_ledger')
                ->where('customer_id', $c->customer_id)
                ->where('entry_date', '<', $from)
                ->selectRaw("COALESCE(SUM(CASE WHEN entry_type='due' THEN amount ELSE -amount END),0) AS b")
                ->value('b');
        }

        $query = DB::table('customer_ledger as l')
            ->leftJoin('memos as m', 'm.memo_id', '=', 'l.memo_id')
            ->where('l.customer_id', $c->customer_id);
        if ($from) {
            $query->where('l.entry_date', '>=', $from);
        }
        if ($to) {
            $query->where('l.entry_date', '<=', $to);
        }
        $entries = $query->orderBy('l.entry_date')->orderBy('l.ledger_id')->get(['l.*', 'm.memo_no']);

        $rows = [];
        foreach ($entries as $e) {
            if ($e->entry_type === 'due') {
                $desc = 'Due';
                if ($e->memo_no) $desc .= ' - Memo #' . $e->memo_no;
                if ($e->note)    $desc .= ' (' . $e->note . ')';
            } else {
                $desc = 'Payment received';
                if ($e->method) $desc .= ' (' . $e->method . ')';
                if ($e->note)   $desc .= ' - ' . $e->note;
            }

            $rows[] = [
                'date' => $e->entry_date,
                'desc' => $desc,
                'a'    => $e->entry_type === 'due' ? (float) $e->amount : 0,
                'b'    => $e->entry_type === 'payment' ? (float) $e->amount : 0,
            ];
        }

        return $this->withBalance(
            trim($c->name ?? '-'),
            trim(($c->phone ?? '') . ' ' . ($c->address ?? '')),
            $opening,
            $rows
        );
    }

    /* ------------------------------------------------------------ Companies */

    public function companies(Request $r)
    {
        $r->validate([
            'mode'       => 'required|in:summary,single,all',
            'company_id' => 'required_if:mode,single|nullable|integer|exists:companies,company_id',
            'from'       => 'nullable|date',
            'to'         => 'nullable|date|after_or_equal:from',
        ]);
        $from = $r->from;
        $to   = $r->to;

        if ($r->mode === 'summary') {
            $query = DB::table('v_company_due');
            if ($r->boolean('due_only')) {
                $query->where('total_due', '>', 0);
            }
            $rows = $query->orderByDesc('total_due')->get();

            $list = [];
            foreach ($rows as $c) {
                $list[] = [
                    trim($c->company_name),
                    $c->phone_1 ?: '-',
                    $c->total_purchased,
                    $c->total_paid,
                    $c->total_due,
                ];
            }

            return $this->pdf('ledgers.summary_pdf', [
                'heading' => 'Company Ledger Summary',
                'period'  => 'As on ' . now()->format('d-m-Y'),
                'cols'    => ['Company', 'Phone', 'Purchased (Tk)', 'Paid (Tk)', 'Payable (Tk)'],
                'rows'    => $list,
                'totals'  => ['Total', '', $rows->sum('total_purchased'), $rows->sum('total_paid'), $rows->sum('total_due')],
                'numFrom' => 2,
            ], 'company-ledger-summary', $r);
        }

        $query = DB::table('companies');
        if ($r->mode === 'single') {
            $query->where('company_id', $r->company_id);
        }
        $companies = $query->orderBy('company_name')->get();

        $sections = [];
        foreach ($companies as $c) {
            $s = $this->companySection($c, $from, $to);
            if ($r->mode === 'all' && !$s['rows'] && !$s['opening']) {
                continue;
            }
            if ($r->mode === 'all' && $r->boolean('due_only') && $s['closing'] <= 0) {
                continue;
            }
            $sections[] = $s;
        }

        return $this->pdf('ledgers.statement_pdf', [
            'heading'  => $r->mode === 'single' ? 'Company Ledger' : 'All Company Ledgers',
            'period'   => $this->period($from, $to),
            'colA'     => 'Purchase (Tk)',
            'colB'     => 'Paid (Tk)',
            'balLabel' => 'Payable (Tk)',
            'sections' => $sections,
        ], $r->mode === 'single' ? 'company-ledger-' . $r->company_id : 'all-company-ledgers', $r);
    }

    private function companySection($c, $from, $to): array
    {
        $purchases = DB::table('purchases')->where('company_id', $c->company_id)
            ->get(['purchase_id', 'invoice_no', 'purchase_date as d', 'total_amount', 'discount_amount']);
        $payments = DB::table('company_payments')->where('company_id', $c->company_id)
            ->get(['payment_id', 'payment_date as d', 'amount', 'method', 'reference_no', 'note']);

        $all = [];
        foreach ($purchases as $p) {
            $all[] = [
                'date' => $p->d,
                'k'    => 0,
                'id'   => $p->purchase_id,
                'desc' => 'Purchase - Invoice ' . $p->invoice_no,
                'a'    => (float) $p->total_amount - (float) $p->discount_amount,
                'b'    => 0,
            ];
        }
        foreach ($payments as $p) {
            $desc = 'Payment';
            if ($p->method)       $desc .= ' (' . $p->method . ')';
            if ($p->reference_no) $desc .= ' Ref ' . $p->reference_no;
            if ($p->note)         $desc .= ' - ' . $p->note;

            $all[] = [
                'date' => $p->d,
                'k'    => 1,
                'id'   => $p->payment_id,
                'desc' => $desc,
                'a'    => 0,
                'b'    => (float) $p->amount,
            ];
        }

        usort($all, function ($x, $y) {
            return [$x['date'], $x['k'], $x['id']] <=> [$y['date'], $y['k'], $y['id']];
        });

        $opening = 0.0;
        $rows    = [];
        foreach ($all as $e) {
            if ($from && $e['date'] < $from) {
                $opening += $e['a'] - $e['b'];
                continue;
            }
            if ($to && $e['date'] > $to) {
                continue;
            }
            $rows[] = $e;
        }

        return $this->withBalance(
            trim($c->company_name),
            trim(($c->phone_1 ?? '') . ' ' . ($c->address ?? '')),
            $opening,
            $rows
        );
    }

    /* -------------------------------------------------------------- helpers */

    private function withBalance(string $title, string $subtitle, float $opening, array $rows): array
    {
        $bal = $opening;
        $a   = 0;
        $b   = 0;
        foreach ($rows as &$row) {
            $bal       += $row['a'] - $row['b'];
            $row['bal'] = $bal;
            $a         += $row['a'];
            $b         += $row['b'];
        }
        unset($row);

        return [
            'title'    => $title,
            'subtitle' => $subtitle,
            'opening'  => $opening,
            'rows'     => $rows,
            'sumA'     => $a,
            'sumB'     => $b,
            'closing'  => $bal,
        ];
    }

    private function period($from, $to): string
    {
        if (!$from && !$to) {
            return 'Full history · generated ' . now()->format('d-m-Y');
        }
        $f = $from ? Carbon::parse($from)->format('d-m-Y') : 'beginning';
        $t = $to ? Carbon::parse($to)->format('d-m-Y') : 'today';
        return 'Period: ' . $f . ' to ' . $t;
    }

    private function pdf(string $view, array $data, string $name, Request $r)
    {
        $data['generated'] = now()->format('d-m-Y H:i');

        $pdf = Pdf::loadView($view, $data)->setPaper('a4', 'portrait');
        $pdf->render();

        // page numbers in the footer
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font   = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
        $canvas->page_text($canvas->get_width() - 70, $canvas->get_height() - 28, '{PAGE_NUM} / {PAGE_COUNT}', $font, 8, [0.4, 0.4, 0.4]);

        $file = $name . '-' . now()->format('Ymd') . '.pdf';
        return $r->boolean('download') ? $pdf->download($file) : $pdf->stream($file);
    }
}