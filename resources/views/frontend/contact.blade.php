@extends('frontend.layouts.app')

@section('title', 'Contact Us')

@section('content')
    <div class="page-banner text-center">
        <div class="container">
            <h1>Contact Us</h1>
            <p class="text-muted mb-0">We would love to hear from you. Send us a message and we will reply soon.</p>
        </div>
    </div>

    <section class="section pt-4">
        <div class="container">
            {{-- Contact details --}}
            <div class="row g-3 mb-4">
                @foreach ([
                    ['icon' => 'bi-geo-alt', 'title' => 'Address', 'value' => setting('contact_address'), 'link' => null],
                    ['icon' => 'bi-telephone', 'title' => 'Phone', 'value' => setting('contact_phone'), 'link' => 'tel:'.preg_replace('/[^0-9+]/', '', (string) setting('contact_phone'))],
                    ['icon' => 'bi-envelope', 'title' => 'Email', 'value' => setting('contact_email'), 'link' => 'mailto:'.setting('contact_email')],
                    ['icon' => 'bi-clock', 'title' => 'Business Hours', 'value' => setting('business_hours'), 'link' => null],
                ] as $info)
                    @continue(! $info['value'])
                    <div class="col-sm-6 col-lg-3">
                        <div class="contact-info-card">
                            <span class="icon"><i class="bi {{ $info['icon'] }}"></i></span>
                            <div>
                                <div class="small text-muted">{{ $info['title'] }}</div>
                                @if ($info['link'])
                                    <a href="{{ $info['link'] }}" class="fw-semibold text-body">{{ $info['value'] }}</a>
                                @else
                                    <div class="fw-semibold">{{ $info['value'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row g-4">
                {{-- Form --}}
                <div class="col-lg-6">
                    <div class="checkout-step h-100 mb-0">
                        <h5 class="fw-bold mb-3">Send us a message</h5>
                        <form method="POST" action="{{ route('contact.store') }}" novalidate>
                            @csrf
                            {{-- Honeypot field for bots --}}
                            <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required" for="name">Your name</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $customer?->name) }}"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="phone">Phone</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $customer?->phone) }}"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required" for="email">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $customer?->email) }}"
                                    class="form-control @error('email') is-invalid @enderror">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label required" for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                                    class="form-control @error('subject') is-invalid @enderror">
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label required" for="message">Message</label>
                                <textarea id="message" name="message" rows="5" class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-send me-1"></i> Send Message</button>
                        </form>

                        <hr class="my-4">
                        <p class="small text-muted mb-0">
                            Have a problem with an order? <a href="{{ route('complaint') }}">File a complaint</a> and get a reference number.
                            You may also find a quick answer in our <a href="{{ route('faq') }}">FAQ</a>.
                        </p>
                    </div>
                </div>

                {{-- Map --}}
                <div class="col-lg-6">
                    <div class="map-box h-100">
                        @if ($location = setting('map_location'))
                            <iframe src="https://maps.google.com/maps?q={{ urlencode($location) }}&z=15&output=embed"
                                title="Our location on Google Maps" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                        @else
                            <div class="empty-state h-100 d-grid align-content-center">Map not configured.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
