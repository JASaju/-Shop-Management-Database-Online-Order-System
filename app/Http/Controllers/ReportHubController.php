<?php

namespace App\Http\Controllers;

use App\Support\ReportFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportHubController extends Controller
{
    /** report key => [title, filters it understands, landscape?] */
    private const REPORTS = [
        'memos'         => ['Memo Report',               ['shop_id', 'from', 'to', 'customer_id', 'staff_id', 'due_only'], true],
        'shops'         => ['Shop Report',               ['shop_id', 'from', 'to'], true],
        'customers'     => ['Customer Report',           ['shop_id', 'from', 'to', 'customer_id', 'due_only'], true],
        'companies'     => ['Company Report',            ['shop_id', 'from', 'to', 'company_id', 'due_only'], true],
        'purchases'     => ['Purchase Report',           ['shop_id', 'from', 'to', 'company_id', 'due_only'], true],
        'product_sales' => ['Product Sales Report',      ['shop_id', 'from', 'to', 'company_id', 'customer_id', 'staff_id'], false],
        'product_stock' => ['Product Stock & Expiry Report', ['shop_id', 'company_id', 'expiry', 'days'], true],
    ];

    /** Max rows per PDF. Bigger lists are split into parts (1-100, 101-200, ...). */
    private const PAGE_SIZE = 100;

    public function index()
    {
        return view('reports.hub', [
            'shops'     => DB::table('shops')->orderBy('shop_name')->get(['shop_id', 'shop_name']),
            'companies' => DB::table('companies')->orderBy('company_name')->get(['company_id', 'company_name']),
            'customers' => DB::table('customers')->whereNotNull('name')->orderBy('name')->get(['customer_id', 'name']),
            'staff'     => DB::table('staff')->orderBy('name')->get(['staff_id', 'name']),
            'month_start' => now()->startOfMonth()->toDateString(),
            'today'       => now()->toDateString(),
        ]);
    }

    /** Validate request and return [title, landscape, filters]. */
    private function parse(Request $r): array
    {
        $r->validate([
            'report'      => 'required|in:' . implode(',', array_keys(self::REPORTS)),
            'shop_id'     => 'nullable|integer|exists:shops,shop_id',
            'company_id'  => 'nullable|integer|exists:companies,company_id',
            'customer_id' => 'nullable|integer|exists:customers,customer_id',
            'staff_id'    => 'nullable|integer|exists:staff,staff_id',
            'from'        => 'nullable|date',
            'to'          => 'nullable|date|after_or_equal:from',
            'expiry'      => 'nullable|in:stock,expired,soon,valid,no_stock,all',
            'days'        => 'nullable|integer|min:1|max:1000',
            'part'        => 'nullable|integer|min:0|max:10000',
        ]);

        [$title, $allowed, $landscape] = self::REPORTS[$r->report];

        // keep only the filters this report understands
        $f = [];
        foreach ($allowed as $k) {
            $f[$k] = $k === 'due_only' ? $r->boolean('due_only') : $r->input($k);
        }

        return [$title, $landscape, $f];
    }

    /** JSON: record count + ready-made labels for the "part" dropdown (0 – 99, 100 – 199, ...). */
    public function count(Request $r)
    {
        [, , $f] = $this->parse($r);

        $built = $this->{'build' . Str::studly($r->report)}($f, ['offset' => 0, 'limit' => 1]);

        $total = (int) $built['total'];
        $size  = self::PAGE_SIZE;

        $parts     = [];
        $partCount = max(1, (int) ceil($total / $size));
        for ($i = 0; $i < $partCount; $i++) {
            $from = $i * $size;
            $to   = min($total, ($i + 1) * $size) - 1;
            $parts[] = [
                'value' => $i,
                'label' => $total === 0 ? '0' : $from . ' – ' . $to,
            ];
        }

        return response()->json(['total' => $total, 'size' => $size, 'parts' => $parts]);
    }

    public function pdf(Request $r)
    {
        [$title, $landscape, $f] = $this->parse($r);

        $size   = self::PAGE_SIZE;
        $offset = (int) $r->input('part', 0) * $size;

        $built = $this->{'build' . Str::studly($r->report)}($f, ['offset' => $offset, 'limit' => $size]);
        $total = (int) $built['total'];

        [$rows, $totals] = $this->format($built['cols'], $built['rows'], $total > count($built['rows']) ? 'Page Total' : 'Total');

        $rangeText = $total === 0
            ? '0 record(s)'
            : (count($rows)
                ? 'Showing ' . $offset . ' to ' . ($offset + count($rows) - 1) . ' of ' . $total . ' record(s)'
                : 'No records in this part (total ' . $total . ')');

        $pdf = Pdf::loadView('reports.hub_pdf', [
            'title'      => $title,
            'filterText' => $this->filterText($f),
            'cols'       => $built['cols'],
            'rows'       => $rows,
            'totals'     => $totals,
            'count'      => count($rows),
            'rangeText'  => $rangeText,
            'generated'  => now()->format('d-m-Y h:i A'),
        ])->setPaper('a4', $landscape ? 'landscape' : 'portrait');
        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font   = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
        $canvas->page_text($canvas->get_width() - 70, $canvas->get_height() - 28, '{PAGE_NUM} / {PAGE_COUNT}', $font, 8, [0.4, 0.4, 0.4]);

        $from = $offset;
        $to   = $offset + max(count($rows), 1) - 1;
        $file = Str::slug($r->report) . "-report-{$from}-{$to}-" . now()->format('Ymd') . '.pdf';
        return $r->boolean('download') ? $pdf->download($file) : $pdf->stream($file);
    }

    /* ======================================================== report builders */

    private function buildMemos(array $f, array $page): array
    {
        $q = DB::table('memos as m')
            ->leftJoin('shops as s', 's.shop_id', '=', 'm.shop_id')
            ->leftJoin('staff as st', 'st.staff_id', '=', 'm.staff_id');
        ReportFilter::apply($q, $f, ['shop' => 'm.shop_id', 'date' => 'm.memo_date', 'customer' => 'm.customer_id', 'staff' => 'm.staff_id', 'due' => 'm.due_amount']);

        $total = $this->countRows($q);

        $rows = $q->orderByDesc('m.memo_date')->orderByDesc('m.memo_id')
            ->offset($page['offset'])->limit($page['limit'])
            ->get(['m.memo_no', 'm.memo_date', 's.shop_name', 'm.customer_name', 'st.name as staff_name', 'm.total_amount', 'm.discount_amount', 'm.due_amount'])
            ->map(function ($m) {
                $net = (float) $m->total_amount - (float) $m->discount_amount;
                return [
                    'no' => $m->memo_no, 'date' => $m->memo_date, 'shop' => trim((string) $m->shop_name),
                    'customer' => $m->customer_name ?: '-', 'staff' => $m->staff_name ?: '-',
                    'total' => (float) $m->total_amount, 'discount' => (float) $m->discount_amount, 'net' => $net,
                    'paid' => $net - (float) $m->due_amount, 'due' => (float) $m->due_amount,
                ];
            })->all();

        return ['rows' => $rows, 'total' => $total, 'cols' => [
            $this->c('no', 'Memo No', 'text'), $this->c('date', 'Date', 'date'), $this->c('shop', 'Shop', 'text'),
            $this->c('customer', 'Customer', 'text'), $this->c('staff', 'Seller', 'text'),
            $this->c('total', 'Total', 'money', true), $this->c('discount', 'Discount', 'money', true),
            $this->c('net', 'Net', 'money', true), $this->c('paid', 'Paid', 'money', true), $this->c('due', 'Due', 'money', true),
        ]];
    }

    private function buildShops(array $f, array $page): array
    {
        $shops = DB::table('shops')->when(!empty($f['shop_id']), fn ($q) => $q->where('shop_id', $f['shop_id']))->orderBy('shop_name')->get();
        $rows = [];
        foreach ($shops as $s) {
            $fs = array_merge($f, ['shop_id' => $s->shop_id]);

            $m = ReportFilter::apply(DB::table('memos'), $fs, ['shop' => 'shop_id', 'date' => 'memo_date'])
                ->selectRaw('COUNT(*) AS c, COALESCE(SUM(total_amount),0) AS t, COALESCE(SUM(discount_amount),0) AS d, COALESCE(SUM(due_amount),0) AS due')->first();
            $p = ReportFilter::apply(DB::table('purchases'), $fs, ['shop' => 'shop_id', 'date' => 'purchase_date'])
                ->selectRaw('COALESCE(SUM(total_amount - discount_amount),0) AS net, COALESCE(SUM(due_amount),0) AS due')->first();

            $rows[] = [
                'shop' => trim($s->shop_name), 'owner' => $s->owner_name ?: '-',
                'memos' => (int) $m->c, 'sales' => (float) $m->t - (float) $m->d, 'discount' => (float) $m->d, 'cdue' => (float) $m->due,
                'purchases' => (float) $p->net, 'pdue' => (float) $p->due,
                'staff' => DB::table('staff')->where('shop_id', $s->shop_id)->where('is_active', 1)->count(),
                'instr' => (int) DB::table('shop_instruments')->where('shop_id', $s->shop_id)->sum('quantity'),
            ];
        }
        $total = count($rows);
        $rows  = array_slice($rows, $page['offset'], $page['limit']);

        return ['rows' => $rows, 'total' => $total, 'cols' => [
            $this->c('shop', 'Shop', 'text'), $this->c('owner', 'Owner', 'text'), $this->c('memos', 'Memos', 'int', true),
            $this->c('sales', 'Net Sales', 'money', true), $this->c('discount', 'Discount Given', 'money', true), $this->c('cdue', 'Customer Due', 'money', true),
            $this->c('purchases', 'Purchases (net)', 'money', true), $this->c('pdue', 'Payable to Companies', 'money', true),
            $this->c('staff', 'Staff', 'int', true), $this->c('instr', 'Instruments', 'int', true),
        ]];
    }

    private function buildCustomers(array $f, array $page): array
    {
        $sub = DB::table('memos')->whereNotNull('customer_id')
            ->selectRaw('customer_id, COUNT(*) AS memo_count, COALESCE(SUM(total_amount - discount_amount),0) AS sales');
        ReportFilter::apply($sub, $f, ['shop' => 'shop_id', 'date' => 'memo_date']);
        $sub->groupBy('customer_id');

        $q = DB::table('customers as c')
            ->leftJoin('v_customer_due as v', 'v.customer_id', '=', 'c.customer_id')
            ->leftJoinSub($sub, 'm', 'm.customer_id', '=', 'c.customer_id');
        ReportFilter::apply($q, $f, ['customer' => 'c.customer_id', 'due' => 'v.balance']);

        $total = $this->countRows($q);

        $rows = $q->selectRaw('c.name, c.phone, c.address, COALESCE(m.memo_count,0) AS memo_count, COALESCE(m.sales,0) AS sales,
                               COALESCE(v.total_due,0) AS total_due, COALESCE(v.total_paid,0) AS total_paid, COALESCE(v.balance,0) AS balance')
            ->orderByDesc('balance')->orderBy('c.name')->offset($page['offset'])->limit($page['limit'])->get()
            ->map(fn ($c) => [
                'name' => trim((string) $c->name), 'phone' => $c->phone ?: '-', 'address' => $c->address ?: '-',
                'memos' => (int) $c->memo_count, 'sales' => (float) $c->sales,
                'due' => (float) $c->total_due, 'paid' => (float) $c->total_paid, 'balance' => (float) $c->balance,
            ])->all();

        return ['rows' => $rows, 'total' => $total, 'cols' => [
            $this->c('name', 'Customer', 'text'), $this->c('phone', 'Phone', 'text'), $this->c('address', 'Address', 'text'),
            $this->c('memos', 'Memos', 'int', true), $this->c('sales', 'Net Sales (period)', 'money', true),
            $this->c('due', 'Total Due', 'money', true), $this->c('paid', 'Received', 'money', true), $this->c('balance', 'Balance', 'money', true),
        ]];
    }

    private function buildCompanies(array $f, array $page): array
    {
        $sub = DB::table('purchases')
            ->selectRaw('company_id, COUNT(*) AS cnt, SUM(total_amount) AS total, SUM(discount_amount) AS disc, SUM(paid_amount) AS paid, SUM(due_amount) AS due');
        ReportFilter::apply($sub, $f, ['shop' => 'shop_id', 'date' => 'purchase_date', 'company' => 'company_id']);
        $sub->groupBy('company_id');

        $q = DB::table('companies as c')->leftJoinSub($sub, 'p', 'p.company_id', '=', 'c.company_id');
        ReportFilter::apply($q, $f, ['company' => 'c.company_id', 'due' => 'p.due']);

        $total = $this->countRows($q);

        $rows = $q->selectRaw('c.company_name, c.phone_1, c.contact_person, COALESCE(p.cnt,0) AS cnt, COALESCE(p.total,0) AS total,
                               COALESCE(p.disc,0) AS disc, COALESCE(p.paid,0) AS paid, COALESCE(p.due,0) AS due')
            ->orderByDesc('due')->orderBy('c.company_name')->offset($page['offset'])->limit($page['limit'])->get()
            ->map(fn ($c) => [
                'name' => trim($c->company_name), 'contact' => trim(($c->contact_person ?: '') . ' ' . ($c->phone_1 ?: '')) ?: '-',
                'cnt' => (int) $c->cnt, 'total' => (float) $c->total, 'disc' => (float) $c->disc,
                'net' => (float) $c->total - (float) $c->disc, 'paid' => (float) $c->paid, 'due' => (float) $c->due,
            ])->all();

        return ['rows' => $rows, 'total' => $total, 'cols' => [
            $this->c('name', 'Company', 'text'), $this->c('contact', 'Contact', 'text'), $this->c('cnt', 'Purchases', 'int', true),
            $this->c('total', 'Total', 'money', true), $this->c('disc', 'Discount', 'money', true), $this->c('net', 'Net', 'money', true),
            $this->c('paid', 'Paid', 'money', true), $this->c('due', 'Payable', 'money', true),
        ]];
    }

    private function buildPurchases(array $f, array $page): array
    {
        $q = DB::table('purchases as p')
            ->join('companies as c', 'c.company_id', '=', 'p.company_id')
            ->leftJoin('shops as s', 's.shop_id', '=', 'p.shop_id');
        ReportFilter::apply($q, $f, ['shop' => 'p.shop_id', 'date' => 'p.purchase_date', 'company' => 'p.company_id', 'due' => 'p.due_amount']);

        $total = $this->countRows($q);

        $rows = $q->orderByDesc('p.purchase_date')->orderByDesc('p.purchase_id')
            ->offset($page['offset'])->limit($page['limit'])
            ->get(['p.invoice_no', 'p.purchase_date', 'p.due_date', 'c.company_name', 's.shop_name', 'p.total_amount', 'p.discount_amount', 'p.paid_amount', 'p.due_amount'])
            ->map(fn ($p) => [
                'inv' => $p->invoice_no, 'date' => $p->purchase_date, 'dueDate' => $p->due_date,
                'company' => trim($p->company_name), 'shop' => trim((string) $p->shop_name),
                'net' => (float) $p->total_amount - (float) $p->discount_amount, 'paid' => (float) $p->paid_amount, 'due' => (float) $p->due_amount,
                '_class' => ((float) $p->due_amount > 0 && $p->due_date && $p->due_date < now()->toDateString()) ? 'exp' : '',
            ])->all();

        return ['rows' => $rows, 'total' => $total, 'cols' => [
            $this->c('inv', 'Invoice', 'text'), $this->c('date', 'Date', 'date'), $this->c('company', 'Company', 'text'), $this->c('shop', 'Shop', 'text'),
            $this->c('net', 'Net Amount', 'money', true), $this->c('paid', 'Paid', 'money', true), $this->c('due', 'Due', 'money', true),
            $this->c('dueDate', 'Pay By', 'date'),
        ]];
    }

    private function buildProductSales(array $f, array $page): array
    {
        $q = DB::table('memo_items as i')
            ->join('memos as m', 'm.memo_id', '=', 'i.memo_id')
            ->leftJoin('medicines as md', 'md.medicine_id', '=', 'i.medicine_id');
        ReportFilter::apply($q, $f, ['shop' => 'm.shop_id', 'date' => 'm.memo_date', 'customer' => 'm.customer_id', 'staff' => 'm.staff_id', 'company' => 'md.company_id']);

        $q->groupBy('i.medicine_name');
        $total = $this->countRows($q);

        $rows = $q->selectRaw('i.medicine_name AS name, SUM(i.quantity) AS qty, COUNT(DISTINCT m.memo_id) AS memos, SUM(i.quantity * i.unit_price) AS amount')
            ->orderByDesc('amount')->offset($page['offset'])->limit($page['limit'])->get()
            ->map(fn ($p) => ['name' => $p->name, 'qty' => (float) $p->qty, 'memos' => (int) $p->memos, 'amount' => (float) $p->amount])->all();

        return ['rows' => $rows, 'total' => $total, 'cols' => [
            $this->c('name', 'Product', 'text'), $this->c('memos', 'In Memos', 'int', true),
            $this->c('qty', 'Qty Sold', 'num', true), $this->c('amount', 'Amount (Tk)', 'money', true),
        ]];
    }

    private function buildProductStock(array $f, array $page): array
    {
        $mode = $f['expiry'] ?: 'stock';
        $days = (int) ($f['days'] ?: 90);

        $q = DB::table('medicines as md')
            ->leftJoin('companies as c', 'c.company_id', '=', 'md.company_id')
            ->leftJoin('purchase_items as b', function ($j) {
                $j->on('b.medicine_id', '=', 'md.medicine_id')->where('b.quantity_remaining', '>', 0);
            })
            ->leftJoin('purchases as p', 'p.purchase_id', '=', 'b.purchase_id');
        ReportFilter::apply($q, $f, ['shop' => 'p.shop_id', 'company' => 'md.company_id']);

        switch ($mode) {
            case 'stock':    $q->whereNotNull('b.batch_id'); break;
            case 'expired':  $q->whereRaw('b.expiry_date < CURDATE()'); break;
            case 'soon':     $q->whereRaw('b.expiry_date >= CURDATE() AND b.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)', [$days]); break;
            case 'valid':    $q->whereRaw('b.expiry_date >= CURDATE()'); break;
            case 'no_stock': $q->whereNull('b.batch_id'); break;
            default: break; // all
        }

        $total = $this->countRows($q);

        $rows = $q->selectRaw('md.name, c.company_name, md.unit, b.batch_no, b.expiry_date, DATEDIFF(b.expiry_date, CURDATE()) AS days_left,
                               b.quantity_remaining AS rem, b.purchase_price, b.mrp, (b.quantity_remaining * b.purchase_price) AS value')
            ->orderByRaw('b.expiry_date IS NULL, b.expiry_date')->orderBy('md.name')->orderBy('b.batch_id')
            ->offset($page['offset'])->limit($page['limit'])->get()
            ->map(function ($b) {
                $d = $b->days_left;
                if ($d === null)      { $status = 'No stock / no expiry date'; $cls = ''; }
                elseif ($d < 0)       { $status = 'Expired ' . abs($d) . ' days ago'; $cls = 'exp'; }
                elseif ($d <= 30)     { $status = 'Expires in ' . $d . ' days'; $cls = 'exp'; }
                elseif ($d <= 90)     { $status = 'Expires in ' . $d . ' days'; $cls = 'warn'; }
                else                  { $status = 'OK (' . $d . ' days)'; $cls = ''; }
                return [
                    'name' => $b->name, 'company' => $b->company_name ?: '-', 'batch' => $b->batch_no ?: '-',
                    'expiry' => $b->expiry_date, 'status' => $status,
                    'rem' => $d === null ? null : (float) $b->rem, 'mrp' => $d === null ? null : (float) $b->mrp,
                    'value' => $d === null ? null : (float) $b->value, '_class' => $cls,
                ];
            })->all();

        return ['rows' => $rows, 'total' => $total, 'cols' => [
            $this->c('name', 'Medicine', 'text'), $this->c('company', 'Company', 'text'), $this->c('batch', 'Batch', 'text'),
            $this->c('expiry', 'Expiry Date', 'date'), $this->c('status', 'Status', 'text'),
            $this->c('rem', 'In Stock', 'num', true), $this->c('mrp', 'MRP', 'money'), $this->c('value', 'Stock Value (cost)', 'money', true),
        ]];
    }

    /* ================================================================ helpers */

    /** Count the rows a query would return (works for GROUP BY queries too). */
    private function countRows($q): int
    {
        return (int) DB::query()->fromSub((clone $q)->select(DB::raw('1 as x')), 't')->count();
    }

    private function c(string $key, string $label, string $type, bool $sum = false): array
    {
        return ['key' => $key, 'label' => $label, 'type' => $type, 'sum' => $sum];
    }

    /** Turn raw rows into display strings + a totals line. */
    private function format(array $cols, array $raw, string $totalLabel = 'Total'): array
    {
        $rows = []; $sums = [];
        foreach ($raw as $r) {
            $cells = [];
            foreach ($cols as $i => $c) {
                $v = $r[$c['key']] ?? null;
                if ($c['sum'] && $v !== null) $sums[$i] = ($sums[$i] ?? 0) + (float) $v;
                $cells[] = $this->fmt($v, $c['type']);
            }
            $rows[] = ['cells' => $cells, 'class' => $r['_class'] ?? ''];
        }
        $totals = [];
        foreach ($cols as $i => $c) {
            $totals[] = $i === 0 ? $totalLabel : ($c['sum'] ? $this->fmt($sums[$i] ?? 0, $c['type']) : '');
        }
        return [$rows, $totals];
    }

    private function fmt($v, string $type): string
    {
        if ($v === null || $v === '') return '-';
        return match ($type) {
            'money' => number_format((float) $v, 2),
            'int'   => number_format((float) $v, 0),
            'num'   => rtrim(rtrim(number_format((float) $v, 2), '0'), '.'),
            'date'  => Carbon::parse($v)->format('d-m-Y'),
            default => (string) $v,
        };
    }

    private function filterText(array $f): array
    {
        $t = [];
        if (!empty($f['shop_id']))     $t[] = 'Shop: ' . trim((string) DB::table('shops')->where('shop_id', $f['shop_id'])->value('shop_name'));
        if (!empty($f['company_id']))  $t[] = 'Company: ' . DB::table('companies')->where('company_id', $f['company_id'])->value('company_name');
        if (!empty($f['customer_id'])) $t[] = 'Customer: ' . DB::table('customers')->where('customer_id', $f['customer_id'])->value('name');
        if (!empty($f['staff_id']))    $t[] = 'Seller: ' . DB::table('staff')->where('staff_id', $f['staff_id'])->value('name');
        if (!empty($f['from']) || !empty($f['to'])) {
            $t[] = 'Period: ' . (!empty($f['from']) ? Carbon::parse($f['from'])->format('d-m-Y') : 'beginning') . ' to ' . (!empty($f['to']) ? Carbon::parse($f['to'])->format('d-m-Y') : 'today');
        }
        if (!empty($f['due_only'])) $t[] = 'Only with due';
        if (array_key_exists('expiry', $f)) {
            $names = ['stock' => 'In stock (with expiry date)', 'expired' => 'Expired', 'soon' => 'Expiring within ' . ((int) ($f['days'] ?: 90)) . ' days',
                      'valid' => 'Not expired', 'no_stock' => 'No stock / no expiry date', 'all' => 'All medicines'];
            $t[] = 'Expiry: ' . $names[$f['expiry'] ?: 'stock'];
        }
        return $t ?: ['All records'];
    }
}
