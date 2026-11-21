@extends('layouts.app')

@section('title', 'রিপোর্ট')

@section('content')
<h4 class="mb-3">বিক্রয় রিপোর্ট (PDF)</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    {{-- Daily --}}
    <div class="col-lg-4">
        <form method="GET" action="{{ route('reports.pdf') }}" target="_blank" class="card h-100 js-sales-form">
            <input type="hidden" name="type" value="daily">
            <div class="card-header fw-semibold">📅 দৈনিক রিপোর্ট</div>
            <div class="card-body">
                <label class="form-label">তারিখ</label>
                <input type="date" name="date" value="{{ $today }}" required class="form-control mb-3">
                @include('reports._shop', ['shops' => $shops])
                @include('reports._part')
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn btn-success flex-fill">দেখুন</button>
                <button class="btn btn-outline-success" name="download" value="1">ডাউনলোড</button>
            </div>
        </form>
    </div>

    {{-- Monthly --}}
    <div class="col-lg-4">
        <form method="GET" action="{{ route('reports.pdf') }}" target="_blank" class="card h-100 js-sales-form">
            <input type="hidden" name="type" value="monthly">
            <div class="card-header fw-semibold">🗓️ মাসিক রিপোর্ট</div>
            <div class="card-body">
                <label class="form-label">মাস</label>
                <input type="month" name="month" value="{{ $month }}" required class="form-control mb-3">
                @include('reports._shop', ['shops' => $shops])
                @include('reports._part')
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn btn-success flex-fill">দেখুন</button>
                <button class="btn btn-outline-success" name="download" value="1">ডাউনলোড</button>
            </div>
        </form>
    </div>

    {{-- Yearly --}}
    <div class="col-lg-4">
        <form method="GET" action="{{ route('reports.pdf') }}" target="_blank" class="card h-100 js-sales-form">
            <input type="hidden" name="type" value="yearly">
            <div class="card-header fw-semibold">📊 বার্ষিক রিপোর্ট</div>
            <div class="card-body">
                <label class="form-label">বছর</label>
                <input type="number" name="year" value="{{ $year }}" min="2000" max="2100" required class="form-control mb-3">
                @include('reports._shop', ['shops' => $shops])
                @include('reports._part')
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn btn-success flex-fill">দেখুন</button>
                <button class="btn btn-outline-success" name="download" value="1">ডাউনলোড</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= All other reports ================= --}}
@php
    $ms = now()->startOfMonth()->toDateString();
    $td = now()->toDateString();
    $quick = [
        ['📄 মেমো রিপোর্ট',        'memos',         ['from' => $ms, 'to' => $td], 'এই মাসের সব মেমো'],
        ['🏪 দোকান রিপোর্ট',        'shops',         ['from' => $ms, 'to' => $td], 'দোকান অনুযায়ী বিক্রি, বাকি, ক্রয়'],
        ['👥 গ্রাহক রিপোর্ট',       'customers',     ['due_only' => 1],            'যাদের বাকি আছে'],
        ['🏭 কোম্পানি রিপোর্ট',     'companies',     ['due_only' => 1],            'কোম্পানির কাছে কত দেনা'],
        ['🧾 ক্রয় রিপোর্ট',        'purchases',     ['from' => $ms, 'to' => $td], 'এই মাসের ক্রয়'],
        ['💊 পণ্য বিক্রয় রিপোর্ট',  'product_sales', ['from' => $ms, 'to' => $td], 'এই মাসে কোন পণ্য কত বিক্রি'],
    ];
    $stock = [
        ['📦 স্টকে আছে (মেয়াদসহ)',   ['expiry' => 'stock']],
        ['⛔ মেয়াদোত্তীর্ণ',          ['expiry' => 'expired']],
        ['⚠️ ৯০ দিনের মধ্যে মেয়াদ শেষ', ['expiry' => 'soon', 'days' => 90]],
        ['✅ মেয়াদ আছে',             ['expiry' => 'valid']],
        ['❔ স্টক / মেয়াদের তথ্য নেই', ['expiry' => 'no_stock']],
        ['📚 সব ঔষধ',                ['expiry' => 'all']],
    ];
@endphp

<h5 class="mt-5 mb-3">অন্যান্য রিপোর্ট (PDF)</h5>
<div class="row g-3">
    @foreach ($quick as [$label, $key, $params, $hint])
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 js-quick" data-report="{{ $key }}" data-params='@json($params)'>
                <div class="card-body">
                    <div class="fw-semibold">{{ $label }}</div>
                    <div class="text-muted small mb-2">{{ $hint }}</div>

                    <label class="form-label small mb-1">কোন অংশ</label>
                    <select class="form-select form-select-sm js-part"><option value="0">0</option></select>
                    <div class="form-text js-part-info">লোড হচ্ছে...</div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <a class="btn btn-success flex-fill js-view" target="_blank"
                       href="{{ route('hub.pdf', ['report' => $key] + $params) }}">PDF দেখুন</a>
                    <a class="btn btn-outline-success js-download"
                       href="{{ route('hub.pdf', ['report' => $key, 'download' => 1] + $params) }}">ডাউনলোড</a>
                    <a class="btn btn-outline-secondary" href="{{ route('hub.index', ['report' => $key]) }}">ফিল্টার</a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<h5 class="mt-4 mb-3">📦 পণ্য স্টক ও মেয়াদ রিপোর্ট</h5>
<div class="card js-quick" id="stockCard" data-report="product_stock" data-params="{}">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 mb-3" id="stockModes">
            @foreach ($stock as $i => [$label, $params])
                <input type="radio" class="btn-check" name="stock_mode" id="sm{{ $i }}"
                       data-params='@json($params)' @checked($i === 0)>
                <label class="btn btn-outline-success" for="sm{{ $i }}">{{ $label }}</label>
            @endforeach
        </div>

        <label class="form-label small mb-1">কোন অংশ (প্রতি PDF-এ সর্বোচ্চ ১০০টি)</label>
        <select class="form-select w-auto js-part" style="min-width:220px"><option value="0">0</option></select>
        <div class="form-text js-part-info">লোড হচ্ছে...</div>
    </div>
    <div class="card-footer d-flex flex-wrap gap-2">
        <a class="btn btn-success js-view" target="_blank" href="#">PDF দেখুন</a>
        <a class="btn btn-outline-success js-download" href="#">ডাউনলোড</a>
        <a class="btn btn-secondary ms-auto" href="{{ route('hub.index', ['report' => 'product_stock']) }}">দোকান / কোম্পানি ফিল্টার সহ →</a>
    </div>
</div>

<div class="mt-4">
    <a class="btn btn-primary" href="{{ route('hub.index') }}">সব রিপোর্ট — বিস্তারিত ফিল্টার</a>
    <a class="btn btn-outline-primary" href="{{ route('ledgers.index') }}">লেজার রিপোর্ট</a>
</div>

<script>
(function () {
    const SALES_COUNT_URL = @json(route('reports.count'));
    const HUB_COUNT_URL   = @json(route('hub.count'));
    const HUB_PDF_URL     = @json(route('hub.pdf'));

    /** count endpoint theke "0 – 99, 100 – 199 ..." ene select bhoro */
    const tokens = new WeakMap();
    async function fillParts(sel, info, url, params) {
        const token = (tokens.get(sel) || 0) + 1;
        tokens.set(sel, token);
        const keep = sel.value;
        info.textContent = 'লোড হচ্ছে...';
        try {
            const res = await fetch(url + '?' + params, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const d = await res.json();
            if (tokens.get(sel) !== token) return;          // পুরনো রেসপন্স বাদ
            sel.innerHTML = '';
            d.parts.forEach(p => sel.add(new Option(p.label, p.value)));
            if ([...sel.options].some(o => o.value === keep)) sel.value = keep;
            info.textContent = 'মোট ' + d.total + ' টি রেকর্ড, ' + d.parts.length + ' টি অংশে ভাগ করা।';
        } catch (e) {
            if (tokens.get(sel) !== token) return;
            info.textContent = 'রেকর্ড সংখ্যা জানা যায়নি।';
        }
        sel.dispatchEvent(new Event('partsloaded'));
    }

    const debounce = (fn, ms = 250) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

    /* ---------- Daily / Monthly / Yearly forms ---------- */
    document.querySelectorAll('.js-sales-form').forEach(form => {
        const sel  = form.querySelector('.js-part');
        const info = form.querySelector('.js-part-info');
        const load = debounce(() => {
            const p = new URLSearchParams();
            ['type', 'date', 'month', 'year', 'shop_id'].forEach(n => {
                const el = form.elements[n];
                if (el && el.value !== '') p.set(n, el.value);
            });
            fillParts(sel, info, SALES_COUNT_URL, p);
        });
        form.addEventListener('input', e => { if (e.target !== sel) load(); });
        load();
    });

    /* ---------- Quick cards + stock card ---------- */
    document.querySelectorAll('.js-quick').forEach(card => {
        const sel  = card.querySelector('.js-part');
        const info = card.querySelector('.js-part-info');
        const view = card.querySelector('.js-view');
        const dl   = card.querySelector('.js-download');
        const isStock = card.id === 'stockCard';

        const params = () => {
            let extra = JSON.parse(card.dataset.params || '{}');
            if (isStock) {
                const m = card.querySelector('input[name="stock_mode"]:checked');
                extra = m ? JSON.parse(m.dataset.params) : {};
            }
            return Object.assign({ report: card.dataset.report }, extra);
        };

        const links = () => {
            const p = new URLSearchParams(params());
            p.set('part', sel.value || 0);
            view.href = HUB_PDF_URL + '?' + p;
            p.set('download', '1');
            dl.href = HUB_PDF_URL + '?' + p;
        };

        const load = () => fillParts(sel, info, HUB_COUNT_URL, new URLSearchParams(params()));

        sel.addEventListener('change', links);
        sel.addEventListener('partsloaded', links);
        if (isStock) card.querySelectorAll('input[name="stock_mode"]').forEach(r => r.addEventListener('change', load));
        load();
    });
})();
</script>
@endsection
