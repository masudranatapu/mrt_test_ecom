<div class="row">
    <div class="col-md-4 mb-3">
        <label for="label" class="form-label required">Label</label>
        <input type="text" id="label" name="label" value="{{ old('label', $address->label) }}" list="labelOptions"
            class="form-control @error('label') is-invalid @enderror">
        <datalist id="labelOptions">
            <option value="Home"><option value="Office"><option value="Shop"><option value="Warehouse">
        </datalist>
        @error('label')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="name" class="form-label required">Receiver name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $address->name ?? auth('customer')->user()->name) }}"
            class="form-control @error('name') is-invalid @enderror">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="phone" class="form-label required">Receiver phone</label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $address->phone ?? auth('customer')->user()->phone) }}"
            class="form-control @error('phone') is-invalid @enderror">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="address_line" class="form-label required">Address</label>
    <input type="text" id="address_line" name="address_line" value="{{ old('address_line', $address->address_line) }}"
        placeholder="House, road, block" class="form-control @error('address_line') is-invalid @enderror">
    @error('address_line')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="area" class="form-label">Area / Thana</label>
        <input type="text" id="area" name="area" value="{{ old('area', $address->area) }}"
            class="form-control @error('area') is-invalid @enderror">
        @error('area')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="city" class="form-label required">City / District</label>
        <input type="text" id="city" name="city" value="{{ old('city', $address->city) }}"
            class="form-control @error('city') is-invalid @enderror">
        @error('city')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="postal_code" class="form-label">Postal code</label>
        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}"
            class="form-control @error('postal_code') is-invalid @enderror">
        @error('postal_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<input type="hidden" name="is_default" value="0">
<div class="form-check mb-4">
    <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" @checked(old('is_default', $address->is_default))>
    <label class="form-check-label" for="is_default">Set as default address</label>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $address->exists ? 'Update' : 'Save' }} Address</button>
    <a href="{{ route('customer.addresses.index') }}" class="btn btn-light">Cancel</a>
</div>
