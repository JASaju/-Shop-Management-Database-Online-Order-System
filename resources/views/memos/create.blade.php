<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Memo - {{ $shop->shop_name ?? 'Medical Hall' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
@php
    // Works even if MemoController::create() doesn't pass these.
    $staff = $staff ?? \DB::table('staff')->where('is_active', 1)->orderBy('name')->get();
    $customerList = $customerList ?? \DB::table('customers as c')
        ->leftJoin('v_customer_due as v', 'v.customer_id', '=', 'c.customer_id')
        ->whereNotNull('c.name')->orderBy('c.name')
        ->get(['c.customer_id', 'c.name', 'c.address', 'c.phone', 'v.balance']);
@endphp
<body class="bg-gray-100 min-h-screen py-8">
@include('partials._nav')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('memos.store') }}" id="memo-form">
        @csrf
        <div>
            <label class="block text-sm font-semibold mb-1">Shop Name</label>
            <select name="shop_id" id="shop_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Select a shop --</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->shop_id }}"
                            {{ old('shop_id', $selectedShopId ?? '') == $shop->shop_id ? 'selected' : '' }}>
                        {{ $shop->shop_name }} (ID: {{ $shop->shop_id }})
                    </option>
                @endforeach

            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 mt-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Memo No</label>
                {{-- Preview only. The real number is generated safely on the server when saving. --}}
                <input type="text" value="{{ $nextMemoNo }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Date</label>
                <input type="date" name="memo_date" value="{{ old('memo_date', date('Y-m-d')) }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Seller (Staff)</label>
                <select name="staff_id" id="staff_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Select a seller --</option>
                    @foreach ($staff as $st)
                        <option value="{{ $st->staff_id }}" data-shop="{{ $st->shop_id }}"
                                {{ old('staff_id') == $st->staff_id ? 'selected' : '' }}>{{ $st->name }}{{ $st->designation ? ' (' . $st->designation . ')' : '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-semibold mb-1">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" list="customer-list" autocomplete="off"
                       value="{{ old('customer_name') }}" placeholder="Search existing customer or type a new name"
                       class="w-full border rounded px-3 py-2">
                <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
                <p id="customer-note" class="text-xs mt-1 text-gray-500"></p>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Address, Mobile Number</label>
                <input type="text" name="customer_address" id="customer_address" value="{{ old('customer_address') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <datalist id="customer-list">
            @foreach ($customerList as $c)
                <option value="{{ $c->name }}" data-id="{{ $c->customer_id }}" data-address="{{ trim(($c->address ?? '') . ' ' . ($c->phone ?? '')) }}" data-due="{{ $c->balance ?? 0 }}"></option>
            @endforeach
        </datalist>

        <datalist id="medicine-list">
            @foreach ($medicines as $med)
                <option data-id="{{ $med->medicine_id }}" data-price="{{ $med->default_price }}" value="{{ $med->name }}"></option>
            @endforeach
        </datalist>

        <table class="w-full border text-sm mb-2" id="items-table">
            <thead class="bg-purple-800 text-white">
            <tr>
                <th class="p-2 text-left">Item / Product Name</th>
                <th class="p-2 w-24">Quantity</th>
                <th class="p-2 w-28">Rate</th>
                <th class="p-2 w-28">Amount</th>
                <th class="p-2 w-10"></th>
            </tr>
            </thead>
            <tbody id="items-body">
            {{-- rows injected by JS --}}
            </tbody>
        </table>

        <button type="button" id="add-row" class="mb-6 px-3 py-1 text-sm bg-purple-100 text-purple-800 rounded hover:bg-purple-200">
            + Add another line
        </button>

        <div class="flex justify-end mb-6">
            <div class="w-full md:w-80 space-y-2 text-sm">
                <div class="flex justify-between"><span class="font-semibold">Total</span>
                    <span id="grand-total" class="font-bold">0.00</span></div>
                <div class="flex justify-between items-center"><span class="font-semibold">Discount (Tk)</span>
                    <input type="number" step="0.01" min="0" name="discount_amount" id="discount" value="{{ old('discount_amount', 0) }}"
                           class="w-32 border rounded px-2 py-1 text-right"></div>
                <div class="flex justify-between"><span class="font-semibold">After Discount</span>
                    <span id="net-total" class="font-bold">0.00</span></div>
                <div class="flex justify-between items-center"><span class="font-semibold">Paid (Tk)</span>
                    <input type="number" step="0.01" min="0" name="paid_amount" id="paid" value="{{ old('paid_amount') }}"
                           placeholder="Empty = paid in full" class="w-40 border rounded px-2 py-1 text-right"></div>
                <div class="flex justify-between text-red-700"><span class="font-semibold">Due</span>
                    <span id="due-total" class="font-bold">0.00</span></div>
                <p class="text-xs text-gray-500">If there is a due amount, the customer name is required.</p>
            </div>
        </div>

        <button type="submit" class="w-full md:w-auto px-6 py-2 bg-purple-800 text-white rounded hover:bg-purple-900">
            Save &amp; Print Memo
        </button>
    </form>
</div>

<script>
    const tbody = document.getElementById('items-body');

    const rowTemplate = () => `
            <tr class="border-t">
                <td class="p-1">
                    <input list="medicine-list" name="items[__i__][medicine_name]" class="w-full border rounded px-2 py-1 med-name" required>
                    <input type="hidden" name="items[__i__][medicine_id]" class="med-id">
                </td>
                <td class="p-1">
                    <input type="number" step="0.01" min="0.01" name="items[__i__][quantity]" class="w-full border rounded px-2 py-1 qty" required>
                </td>
                <td class="p-1">
                    <input type="number" step="0.01" min="0" name="items[__i__][unit_price]" class="w-full border rounded px-2 py-1 price" required>
                </td>
                <td class="p-1 text-right line-total pr-2">0.00</td>
                <td class="p-1 text-center">
                    <button type="button" class="remove-row text-red-600 font-bold">&times;</button>
                </td>
            </tr>
        `;

    let rowIndex = 0;

    function addRow() {
        const html = rowTemplate().replaceAll('__i__', rowIndex);
        tbody.insertAdjacentHTML('beforeend', html);
        rowIndex++;
        recalcTotal();
    }

    function recalcTotal() {
        let grand = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const lineTotal = qty * price;
        row.querySelector('.line-total').textContent = lineTotal.toFixed(2);
        grand += lineTotal;
    });
        document.getElementById('grand-total').textContent = grand.toFixed(2);
        const disc = parseFloat(document.getElementById('discount').value) || 0;
        const net = Math.max(grand - disc, 0);
        const paidRaw = document.getElementById('paid').value;
        const paid = paidRaw === '' ? net : (parseFloat(paidRaw) || 0);
        document.getElementById('net-total').textContent = net.toFixed(2);
        document.getElementById('due-total').textContent = Math.max(net - paid, 0).toFixed(2);
    }

    // Procedure 1: name matches an existing medicine -> fill hidden id + suggest its price.
    // Procedure 2: name doesn't match anything -> med-id stays empty, server treats it as new/free text.
    function tryAutofillPrice(input) {
        const list = document.getElementById('medicine-list');
        const options = list.querySelectorAll('option');
        const row = input.closest('tr');

        for (const opt of options) {
            if (opt.value === input.value) {
                row.querySelector('.med-id').value = opt.dataset.id;
                const priceInput = row.querySelector('.price');
                if (!priceInput.value) priceInput.value = opt.dataset.price;
                recalcTotal();
                return;
            }
        }
        row.querySelector('.med-id').value = '';
    }

    document.getElementById('add-row').addEventListener('click', addRow);
    document.getElementById('discount').addEventListener('input', recalcTotal);
    document.getElementById('paid').addEventListener('input', recalcTotal);

    tbody.addEventListener('input', (e) => {
        if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
        recalcTotal();
    }
    if (e.target.classList.contains('med-name')) {
        tryAutofillPrice(e.target);
    }
    });

    tbody.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
        recalcTotal();
    }
    });

    // ---- customer pick: fill address + show previous due ----
    const cName = document.getElementById('customer_name');
    const cId = document.getElementById('customer_id');
    const cAddr = document.getElementById('customer_address');
    const cNote = document.getElementById('customer-note');
    cName.addEventListener('input', () => {
        cId.value = ''; cNote.textContent = ''; cNote.className = 'text-xs mt-1 text-gray-500';
    for (const o of document.getElementById('customer-list').options) {
        if (o.value === cName.value) {
            cId.value = o.dataset.id;
            if (!cAddr.value) cAddr.value = o.dataset.address;
            const due = parseFloat(o.dataset.due) || 0;
            cNote.textContent = due > 0 ? 'Previous due: Tk ' + due.toFixed(2) : 'Existing customer (no due)';
            if (due > 0) cNote.className = 'text-xs mt-1 text-red-600 font-semibold';
            return;
        }
    }
    if (cName.value) cNote.textContent = 'Will be saved as a new customer (if there is a due)';
    });

    // ---- seller list follows the selected shop ----
    const shopSel = document.getElementById('shop_id');
    const staffSel = document.getElementById('staff_id');
    function filterStaff() {
        let keep = false;
        for (const o of staffSel.options) {
            if (!o.dataset.shop) continue;
            const show = !shopSel.value || o.dataset.shop === shopSel.value;
            o.hidden = !show; o.disabled = !show;
            if (show && o.selected) keep = true;
        }
        if (!keep) staffSel.value = '';
    }
    shopSel.addEventListener('change', filterStaff); filterStaff();

    for (let i = 0; i < 1; i++) addRow();
</script>
</body>
</html>