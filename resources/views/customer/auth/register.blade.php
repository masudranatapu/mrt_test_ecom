@extends('frontend.layouts.app')

@section('title', 'Create Account')

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="auth-box auth-box-wide">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold mb-1">Create your account</h1>
                    <p class="text-muted mb-0">Join us to order faster and track your orders</p>
                </div>

                <form method="POST" action="{{ route('customer.register.store') }}" novalidate>
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label required">Full name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label required">Mobile number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="01XXXXXXXXX"
                                class="form-control @error('phone') is-invalid @enderror" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label required">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="business_name" class="form-label">Business name <span class="text-muted small">(optional)</span></label>
                        <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}"
                            class="form-control @error('business_name') is-invalid @enderror">
                        @error('business_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label required">Password</label>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label required">Confirm password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" value="1" @checked(old('terms'))>
                        <label class="form-check-label" for="terms">I agree to the <a href="#">Terms &amp; Conditions</a></label>
                        @error('terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">Create Account</button>
                </form>

                <p class="text-center text-muted mt-4 mb-0">
                    Already have an account? <a href="{{ route('customer.login') }}" class="fw-semibold">Login</a>
                </p>
            </div>
        </div>
    </section>
@endsection
