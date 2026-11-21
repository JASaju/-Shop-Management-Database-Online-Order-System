<!DOCTYPE html>
<html lang="bn"><head><meta charset="UTF-8"><title>গ্রাহকের বাকি রিপোর্ট</title>
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
<h1>গ্রাহকের {{ $all ? 'তালিকা ও' : '' }} বাকি রিপোর্ট</h1>
<div class="sub">{{ $all ? 'সব গ্রাহক' : 'যাদের বাকি আছে' }} — মোট {{ $customers->count() }} জন</div>
<table>
    <thead><tr><th class="c" style="width:6%">#</th><th>নাম</th><th style="width:20%">মোবাইল</th><th>ঠিকানা</th><th class="r" style="width:18%">বাকি (৳)</th></tr></thead>
    <tbody>
    @forelse ($customers as $i => $c)
        <tr>
            <td class="c">{{ $i + 1 }}</td><td>{{ $c->name }}</td><td>{{ $c->phone }}</td><td>{{ $c->address }}</td>
            <td class="r">{{ number_format($c->due_balance, 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="c">কোনো গ্রাহক নেই।</td></tr>
    @endforelse
    @if ($customers->count())
        <tr class="total"><td colspan="4" class="r">মোট বাকি</td><td class="r">{{ number_format($total, 2) }}</td></tr>
    @endif
    </tbody>
</table>
<div class="meta">Generated: {{ $generated }}</div>
</body></html>
