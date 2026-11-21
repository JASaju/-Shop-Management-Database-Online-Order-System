{{-- resources/views/reports/hub.blade.php --}}
{{-- NOTE: layout naam ('layouts.app') ar section naam ('content') tomar ager layout onujayi mil kore nio. --}}
@extends('layouts.app')

@section('title', 'সব রিপোর্ট (PDF)')

@section('content')
<div class="container py-3">
    <h4 class="mb-3 fw-bold">সব রিপোর্ট (PDF)</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- ========== কোন রিপোর্ট ========== --}}
            <label class="form-label fw-bold" for="report">কোন রিপোর্ট?</label>
            <select id="report" class="form-select form-select-lg mb-3">
                <option value="memos">🧾 মেমো রিপোর্ট</option>
                <option value="shops">🏬 দোকান রিপোর্ট</option>
                <option value="customers">👥 কাস্টমার রিপোর্ট</option>
                <option value="companies">🏭 কোম্পানি রিপোর্ট</option>
                <option value="purchases">🛒 ক্রয় রিপোর্ট</option>
                <option value="product_sales">💊 পণ্য বিক্রয় রিপোর্ট</option>
                <option value="product_stock">📦 পণ্য স্টক ও মেয়াদ রিপোর্ট</option>
            </select>

            {{-- ========== ফিল্টার ========== --}}
            <div class="row g-3">

                <div class="col-md-4" data-f="shop_id">
                    <label class="form-label">দোকান</label>
                    <select class="form-select" name="shop_id">
                        <option value="">সব</option>
                        @foreach ($shops as $s)
                            <option value="{{ $s->shop_id }}">{{ trim($s->shop_name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4" data-f="company_id">
                    <label class="form-label">কোম্পানি</label>
                    <select class="form-select" name="company_id">
                        <option value="">সব</option>
                        @foreach ($companies as $c)
                            <option value="{{ $c->company_id }}">{{ trim($c->company_name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4" data-f="customer_id">
                    <label class="form-label">কাস্টমার</label>
                    <select class="form-select" name="customer_id">
                        <option value="">সব</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->customer_id }}">{{ trim($c->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4" data-f="staff_id">
                    <label class="form-label">বিক্রেতা</label>
                    <select class="form-select" name="staff_id">
                        <option value="">সব</option>
                        @foreach ($staff as $st)
                            <option value="{{ $st->staff_id }}">{{ trim($st->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4" data-f="from">
                    <label class="form-label">শুরুর তারিখ</label>
                    <input type="date" class="form-control" name="from" value="{{ $month_start }}">
                </div>

                <div class="col-md-4" data-f="to">
                    <label class="form-label">শেষ তারিখ</label>
                    <input type="date" class="form-control" name="to" value="{{ $today }}">
                </div>

                <div class="col-md-6" data-f="expiry">
                    <label class="form-label">মেয়াদ অবস্থা</label>
                    <select class="form-select" name="expiry">
                        <option value="stock" selected>স্টকে আছে (মেয়াদসহ)</option>
                        <option value="expired">মেয়াদ উত্তীর্ণ</option>
                        <option value="soon">শীঘ্রই মেয়াদ শেষ হবে</option>
                        <option value="valid">মেয়াদ উত্তীর্ণ হয়নি</option>
                        <option value="no_stock">স্টক নেই / মেয়াদ নেই</option>
                        <option value="all">সব ওষুধ</option>
                    </select>
                </div>

                <div class="col-md-3" data-f="days">
                    <label class="form-label">কত দিনের মধ্যে?</label>
                    <input type="number" class="form-control" name="days" min="1" max="1000" value="90">
                </div>

                <div class="col-md-4 d-flex align-items-end" data-f="due_only">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="due_only" name="due_only" value="1">
                        <label class="form-check-label" for="due_only">শুধু যাদের বাকি আছে</label>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- ========== কোন অংশ ========== --}}
            <label class="form-label fw-bold" for="part">কোন অংশ (প্রতি PDF-এ সর্বোচ্চ ১০০টি)</label>
            <select id="part" class="form-select w-auto" style="min-width:260px"></select>
            <div id="partInfo" class="form-text">লোড হচ্ছে...</div>
        </div>

        <div class="card-footer bg-white d-flex gap-2">
            <button type="button" id="btnView" class="btn btn-success flex-grow-1">PDF দেখুন</button>
            <button type="button" id="btnDownload" class="btn btn-outline-success">ডাউনলোড</button>
        </div>
    </div>
</div>

<script>
(function () {
    // কোন রিপোর্টে কোন ফিল্টার কাজ করে (ReportHubController::REPORTS-এর সাথে মিল)
    const FILTERS = {
        memos:         ['shop_id', 'from', 'to', 'customer_id', 'staff_id', 'due_only'],
        shops:         ['shop_id', 'from', 'to'],
        customers:     ['shop_id', 'from', 'to', 'customer_id', 'due_only'],
        companies:     ['shop_id', 'from', 'to', 'company_id', 'due_only'],
        purchases:     ['shop_id', 'from', 'to', 'company_id', 'due_only'],
        product_sales: ['shop_id', 'from', 'to', 'company_id', 'customer_id', 'staff_id'],
        product_stock: ['shop_id', 'company_id', 'expiry', 'days'],
    };

    const COUNT_URL = @json(route('hub.count'));
    const PDF_URL   = @json(route('hub.pdf'));

    const reportEl = document.getElementById('report');
    const partEl   = document.getElementById('part');
    const infoEl   = document.getElementById('partInfo');
    const fields   = document.querySelectorAll('[data-f]');

    // URL-এ ?report=product_stock থাকলে সেটাই select হবে
    const urlReport = new URLSearchParams(location.search).get('report');
    if (urlReport && FILTERS[urlReport]) reportEl.value = urlReport;

    /** এই রিপোর্টের জন্য দরকারি ফিল্টার দেখাও, বাকিগুলো লুকাও */
    function syncFields() {
        const allowed = FILTERS[reportEl.value] || [];
        fields.forEach(box => {
            const key  = box.dataset.f;
            let show   = allowed.includes(key);
            if (key === 'days') {
                const exp = document.querySelector('[name="expiry"]');
                show = show && exp && exp.value === 'soon';
            }
            box.classList.toggle('d-none', !show);
            box.querySelectorAll('input, select').forEach(i => i.disabled = !show);
        });
    }

    /** দৃশ্যমান ফিল্টার থেকে query string বানাও */
    function buildParams(extra = {}) {
        const p = new URLSearchParams({ report: reportEl.value });
        document.querySelectorAll('[data-f] input, [data-f] select').forEach(el => {
            if (el.disabled || !el.name) return;
            if (el.type === 'checkbox') { if (el.checked) p.set(el.name, '1'); return; }
            if (el.value !== '') p.set(el.name, el.value);
        });
        Object.entries(extra).forEach(([k, v]) => p.set(k, v));
        return p;
    }

    /** কয়টা রেকর্ড মিলছে জেনে "কোন অংশ" dropdown ভর্তি করো (0 – 99, 100 – 199 ...) */
    let timer = null;
    function loadParts() {
        clearTimeout(timer);
        timer = setTimeout(async () => {
            infoEl.textContent = 'লোড হচ্ছে...';
            try {
                const res = await fetch(COUNT_URL + '?' + buildParams(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const d = await res.json();

                partEl.innerHTML = '';
                d.parts.forEach(p => partEl.add(new Option(p.label, p.value)));
                infoEl.textContent = 'মোট ' + d.total + ' টি রেকর্ড, ' + d.parts.length + ' টি অংশে ভাগ করা।';
            } catch (e) {
                partEl.innerHTML = '';
                infoEl.textContent = 'রেকর্ড সংখ্যা জানা যায়নি (ফিল্টার ঠিক আছে কিনা দেখুন)।';
            }
        }, 250);
    }

    function openPdf(download) {
        const params = buildParams({ part: partEl.value || 0 });
        if (download) params.set('download', '1');
        window.open(PDF_URL + '?' + params, download ? '_self' : '_blank');
    }

    // রিপোর্ট বদলালে ফিল্টার আপডেট + অংশ রিলোড
    reportEl.addEventListener('change', () => { syncFields(); loadParts(); });

    // যেকোনো ফিল্টার বদলালে অংশ রিলোড
    fields.forEach(box => box.addEventListener('input', () => {
        if (box.dataset.f === 'expiry') syncFields();
        loadParts();
    }));

    document.getElementById('btnView').addEventListener('click', () => openPdf(false));
    document.getElementById('btnDownload').addEventListener('click', () => openPdf(true));

    syncFields();
    loadParts();
})();
</script>
@endsection
