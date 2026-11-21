@extends('layouts.app')
@section('title', 'নতুন কর্মচারী')
@section('content')
@include('partials._errors')
<div class="row justify-content-center"><div class="col-lg-8"><div class="card">
    <div class="card-header fw-semibold">নতুন কর্মচারী যোগ করুন</div>
    <div class="card-body"><form method="POST" action="{{ route('staff.store') }}">@include('staff._form', ['submit' => 'সংরক্ষণ করুন'])</form></div>
</div></div></div>
@endsection
