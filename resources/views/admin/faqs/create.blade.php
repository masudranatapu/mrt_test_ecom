@extends('admin.layouts.app')

@section('title', 'Add FAQ')

@section('content')
    <div class="page-header">
        <div>
            <h1>Add FAQ</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.faqs.index') }}">FAQs</a></li>
                    <li class="breadcrumb-item active">Add</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.faqs.store') }}" novalidate>
        @csrf
        @include('admin.faqs._form')
    </form>
@endsection
