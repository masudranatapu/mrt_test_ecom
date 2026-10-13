@extends('admin.layouts.app')

@section('title', 'Contact & Store Info')

@section('content')
    <div class="page-header">
        <div>
            <h1>Contact &amp; Store Info</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Contact &amp; Store Info</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('contact') }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i> View contact page</a>
    </div>

    @php($v = fn ($key) => old($key, $settings[$key] ?? ''))

    <form method="POST" action="{{ route('admin.settings.contact.update') }}" novalidate>
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header">Contact Details</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required" for="store_name">Store name</label>
                            <input type="text" id="store_name" name="store_name" value="{{ $v('store_name') }}" class="form-control @error('store_name') is-invalid @enderror">
                            @error('store_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required" for="contact_phone">Phone</label>
                                <input type="text" id="contact_phone" name="contact_phone" value="{{ $v('contact_phone') }}" class="form-control @error('contact_phone') is-invalid @enderror">
                                @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="contact_whatsapp">WhatsApp</label>
                                <input type="text" id="contact_whatsapp" name="contact_whatsapp" value="{{ $v('contact_whatsapp') }}" class="form-control @error('contact_whatsapp') is-invalid @enderror">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required" for="contact_email">Email</label>
                            <input type="email" id="contact_email" name="contact_email" value="{{ $v('contact_email') }}" class="form-control @error('contact_email') is-invalid @enderror">
                            @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label required" for="contact_address">Address</label>
                            <textarea id="contact_address" name="contact_address" rows="2" class="form-control @error('contact_address') is-invalid @enderror">{{ $v('contact_address') }}</textarea>
                            @error('contact_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="business_hours">Business hours</label>
                            <input type="text" id="business_hours" name="business_hours" value="{{ $v('business_hours') }}" placeholder="Sat - Thu, 9:00 AM - 8:00 PM" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Social Links</div>
                    <div class="card-body">
                        @foreach (['facebook' => 'bi-facebook', 'instagram' => 'bi-instagram', 'youtube' => 'bi-youtube', 'linkedin' => 'bi-linkedin'] as $network => $icon)
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="bi {{ $icon }}"></i></span>
                                <input type="url" name="social_{{ $network }}" value="{{ $v('social_'.$network) }}" placeholder="https://..."
                                    class="form-control @error('social_'.$network) is-invalid @enderror" aria-label="{{ ucfirst($network) }} URL">
                                @error('social_'.$network)<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">Google Map</div>
                    <div class="card-body">
                        <label class="form-label" for="map_location">Map location</label>
                        <input type="text" id="map_location" name="map_location" value="{{ $v('map_location') }}" placeholder="e.g. Mirpur 10, Dhaka"
                            class="form-control @error('map_location') is-invalid @enderror">
                        <div class="form-text mb-3">An address, place name or "latitude,longitude". Shown on the contact page.</div>
                        @if ($location = $settings['map_location'] ?? null)
                            <div class="ratio ratio-4x3 rounded overflow-hidden border">
                                <iframe src="https://maps.google.com/maps?q={{ urlencode($location) }}&z=15&output=embed" title="Map preview" loading="lazy"></iframe>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save Settings</button>
        </div>
    </form>
@endsection
