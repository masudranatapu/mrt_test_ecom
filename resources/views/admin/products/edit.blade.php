@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Product</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        @include('admin.products._form')
    </form>

    {{-- Gallery delete forms live outside the main form (forms cannot be nested) --}}
    @foreach ($product->images as $image)
        <form id="deleteImage{{ $image->id }}" action="{{ route('admin.products.images.destroy', $image) }}" method="POST"
            data-confirm="Remove this image?" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
