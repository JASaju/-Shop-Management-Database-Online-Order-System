@extends('layouts.app')
@section('title', $company->company_name)
@section('content')
@include('partials._errors')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="mb-0">{{ $company->company_name }}</h4>
        <div class="text-muted small">{{ $company->contact_person }} {{ $company->phone_1 }} {{ $company->phone_2 ? ', '.$company->phone_2 : '' }}</div>
        <div class="text-muted small">{{ $company->address }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('companies.edit', $company->company_id) }}" class="btn btn-outline-primary btn-sm">এডিট</a>
        <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary btn-sm">তালিকা</a>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card card-body"><div class="small text-muted">মোট ক্রয়</div><div class="fs-5">৳ {{ number_format($due->total_purchased ?? 0, 2) }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><div class="small text-muted">মোট পরিশোধ</div><div class="fs-5">৳ {{ number_format($due->total_paid ?? 0, 2) }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><div class="small text-muted">মোট বাকি</div><div class="fs-5 text-danger">৳ {{ number_format($due->total_due ?? 0, 2) }}</div></div></div>
</div>
<div class="card mb-4"><div class="card-header fw-semibold">ক্রয়ের তালিকা</div><div class="table-responsive">
<table class="table table-sm mb-0">
    <thead class="table-light"><tr><th>ইনভয়েস</th><th>তারিখ</th><th class="text-end">মোট</th><th class="text-end">পরিশোধ</th><th class="text-end">বাকি</th></tr></thead>
    <tbody>
    @forelse ($purchases as $p)
        <tr>
            <td><a href="{{ route('purchases.show', $p->purchase_id) }}">{{ $p->invoice_no }}</a></td>
            <td>{{ \Carbon\Carbon::parse($p->purchase_date)->format('d-m-Y') }}</td>
            <td class="text-end">{{ number_format($p->total_amount - $p->discount_amount, 2) }}</td>
            <td class="text-end">{{ number_format($p->paid_amount, 2) }}</td>
            <td class="text-end">{{ number_format($p->due_amount, 2) }}</td>
        </tr>
    @empty <tr><td colspan="5" class="text-center text-muted py-3">কোনো ক্রয় নেই।</td></tr> @endforelse
    </tbody>
</table></div></div>
<div class="card"><div class="card-header fw-semibold">পেমেন্ট ইতিহাস</div><div class="table-responsive">
<table class="table table-sm mb-0">
    <thead class="table-light"><tr><th>তারিখ</th><th>মাধ্যম</th><th>রেফারেন্স</th><th class="text-end">টাকা</th></tr></thead>
    <tbody>
    @forelse ($payments as $pay)
        <tr><td>{{ \Carbon\Carbon::parse($pay->payment_date)->format('d-m-Y') }}</td><td>{{ $pay->method }}</td><td>{{ $pay->reference_no ?: '-' }}</td><td class="text-end">{{ number_format($pay->amount, 2) }}</td></tr>
    @empty <tr><td colspan="4" class="text-center text-muted py-3">কোনো পেমেন্ট নেই।</td></tr> @endforelse
    </tbody>
</table></div></div>
@endsection
