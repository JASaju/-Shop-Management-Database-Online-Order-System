<!DOCTYPE html>
<html><head><meta charset="utf-8">@include('ledgers._style')</head>
<body>
<h1>{{ $heading }}</h1>
<div class="sub">{{ $period }}</div>
<table>
    <thead><tr>
        <th class="c" style="width:5%">#</th>
        @foreach ($cols as $i => $col)<th class="{{ $i >= $numFrom ? 'r' : '' }}">{{ $col }}</th>@endforeach
    </tr></thead>
    <tbody>
    @forelse ($rows as $n => $row)
        <tr>
            <td class="c">{{ $n + 1 }}</td>
            @foreach ($row as $i => $cell)
                @if ($i >= $numFrom)<td class="r {{ $i == count($row) - 1 && $cell > 0 ? 'neg' : '' }}">{{ number_format($cell, 2) }}</td>
                @else<td>{{ $cell }}</td>@endif
            @endforeach
        </tr>
    @empty <tr><td colspan="{{ count($cols) + 1 }}" class="c">No records.</td></tr> @endforelse
    @if (count($rows))
        <tr class="tot"><td></td>
        @foreach ($totals as $i => $cell)
            <td class="{{ $i >= $numFrom ? 'r' : '' }}">{{ $i >= $numFrom ? number_format($cell, 2) : $cell }}</td>
        @endforeach</tr>
    @endif
    </tbody>
</table>
<div class="meta">Generated: {{ $generated }}</div>
</body></html>
