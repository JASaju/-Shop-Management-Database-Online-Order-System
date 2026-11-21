@extends('layouts.app')
@section('title', $customer->name)
@section('content')
@include('partials._errors')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div><h4 class="mb-0">{{ $customer->name }}</h4>
        <div class="text-muted small">{{ $customer->phone }} {{ $customer->address }}</div></div>
    <a href="{{ route('dues.index') }}" class="btn btn-outline-secondary btn-sm">তালিকা</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card card-body"><div class="small text-muted">মোট বাকি পড়েছে</div><div class="fs-5">৳ {{ number_format($customer->total_due, 2) }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><div class="small text-muted">আদায় হয়েছে</div><div class="fs-5">৳ {{ number_format($customer->total_paid, 2) }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><div class="small text-muted">বর্তমান বাকি</div><div class="fs-5 text-danger">৳ {{ number_format($customer->balance, 2) }}</div></div></div>
</div>
<div class="row g-3">
    <div class="col-lg-7"><div class="card"><div class="card-header fw-semibold">লেনদেনের খাতা</div>
    <table class="table table-sm mb-0">
        <thead class="table-light"><tr><th>তারিখ</th><th>ধরন</th><th>মেমো</th><th>নোট</th><th class="text-end">টাকা</th></tr></thead>
        <tbody>
        @forelse ($ledger as $l)
            <tr>
                <td>{{ \Carbon\Carbon::parse($l->entry_date)->format('d-m-Y') }}</td>
                <td>@if($l->entry_type === 'due')<span class="badge bg-danger">বাকি</span>@else<span class="badge bg-success">আদায় ({{ $l->method }})</span>@endif</td>
                <td>@if($l->memo_id)<a href="{{ route('memos.show', $l->memo_id) }}">#{{ $l->memo_no }}</a>@else - @endif</td>
                <td>{{ $l->note }}</td>
                <td class="text-end">{{ number_format($l->amount, 2) }}</td>
            </tr>
        @empty <tr><td colspan="5" class="text-center text-muted py-3">কোনো এন্ট্রি নেই।</td></tr> @endforelse
        </tbody>
    </table></div></div>
    @if ($customer->balance > 0)
    <div class="col-lg-5"><form method="POST" action="{{ route('dues.pay', $customer->customer_id) }}" class="card">
        @csrf
        <div class="card-header fw-semibold">বাকি আদায় করুন</div>
        <div class="card-body">
            <div class="mb-2"><label class="form-label">টাকা (সর্বোচ্চ {{ number_format($customer->balance, 2) }})</label>
                <input type="number" step="0.01" min="0.01" max="{{ $customer->balance }}" name="amount" required class="form-control"></div>
            <div class="mb-2"><label class="form-label">দোকান</label>
                <select name="shop_id" required class="form-select">@foreach ($shops as $s)<option value="{{ $s->shop_id }}">{{ trim($s->shop_name) }}</option>@endforeach</select></div>
            <div class="mb-2"><label class="form-label">তারিখ</label><input type="date" name="entry_date" value="{{ date('Y-m-d') }}" required class="form-control"></div>
            <div class="mb-2"><label class="form-label">মাধ্যম</label>
                <select name="method" class="form-select">@foreach (['cash'=>'নগদ','bkash'=>'বিকাশ','nagad'=>'নগদ (Nagad)','bank'=>'ব্যাংক','other'=>'অন্যান্য'] as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
            <div><label class="form-label">নোট</label><input type="text" name="note" maxlength="255" class="form-control"></div>
        </div>
        <div class="card-footer"><button class="btn btn-success">আদায় সংরক্ষণ</button></div>
    </form></div>
    @endif
</div>
@endsection
