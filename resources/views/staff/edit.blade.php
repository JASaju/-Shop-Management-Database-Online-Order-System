@extends('layouts.app')
@section('title', 'কর্মচারী এডিট')
@section('content')
@include('partials._errors')
<div class="row justify-content-center"><div class="col-lg-8"><div class="card">
    <div class="card-header fw-semibold">কর্মচারী এডিট করুন</div>
    <div class="card-body"><form method="POST" action="{{ route('staff.update', $row->staff_id) }}">@method('PUT')@include('staff._form', ['submit' => 'আপডেট করুন'])</form></div>
</div></div></div>
@endsection
