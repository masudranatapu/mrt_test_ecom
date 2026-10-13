@extends('frontend.layouts.app')

@section('title', 'Login')

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="auth-box">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold mb-1">Welcome back</h1>
                    <p class="text-muted mb-0">Login to your account</p>
                </div>

                <form method="POST" action="{{ route('customer.login.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="password" class="form-label">Password</label>
                            <a href="{{ route('customer.password.request') }}" class="small">Forgot password?</a>
                        </div>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                class="form-control form-control-lg @error('password') is-invalid @enderror" required>
                            <button type="button" class="btn btn-outline-secondary" data-toggle-password="#password"><i class="bi bi-eye"></i></button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" @checked(old('remember'))>
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">Login</button>
                </form>

                <p class="text-center text-muted mt-4 mb-0">
                    New here? <a href="{{ route('customer.register') }}" class="fw-semibold">Create an account</a>
                </p>
            </div>
        </div>
    </section>
@endsection
