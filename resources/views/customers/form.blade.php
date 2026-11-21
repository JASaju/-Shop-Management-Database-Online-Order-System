@extends('layouts.app')

@section('title', $customer->exists ? 'গ্রাহক এডিট' : 'নতুন গ্রাহক')

@section('content')
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card">
    <div class="card-header fw-semibold">{{ $customer->exists ? 'গ্রাহক এডিট (#' . $customer->customer_id . ')' : 'নতুন গ্রাহক' }}</div>
    <div class="card-body">
        <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer->customer_id) : route('customers.store') }}">
            @csrf
            @if ($customer->exists) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">নাম <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" required maxlength="150"
                       class="form-control @error('name') is-invalid @enderror">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">মোবাইল নম্বর</label>
                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" maxlength="20"
                       class="form-control @error('phone') is-invalid @enderror">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">ঠিকানা</label>
                <input type="text" name="address" value="{{ old('address', $customer->address) }}" maxlength="255"
                       class="form-control @error('address') is-invalid @enderror">
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-success">সংরক্ষণ করুন</button>
                <a href="{{ $customer->exists ? route('customers.show', $customer->customer_id) : route('customers.index') }}" class="btn btn-outline-secondary">বাতিল</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection
