import './bootstrap';
import { listenForNewOrders } from './admin/live-orders';

document.addEventListener('DOMContentLoaded', () => {
    // Mobile sidebar toggle
    document.querySelectorAll('[data-sidebar-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));
    });

    document.querySelector('.sidebar-backdrop')?.addEventListener('click', () => {
        document.body.classList.remove('sidebar-open');
    });

    // Shared "Adjust Stock" modal
    const modalEl = document.getElementById('stockAdjustModal');
    if (modalEl) {
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        const form = document.getElementById('stockAdjustForm');
        const field = (name) => modalEl.querySelector(`[data-field="${name}"]`);

        document.querySelectorAll('[data-stock-adjust]').forEach((btn) => {
            btn.addEventListener('click', () => {
                form.action = btn.dataset.url;
                form.reset();
                field('name').textContent = btn.dataset.name;
                field('stock').textContent = btn.dataset.stock;
                field('unit').textContent = btn.dataset.unit;
                modal.show();
                setTimeout(() => document.getElementById('adjustQuantity').focus(), 300);
            });
        });
    }

    // Auto-fill slug from name while the slug field is untouched
    const nameInput = document.querySelector('[data-slug-source]');
    const slugInput = document.querySelector('[data-slug-target]');
    if (nameInput && slugInput) {
        let touched = slugInput.value !== '';
        slugInput.addEventListener('input', () => (touched = slugInput.value !== ''));
        nameInput.addEventListener('input', () => {
            if (touched) return;
            slugInput.value = nameInput.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/[\s-]+/g, '-')
                .replace(/^-|-$/g, '');
        });
    }

    listenForNewOrders();
});
