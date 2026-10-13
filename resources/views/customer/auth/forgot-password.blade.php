@extends('frontend.layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="auth-box">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold mb-1">Forgot password?</h1>
                    <p class="text-muted mb-0">Enter your email and we will send you a reset link</p>
                </div>

                <form method="POST" action="{{ route('customer.password.email') }}" novalidate>
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">Send Reset Link</button>
                </form>

                <p class="text-center mt-4 mb-0">
                    <a href="{{ route('customer.login') }}"><i class="bi bi-arrow-left"></i> Back to login</a>
                </p>
            </div>
        </div>
    </section>
@endsection
