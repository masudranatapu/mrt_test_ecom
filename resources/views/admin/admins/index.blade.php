@extends('admin.layouts.app')

@section('title', 'Admins')

@section('content')
    <div class="page-header">
        <div>
            <h1>Admins</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admins</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Admin
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Search by name, email or phone">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
                    @if (request('search'))
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-link">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Admin</th>
                        <th>Phone</th>
                        <th>Role / Permissions</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr>
                            <td>{{ $admins->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $admin->avatar_url }}" {!! img_fallback(true) !!} class="rounded-circle avatar-sm" alt="">
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $admin->name }}
                                            @if ($admin->is(auth('admin')->user()))
                                                <span class="badge bg-info-subtle text-info ms-1">You</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">{{ $admin->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $admin->phone ?? '—' }}</td>
                            <td>
                                @if ($admin->is_super_admin)
                                    <span class="badge bg-primary"><i class="bi bi-shield-check"></i> Super Admin</span>
                                @else
                                    <span class="badge bg-secondary">Staff</span>
                                    <div class="small text-muted mt-1">
                                        {{ collect($admin->permissions ?? [])->map(fn ($p) => \App\Models\User::PERMISSIONS[$p]['label'] ?? $p)->implode(', ') ?: 'No permissions' }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($admin->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $admin->created_at->format('d M Y') }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @unless ($admin->is(auth('admin')->user()))
                                    <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="d-inline"
                                        data-confirm="Delete this admin? This cannot be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">No admins found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($admins->hasPages())
            <div class="card-footer bg-white">{{ $admins->links() }}</div>
        @endif
    </div>
@endsection
