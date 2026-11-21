@extends('layouts.app')
@section('title', 'যন্ত্রপাতি এডিট')
@section('content')
@include('partials._errors')
<div class="row justify-content-center"><div class="col-lg-8"><div class="card">
    <div class="card-header fw-semibold">যন্ত্রপাতি এডিট করুন</div>
    <div class="card-body"><form method="POST" action="{{ route('instruments.update', $row->instrument_id) }}">@method('PUT')@include('instruments._form', ['submit' => 'আপডেট করুন'])</form></div>
</div></div></div>
@endsection
