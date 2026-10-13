@extends('frontend.layouts.app')

@section('title', 'File a Complaint')

@section('content')
    <div class="page-banner text-center">
        <div class="container">
            <h1>File a Complaint</h1>
            <p class="text-muted mb-0">Tell us what went wrong. You will get a reference number and our team will contact you.</p>
        </div>
    </div>

    <section class="section pt-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="checkout-step">
                        <form method="POST" action="{{ route('complaint.store') }}" novalidate>
                            @csrf
                            <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required" for="name">Your name</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $customer?->name) }}"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required" for="phone">Mobile number</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $customer?->phone) }}"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required" for="email">Email</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $customer?->email) }}"
                                        class="form-control @error('email') is-invalid @enderror">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required" for="category">Complaint type</label>
                                    <select id="category" name="category" class="form-select @error('category') is-invalid @enderror">
                                        <option value="">— Select —</option>
                                        @foreach (\App\Models\Complaint::CATEGORIES as $key => $label)
                                            <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="order_id">Related order</label>
                                    @if ($customer)
                                        <select id="order_id" name="order_id" class="form-select @error('order_id') is-invalid @enderror">
                                            <option value="">— Not about a specific order —</option>
                                            @foreach ($orders as $order)
                                                <option value="{{ $order->id }}" @selected((int) old('order_id', $selectedOrder) === $order->id)>
                                                    {{ $order->order_number }} — {{ $order->created_at->format('d M Y') }} — {{ money($order->total, 0) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @else
                                        <div class="form-control bg-light small text-muted">
                                            <a href="{{ route('customer.login') }}">Log in</a> to link one of your orders.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required" for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="e.g. Received a damaged item"
                                    class="form-control @error('subject') is-invalid @enderror">
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label required" for="message">Details</label>
                                <textarea id="message" name="message" rows="6" placeholder="Please describe the problem in detail (at least 20 characters)."
                                    class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <button type="submit" class="btn btn-danger btn-lg"><i class="bi bi-send me-1"></i> Submit Complaint</button>
                        </form>
                    </div>

                    <p class="text-center small text-muted">
                        For general questions please use the <a href="{{ route('contact') }}">contact form</a> or call {{ setting('contact_phone') }}.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
