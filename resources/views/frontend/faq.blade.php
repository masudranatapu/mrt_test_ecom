@extends('frontend.layouts.app')

@section('title', 'FAQ')

@section('content')
    <div class="page-banner text-center">
        <div class="container">
            <h1>Frequently Asked Questions</h1>
            <p class="text-muted mb-3">Quick answers about orders, delivery, payment and returns</p>
            <div class="faq-search mx-auto">
                <i class="bi bi-search"></i>
                <input type="search" id="faqSearch" class="form-control form-control-lg" placeholder="Search questions...">
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="shop-sidebar">
                        <h6 class="sidebar-title">Topics</h6>
                        <ul class="list-unstyled category-list mb-0">
                            @foreach ($groups as $key => $group)
                                <li><a href="#faq-{{ $key }}"><i class="bi {{ $group['icon'] }} me-1"></i> {{ $group['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-lg-9">
                    @forelse ($groups as $key => $group)
                        <div class="faq-group mb-4" id="faq-{{ $key }}" data-faq-group>
                            <h2 class="h5 fw-bold mb-3"><i class="bi {{ $group['icon'] }} text-primary me-1"></i> {{ $group['label'] }}</h2>
                            <div class="accordion" id="accordion-{{ $key }}">
                                @foreach ($group['items'] as $faq)
                                    <div class="accordion-item" data-faq-item>
                                        <h3 class="accordion-header">
                                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#faq{{ $faq->id }}">
                                                {{ $faq->question }}
                                            </button>
                                        </h3>
                                        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#accordion-{{ $key }}">
                                            <div class="accordion-body text-muted">{!! nl2br(e($faq->answer)) !!}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No FAQs yet.</div>
                    @endforelse

                    <div class="text-center text-muted py-4 d-none" id="faqNoResult">No questions match your search.</div>

                    <div class="help-cta mt-4">
                        <div>
                            <h5 class="fw-bold mb-1">Still need help?</h5>
                            <p class="mb-0 text-muted">Our support team is happy to answer your questions.</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('contact') }}" class="btn btn-primary"><i class="bi bi-envelope me-1"></i> Contact Us</a>
                            <a href="{{ route('complaint') }}" class="btn btn-outline-danger"><i class="bi bi-exclamation-octagon me-1"></i> File a Complaint</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Client-side search across all questions and answers
        document.getElementById('faqSearch')?.addEventListener('input', (e) => {
            const term = e.target.value.trim().toLowerCase();
            let visible = 0;

            document.querySelectorAll('[data-faq-group]').forEach((group) => {
                let groupVisible = 0;
                group.querySelectorAll('[data-faq-item]').forEach((item) => {
                    const match = !term || item.textContent.toLowerCase().includes(term);
                    item.classList.toggle('d-none', !match);
                    groupVisible += match ? 1 : 0;
                });
                group.classList.toggle('d-none', groupVisible === 0);
                visible += groupVisible;
            });

            document.getElementById('faqNoResult').classList.toggle('d-none', visible > 0);
        });
    </script>
@endpush
