@extends('admin.layouts.app')

@section('title', 'Campaigns')

@section('content')
    <div class="page-header">
        <div>
            <h1>Campaigns</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Campaigns</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Campaign</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th class="text-center">Discount</th>
                        <th>Period</th>
                        <th class="text-center">Products</th>
                        <th>State</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($campaigns as $campaign)
                        @php($state = $campaign->state())
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $campaign->title }}
                                    @if ($campaign->is_featured)<i class="bi bi-house-heart text-primary small" title="Shown on home page"></i>@endif
                                </div>
                                <div class="small text-muted">{{ $campaign->subtitle }}</div>
                            </td>
                            <td class="text-center"><span class="badge bg-danger">{{ $campaign->discount_percent }}% OFF</span></td>
                            <td class="small">{{ $campaign->starts_at->format('d M, h:i A') }} → {{ $campaign->ends_at->format('d M Y, h:i A') }}</td>
                            <td class="text-center">{{ $campaign->products_count }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'bg-success' => $state === 'running',
                                    'bg-info' => $state === 'upcoming',
                                    'bg-secondary' => in_array($state, ['ended', 'disabled']),
                                ])>{{ ucfirst($state) }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('campaign.show', $campaign->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-box-arrow-up-right"></i></a>
                                <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" class="d-inline" data-confirm="Delete this campaign?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No campaigns yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($campaigns->hasPages())
            <div class="card-footer bg-white">{{ $campaigns->links() }}</div>
        @endif
    </div>
@endsection
