@extends('admin.layouts.app')

@section('title', 'New Flash Sale')

@section('content')
    <div class="page-header">
        <div>
            <h1>New Flash Sale</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.flash-sales.index') }}">Flash Sales</a></li>
                    <li class="breadcrumb-item active">New</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.flash-sales.store') }}" novalidate>
        @csrf
        @include('admin.flash-sales._form')
    </form>
@endsection
