<!DOCTYPE html>
<html><head><meta charset="utf-8">
@include('ledgers._style')
<style>
    tr.exp td { background: #fdecea; }
    tr.warn td { background: #fff8e1; }
    th, td { font-size: 10px; }
</style>
</head>
<body>
<h1>{{ $title }}</h1>
<div class="sub">{{ implode('  |  ', $filterText) }} &nbsp;&middot;&nbsp; {{ $rangeText }}</div>

<table>
    <thead><tr>
        <th class="c" style="width:3%">#</th>
        @foreach ($cols as $c)
            <th class="{{ in_array($c['type'], ['money', 'num', 'int']) ? 'r' : '' }}">{{ $c['label'] }}</th>
        @endforeach
    </tr></thead>
    <tbody>
    @forelse ($rows as $n => $row)
        <tr class="{{ $row['class'] }}">
            <td class="c">{{ $n + 1 }}</td>
            @foreach ($row['cells'] as $i => $cell)
                <td class="{{ in_array($cols[$i]['type'], ['money', 'num', 'int']) ? 'r' : '' }}">{{ $cell }}</td>
            @endforeach
        </tr>
    @empty
        <tr><td colspan="{{ count($cols) + 1 }}" class="c">No records found.</td></tr>
    @endforelse
    @if ($count)
        <tr class="tot"><td></td>
        @foreach ($totals as $i => $cell)
            <td class="{{ in_array($cols[$i]['type'], ['money', 'num', 'int']) ? 'r' : '' }}">{{ $cell }}</td>
        @endforeach</tr>
    @endif
    </tbody>
</table>
<div class="meta">Generated: {{ $generated }}</div>
</body></html>
