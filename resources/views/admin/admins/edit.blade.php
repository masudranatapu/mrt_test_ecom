@extends('admin.layouts.app')

@section('title', 'Edit Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Admin</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.admins.update', $admin) }}" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        @include('admin.admins._form')
    </form>
@endsection
