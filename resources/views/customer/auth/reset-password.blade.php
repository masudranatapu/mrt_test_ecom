@extends('frontend.layouts.app')

@section('title', 'Reset Password')

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="auth-box">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold mb-1">Reset password</h1>
                    <p class="text-muted mb-0">Choose a new password for your account</p>
                </div>

                <form method="POST" action="{{ route('customer.password.update') }}" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $email) }}"
                            class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New password</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required autofocus>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm new password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">Reset Password</button>
                </form>
            </div>
        </div>
    </section>
@endsection
