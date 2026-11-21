@extends('layouts.app')

@section('title', 'নতুন পণ্য')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-semibold">নতুন পণ্য যোগ করুন</div>
            <div class="card-body">
                <form method="POST" action="{{ route('products.store') }}">
                    @include('products._form', ['submit' => 'সংরক্ষণ করুন'])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
