@extends('layouts.app')
@section('title', 'কর্মচারী')
@section('content')
@include('partials._errors')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">কর্মচারী</h4><a href="{{ route('staff.create') }}" class="btn btn-success">+ নতুন কর্মচারী</a></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead class="table-light"><tr><th>নাম</th><th>পদবি</th><th>দোকান</th><th>ফোন</th><th class="text-end">বেতন</th><th>অবস্থা</th><th></th></tr></thead><tbody>
@forelse ($rows as $r)
<tr><td>{{ $r->name }}</td><td>{{ $r->designation ?: '-' }}</td><td>{{ trim($r->shop_name) }}</td><td>{{ $r->phone ?: '-' }}</td>
<td class="text-end">{{ number_format($r->salary, 2) }}</td><td>{!! $r->is_active ? '<span class="badge bg-success">সক্রিয়</span>' : '<span class="badge bg-secondary">নিষ্ক্রিয়</span>' !!}</td>
<td class="text-end text-nowrap">
    <a href="{{ route('staff.edit', $r->staff_id) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
    <form method="POST" action="{{ route('staff.destroy', $r->staff_id) }}" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত?');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">ডিলিট</button></form></td></tr>
@empty <tr><td colspan="7" class="text-center text-muted py-4">কেউ নেই।</td></tr> @endforelse
</tbody></table></div></div>
<div class="mt-3">{{ $rows->links('pagination::bootstrap-5') }}</div>
@endsection
