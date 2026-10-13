@extends('admin.layouts.app')

@section('title', 'Add Category')

@section('content')
    <div class="page-header">
        <div>
            <h1>Add Category</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active">Add</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @include('admin.categories._form')
    </form>
@endsection
