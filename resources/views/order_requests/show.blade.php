<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Order Request #{{ $order->order_request_id }}</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            background: #f4f6f8;
            color: #1f2937;
        }

        .wrap { max-width: 900px; margin: 0 auto; padding: 16px; }

        .box {
            background: #fff;
            border-radius: 10px;
            padding: 16px;
            margin-top: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .back { color: #2563eb; text-decoration: none; font-weight: 600; }

        .big { font-size: 24px; font-weight: 700; margin: 8px 0; }

        .callbtn {
            display: inline-block;
            background: #0f9d58;
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
        }

        .voucherbtn {
            display: inline-block;
            background: #5b21b6;
            color: #fff;
            padding: 9px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .ok { background: #dcfce7; color: #166534; padding: 10px; border-radius: 8px; margin-top: 8px; }
        .error { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 8px; margin-top: 8px; }

        table { width: 100%; border-collapse: collapse; }

        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
            font-size: 14px;
        }

        td.r, th.r { text-align: right; }

        .qty-control { display: flex; align-items: center; justify-content: flex-end; gap: 5px; }

        .qty-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            border: 0;
            border-radius: 6px;
            color: #fff;
            font-size: 19px;
            font-weight: 700;
            cursor: pointer;
        }

        .minus { background: #dc2626; }
        .plus { background: #0f9d58; }

        .qty-input {
            width: 70px;
            text-align: center;
            padding: 7px 4px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .unit { display: block; text-align: right; color: #6b7280; font-size: 12px; margin-top: 4px; }

        .total { font-size: 20px; font-weight: 700; color: #0f9d58; }

        select, input, button {
            padding: 9px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }

        button { background: #0f9d58; color: #fff; border: 0; cursor: pointer; font-weight: 600; }
        button.del { background: #dc2626; }

        .form-row { display: flex; gap: 8px; flex-wrap: wrap; }
        .form-row input, .form-row select { flex: 1; min-width: 160px; }

        .confirm-box {
            display: none;
            margin-top: 10px;
            padding: 12px;
            border: 1px dashed #0f9d58;
            border-radius: 8px;
            background: #f0fdf4;
        }

        .confirm-box label { display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px; color: #166534; }
        .confirm-box .col { flex: 1; min-width: 200px; }
        .confirm-box .col select { width: 100%; }

        .muted { color: #6b7280; font-size: 13px; }

        @media(max-width: 600px) {
            .wrap { padding: 10px; }
            .box { padding: 12px; }
            th, td { padding: 8px 5px; font-size: 12px; }
            .qty-control { gap: 3px; }
            .qty-btn { width: 27px; height: 29px; }
            .qty-input { width: 48px; padding: 6px 2px; }
        }
    </style>
</head>

<body>
@php
    // Works even if the controller doesn't pass these.
    $shops = $shops ?? \DB::table('shops')->orderBy('shop_name')->get();
    $staff = $staff ?? \DB::table('staff')->where('is_active', 1)->orderBy('name')->get();
    $selectedShop = old('shop_id', $order->shop_id ?? ($shops->count() === 1 ? $shops->first()->shop_id : ''));
    $selectedStaff = old('delivery_staff_id', $order->delivery_staff_id ?? '');
@endphp

<div class="wrap">

    <div class="header">
        <a href="{{ route('order-requests.index') }}" class="back">&larr; Back to Orders</a>
        <strong>Order #{{ $order->order_request_id }}</strong>
    </div>

    @if(session('success'))
        <div class="ok">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- CUSTOMER INFORMATION --}}
    <div class="box">
        <div>Customer: <b>{{ $order->customer_name ?: '—' }}</b></div>

        <div class="big">{{ $order->phone }}</div>

        @if($order->phone)
            <a class="callbtn" href="tel:{{ $order->phone }}">Call Customer</a>
        @endif

        <div class="muted" style="margin-top:10px">
            Order Date: {{ $order->created_at?->format('d M Y, h:i A') }}
        </div>

        <div style="margin-top:8px">
            Status: <b>{{ ucfirst($order->status) }}</b>
        </div>

        @if($order->memo_id)
            <div style="margin-top:10px">
                <a class="voucherbtn" href="{{ route('memos.show', $order->memo_id) }}">Voucher dekhun</a>
            </div>
        @endif
    </div>

    {{-- ONE FORM: qty inputs (below) + status + shop + delivery staff all submit together --}}
    <form id="order-form" method="POST" action="{{ route('order-requests.status', $order) }}">
        @csrf
        @method('PATCH')
    </form>

    {{-- PRODUCT DETAILS --}}
    <div class="box">

        <h3 style="margin-top:0">Order Products</h3>

        <div style="overflow-x:auto">

            <table>

                <thead>
                <tr>
                    <th>Product</th>
                    <th class="r">Price</th>
                    <th class="r">Qty</th>
                    <th class="r">Total</th>
                </tr>
                </thead>

                <tbody>

                @foreach($order->items as $index => $i)

                    <tr class="product-row" data-price="{{ $i['price'] }}">

                        <td><b>{{ $i['name'] }}</b></td>

                        <td class="r">৳{{ number_format($i['price'], 2) }}</td>

                        <td class="r">

                            <div class="qty-control">

                                <button type="button" class="qty-btn minus" onclick="changeQty(this, -1)">−</button>

                                {{-- form="order-form" => saved when you press Update --}}
                                <input type="number"
                                       class="qty-input"
                                       name="items[{{ $index }}][qty]"
                                       form="order-form"
                                       value="{{ old('items.' . $index . '.qty', $i['qty']) }}"
                                       min="0.01"
                                       step="0.01"
                                       onchange="updateTotals()">

                                <button type="button" class="qty-btn plus" onclick="changeQty(this, 1)">+</button>

                            </div>

                            <small class="unit">{{ $i['unit'] ?? '' }}</small>

                        </td>

                        <td class="r line-total">৳{{ number_format($i['line_total'], 2) }}</td>

                    </tr>

                @endforeach

                </tbody>

                <tfoot>

                <tr>
                    <th colspan="3" class="r">Grand Total</th>
                    <th class="r total" id="grand-total">৳{{ number_format($order->total_amount, 2) }}</th>
                </tr>

                </tfoot>

            </table>

        </div>

        <p class="muted">
            Use + / − to change quantity, then press <b>Update</b> to save. Voucher ao update hoye jabe.
        </p>

    </div>

    {{-- UPDATE ORDER STATUS --}}
    <div class="box">

        <h3 style="margin-top:0">Update Order</h3>

        <div class="form-row">

            <select name="status" id="status" form="order-form" required onchange="toggleConfirm()">
                @foreach(['pending', 'called', 'confirmed', 'cancelled'] as $s)
                    <option value="{{ $s }}" @selected(old('status', $order->status) === $s)>
                    {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>

            <input type="text"
                   name="note"
                   form="order-form"
                   value="{{ old('note', $order->note) }}"
                   placeholder="Note (optional)">

        </div>

        {{-- Shown only when status = confirmed --}}
        <div class="confirm-box" id="confirm-box">
            <div class="muted" style="margin-bottom:8px">
                Confirm korle auto voucher (memo) toiri hobe.
            </div>
            <div class="form-row">
                <div class="col">
                    <label for="shop_id">দোকান (Shop)</label>
                    <select name="shop_id" id="shop_id" form="order-form">
                        <option value="">-- দোকান নির্বাচন করুন --</option>
                        @foreach($shops as $sh)
                            <option value="{{ $sh->shop_id }}" @selected($selectedShop == $sh->shop_id)>
                                {{ $sh->shop_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label for="delivery_staff_id">Delivery Staff (নাম ও মোবাইল)</label>
                    <select name="delivery_staff_id" id="delivery_staff_id" form="order-form">
                        <option value="">-- Delivery staff বাছাই করুন --</option>
                        @foreach($staff as $st)
                            <option value="{{ $st->staff_id }}"
                                    data-shop="{{ $st->shop_id }}"
                                    @selected($selectedStaff == $st->staff_id)>
                                {{ $st->name }}{{ !empty($st->phone) ? ' – ' . $st->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-top:10px">
            <button type="submit" form="order-form">Update</button>
        </div>

    </div>

    {{-- DELETE ORDER --}}
    <div class="box">

        <h3 style="margin-top:0">Delete Order</h3>

        <form method="POST"
              action="{{ route('order-requests.destroy', $order) }}"
              onsubmit="return confirm('Are you sure you want to delete this order?')">

            @csrf
            @method('DELETE')

            <button class="del" type="submit">Delete Order</button>

        </form>

    </div>

</div>

<script>

    // Increase or decrease quantity
    function changeQty(button, amount) {
        const row = button.closest('.product-row');
        const input = row.querySelector('.qty-input');

        let qty = parseFloat(input.value) || 0;
        qty = Math.max(0.01, qty + amount);
        input.value = Number(qty.toFixed(2));

        updateTotals();
    }

    // Update line totals and grand total
    function updateTotals() {
        let grandTotal = 0;

        document.querySelectorAll('.product-row').forEach(row => {
            const price = parseFloat(row.dataset.price) || 0;
        const input = row.querySelector('.qty-input');

        let qty = parseFloat(input.value);

        if (!Number.isFinite(qty) || qty < 0.01) {
            qty = 0.01;
            input.value = '0.01';
        }

        const lineTotal = price * qty;
        row.querySelector('.line-total').textContent = '৳' + lineTotal.toFixed(2);
        grandTotal += lineTotal;
    });

        document.getElementById('grand-total').textContent = '৳' + grandTotal.toFixed(2);
    }

    // Show shop + delivery staff only when status = confirmed
    function toggleConfirm() {
        const isConfirmed = document.getElementById('status').value === 'confirmed';
        document.getElementById('confirm-box').style.display = isConfirmed ? 'block' : 'none';
        document.getElementById('shop_id').required = isConfirmed;
        document.getElementById('delivery_staff_id').required = isConfirmed;
    }

    // Delivery staff list follows the selected shop
    function filterStaff() {
        const shop = document.getElementById('shop_id').value;
        const sel = document.getElementById('delivery_staff_id');
        let keep = false;

        for (const o of sel.options) {
            if (!o.dataset.shop) continue;
            const show = !shop || o.dataset.shop === shop;
            o.hidden = !show;
            o.disabled = !show;
            if (show && o.selected) keep = true;
        }

        if (!keep) sel.value = '';
    }

    document.getElementById('shop_id').addEventListener('change', filterStaff);

    toggleConfirm();
    filterStaff();

</script>

</body>
</html>