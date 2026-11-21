@extends('layouts.app')

@section('title', 'গ্রাহক তালিকা')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">গ্রাহক তালিকা <small class="text-muted fs-6">(মোট {{ $customers->total() }}জন)</small></h4>
    <div class="d-flex gap-2">
        <a href="{{ route('customers.report') }}" target="_blank" class="btn btn-outline-danger">বাকির রিপোর্ট (PDF)</a>
        <a href="{{ route('customers.report', ['all' => 1]) }}" target="_blank" class="btn btn-outline-secondary">সব গ্রাহক (PDF)</a>
        <a href="{{ route('customers.create') }}" class="btn btn-success">+ নতুন গ্রাহক</a>
    </div>
</div>

@error('delete')<div class="alert alert-danger">{{ $message }}</div>@enderror

<form method="GET" class="card card-body mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6"><label class="form-label small mb-1">সার্চ</label>
            <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="নাম, মোবাইল বা ঠিকানা..."></div>
        <div class="col-md-3">
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="due_only" value="1" id="due_only" @checked(request()->boolean('due_only'))>
                <label class="form-check-label" for="due_only">শুধু যাদের বাকি আছে</label></div>
        </div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-fill">খুঁজুন</button><a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">রিসেট</a></div>
    </div>
</form>

<div class="card"><div class="table-responsive">
<table class="table table-hover table-striped align-middle mb-0">
    <thead class="table-light"><tr><th>#</th><th>নাম</th><th>মোবাইল</th><th>ঠিকানা</th><th class="text-end">বাকি (৳)</th><th class="text-end">অ্যাকশন</th></tr></thead>
    <tbody>
    @forelse ($customers as $c)
        <tr>
            <td>{{ $c->customer_id }}</td>
            <td><a href="{{ route('customers.show', $c->customer_id) }}" class="fw-semibold text-decoration-none">{{ $c->name }}</a></td>
            <td>{{ $c->phone ?: '-' }}</td>
            <td>{{ $c->address ?: '-' }}</td>
            <td class="text-end {{ $c->due_balance > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ number_format($c->due_balance, 2) }}</td>
            <td class="text-end text-nowrap">
                <a href="{{ route('customers.show', $c->customer_id) }}" class="btn btn-sm btn-outline-secondary">দেখুন</a>
                <a href="{{ route('customers.edit', $c->customer_id) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
                <form method="POST" action="{{ route('customers.destroy', $c->customer_id) }}" class="d-inline" onsubmit="return confirm('মুছে ফেলতে চান?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">ডিলিট</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted py-4">কোনো গ্রাহক পাওয়া যায়নি।</td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $customers->links('pagination::bootstrap-5') }}</div>
@endsection
