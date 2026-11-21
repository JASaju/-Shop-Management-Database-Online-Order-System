<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Memo #{{ $memo->memo_no }} - {{ $memo->shop->shop_name ?? '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        :root {
            --theme-main: #5b21b6;
            --theme-dark: #4c1d95;
            --theme-light: #7c3aed;
            --theme-border: #7c3aed44;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #e5e7eb;
            margin: 0;
            padding: 24px;
        }
        .voucher {
            max-width: 780px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid var(--theme-light);
            padding: 24px 28px;
            position: relative;
        }
        .top-line {
            text-align: center;
            font-size: 13px;
            color: var(--theme-dark);
            margin-bottom: 2px;
        }
        .shop-name {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: var(--theme-dark);
            margin: 0 0 2px;
        }
        .tagline {
            text-align: center;
            font-size: 13px;
            color: var(--theme-dark);
            margin-bottom: 10px;
        }
        .address-bar {
            background: var(--theme-main);
            color: #fff;
            padding: 6px 12px;
            font-size: 13px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .serial-box {
            position: absolute;
            left: 20px;
            top: 90px;
            font-size: 22px;
            font-weight: 700;
            border: 1px solid #999;
            padding: 2px 14px;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0 14px;
            font-size: 14px;
        }
        .field-row {
            display: flex;
            gap: 6px;
            font-size: 14px;
            margin-bottom: 6px;
            border-bottom: 1px dotted #333;
            padding-bottom: 2px;
        }
        .field-label { white-space: nowrap; font-weight: 600; }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        table.items th {
            background: var(--theme-main);
            color: #fff;
            font-size: 13px;
            padding: 6px 8px;
            border: 1px solid var(--theme-main);
        }
        table.items td {
            border: 1px solid var(--theme-border);
            padding: 6px 8px;
            font-size: 13.5px;
            vertical-align: top;
        }
        table.items tbody tr td { height: 26px; }
        .total-row td {
            border-top: 2px solid var(--theme-main);
            font-weight: 700;
        }
        .total-label {
            display: inline-block;
            border: 1px solid var(--theme-main);
            border-radius: 12px;
            padding: 2px 14px;
            font-weight: 700;
        }
        .words-row {
            font-size: 14px;
            margin-top: 10px;
            border-bottom: 1px dotted #333;
            padding-bottom: 4px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 46px;
            font-size: 13px;
        }
        .signatures div {
            border-top: 1px solid #333;
            padding-top: 4px;
            width: 180px;
            text-align: center;
        }
        .print-bar {
            max-width: 780px;
            margin: 0 auto 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }
        .print-bar button, .print-bar a {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-print { background: var(--theme-main); color: #fff; }
        .btn-new { background: #e5e7eb; color: #111; }
        .btn-group { display: flex; gap: 8px; }

        .theme-swatches {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .swatch {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            padding: 0;
        }
        .swatch.active {
            border-color: #111;
            transform: scale(1.15);
        }

        @media print {
            body { background: #fff; padding: 0; }
            .print-bar { display: none; }
            .voucher { border: none; max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="print-bar">
    <div class="theme-swatches" id="themeSwatches">
        <button class="swatch" data-theme="purple" style="background:#5b21b6" onclick="setTheme('purple')" title="Purple"></button>
        <button class="swatch" data-theme="blue" style="background:#1d4ed8" onclick="setTheme('blue')" title="Blue"></button>
        <button class="swatch" data-theme="green" style="background:#15803d" onclick="setTheme('green')" title="Green"></button>
        <button class="swatch" data-theme="red" style="background:#b91c1c" onclick="setTheme('red')" title="Red"></button>
        <button class="swatch" data-theme="teal" style="background:#0f766e" onclick="setTheme('teal')" title="Teal"></button>
        <button class="swatch" data-theme="orange" style="background:#c2410c" onclick="setTheme('orange')" title="Orange"></button>
        <button class="swatch" data-theme="gray" style="background:#374151" onclick="setTheme('gray')" title="Gray"></button>
    </div>
    <div class="btn-group">
        <a class="btn-new" href="{{ route('memos.create') }}">+ New Memo</a>
        <a class="btn-new" href="{{ route('memos.pdf', $memo->memo_id) }}" target="_blank">Download PDF</a>
        <button class="btn-print" onclick="window.print()">Print</button>
    </div>
</div>

<div class="voucher">

    <h1 class="shop-name">{{ $memo->shop->shop_name ?? 'Shaheen Medical Hall' }}</h1>
    <div class="top-line">{{ $memo->shop->owner_name ?? '' }}</div>
    <div class="tagline">{{ $memo->shop->tagline ?? 'Local and imported medicines sold here' }}</div>
    <div class="address-bar">
        <span>{{ $memo->shop->address ?? '' }}</span>
        <span>
                Mobile:
            {{ $memo->shop->phone_1 ?? '' }}@if($memo->shop->phone_2 ?? false), {{ $memo->shop->phone_2 }}@endif
            </span>
    </div>
    <span class="field-label" style="float: left">Memo No: {{ $memo->memo_no }}</span>
    <div class="meta-row" style="justify-content: flex-end;">
        <span>@if(optional($memo->staff)->name)Seller: {{ $memo->staff->name }} &nbsp;|&nbsp; @endif Date: {{ \Carbon\Carbon::parse($memo->memo_date)->format('d-m-Y') }}</span>
    </div>
    <div class="field-row">
        <span class="field-label">Name :</span>
        <span>{{ $memo->customer_name }}</span>
    </div>
    <div class="field-row">
        <span class="field-label">Address :</span>
        <span>{{ $memo->customer_address }}</span>
    </div>
    @if(optional($memo->deliveryStaff)->name)
        <div class="field-row">
            <span class="field-label">Delivery :</span>
            <span>{{ $memo->deliveryStaff->name }}@if(!empty($memo->deliveryStaff->phone)), Mobile: {{ $memo->deliveryStaff->phone }}@endif</span>
        </div>
    @endif

    <table class="items">
        <thead>
        <tr>
            <th style="text-align:left;">Item / Product</th>
            <th width="80">Quantity</th>
            <th width="90">Rate</th>
            <th width="110">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($memo->items as $item)
            <tr>
                <td>{{ $item->medicine_name }}</td>
                <td style="text-align:center;">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                <td style="text-align:right;">{{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align:right;">{{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
        {{-- pad a couple of blank rows so short bills still look like the printed pad --}}
        @for ($i = $memo->items->count(); $i < max(4, $memo->items->count()); $i++)
            <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        @endfor
        <tr class="total-row">
            <td colspan="2"></td>
            <td style="text-align:right;"><span class="total-label">Total =</span></td>
            <td style="text-align:right;">{{ number_format($memo->total_amount, 2) }}</td>
        </tr>
        @if (($memo->discount_amount ?? 0) > 0 || ($memo->due_amount ?? 0) > 0)
            <tr><td colspan="2"></td><td style="text-align:right;">Discount</td><td style="text-align:right;">- {{ number_format($memo->discount_amount, 2) }}</td></tr>
            <tr><td colspan="2"></td><td style="text-align:right;">After Discount</td><td style="text-align:right;">{{ number_format($memo->total_amount - $memo->discount_amount, 2) }}</td></tr>
            <tr><td colspan="2"></td><td style="text-align:right;">Paid</td><td style="text-align:right;">{{ number_format($memo->total_amount - $memo->discount_amount - $memo->due_amount, 2) }}</td></tr>
            <tr><td colspan="2"></td><td style="text-align:right; font-weight:700;">Due</td><td style="text-align:right; font-weight:700;">{{ number_format($memo->due_amount, 2) }}</td></tr>
        @endif
        </tbody>
    </table>
    <div class="words-row">
        In words : {{ $memo->amount_in_words }}
    </div>

    <div class="signatures">
        <div>Customer's Signature</div>
        <div>Seller's Signature</div>
    </div>
</div>
<script>
    const THEMES = {
        purple: { main: '#5b21b6', dark: '#4c1d95', light: '#7c3aed', border: '#7c3aed44' },
        blue:   { main: '#1d4ed8', dark: '#1e3a8a', light: '#3b82f6', border: '#3b82f644' },
        green:  { main: '#15803d', dark: '#14532d', light: '#22c55e', border: '#22c55e44' },
        red:    { main: '#b91c1c', dark: '#7f1d1d', light: '#ef4444', border: '#ef444444' },
        teal:   { main: '#0f766e', dark: '#134e4a', light: '#14b8a6', border: '#14b8a644' },
        orange: { main: '#c2410c', dark: '#7c2d12', light: '#f97316', border: '#f9731644' },
        gray:   { main: '#374151', dark: '#1f2937', light: '#6b7280', border: '#6b728044' },
    };
    function setTheme(name) {
        const t = THEMES[name] || THEMES.purple;
        const root = document.documentElement.style;
        root.setProperty('--theme-main', t.main);
        root.setProperty('--theme-dark', t.dark);
        root.setProperty('--theme-light', t.light);
        root.setProperty('--theme-border', t.border);
        document.querySelectorAll('.swatch').forEach(el => el.classList.remove('active'));
        const active = document.querySelector(`.swatch[data-theme="${name}"]`);
        if (active) active.classList.add('active');
        try { localStorage.setItem('memoTheme', name); } catch (e) {}
    }
    document.addEventListener('DOMContentLoaded', () => {
        let saved = 'purple';
    try { saved = localStorage.getItem('memoTheme') || 'purple'; } catch (e) {}
    setTheme(saved);
    });
</script>
</body>
</html>