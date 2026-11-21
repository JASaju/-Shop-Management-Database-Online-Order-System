@extends('layouts.app')

@section('title', $customer->name)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <h4 class="mb-0">{{ $customer->name }}</h4>
        <div class="text-muted small">{{ $customer->phone ?: 'মোবাইল নেই' }} · {{ $customer->address ?: 'ঠিকানা নেই' }}</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('customers.statement', $customer->customer_id) }}" target="_blank" class="btn btn-outline-danger">স্টেটমেন্ট (PDF)</a>
        @if ($balance > 0)
            <a href="{{ route('dues.show', $customer->customer_id) }}" class="btn btn-success">টাকা জমা নিন</a>
        @endif
        <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-outline-primary">এডিট</a>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">তালিকা</a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card"><div class="card-body">
        <div class="text-muted small">বর্তমান বাকি</div>
        <div class="fs-3 fw-bold {{ $balance > 0 ? 'text-danger' : 'text-success' }}">৳ {{ number_format($balance, 2) }}</div>
    </div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body">
        <div class="text-muted small">মোট বাকি দেওয়া হয়েছে</div>
        <div class="fs-4">৳ {{ number_format($ledger->where('entry_type', 'due')->sum('amount'), 2) }}</div>
    </div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body">
        <div class="text-muted small">মোট জমা পাওয়া গেছে</div>
        <div class="fs-4 text-success">৳ {{ number_format($ledger->where('entry_type', 'payment')->sum('amount'), 2) }}</div>
    </div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-semibold">বাকি হিসাব (লেজার)</div>
            <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>তারিখ</th><th>বিবরণ</th><th class="text-end">বাকি</th><th class="text-end">জমা</th><th class="text-end">ব্যালেন্স</th></tr></thead>
                <tbody>
                @php $run = 0; @endphp
                @forelse ($ledger as $l)
                    @php $run += $l->entry_type === 'due' ? $l->amount : -$l->amount; @endphp
                    <tr>
                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($l->entry_date)->format('d-m-Y') }}</td>
                        <td>@if ($l->memo)<a href="{{ route('memos.show', $l->memo_id) }}">মেমো #{{ $l->memo->memo_no }}</a>@else {{ $l->entry_type === 'payment' ? 'জমা' : 'বাকি' }} @endif
                            @if ($l->method)<span class="badge text-bg-light">{{ $l->method }}</span>@endif</td>
                        <td class="text-end">{{ $l->entry_type === 'due' ? number_format($l->amount, 2) : '' }}</td>
                        <td class="text-end text-success">{{ $l->entry_type === 'payment' ? number_format($l->amount, 2) : '' }}</td>
                        <td class="text-end fw-semibold">{{ number_format($run, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">কোনো বাকি/জমা নেই।</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header fw-semibold">সাম্প্রতিক মেমো</div>
            <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>মেমো</th><th>তারিখ</th><th class="text-end">টাকা</th><th></th></tr></thead>
                <tbody>
                @forelse ($customer->memos as $m)
                    <tr>
                        <td>#{{ $m->memo_no }}</td>
                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($m->memo_date)->format('d-m-Y') }}</td>
                        <td class="text-end">{{ number_format($m->total_amount - $m->discount_amount, 2) }}</td>
                        <td class="text-end"><a href="{{ route('memos.show', $m->memo_id) }}" class="btn btn-sm btn-outline-primary">দেখুন</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">কোনো মেমো নেই।</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
