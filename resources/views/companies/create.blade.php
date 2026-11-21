@extends('layouts.app')
@section('title', 'নতুন কোম্পানি')
@section('content')
@include('partials._errors')
<div class="row justify-content-center"><div class="col-lg-8"><div class="card">
    <div class="card-header fw-semibold">নতুন কোম্পানি যোগ করুন</div>
    <div class="card-body">
        <form method="POST" action="{{ route('companies.store') }}">
            @include('companies._form', ['submit' => 'সংরক্ষণ করুন'])
        </form>
    </div>
</div></div></div>
@endsection
