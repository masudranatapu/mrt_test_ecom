@extends('frontend.layouts.app')

@section('title', $campaign->title)
@section('meta_description', $campaign->subtitle)

@section('content')
    @php($state = $campaign->state())

    <section class="py-4">
        <div class="container">
            <div class="campaign-banner tone-0 campaign-banner-lg"
                @if ($campaign->banner_url) style="background-image: linear-gradient(90deg, rgba(15,23,42,.85) 0%, rgba(15,23,42,.35) 100%), url('{{ $campaign->banner_url }}')" @endif>
                <div class="campaign-content">
                    <span class="campaign-tag">
                        @if ($state === 'running') <i class="bi bi-broadcast me-1"></i> Live now
                        @elseif ($state === 'upcoming') <i class="bi bi-hourglass-split me-1"></i> Coming soon
                        @else <i class="bi bi-flag me-1"></i> Campaign ended @endif
                    </span>
                    <h1 class="h2 fw-bold">{{ $campaign->title }}</h1>
                    @if ($campaign->subtitle)<p class="mb-2">{{ $campaign->subtitle }}</p>@endif
                    <div class="campaign-off">Get <strong>{{ $campaign->discount_percent }}% OFF</strong> on every product below</div>

                    @if (in_array($state, ['running', 'upcoming']))
                        <div class="small text-uppercase fw-semibold opacity-75 mt-3 mb-1">{{ $state === 'upcoming' ? 'Starts in' : 'Ends in' }}</div>
                        @include('frontend.partials.countdown', ['target' => $campaign->countdownTarget(), 'class' => 'countdown-light'])
                    @endif
                </div>
                <span class="campaign-percent">{{ $campaign->discount_percent }}%</span>
            </div>

            @if ($campaign->description)
                <p class="text-muted mt-3 mb-0">{!! nl2br(e($campaign->description)) !!}</p>
            @endif
        </div>
    </section>

    <section class="section pt-2">
        <div class="container">
            @if ($state === 'upcoming')
                <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i> Campaign prices apply from {{ $campaign->starts_at->format('d M Y, h:i A') }}.</div>
            @elseif ($state === 'ended')
                <div class="alert alert-secondary">This campaign has ended. Products are shown at their current price.</div>
            @endif

            <div class="row g-3 g-lg-4">
                @forelse ($products as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('frontend.products._card')
                    </div>
                @empty
                    <div class="col-12"><div class="empty-state">No products in this campaign.</div></div>
                @endforelse
            </div>

            @if ($products->hasPages())
                <div class="mt-4 d-flex justify-content-center">{{ $products->links() }}</div>
            @endif
        </div>
    </section>
@endsection
