@csrf
@php $r = $row ?? null; @endphp
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">দোকান <span class="text-danger">*</span></label>
        <select name="shop_id" required class="form-select"><option value="">-- বাছাই করুন --</option>
        @foreach ($shops as $s)<option value="{{ $s->shop_id }}" @selected(old('shop_id', $r->shop_id ?? '') == $s->shop_id)>{{ trim($s->shop_name) }}</option>@endforeach
        </select></div>
<div class="col-md-6 mb-3"><label class="form-label">নাম *</label>
        <input type="text" name="name" required maxlength="150" value="{{ old('name', $r->name ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">পদবি</label>
        <input type="text" name="designation"  value="{{ old('designation', $r->designation ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">ফোন</label>
        <input type="text" name="phone"  value="{{ old('phone', $r->phone ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">NID নং</label>
        <input type="text" name="nid_no"  value="{{ old('nid_no', $r->nid_no ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">ঠিকানা</label>
        <input type="text" name="address"  value="{{ old('address', $r->address ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">বেতন (৳)</label>
        <input type="number" name="salary" step="0.01" min="0" value="{{ old('salary', $r->salary ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">যোগদানের তারিখ</label>
        <input type="date" name="join_date"  value="{{ old('join_date', $r->join_date ?? '') }}" class="form-control"></div>
<div class="col-12 mb-3 form-check ms-2"><input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $r->is_active ?? 1))><label class="form-check-label" for="is_active">সক্রিয়</label></div>
</div>
<div class="d-flex gap-2"><button class="btn btn-success">{{ $submit }}</button><a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">বাতিল</a></div>
