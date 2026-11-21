@extends('layouts.app')
@section('title', 'ক্রয় ' . $purchase->invoice_no)
@section('content')
@include('partials._errors')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="mb-0">ইনভয়েস {{ $purchase->invoice_no }}</h4>
        <div class="text-muted small">{{ $purchase->company_name }} · {{ trim($purchase->shop_name) }} · {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y') }}</div>
        @if($purchase->note)<div class="small">{{ $purchase->note }}</div>@endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">তালিকা</a>
        <form method="POST" action="{{ route('purchases.destroy', $purchase->purchase_id) }}" onsubmit="return confirm('আপনি কি নিশ্চিত?');">
            @csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">ডিলিট</button></form>
    </div>
</div>
<div class="card mb-4"><div class="table-responsive">
<table class="table table-sm mb-0">
    <thead class="table-light"><tr><th>ঔষধ</th><th>ব্যাচ</th><th>মেয়াদ</th><th class="text-end">পরিমাণ</th><th class="text-end">অবশিষ্ট</th><th class="text-end">দর</th><th class="text-end">ছাড়</th><th class="text-end">টাকা</th></tr></thead>
    <tbody>
    @foreach ($items as $i)
        <tr>
            <td>{{ $i->name }}</td><td>{{ $i->batch_no ?: '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($i->expiry_date)->format('d-m-Y') }}</td>
            <td class="text-end">{{ rtrim(rtrim(number_format($i->quantity, 2), '0'), '.') }}</td>
            <td class="text-end">{{ rtrim(rtrim(number_format($i->quantity_remaining, 2), '0'), '.') }}</td>
            <td class="text-end">{{ number_format($i->purchase_price, 2) }}</td>
            <td class="text-end">{{ number_format($i->discount_amount, 2) }}</td>
            <td class="text-end">{{ number_format($i->total_price, 2) }}</td>
        </tr>
    @endforeach
    <tr><td colspan="7" class="text-end">সাব-টোটাল</td><td class="text-end">{{ number_format($purchase->total_amount, 2) }}</td></tr>
    <tr><td colspan="7" class="text-end">অতিরিক্ত ছাড়</td><td class="text-end">{{ number_format($purchase->discount_amount, 2) }}</td></tr>
    <tr class="fw-bold"><td colspan="7" class="text-end">নেট</td><td class="text-end">{{ number_format($purchase->total_amount - $purchase->discount_amount, 2) }}</td></tr>
    <tr><td colspan="7" class="text-end">পরিশোধ</td><td class="text-end">{{ number_format($purchase->paid_amount, 2) }}</td></tr>
    <tr class="fw-bold text-danger"><td colspan="7" class="text-end">বাকি</td><td class="text-end">{{ number_format($purchase->due_amount, 2) }}</td></tr>
    </tbody>
</table></div></div>

<div class="row g-3">
    <div class="col-lg-7"><div class="card"><div class="card-header fw-semibold">পেমেন্ট ইতিহাস</div>
    <table class="table table-sm mb-0">
        <thead class="table-light"><tr><th>তারিখ</th><th>মাধ্যম</th><th>রেফারেন্স</th><th class="text-end">টাকা</th></tr></thead>
        <tbody>
        @forelse ($payments as $pay)
            <tr><td>{{ \Carbon\Carbon::parse($pay->payment_date)->format('d-m-Y') }}</td><td>{{ $pay->method }}</td><td>{{ $pay->reference_no ?: '-' }}</td><td class="text-end">{{ number_format($pay->amount, 2) }}</td></tr>
        @empty <tr><td colspan="4" class="text-center text-muted py-3">কোনো পেমেন্ট নেই।</td></tr> @endforelse
        </tbody>
    </table></div></div>
    @if ($purchase->due_amount > 0)
    <div class="col-lg-5"><form method="POST" action="{{ route('purchases.pay', $purchase->purchase_id) }}" class="card">
        @csrf
        <div class="card-header fw-semibold">কোম্পানিকে টাকা দিন</div>
        <div class="card-body">
            <div class="mb-2"><label class="form-label">টাকা (সর্বোচ্চ {{ number_format($purchase->due_amount, 2) }})</label>
                <input type="number" step="0.01" min="0.01" max="{{ $purchase->due_amount }}" name="amount" required class="form-control"></div>
            <div class="mb-2"><label class="form-label">তারিখ</label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="form-control"></div>
            <div class="mb-2"><label class="form-label">মাধ্যম</label>
                <select name="method" class="form-select">
                    @foreach (['cash'=>'নগদ','bkash'=>'বিকাশ','nagad'=>'নগদ (Nagad)','bank'=>'ব্যাংক','cheque'=>'চেক','other'=>'অন্যান্য'] as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                </select></div>
            <div class="mb-2"><label class="form-label">রেফারেন্স / চেক নং</label><input type="text" name="reference_no" maxlength="80" class="form-control"></div>
            <div><label class="form-label">নোট</label><input type="text" name="note" maxlength="255" class="form-control"></div>
        </div>
        <div class="card-footer"><button class="btn btn-success">পেমেন্ট সংরক্ষণ</button></div>
    </form></div>
    @endif
</div>
@endsection
