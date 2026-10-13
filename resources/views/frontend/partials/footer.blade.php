<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5>{{ setting('store_name', config('app.name')) }}</h5>
                <p>Quality products delivered to your door. Shop with confidence with secure payment, cash on delivery and easy returns.</p>
                <div class="social mt-3">
                    @foreach (['facebook' => 'bi-facebook', 'instagram' => 'bi-instagram', 'youtube' => 'bi-youtube', 'linkedin' => 'bi-linkedin'] as $network => $icon)
                        @if ($url = setting('social_'.$network))
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"><i class="bi {{ $icon }}"></i></a>
                        @endif
                    @endforeach
                    @if ($whatsapp = setting('contact_whatsapp'))
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    @endif
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h5>Shop</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('shop') }}">All Products</a></li>
                    <li><a href="{{ route('shop', ['sort' => 'latest']) }}">New Arrivals</a></li>
                    <li><a href="{{ route('flash-sale') }}">Flash Sale</a></li>
                    <li><a href="{{ route('about') }}">Vision &amp; Mission</a></li>
                    <li><a href="{{ route('cart.index') }}">My Cart</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h5>Customer Service</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                    <li><a href="{{ route('faq') }}#faq-shipping">Shipping &amp; Returns</a></li>
                    <li><a href="{{ route('customer.orders.index') }}">Track Your Order</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="{{ route('complaint') }}">File a Complaint</a></li>
                </ul>
            </div>

            <div class="col-lg-3">
                <h5>Contact</h5>
                <ul class="list-unstyled footer-links">
                    @if (setting('contact_address'))
                        <li class="d-flex"><i class="bi bi-geo-alt me-2"></i> <span>{{ setting('contact_address') }}</span></li>
                    @endif
                    @if (setting('contact_phone'))
                        <li><i class="bi bi-telephone me-2"></i> {{ setting('contact_phone') }}</li>
                    @endif
                    @if (setting('contact_email'))
                        <li><i class="bi bi-envelope me-2"></i> {{ setting('contact_email') }}</li>
                    @endif
                    @if (setting('business_hours'))
                        <li><i class="bi bi-clock me-2"></i> {{ setting('business_hours') }}</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span>&copy; {{ date('Y') }} {{ setting('store_name', config('app.name')) }}. All rights reserved.</span>
            <span>
                We accept:
                <span class="pay-badge">bKash</span>
                <span class="pay-badge">Visa / Master</span>
                <span class="pay-badge">Nagad</span>
                <span class="pay-badge">Cash on Delivery</span>
            </span>
        </div>
    </div>
</footer>
