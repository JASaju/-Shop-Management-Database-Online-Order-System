<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Memo #{{ $memo->memo_no }}</title>
    <style>
        /* dompdf does not support flexbox, CSS variables in all contexts, or box-shadow well.
           This template uses tables/floats and hard-coded colors for reliable A4 rendering.
           English text uses dompdf's built-in DejaVu Sans, so no custom font files are needed. */
        @page {
            size: A4;
            margin: 18mm 14mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #111;
            margin: 0;
        }
        .top-line {
            text-align: center;
            font-size: 11px;
            color: #4c1d95;
            margin-bottom: 2px;
        }
        .shop-name {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #4c1d95;
            margin: 0 0 2px;
        }
        .tagline {
            text-align: center;
            font-size: 11px;
            color: #4c1d95;
            margin-bottom: 8px;
        }
        .address-bar {
            background: #5b21b6;
            color: #fff;
            padding: 5px 10px;
            font-size: 10px;
            width: 100%;
        }
        .address-bar table { width: 100%; border-collapse: collapse; }
        .address-bar td { color: #fff; border: none; padding: 0; }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 6px;
            font-size: 11px;
        }
        .meta-table td { border: none; padding: 2px 0; }

        .field-row {
            font-size: 11px;
            margin-bottom: 5px;
            border-bottom: 1px dotted #333;
            padding-bottom: 2px;
        }
        .field-label { font-weight: 700; }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        table.items th {
            background: #5b21b6;
            color: #fff;
            font-size: 10px;
            padding: 5px 6px;
            border: 1px solid #5b21b6;
        }
        table.items td {
            border: 1px solid #7c3aed44;
            padding: 5px 6px;
            font-size: 10.5px;
        }
        table.items tbody tr td { height: 20px; }
        .total-row td {
            border-top: 2px solid #5b21b6;
            font-weight: 700;
        }
        .total-label {
            border: 1px solid #5b21b6;
            padding: 2px 12px;
            font-weight: 700;
        }
        .words-row {
            font-size: 11px;
            margin-top: 8px;
            border-bottom: 1px dotted #333;
            padding-bottom: 3px;
        }
        .signatures {
            width: 100%;
            margin-top: 60px;
            font-size: 10px;
        }
        .signatures td {
            border-top: 1px solid #333;
            padding-top: 4px;
            text-align: center;
            width: 160px;
        }
    </style>
</head>
<body>

<h1 class="shop-name">{{ $memo->shop->shop_name ?? 'Shaheen Medical Hall' }}</h1>
<div class="top-line">{{ $memo->shop->owner_name ?? '' }}</div>
<div class="tagline">{{ $memo->shop->tagline ?? 'Local and imported medicines sold here' }}</div>

<div class="address-bar">
    <table>
        <tr>
            <td style="text-align:left;">{{ $memo->shop->address ?? '' }}</td>
            <td style="text-align:right;">
                Mobile: {{ $memo->shop->phone_1 ?? '' }}@if($memo->shop->phone_2 ?? false), {{ $memo->shop->phone_2 }}@endif
            </td>
        </tr>
    </table>
</div>

<table class="meta-table">
    <tr>
        <td style="text-align:left; font-weight:700;">Memo No: {{ $memo->memo_no }}</td>
        <td style="text-align:right;">@if(optional($memo->staff)->name)Seller: {{ $memo->staff->name }} &nbsp;|&nbsp; @endif Date: {{ \Carbon\Carbon::parse($memo->memo_date)->format('d-m-Y') }}</td>
    </tr>
</table>

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
        <th style="text-align:left;">Medicine Name</th>
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
    @for ($i = $memo->items->count(); $i < max(6, $memo->items->count()); $i++)
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

<table class="signatures">
    <tr>
        <td>Customer's Signature</td>
        <td style="width: 40%; border-top: none;"></td>
        <td>Seller's Signature</td>
    </tr>
</table>

</body>
</html>