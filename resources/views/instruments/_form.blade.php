@csrf
@php $r = $row ?? null; @endphp
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">দোকান <span class="text-danger">*</span></label>
        <select name="shop_id" required class="form-select"><option value="">-- বাছাই করুন --</option>
        @foreach ($shops as $s)<option value="{{ $s->shop_id }}" @selected(old('shop_id', $r->shop_id ?? '') == $s->shop_id)>{{ trim($s->shop_name) }}</option>@endforeach
        </select></div>
<div class="col-md-6 mb-3"><label class="form-label">যন্ত্রের নাম *</label>
        <input type="text" name="instrument_name" required maxlength="150" value="{{ old('instrument_name', $r->instrument_name ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">ব্র্যান্ড / মডেল</label>
        <input type="text" name="brand_model"  value="{{ old('brand_model', $r->brand_model ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">সিরিয়াল নং</label>
        <input type="text" name="serial_no"  value="{{ old('serial_no', $r->serial_no ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">পরিমাণ *</label>
        <input type="number" name="quantity" min="1" required value="{{ old('quantity', $r->quantity ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">কেনার তারিখ</label>
        <input type="date" name="purchase_date"  value="{{ old('purchase_date', $r->purchase_date ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">কেনার দাম (৳)</label>
        <input type="number" name="purchase_price" step="0.01" min="0" value="{{ old('purchase_price', $r->purchase_price ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">ওয়ারেন্টি শেষ</label>
        <input type="date" name="warranty_until"  value="{{ old('warranty_until', $r->warranty_until ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">কোম্পানি</label>
    <select name="company_id" class="form-select"><option value="">-- নেই --</option>
    @foreach ($companies as $c)<option value="{{ $c->company_id }}" @selected(old('company_id', $r->company_id ?? '') == $c->company_id)>{{ $c->company_name }}</option>@endforeach</select></div>
<div class="col-md-6 mb-3"><label class="form-label">অবস্থা</label>
    <select name="condition_status" class="form-select">
    @foreach (['good'=>'ভালো','needs_repair'=>'মেরামত দরকার','out_of_order'=>'নষ্ট'] as $k=>$v)<option value="{{ $k }}" @selected(old('condition_status', $r->condition_status ?? 'good') === $k)>{{ $v }}</option>@endforeach</select></div>
<div class="col-12 mb-3"><label class="form-label">নোট</label><input type="text" name="note" maxlength="255" value="{{ old('note', $r->note ?? '') }}" class="form-control"></div>
</div>
<div class="d-flex gap-2"><button class="btn btn-success">{{ $submit }}</button><a href="{{ route('instruments.index') }}" class="btn btn-outline-secondary">বাতিল</a></div>
