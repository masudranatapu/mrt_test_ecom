import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const meta = (name) => document.querySelector(`meta[name="${name}"]`)?.content;

/**
 * Listen on the private "admin.orders" channel and show new orders instantly:
 * bell counter, notification list, sidebar badge, toast and a short sound.
 */
export function listenForNewOrders() {
    const key = meta('pusher-key');
    if (!key) return;

    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key,
        cluster: meta('pusher-cluster'),
        forceTLS: true,
        authEndpoint: meta('broadcast-auth'),
        auth: { headers: { 'X-CSRF-TOKEN': meta('csrf-token') } },
    });

    window.Echo.private('admin.orders').listen('.order.placed', (order) => {
        bumpCounter(document.getElementById('notificationCount'));
        bumpCounter(document.querySelector('[data-pending-orders]'));
        prependNotification(order);
        showToast(order);
        playChime();
    });
}

function bumpCounter(el) {
    if (!el) return;
    el.textContent = (parseInt(el.textContent, 10) || 0) + 1;
    el.classList.remove('d-none');
}

function prependNotification(order) {
    const list = document.getElementById('notificationList');
    if (!list) return;

    list.querySelector('[data-empty]')?.remove();

    const item = document.createElement('a');
    item.href = order.url;
    item.className = 'notification-item unread';
    item.innerHTML = `
        <span class="icon bg-success-subtle text-success"><i class="bi bi-bag-check"></i></span>
        <span class="flex-grow-1">
            <span class="d-block small fw-semibold"></span>
            <span class="d-block small text-muted"></span>
        </span>`;
    const [title, meta] = item.querySelectorAll('.d-block');
    title.textContent = `New order ${order.order_number} from ${order.customer}`;
    meta.textContent = `${order.total} · just now`;

    list.prepend(item);
}

function showToast(order) {
    const stack = document.querySelector('.toast-stack');
    if (!stack) return;

    const toast = document.createElement('div');
    toast.className = 'toast live-order-toast border-0 mb-2';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="toast-header">
            <i class="bi bi-bag-check-fill text-success me-2"></i>
            <strong class="me-auto">New order received</strong>
            <small>just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            <div class="fw-semibold" data-number></div>
            <div class="small text-muted" data-details></div>
            <a class="btn btn-sm btn-primary mt-2">View order</a>
        </div>`;
    toast.querySelector('[data-number]').textContent = order.order_number;
    toast.querySelector('[data-details]').textContent = `${order.customer} · ${order.total} · ${order.payment_method}`;
    toast.querySelector('a').href = order.url;

    stack.prepend(toast);
    const instance = window.bootstrap.Toast.getOrCreateInstance(toast, { delay: 10000 });
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
    instance.show();
}

/** Two-tone chime made with the Web Audio API (no sound file needed). */
function playChime() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        [880, 1320].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.frequency.value = freq;
            osc.connect(gain);
            gain.connect(ctx.destination);
            const start = ctx.currentTime + i * 0.18;
            gain.gain.setValueAtTime(0.0001, start);
            gain.gain.exponentialRampToValueAtTime(0.25, start + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, start + 0.3);
            osc.start(start);
            osc.stop(start + 0.32);
        });
    } catch {
        // Browsers may block audio until the admin interacts with the page
    }
}
