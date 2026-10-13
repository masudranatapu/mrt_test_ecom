@extends('frontend.layouts.app')

@section('title', 'Flash Sale')

@section('content')
    <div class="flash-hero">
        <div class="container text-center text-white">
            <h1 class="fw-bold mb-1"><i class="bi bi-lightning-charge-fill text-warning"></i> {{ $flashSale?->title ?? 'Flash Sale' }}</h1>
            @if ($flashSale)
                @php($upcoming = $flashSale->starts_at->isFuture())
                <p class="mb-3 opacity-75">{{ $upcoming ? 'Get ready! The flash sale starts in' : 'Hurry! Limited time deals end in' }}</p>
                <div class="d-flex justify-content-center">
                    @include('frontend.partials.countdown', ['target' => $upcoming ? $flashSale->starts_at : $flashSale->ends_at, 'class' => 'countdown-light'])
                </div>
            @else
                <p class="mb-0 opacity-75">No flash sale is running right now. Check back soon!</p>
            @endif
        </div>
    </div>

    <section class="section">
        <div class="container">
            @if ($items->isNotEmpty())
                @if ($flashSale->starts_at->isFuture())
                    <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i> These prices go live on {{ $flashSale->starts_at->format('d M Y, h:i A') }}.</div>
                @endif
                <div class="row g-3 g-lg-4">
                    @foreach ($items as $item)
                        <div class="col-6 col-md-4 col-lg-3">
                            @include('frontend.flash-sale._item', ['item' => $item])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-lightning display-4 d-block mb-2"></i>
                    <p>No flash deals at the moment.</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary">Browse all products</a>
                </div>
            @endif
        </div>
    </section>
@endsection
