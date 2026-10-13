@extends('admin.layouts.auth')

@section('title', 'Forgot Password')
@section('heading', 'Forgot Password')
@section('subheading', 'Enter your email and we will send you a reset link')

@section('content')
    <form method="POST" action="{{ route('admin.password.email') }}" novalidate>
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">
            <i class="bi bi-send me-1"></i> Send Reset Link
        </button>

        <div class="text-center">
            <a href="{{ route('admin.login') }}" class="small"><i class="bi bi-arrow-left"></i> Back to login</a>
        </div>
    </form>
@endsection
