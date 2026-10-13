@extends('admin.layouts.app')

@section('title', 'Add Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>Add Admin</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
                    <li class="breadcrumb-item active">Add</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.admins.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @include('admin.admins._form')
    </form>
@endsection
