@extends('layouts.app')
@section('title', 'ক্রয়')
@section('content')
@include('partials._errors')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">ক্রয়</h4>
    <a href="{{ route('purchases.create') }}" class="btn btn-success">+ নতুন ক্রয়</a>
</div>
<form method="GET" class="card card-body mb-3"><div class="row g-2 align-items-end">
    <div class="col-md-5"><label class="form-label small mb-1">কোম্পানি</label>
        <select name="company_id" class="form-select"><option value="">সব</option>
        @foreach ($companies as $c)<option value="{{ $c->company_id }}" @selected(request('company_id') == $c->company_id)>{{ $c->company_name }}</option>@endforeach
        </select></div>
    <div class="col-md-4 form-check ms-2"><input type="checkbox" class="form-check-input" id="due_only" name="due_only" value="1" @checked(request('due_only'))>
        <label class="form-check-label" for="due_only">শুধু বাকি আছে</label></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">খুঁজুন</button></div>
</div></form>
<div class="card"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>ইনভয়েস</th><th>তারিখ</th><th>কোম্পানি</th><th>দোকান</th><th class="text-end">মোট</th><th class="text-end">বাকি</th><th>পরিশোধের শেষ তারিখ</th></tr></thead>
    <tbody>
    @forelse ($purchases as $p)
        <tr>
            <td><a href="{{ route('purchases.show', $p->purchase_id) }}">{{ $p->invoice_no }}</a></td>
            <td>{{ \Carbon\Carbon::parse($p->purchase_date)->format('d-m-Y') }}</td>
            <td>{{ $p->company_name }}</td><td>{{ trim($p->shop_name) }}</td>
            <td class="text-end">{{ number_format($p->total_amount - $p->discount_amount, 2) }}</td>
            <td class="text-end {{ $p->due_amount > 0 ? 'text-danger fw-semibold' : '' }}">{{ number_format($p->due_amount, 2) }}</td>
            <td>{{ $p->due_date ? \Carbon\Carbon::parse($p->due_date)->format('d-m-Y') : '-' }}</td>
        </tr>
    @empty <tr><td colspan="7" class="text-center text-muted py-4">কোনো ক্রয় নেই।</td></tr> @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $purchases->links('pagination::bootstrap-5') }}</div>
@endsection
