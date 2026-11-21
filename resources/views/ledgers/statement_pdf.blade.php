<!DOCTYPE html>
<html><head><meta charset="utf-8">@include('ledgers._style')</head>
<body>
@forelse ($sections as $idx => $s)
    <div class="section">
    <h1>{{ $heading }}</h1>
    <div class="sub">{{ $period }}</div>
    <h2>{{ $s['title'] }}</h2>
    <div class="addr">{{ $s['subtitle'] }}</div>
    <table>
        <thead><tr>
            <th style="width:13%">Date</th><th>Description</th>
            <th class="r" style="width:15%">{{ $colA }}</th><th class="r" style="width:15%">{{ $colB }}</th><th class="r" style="width:16%">{{ $balLabel }}</th>
        </tr></thead>
        <tbody>
            @if ($s['opening'] != 0)
                <tr><td></td><td><i>Opening balance</i></td><td></td><td></td><td class="r">{{ number_format($s['opening'], 2) }}</td></tr>
            @endif
            @forelse ($s['rows'] as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                    <td>{{ $row['desc'] }}</td>
                    <td class="r">{{ $row['a'] ? number_format($row['a'], 2) : '' }}</td>
                    <td class="r">{{ $row['b'] ? number_format($row['b'], 2) : '' }}</td>
                    <td class="r">{{ number_format($row['bal'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="c">No entries in this period.</td></tr>
            @endforelse
            <tr class="tot"><td colspan="2" class="r">Total / Closing balance</td>
                <td class="r">{{ number_format($s['sumA'], 2) }}</td><td class="r">{{ number_format($s['sumB'], 2) }}</td>
                <td class="r {{ $s['closing'] > 0 ? 'neg' : '' }}">{{ number_format($s['closing'], 2) }}</td></tr>
        </tbody>
    </table>
    <div class="meta">Generated: {{ $generated }}</div>
    </div>
@empty
    <h1>{{ $heading }}</h1><div class="sub">{{ $period }}</div>
    <p style="text-align:center">No records found.</p>
@endforelse
</body></html>
