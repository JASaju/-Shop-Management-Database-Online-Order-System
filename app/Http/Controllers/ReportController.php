<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\MemoItem;
use App\Models\Shop;
use App\Support\ReportFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /** Max rows in any list inside one PDF part. */
    private const LIST_LIMIT = 100;

    public function index()
    {
        return view('reports.index', [
            'shops' => Shop::orderBy('shop_name')->get(),
            'today' => now()->toDateString(),
            'month' => now()->format('Y-m'),
            'year'  => now()->year,
        ]);
    }

    /** Validate + work out period/filters (shared by count() and pdf()). */
    private function context(Request $request): array
    {
        $request->validate([
            'type'    => ['required', 'in:daily,monthly,yearly'],
            'date'    => ['nullable', 'date_format:Y-m-d'],
            'month'   => ['nullable', 'date_format:Y-m'],
            'year'    => ['nullable', 'digits:4'],
            'shop_id' => ['nullable', 'exists:shops,shop_id'],
            'part'    => ['nullable', 'integer', 'min:0', 'max:10000'],
        ]);

        $type   = $request->input('type');
        $shopId = $request->filled('shop_id') ? (int) $request->input('shop_id') : null;
        $shop   = $shopId ? Shop::find($shopId) : null;

        switch ($type) {
            case 'daily':
                $day   = Carbon::parse($request->input('date', now()->toDateString()));
                $start = $day->copy()->startOfDay();
                $end   = $day->copy()->endOfDay();
                $title = 'Daily Sales Report';
                $label = $day->format('d-m-Y');
                break;

            case 'monthly':
                $m     = Carbon::createFromFormat('Y-m-d', ($request->input('month', now()->format('Y-m'))) . '-01');
                $start = $m->copy()->startOfMonth();
                $end   = $m->copy()->endOfMonth();
                $title = 'Monthly Sales Report';
                $label = $m->format('m-Y');
                break;

            default: // yearly
                $y     = Carbon::create((int) $request->input('year', now()->year), 1, 1);
                $start = $y->copy()->startOfYear();
                $end   = $y->copy()->endOfYear();
                $title = 'Yearly Sales Report';
                $label = $y->format('Y');
        }

        $filters = ['shop_id' => $shopId, 'from' => $start->toDateString(), 'to' => $end->toDateString()];

        return compact('type', 'shopId', 'shop', 'start', 'end', 'title', 'label', 'filters');
    }

    private function memoBase(array $filters)
    {
        return ReportFilter::apply(Memo::query(), $filters, ['shop' => 'shop_id', 'date' => 'memo_date']);
    }

    private function productQuery(array $filters)
    {
        return ReportFilter::apply(
                MemoItem::query()->join('memos', 'memos.memo_id', '=', 'memo_items.memo_id'),
                $filters,
                ['shop' => 'memos.shop_id', 'date' => 'memos.memo_date']
            )
            ->selectRaw('memo_items.medicine_name as name,
                         SUM(memo_items.quantity) as qty,
                         SUM(memo_items.quantity * memo_items.unit_price) as amount')
            ->groupBy('memo_items.medicine_name');
    }

    private function productTotal($productQuery): int
    {
        return (int) DB::query()->fromSub((clone $productQuery)->select(DB::raw('1 as x')), 't')->count();
    }

    /**
     * GET /reports/count?type=&date=&month=&year=&shop_id=
     * JSON for the "part" dropdown (0 – 99, 100 – 199, ...).
     */
    public function count(Request $request)
    {
        $c = $this->context($request);

        $memoCount    = (clone $this->memoBase($c['filters']))->count();
        $productTotal = $this->productTotal($this->productQuery($c['filters']));

        // daily has two paged lists (memos + products) -> parts cover the bigger one
        $total = $c['type'] === 'daily' ? max($memoCount, $productTotal) : $productTotal;

        $size      = self::LIST_LIMIT;
        $partCount = max(1, (int) ceil($total / $size));
        $parts     = [];
        for ($i = 0; $i < $partCount; $i++) {
            $from = $i * $size;
            $to   = min($total, ($i + 1) * $size) - 1;
            $parts[] = ['value' => $i, 'label' => $total === 0 ? '0' : $from . ' – ' . $to];
        }

        return response()->json(['total' => $total, 'size' => $size, 'parts' => $parts]);
    }

    /**
     * GET /reports/pdf?type=daily|monthly|yearly&date=&month=&year=&shop_id=&part=0&download=1
     */
    public function pdf(Request $request)
    {
        $c = $this->context($request);
        extract($c); // type, shop, start, end, title, label, filters

        $part   = (int) $request->input('part', 0);
        $offset = $part * self::LIST_LIMIT;

        $memoBase = $this->memoBase($filters);

        $summary = (clone $memoBase)
            ->selectRaw('COUNT(*) as memo_count, COALESCE(SUM(total_amount), 0) as grand_total')
            ->first();

        $memos = collect();
        $breakdown = collect();

        if ($type === 'daily') {
            $memos = (clone $memoBase)->with('shop')->orderByDesc('memo_id')
                ->offset($offset)->limit(self::LIST_LIMIT)->get();
        } elseif ($part === 0) {
            // day/month breakdown is small -> shown once, in the first part only
            $periodExpr = $type === 'monthly' ? 'memo_date' : "DATE_FORMAT(memo_date, '%Y-%m')";

            $breakdown = (clone $memoBase)
                ->select(DB::raw("$periodExpr as period"))
                ->selectRaw('COUNT(*) as memo_count, SUM(total_amount) as total')
                ->groupBy(DB::raw($periodExpr))
                ->orderBy(DB::raw($periodExpr))
                ->get();
        }

        $productQuery = $this->productQuery($filters);
        $productTotal = $this->productTotal($productQuery);
        $products     = $productQuery->orderByDesc('amount')->offset($offset)->limit(self::LIST_LIMIT)->get();

        $pdf = Pdf::loadView('reports.sales_pdf', [
            'type'         => $type,
            'title'        => $title,
            'label'        => $label,
            'from'         => $start,
            'to'           => $end,
            'shop'         => $shop,
            'summary'      => $summary,
            'memos'        => $memos,
            'breakdown'    => $breakdown,
            'products'     => $products,
            'productTotal' => $productTotal,
            'limit'        => self::LIST_LIMIT,
            'part'         => $part,
            'offset'       => $offset,
            'generated'    => now()->format('d-m-Y h:i A'),
        ])->setPaper('a4', 'portrait');

        $filename = "sales-report-{$type}-{$label}-part" . ($part + 1) . '.pdf';

        return $request->boolean('download')
            ? $pdf->download($filename)
            : $pdf->stream($filename);
    }
}
