import './bootstrap';

/**
 * Show a Bootstrap toast in the top-right corner.
 */
function toast(message, type = 'success') {
    const stack = document.querySelector('.toast-stack');
    if (!stack) return;

    const colors = { success: 'bg-success', error: 'bg-danger', warning: 'bg-warning text-dark' };
    const el = document.createElement('div');
    el.className = `toast align-items-center text-white border-0 mb-2 ${colors[type] ?? colors.success}`;
    el.setAttribute('role', 'alert');
    el.innerHTML = `<div class="d-flex"><div class="toast-body"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    el.querySelector('.toast-body').textContent = message;

    stack.appendChild(el);
    el.addEventListener('hidden.bs.toast', () => el.remove());
    window.bootstrap.Toast.getOrCreateInstance(el, { delay: 3500 }).show();
}

function setCounter(selector, value) {
    document.querySelectorAll(selector).forEach((el) => {
        el.textContent = value;
        el.classList.toggle('d-none', !value);
    });
}

/**
 * Submit a form with axios and return the JSON response.
 * Guests hitting a login-only action are sent to the login page.
 */
async function submit(form) {
    const button = form.querySelector('[type="submit"]');
    button?.setAttribute('disabled', 'disabled');

    try {
        const { data } = await window.axios.post(form.action, new FormData(form), {
            headers: { Accept: 'application/json' },
        });
        return data;
    } catch (error) {
        const response = error.response;

        if (response?.status === 401 || response?.data?.redirect) {
            window.location.href = response?.data?.redirect || document.body.dataset.loginUrl;
            return null;
        }

        const message =
            response?.data?.message ||
            Object.values(response?.data?.errors ?? {})[0]?.[0] ||
            'Something went wrong. Please try again.';
        toast(message, 'error');
        return null;
    } finally {
        button?.removeAttribute('disabled');
    }
}

document.addEventListener('submit', async (e) => {
    const form = e.target;

    // Add to cart
    if (form.matches('[data-ajax-cart]')) {
        e.preventDefault();
        const data = await submit(form);
        if (data) {
            setCounter('[data-cart-count]', data.cart_count);
            toast(data.message);
        }
    }

    // Wishlist toggle
    if (form.matches('[data-ajax-wishlist]')) {
        e.preventDefault();
        const data = await submit(form);
        if (data) {
            setCounter('[data-wishlist-count]', data.wishlist_count);
            const button = form.querySelector('[type="submit"]');
            const icon = button?.querySelector('i');
            button?.classList.toggle('active', data.added);
            icon?.classList.toggle('bi-heart-fill', data.added);
            icon?.classList.toggle('bi-heart', !data.added);
            toast(data.message);
        }
    }
});

/**
 * Countdowns: <div data-countdown="2026-10-20T18:00:00+06:00"> with
 * [data-days] [data-hours] [data-minutes] [data-seconds] inside.
 * The page reloads once a countdown reaches zero so prices switch.
 */
function startCountdowns() {
    const timers = [...document.querySelectorAll('[data-countdown]')];
    if (!timers.length) return;

    const pad = (n) => String(n).padStart(2, '0');

    const tick = () => {
        const now = Date.now();
        timers.forEach((el) => {
            let diff = Math.max(0, Math.floor((new Date(el.dataset.countdown).getTime() - now) / 1000));

            if (diff === 0 && !el.dataset.done) {
                el.dataset.done = '1';
                setTimeout(() => window.location.reload(), 1500);
            }

            const days = Math.floor(diff / 86400);
            diff %= 86400;
            el.querySelector('[data-days]').textContent = pad(days);
            el.querySelector('[data-hours]').textContent = pad(Math.floor(diff / 3600));
            el.querySelector('[data-minutes]').textContent = pad(Math.floor((diff % 3600) / 60));
            el.querySelector('[data-seconds]').textContent = pad(diff % 60);
        });
    };

    tick();
    setInterval(tick, 1000);
}

document.addEventListener('DOMContentLoaded', startCountdowns);

// Cart page: submit the quantity form as soon as the number changes
document.addEventListener('change', (e) => {
    if (e.target.matches('[data-auto-submit]')) {
        e.target.form.submit();
    }
});

// Checkout: show the new-address fields only when "new address" is chosen,
// and keep the delivery charge / total in sync with the selected zone.
document.addEventListener('DOMContentLoaded', () => {
    const newAddress = document.getElementById('newAddressFields');
    if (newAddress) {
        const sync = () => {
            const checked = document.querySelector('input[name="address_id"]:checked');
            newAddress.classList.toggle('d-none', Boolean(checked && checked.value !== ''));
        };
        document.querySelectorAll('input[name="address_id"]').forEach((r) => r.addEventListener('change', sync));
        sync();
    }

    const summary = document.getElementById('orderSummary');
    if (summary) {
        const subtotal = parseFloat(summary.dataset.subtotal);
        const format = (n) => '৳' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const update = () => {
            const zone = document.querySelector('input[name="shipping_zone"]:checked');
            const charge = parseFloat(zone?.dataset.charge ?? 0);
            summary.querySelector('[data-shipping]').textContent = format(charge);
            summary.querySelector('[data-total]').textContent = format(subtotal + charge);
        };
        document.querySelectorAll('input[name="shipping_zone"]').forEach((r) => r.addEventListener('change', update));
        update();
    }
});
