@extends('admin.layouts.app')

@section('title', 'Edit Campaign')

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Campaign</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.campaigns.index') }}">Campaigns</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.campaigns.update', $campaign) }}" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        @include('admin.campaigns._form')
    </form>
@endsection
