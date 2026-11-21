@extends('layouts.app')
@section('title', 'কোম্পানি এডিট')
@section('content')
@include('partials._errors')
<div class="row justify-content-center"><div class="col-lg-8"><div class="card">
    <div class="card-header fw-semibold">কোম্পানি এডিট করুন</div>
    <div class="card-body">
        <form method="POST" action="{{ route('companies.update', $company->company_id) }}">
            @method('PUT')
            @include('companies._form', ['submit' => 'আপডেট করুন'])
        </form>
    </div>
</div></div></div>
@endsection
