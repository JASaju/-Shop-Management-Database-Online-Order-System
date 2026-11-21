@extends('layouts.app')

@section('title', 'পণ্য এডিট')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-semibold">পণ্য এডিট করুন (#{{ $product->medicine_id }})</div>
            <div class="card-body">
                <form method="POST" action="{{ route('products.update', $product->medicine_id) }}">
                    @method('PUT')
                    @include('products._form', ['submit' => 'আপডেট করুন'])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
