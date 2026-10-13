@extends('admin.layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Category</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        @include('admin.categories._form')
    </form>
@endsection
