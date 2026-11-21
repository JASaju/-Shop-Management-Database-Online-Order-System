@csrf
@php $c = $company ?? null; @endphp
<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">কোম্পানির নাম <span class="text-danger">*</span></label>
        <input type="text" name="company_name" required maxlength="150" value="{{ old('company_name', $c->company_name ?? '') }}" class="form-control"></div>
    <div class="col-md-6 mb-3"><label class="form-label">যোগাযোগকারী ব্যক্তি</label>
        <input type="text" name="contact_person" maxlength="150" value="{{ old('contact_person', $c->contact_person ?? '') }}" class="form-control"></div>
    <div class="col-12 mb-3"><label class="form-label">ঠিকানা</label>
        <input type="text" name="address" maxlength="255" value="{{ old('address', $c->address ?? '') }}" class="form-control"></div>
    <div class="col-md-6 mb-3"><label class="form-label">ফোন ১</label>
        <input type="text" name="phone_1" maxlength="20" value="{{ old('phone_1', $c->phone_1 ?? '') }}" class="form-control"></div>
    <div class="col-md-6 mb-3"><label class="form-label">ফোন ২</label>
        <input type="text" name="phone_2" maxlength="20" value="{{ old('phone_2', $c->phone_2 ?? '') }}" class="form-control"></div>
    <div class="col-md-6 mb-3"><label class="form-label">ইমেইল</label>
        <input type="email" name="email" maxlength="150" value="{{ old('email', $c->email ?? '') }}" class="form-control"></div>
    <div class="col-md-6 mb-3"><label class="form-label">ট্রেড লাইসেন্স নং</label>
        <input type="text" name="trade_license_no" maxlength="60" value="{{ old('trade_license_no', $c->trade_license_no ?? '') }}" class="form-control"></div>
    <div class="col-12 mb-3"><label class="form-label">নোট</label>
        <input type="text" name="note" maxlength="255" value="{{ old('note', $c->note ?? '') }}" class="form-control"></div>
    <div class="col-12 mb-3 form-check ms-2">
        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $c->is_active ?? 1))>
        <label class="form-check-label" for="is_active">সক্রিয়</label></div>
</div>
<div class="d-flex gap-2">
    <button class="btn btn-success">{{ $submit }}</button>
    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">বাতিল</a>
</div>
