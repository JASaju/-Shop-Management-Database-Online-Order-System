@extends('layouts.app')
@section('title', 'যন্ত্রপাতি')
@section('content')
@include('partials._errors')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">দোকানের যন্ত্রপাতি</h4><a href="{{ route('instruments.create') }}" class="btn btn-success">+ নতুন যন্ত্র</a></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead class="table-light"><tr><th>যন্ত্র</th><th>দোকান</th><th class="text-end">পরিমাণ</th><th>ওয়ারেন্টি</th><th>অবস্থা</th><th></th></tr></thead><tbody>
@php $cond = ['good'=>['ভালো','success'],'needs_repair'=>['মেরামত দরকার','warning'],'out_of_order'=>['নষ্ট','danger']]; @endphp
@forelse ($rows as $r)
<tr><td>{{ $r->instrument_name }}<div class="small text-muted">{{ $r->brand_model }} {{ $r->serial_no }}</div></td><td>{{ trim($r->shop_name) }}</td>
<td class="text-end">{{ $r->quantity }}</td><td>{{ $r->warranty_until ? \Carbon\Carbon::parse($r->warranty_until)->format('d-m-Y') : '-' }}</td>
<td><span class="badge bg-{{ $cond[$r->condition_status][1] }}">{{ $cond[$r->condition_status][0] }}</span></td>
<td class="text-end text-nowrap">
    <a href="{{ route('instruments.edit', $r->instrument_id) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
    <form method="POST" action="{{ route('instruments.destroy', $r->instrument_id) }}" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত?');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">ডিলিট</button></form></td></tr>
@empty <tr><td colspan="6" class="text-center text-muted py-4">কিছু নেই।</td></tr> @endforelse
</tbody></table></div></div>
<div class="mt-3">{{ $rows->links('pagination::bootstrap-5') }}</div>
@endsection
