@extends('layouts.app')
@section('title', 'নতুন ক্রয়')
@section('content')
    @include('partials._errors')
    <h4 class="mb-3">নতুন ক্রয় এন্ট্রি</h4>
    <form method="POST" action="{{ route('purchases.store') }}" id="purchase-form">
        @csrf
        <div class="card mb-3"><div class="card-body"><div class="row g-3">
                    <div class="col-md-3"><label class="form-label">ইনভয়েস নং <span class="text-danger">*</span></label>
                        <input type="text" name="invoice_no" value="{{ old('invoice_no') }}" required maxlength="40" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">কোম্পানি</label>
                        <select name="company_id" id="company_id" class="form-select"><option value="">-- বাছাই করুন --</option>
                            @foreach ($companies as $c)<option value="{{ $c->company_id }}" @selected(old('company_id') == $c->company_id)>{{ $c->company_name }}</option>@endforeach
                        </select></div>
                    <div class="col-md-3"><label class="form-label">দোকান</label>
                        <select name="shop_id" id="shop_id" class="form-select"><option value="">-- বাছাই করুন --</option>
                            @foreach ($shops as $s)<option value="{{ $s->shop_id }}" @selected(old('shop_id') == $s->shop_id)>{{ trim($s->shop_name) }}</option>@endforeach
                        </select></div>
                    <div class="col-md-3"><label class="form-label">তারিখ <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">পরিশোধের শেষ তারিখ</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" class="form-control"></div>
                    <div class="col-md-9"><label class="form-label">নোট</label>
                        <input type="text" name="note" value="{{ old('note') }}" maxlength="255" class="form-control"></div>
                </div></div></div>

        <datalist id="med-list">
            @foreach ($medicines as $m)
                <option value="{{ $m->name }}"
                        data-id="{{ $m->medicine_id }}"
                        data-category="{{ $m->category_id }}"{{-- NEW --}}
                        data-company="{{ $m->company_id }}"
                        data-price="{{ $m->last_price }}"
                        data-pct="{{ $m->last_pct }}"
                        data-mrp="{{ $m->default_price }}"></option>
            @endforeach
        </datalist>

        <div class="card mb-3"><div class="table-responsive">
                <table class="table table-sm align-middle mb-0" style="min-width:1250px">{{-- NEW: width 1100 -> 1250 --}}
                    <thead class="table-light"><tr>
                        <th style="width:22%">ঔষধ</th>
                        <th style="width:14%">ক্যাটাগরি <span class="text-danger">*</span></th>{{-- NEW --}}
                        <th>ব্যাচ</th><th>মেয়াদ শেষ *</th><th>পরিমাণ</th><th>ক্রয়মূল্য</th><th>MRP</th><th>ছাড় %</th><th class="text-end">টাকা</th><th></th>
                    </tr></thead>
                    <tbody id="rows"></tbody>
                </table></div>
            <div class="card-footer"><button type="button" id="add-row" class="btn btn-sm btn-outline-success">+ আরেকটি লাইন</button></div></div>

        <div class="row g-3 justify-content-end">
            <div class="col-md-3"><label class="form-label">অতিরিক্ত ছাড় (৳)</label>
                <input type="number" step="0.01" min="0" name="discount_amount" id="discount" value="{{ old('discount_amount', 0) }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">এখন পরিশোধ (৳)</label>
                <input type="number" step="0.01" min="0" name="paid_amount" id="paid" value="{{ old('paid_amount', 0) }}" class="form-control"></div>
            <div class="col-md-3"><div class="card card-body py-2">
                    <div class="d-flex justify-content-between"><span>সাব-টোটাল</span><b id="sub">0.00</b></div>
                    <div class="d-flex justify-content-between"><span>নেট</span><b id="net">0.00</b></div>
                    <div class="d-flex justify-content-between text-danger"><span>বাকি</span><b id="due">0.00</b></div>
                </div></div>
        </div>
        <button class="btn btn-success mt-3">ক্রয় সংরক্ষণ করুন</button>
    </form>

    <script>
        const rows = document.getElementById('rows');
        const medList = document.getElementById('med-list');
        let idx = 0;

        // NEW: category dropdown options (used in every row)
        const catOptions = `<option value="">-- বাছাই --</option>
@foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>
@endforeach`;

        const tpl = () => `
<tr>
  <td><input list="med-list" class="form-control form-control-sm med-name" autocomplete="off" required>
      <input type="hidden" name="items[__i__][medicine_id]" class="med-id"></td>
  <td><select name="items[__i__][category_id]" class="form-select form-select-sm cat" required>${catOptions}</select></td>
  <td><input type="text" name="items[__i__][batch_no]" maxlength="50" class="form-control form-control-sm"></td>
  <td><input type="date" name="items[__i__][expiry_date]" class="form-control form-control-sm" required></td>
  <td><input type="number" step="0.01" min="0.01" name="items[__i__][quantity]" class="form-control form-control-sm qty" required></td>
  <td><input type="number" step="0.01" min="0" name="items[__i__][purchase_price]" class="form-control form-control-sm price" required></td>
  <td><input type="number" step="0.01" min="0" name="items[__i__][mrp]" class="form-control form-control-sm mrp"></td>
  <td><input type="number" step="0.01" min="0" max="100" name="items[__i__][discount_percent]" class="form-control form-control-sm pct" value="0"></td>
  <td class="text-end line">0.00</td>
  <td><button type="button" class="btn btn-sm btn-link text-danger remove">&times;</button></td>
</tr>`;

        function addRow(){ rows.insertAdjacentHTML('beforeend', tpl().replaceAll('__i__', idx++)); calc(); }

        function calc(){
            let sub = 0;
            rows.querySelectorAll('tr').forEach(r => {
                const q = parseFloat(r.querySelector('.qty').value)||0,
                      p = parseFloat(r.querySelector('.price').value)||0,
                      d = parseFloat(r.querySelector('.pct').value)||0;
                const t = q*p*(1-d/100);
                r.querySelector('.line').textContent = t.toFixed(2);
                sub += t;
            });
            const disc = parseFloat(document.getElementById('discount').value)||0,
                  paid = parseFloat(document.getElementById('paid').value)||0;
            document.getElementById('sub').textContent = sub.toFixed(2);
            document.getElementById('net').textContent = (sub-disc).toFixed(2);
            document.getElementById('due').textContent = (sub-disc-paid).toFixed(2);
        }

        rows.addEventListener('input', e => {
            if (e.target.classList.contains('med-name')) {
                const tr  = e.target.closest('tr');
                const hid = tr.querySelector('.med-id'); hid.value = '';
                for (const o of medList.options) {
                    if (o.value === e.target.value) {
                        hid.value = o.dataset.id;
                        if (o.dataset.price) tr.querySelector('.price').value = o.dataset.price;  // last purchase price
                        if (o.dataset.mrp)   tr.querySelector('.mrp').value   = o.dataset.mrp;    // medicine default price
                        if (o.dataset.pct)   tr.querySelector('.pct').value   = o.dataset.pct;    // last discount %
                        if (o.dataset.category) tr.querySelector('.cat').value = o.dataset.category; // NEW: category auto
                        const co = document.getElementById('company_id');
                        if (o.dataset.company && !co.value) co.value = o.dataset.company;         // company auto
                        break;
                    }
                }
                e.target.classList.toggle('is-invalid', e.target.value !== '' && !hid.value);
            }
            calc();
        });

        rows.addEventListener('click', e => { if (e.target.classList.contains('remove')) { e.target.closest('tr').remove(); calc(); } });
        document.getElementById('discount').addEventListener('input', calc);
        document.getElementById('paid').addEventListener('input', calc);
        document.getElementById('add-row').addEventListener('click', addRow);
        document.getElementById('purchase-form').addEventListener('submit', e => {
            if ([...rows.querySelectorAll('.med-id')].some(h => !h.value)) { e.preventDefault(); alert('প্রতিটি লাইনে তালিকা থেকে ঔষধ বেছে নিন।'); }
        });
        addRow();
    </script>
@endsection