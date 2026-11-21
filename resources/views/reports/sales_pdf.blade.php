<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 18px; margin: 0 0 2px 0; text-align: center; }
        h2 { font-size: 13px; margin: 16px 0 6px 0; border-bottom: 1px solid #2e7d32; padding-bottom: 2px; color: #1b5e20; }
        .sub { text-align: center; color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 6px; }
        th { background: #e8f5e9; text-align: left; }
        .r { text-align: right; }
        .c { text-align: center; }
        .box { border: 1px solid #2e7d32; padding: 6px 10px; margin-bottom: 6px; }
        .total td { font-weight: bold; background: #f1f8e9; }
        .meta { font-size: 9px; color: #777; margin-top: 12px; }
    </style>
</head>
<body>

<h1>{{ $shop ? trim($shop->shop_name) : 'All shops' }}</h1>
<div class="sub">
    {{ $title }} —
    @if ($type === 'daily')
        {{ $label }}
    @else
        {{ $from->format('d-m-Y') }} to {{ $to->format('d-m-Y') }}
    @endif
    @if ($summary->memo_count > $limit || $productTotal > $limit)
        &nbsp;&middot;&nbsp; Part {{ $part + 1 }}
    @endif
</div>

<table class="box">
    <tr>
        <td style="border:none;">Total memos: <b>{{ number_format($summary->memo_count) }}</b></td>
        <td style="border:none;" class="r">Total sales: <b>Tk {{ number_format($summary->grand_total, 2) }}</b></td>
    </tr>
</table>

{{-- Daily: memo list --}}
@if ($type === 'daily')
    <h2>Memo List @if ($summary->memo_count > $limit)(showing {{ $memos->count() ? $offset . ' – ' . ($offset + $memos->count() - 1) : '0' }} of {{ $summary->memo_count }})@endif</h2>
    <table>
        <thead>
        <tr>
            <th class="c" style="width:8%;">Memo No</th>
            <th style="width:30%;">Shop</th>
            <th>Customer</th>
            <th class="r" style="width:18%;">Total (Tk)</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($memos as $memo)
            <tr>
                <td class="c">{{ $memo->memo_no }}</td>
                <td>{{ trim(optional($memo->shop)->shop_name) }}</td>
                <td>{{ $memo->customer_name ?: '—' }}</td>
                <td class="r">{{ number_format($memo->total_amount, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="c">{{ $summary->memo_count > 0 ? 'No more memos in this part.' : 'No memos on this day.' }}</td></tr>
        @endforelse
        @if ($memos->count())
            @if ($summary->memo_count > $limit)
                <tr class="total"><td colspan="3" class="r">Total of listed memos</td><td class="r">{{ number_format($memos->sum('total_amount'), 2) }}</td></tr>
            @else
                <tr class="total"><td colspan="3" class="r">Grand Total (all {{ $summary->memo_count }} memos)</td><td class="r">{{ number_format($summary->grand_total, 2) }}</td></tr>
            @endif
        @endif
        </tbody>
    </table>
@elseif ($part === 0)
    {{-- Monthly / Yearly: breakdown (first part only) --}}
    <h2>{{ $type === 'monthly' ? 'Sales by Day' : 'Sales by Month' }}</h2>
    <table>
        <thead>
        <tr>
            <th>{{ $type === 'monthly' ? 'Date' : 'Month' }}</th>
            <th class="c" style="width:18%;">Memos</th>
            <th class="r" style="width:25%;">Total (Tk)</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($breakdown as $row)
            <tr>
                <td>
                    @if ($type === 'monthly')
                        {{ \Carbon\Carbon::parse($row->period)->format('d-m-Y') }}
                    @else
                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $row->period . '-01')->format('m-Y') }}
                    @endif
                </td>
                <td class="c">{{ $row->memo_count }}</td>
                <td class="r">{{ number_format($row->total, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="c">No memos in this period.</td></tr>
        @endforelse
        @if ($breakdown->count())
            <tr class="total">
                <td>Grand Total</td>
                <td class="c">{{ $summary->memo_count }}</td>
                <td class="r">{{ number_format($summary->grand_total, 2) }}</td>
            </tr>
        @endif
        </tbody>
    </table>
@endif

{{-- Product wise --}}
<h2>Sales by Product @if ($productTotal > $limit)(showing {{ $products->count() ? $offset . ' – ' . ($offset + $products->count() - 1) : '0' }} of {{ $productTotal }})@endif</h2>
<table>
    <thead>
    <tr>
        <th class="c" style="width:6%;">#</th>
        <th>Product name</th>
        <th class="r" style="width:14%;">Qty</th>
        <th class="r" style="width:20%;">Total (Tk)</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($products as $i => $p)
        <tr>
            <td class="c">{{ $offset + $i + 1 }}</td>
            <td>{{ $p->name }}</td>
            <td class="r">{{ rtrim(rtrim(number_format($p->qty, 2), '0'), '.') }}</td>
            <td class="r">{{ number_format($p->amount, 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="c">{{ $productTotal > 0 ? 'No more products in this part.' : 'No sales.' }}</td></tr>
    @endforelse
    @if ($products->count())
        <tr class="total">
            <td colspan="3" class="r">{{ $productTotal > $limit ? 'Total of listed products' : 'Grand Total' }}</td>
            <td class="r">{{ number_format($products->sum('amount'), 2) }}</td>
        </tr>
    @endif
    </tbody>
</table>

<div class="meta">Generated: {{ $generated }}</div>
</body>
</html>