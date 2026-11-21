@csrf

<div class="mb-3">
    <label class="form-label">পণ্যের নাম <span class="text-danger">*</span></label>
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required maxlength="150"
           class="form-control @error('name') is-invalid @enderror">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">ইউনিট <span class="text-danger">*</span></label>
        <select name="unit" required class="form-select @error('unit') is-invalid @enderror">
            @foreach ($units as $u)
                <option value="{{ $u }}" @selected(old('unit', $product->unit ?? 'pcs') === $u)>{{ $u }}</option>
            @endforeach
        </select>
        @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">দাম (৳) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="default_price" required
               value="{{ old('default_price', $product->default_price ?? '0.00') }}"
               class="form-control @error('default_price') is-invalid @enderror">
        @error('default_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="d-flex gap-2">
    <button class="btn btn-success">{{ $submit }}</button>
    <a href="{{ route('home') }}" class="btn btn-outline-secondary">বাতিল</a>
</div>
