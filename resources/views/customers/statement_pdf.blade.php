<!DOCTYPE html>
<html lang="bn"><head><meta charset="UTF-8"><title>স্টেটমেন্ট - {{ $customer->name }}</title>
    <style>
        @page { size: A4; margin: 16mm 12mm; }
        @font-face { font-family: 'SolaimanLipi'; font-style: normal; font-weight: 400;
            src: url("{{ str_replace('\\', '/', storage_path('fonts/SolaimanLipi.ttf')) }}") format('truetype'); }
        @font-face { font-family: 'SolaimanLipi'; font-style: normal; font-weight: 700;
            src: url("{{ str_replace('\\', '/', storage_path('fonts/SolaimanLipi_Bold.ttf')) }}") format('truetype'); }
        body { font-family: 'SolaimanLipi', 'Nikosh', sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; text-align: center; margin: 0 0 2px; color: #4c1d95; }
        .sub { text-align: center; color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #5b21b6; color: #fff; padding: 5px 6px; border: 1px solid #5b21b6; font-size: 11px; }
        td { border: 1px solid #c4b5fd; padding: 4px 6px; font-size: 11.5px; }
        .r { text-align: right; } .c { text-align: center; }
        .total td { font-weight: 700; background: #f5f3ff; }
        .meta { font-size: 9px; color: #777; margin-top: 10px; }
    </style>
</head><body>
<h1>গ্রাহকের হিসাব বিবরণী</h1>
<div class="sub">{{ $customer->name }} · {{ $customer->phone }} · {{ $customer->address }}</div>
<table>
    <thead><tr><th style="width:14%">তারিখ</th><th>বিবরণ</th><th class="r" style="width:16%">বাকি</th><th class="r" style="width:16%">জমা</th><th class="r" style="width:18%">ব্যালেন্স</th></tr></thead>
    <tbody>
    @php $run = 0; @endphp
    @forelse ($ledger as $l)
        @php $run += $l->entry_type === 'due' ? $l->amount : -$l->amount; @endphp
        <tr>
            <td>{{ \Carbon\Carbon::parse($l->entry_date)->format('d-m-Y') }}</td>
            <td>@if ($l->memo)মেমো #{{ $l->memo->memo_no }}@else{{ $l->entry_type === 'payment' ? 'জমা' : 'বাকি' }}@endif @if($l->note && !$l->memo) — {{ $l->note }}@endif</td>
            <td class="r">{{ $l->entry_type === 'due' ? number_format($l->amount, 2) : '' }}</td>
            <td class="r">{{ $l->entry_type === 'payment' ? number_format($l->amount, 2) : '' }}</td>
            <td class="r">{{ number_format($run, 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="c">কোনো লেনদেন নেই।</td></tr>
    @endforelse
    <tr class="total"><td colspan="4" class="r">বর্তমান বাকি</td><td class="r">{{ number_format($balance, 2) }}</td></tr>
    </tbody>
</table>
<div class="meta">Generated: {{ $generated }}</div>
</body></html>
