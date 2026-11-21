@extends('layouts.app')
@section('title', 'কোম্পানি')
@section('content')
@include('partials._errors')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">কোম্পানি / সরবরাহকারী</h4>
    <a href="{{ route('companies.create') }}" class="btn btn-success">+ নতুন কোম্পানি</a>
</div>
<div class="card"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr>
        <th>নাম</th><th>ফোন</th><th class="text-end">মোট ক্রয়</th><th class="text-end">পরিশোধ</th><th class="text-end">বাকি</th><th class="text-end">অ্যাকশন</th>
    </tr></thead>
    <tbody>
    @forelse ($companies as $c)
        <tr>
            <td><a href="{{ route('companies.show', $c->company_id) }}">{{ $c->company_name }}</a>
                @unless($c->is_active)<span class="badge bg-secondary">নিষ্ক্রিয়</span>@endunless</td>
            <td>{{ $c->phone_1 ?: '-' }}</td>
            <td class="text-end">{{ number_format($c->total_purchased ?? 0, 2) }}</td>
            <td class="text-end">{{ number_format($c->total_paid ?? 0, 2) }}</td>
            <td class="text-end {{ ($c->total_due ?? 0) > 0 ? 'text-danger fw-semibold' : '' }}">{{ number_format($c->total_due ?? 0, 2) }}</td>
            <td class="text-end text-nowrap">
                <a href="{{ route('companies.edit', $c->company_id) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
                <form method="POST" action="{{ route('companies.destroy', $c->company_id) }}" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত?');">
                    @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">ডিলিট</button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted py-4">কোনো কোম্পানি নেই।</td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $companies->links('pagination::bootstrap-5') }}</div>
@endsection
