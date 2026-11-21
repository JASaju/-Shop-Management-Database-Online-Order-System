@extends('layouts.app')
@section('title', 'গ্রাহকের বাকি')
@section('content')
@include('partials._errors')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">গ্রাহকের বাকি</h4>
    <div class="fs-5">মোট বাকি: <span class="text-danger fw-semibold">৳ {{ number_format($totalDue, 2) }}</span></div>
</div>
<form method="GET" class="card card-body mb-3"><div class="row g-2 align-items-end">
    <div class="col-md-6"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="নাম বা ফোন..."></div>
    <div class="col-md-3 form-check ms-2"><input type="checkbox" class="form-check-input" id="all" name="all" value="1" @checked(request('all'))><label class="form-check-label" for="all">বাকি নেই এমনও দেখান</label></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">খুঁজুন</button></div>
</div></form>
<div class="card"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>নাম</th><th>ফোন / ঠিকানা</th><th class="text-end">মোট বাকি</th><th class="text-end">আদায়</th><th class="text-end">বর্তমান বাকি</th><th></th></tr></thead>
    <tbody>
    @forelse ($customers as $c)
        <tr>
            <td>{{ $c->name }}</td><td>{{ $c->phone ?: '' }} {{ $c->address }}</td>
            <td class="text-end">{{ number_format($c->total_due, 2) }}</td>
            <td class="text-end">{{ number_format($c->total_paid, 2) }}</td>
            <td class="text-end {{ $c->balance > 0 ? 'text-danger fw-semibold' : '' }}">{{ number_format($c->balance, 2) }}</td>
            <td class="text-end"><a href="{{ route('dues.show', $c->customer_id) }}" class="btn btn-sm btn-outline-primary">বিস্তারিত</a></td>
        </tr>
    @empty <tr><td colspan="6" class="text-center text-muted py-4">কোনো বাকি নেই।</td></tr> @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $customers->links('pagination::bootstrap-5') }}</div>
@endsection
