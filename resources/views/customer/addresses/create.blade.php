@extends('customer.layouts.account')

@section('title', 'Add Address')

@section('account')
    <h1 class="h4 fw-bold mb-4">Add Address</h1>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('customer.addresses.store') }}" novalidate>
                @csrf
                @include('customer.addresses._form')
            </form>
        </div>
    </div>
@endsection
