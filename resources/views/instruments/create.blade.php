@extends('layouts.app')
@section('title', 'নতুন যন্ত্রপাতি')
@section('content')
@include('partials._errors')
<div class="row justify-content-center"><div class="col-lg-8"><div class="card">
    <div class="card-header fw-semibold">নতুন যন্ত্রপাতি যোগ করুন</div>
    <div class="card-body"><form method="POST" action="{{ route('instruments.store') }}">@include('instruments._form', ['submit' => 'সংরক্ষণ করুন'])</form></div>
</div></div></div>
@endsection
