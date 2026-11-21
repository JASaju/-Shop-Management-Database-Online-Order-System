@extends('layouts.app')
@section('title', 'মেয়াদ সতর্কতা')
@section('content')
@include('partials._errors')
<h4 class="mb-3">মেয়াদোত্তীর্ণ / মেয়াদ শেষ হতে চলেছে</h4>
<form method="GET" class="card card-body mb-3"><div class="row g-2 align-items-end">
    <div class="col-md-4"><label class="form-label small mb-1">আগামী কত দিনের মধ্যে</label>
        <select name="days" class="form-select">
            @foreach ([0 => 'শুধু মেয়াদোত্তীর্ণ', 30 => '৩০ দিন', 60 => '৬০ দিন', 90 => '৯০ দিন', 180 => '১৮০ দিন'] as $d => $lbl)
                <option value="{{ $d }}" @selected($days === $d)>{{ $lbl }}</option>@endforeach
        </select></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">দেখুন</button></div>
</div></form>
<div class="card"><div class="table-responsive">
<table class="table align-middle mb-0">
    <thead class="table-light"><tr><th>ঔষধ</th><th>ব্যাচ</th><th>কোম্পানি</th><th>মেয়াদ</th><th class="text-end">স্টকে আছে</th><th>অবস্থা</th><th></th></tr></thead>
    <tbody>
    @forelse ($batches as $b)
        <tr class="{{ $b->days_left < 0 ? 'table-danger' : ($b->days_left <= 30 ? 'table-warning' : '') }}">
            <td>{{ $b->medicine_name }}</td><td>{{ $b->batch_no ?: '-' }}</td><td>{{ $b->company_name ?: '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($b->expiry_date)->format('d-m-Y') }}</td>
            <td class="text-end">{{ rtrim(rtrim(number_format($b->quantity_remaining, 2), '0'), '.') }}</td>
            <td>@if($b->days_left < 0)<span class="badge bg-danger">মেয়াদোত্তীর্ণ ({{ abs($b->days_left) }} দিন আগে)</span>
                @else<span class="badge bg-warning text-dark">আর {{ $b->days_left }} দিন</span>@endif</td>
            <td class="text-end">
                <form method="POST" action="{{ route('expiry.destroy', $b->batch_id) }}"
                      onsubmit="return confirm('{{ $b->medicine_name }} (ব্যাচ: {{ $b->batch_no ?: '-' }}) — {{ rtrim(rtrim(number_format($b->quantity_remaining, 2), '0'), '.') }} টি স্টক তালিকা থেকে মুছে ফেলবেন?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">ডিলিট</button>
                </form>
            </td>
        </tr>
    @empty <tr><td colspan="7" class="text-center text-muted py-4">কিছু পাওয়া যায়নি।</td></tr> @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $batches->links('pagination::bootstrap-5') }}</div>
@endsection