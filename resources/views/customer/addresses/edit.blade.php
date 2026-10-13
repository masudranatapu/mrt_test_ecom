@extends('customer.layouts.account')

@section('title', 'Edit Address')

@section('account')
    <h1 class="h4 fw-bold mb-4">Edit Address</h1>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('customer.addresses.update', $address) }}" novalidate>
                @csrf
                @method('PUT')
                @include('customer.addresses._form')
            </form>
        </div>
    </div>
@endsection
