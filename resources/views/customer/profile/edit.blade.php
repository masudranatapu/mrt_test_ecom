@extends('customer.layouts.account')

@section('title', 'My Profile')

@section('account')
    <h1 class="h4 fw-bold mb-4">My Profile</h1>

    <div class="card mb-4">
        <div class="card-header bg-white fw-semibold">Personal Information</div>
        <div class="card-body">
            <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <div class="d-flex align-items-center gap-3 mb-4">
                    <img src="{{ $customer->avatar_url }}" {!! img_fallback(true) !!} id="avatarPreview" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover" alt="">
                    <div>
                        <label for="avatar" class="form-label mb-1">Profile photo</label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" data-preview="#avatarPreview"
                            class="form-control form-control-sm @error('avatar') is-invalid @enderror">
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label required">Full name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}"
                            class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label required">Mobile number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"
                            class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}"
                            class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="business_name" class="form-label">Business name</label>
                        <input type="text" id="business_name" name="business_name" value="{{ old('business_name', $customer->business_name) }}"
                            class="form-control @error('business_name') is-invalid @enderror">
                        @error('business_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white fw-semibold">Change Password</div>
        <div class="card-body">
            <form method="POST" action="{{ route('customer.profile.password') }}" novalidate>
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="current_password" class="form-label required">Current password</label>
                        <input type="password" id="current_password" name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="password" class="form-label required">New password</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="password_confirmation" class="form-label required">Confirm password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-dark">Update Password</button>
            </form>
        </div>
    </div>
@endsection
