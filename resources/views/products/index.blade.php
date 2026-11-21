@extends('layouts.app')

@section('title', 'পণ্যের তালিকা')

@section('content')
@php
    // Sortable column header link
    $sortLink = function (string $key, string $label) use ($sort, $dir) {
        $nextDir = ($sort === $key && $dir === 'asc') ? 'desc' : 'asc';
        $arrow   = $sort === $key ? ($dir === 'asc' ? ' ▲' : ' ▼') : '';
        $url     = request()->fullUrlWithQuery(['sort' => $key, 'dir' => $nextDir, 'page' => 1]);
        return '<a href="' . e($url) . '">' . e($label) . $arrow . '</a>';
    };
@endphp
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">পণ্যের তালিকা <small class="text-muted fs-6">(মোট {{ $products->total() }}টি)</small></h4>
    <a href="{{ route('products.create') }}" class="btn btn-success">+ নতুন পণ্য</a>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('home') }}" class="card card-body mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small mb-1">সার্চ</label>
            <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="পণ্যের নাম, ইউনিট, দাম বা আইডি...">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">কোন ফিল্ডে খুঁজবেন</label>
            <select name="field" class="form-select">
                <option value="all"   @selected($field === 'all')>সব ফিল্ড</option>
                <option value="name"  @selected($field === 'name')>নাম</option>
                <option value="unit"  @selected($field === 'unit')>ইউনিট</option>
                <option value="price" @selected($field === 'price')>দাম</option>
                <option value="id"    @selected($field === 'id')>আইডি</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">ইউনিট ফিল্টার</label>
            <select name="unit" class="form-select">
                <option value="">সব</option>
                @foreach ($units as $u)
                    <option value="{{ $u }}" @selected($unit === $u)>{{ $u }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-fill">খুঁজুন</button>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">রিসেট</a>
        </div>
    </div>
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir" value="{{ $dir }}">
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>{!! $sortLink('id', '#') !!}</th>
                <th>{!! $sortLink('name', 'নাম') !!}</th>
                <th>{!! $sortLink('unit', 'ইউনিট') !!}</th>
                <th class="text-end">{!! $sortLink('price', 'দাম (৳)') !!}</th>
                <th>{!! $sortLink('date', 'যোগ করা হয়েছে') !!}</th>
                <th class="text-end">অ্যাকশন</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->medicine_id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->unit }}</td>
                    <td class="text-end">{{ number_format($product->default_price, 2) }}</td>
                    <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($product->created_at)->format('d-m-Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('products.edit', $product->medicine_id) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
                        <form method="POST" action="{{ route('products.destroy', $product->medicine_id) }}" class="d-inline"
                              onsubmit="return confirm('আপনি কি নিশ্চিত যে \'{{ e(addslashes($product->name)) }}\' মুছে ফেলতে চান?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">ডিলিট</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">কোনো পণ্য পাওয়া যায়নি।</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $products->links('pagination::bootstrap-5') }}
</div>
@endsection
