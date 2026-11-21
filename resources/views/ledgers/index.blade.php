@extends('layouts.app')
@section('title', 'লেজার রিপোর্ট')
@section('content')
<h4 class="mb-3">লেজার রিপোর্ট (PDF)</h4>
@include('partials._errors')
<div class="row g-3">
    <div class="col-lg-6"><div class="card h-100">
        <div class="card-header fw-semibold">👥 গ্রাহক লেজার</div>
        <form method="GET" action="{{ route('ledgers.customers') }}" target="_blank">
        <div class="card-body">
            <div class="mb-3"><label class="form-label">ধরন</label>
                <select name="mode" class="form-select mode">
                    <option value="summary">সারাংশ (সবার বাকি এক পাতায়)</option>
                    <option value="single">একজনের বিস্তারিত লেজার</option>
                    <option value="all">সবার আলাদা আলাদা লেজার (এক PDF-এ)</option>
                </select></div>
            <div class="mb-3 single-box" style="display:none"><label class="form-label">গ্রাহক</label>
                <select name="customer_id" class="form-select">
                    <option value="">-- বাছাই করুন --</option>
                    @foreach ($customers as $x)<option value="{{ $x->customer_id }}">{{ trim($x->name) }}</option>@endforeach
                </select></div>
            <div class="row g-2 mb-3 range-box">
                <div class="col"><label class="form-label">থেকে</label><input type="date" name="from" class="form-control"></div>
                <div class="col"><label class="form-label">পর্যন্ত</label><input type="date" name="to" class="form-control"></div>
            </div>
            <div class="form-check"><input type="checkbox" class="form-check-input" id="due_customers" name="due_only" value="1">
                <label class="form-check-label" for="due_customers">শুধু যাদের বাকি আছে</label></div>
        </div>
        <div class="card-footer d-flex gap-2">
            <button class="btn btn-success flex-fill">PDF দেখুন</button>
            <button class="btn btn-outline-success" name="download" value="1">ডাউনলোড</button>
        </div></form>
    </div></div>
    <div class="col-lg-6"><div class="card h-100">
        <div class="card-header fw-semibold">🏭 কোম্পানি লেজার</div>
        <form method="GET" action="{{ route('ledgers.companies') }}" target="_blank">
        <div class="card-body">
            <div class="mb-3"><label class="form-label">ধরন</label>
                <select name="mode" class="form-select mode">
                    <option value="summary">সারাংশ (সবার বাকি এক পাতায়)</option>
                    <option value="single">একজনের বিস্তারিত লেজার</option>
                    <option value="all">সবার আলাদা আলাদা লেজার (এক PDF-এ)</option>
                </select></div>
            <div class="mb-3 single-box" style="display:none"><label class="form-label">কোম্পানি</label>
                <select name="company_id" class="form-select">
                    <option value="">-- বাছাই করুন --</option>
                    @foreach ($companies as $x)<option value="{{ $x->company_id }}">{{ trim($x->company_name) }}</option>@endforeach
                </select></div>
            <div class="row g-2 mb-3 range-box">
                <div class="col"><label class="form-label">থেকে</label><input type="date" name="from" class="form-control"></div>
                <div class="col"><label class="form-label">পর্যন্ত</label><input type="date" name="to" class="form-control"></div>
            </div>
            <div class="form-check"><input type="checkbox" class="form-check-input" id="due_companies" name="due_only" value="1">
                <label class="form-check-label" for="due_companies">শুধু যাদের বাকি আছে</label></div>
        </div>
        <div class="card-footer d-flex gap-2">
            <button class="btn btn-success flex-fill">PDF দেখুন</button>
            <button class="btn btn-outline-success" name="download" value="1">ডাউনলোড</button>
        </div></form>
    </div></div>
</div>
<script>
document.querySelectorAll('.card form').forEach(f => {
    const sel = f.querySelector('.mode'), box = f.querySelector('.single-box');
    const upd = () => { box.style.display = sel.value === 'single' ? '' : 'none';
                        f.querySelector('.range-box').style.display = sel.value === 'summary' ? 'none' : ''; };
    sel.addEventListener('change', upd); upd();
});
</script>
@endsection
