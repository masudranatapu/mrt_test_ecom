import * as bootstrap from 'bootstrap';
import axios from 'axios';
import 'bootstrap-icons/font/bootstrap-icons.css';

window.bootstrap = bootstrap;

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const csrf = document.querySelector('meta[name="csrf-token"]');
if (csrf) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf.content;
}

/**
 * Shared UI helpers used on both admin and frontend.
 */
document.addEventListener('DOMContentLoaded', () => {
    // Auto-show flash toasts
    document.querySelectorAll('.toast[data-autoshow]').forEach((el) => {
        bootstrap.Toast.getOrCreateInstance(el, { delay: 4000 }).show();
    });

    // Confirm before submitting dangerous forms: <form data-confirm="Are you sure?">
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (!window.confirm(form.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // Live image preview: <input type="file" data-preview="#imgId">
    document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
        input.addEventListener('change', () => {
            const target = document.querySelector(input.dataset.preview);
            const file = input.files?.[0];
            if (target && file) {
                target.src = URL.createObjectURL(file);
            }
        });
    });

    // Show / hide password: <button data-toggle-password="#inputId">
    document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.querySelector(btn.dataset.togglePassword);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('i')?.classList.toggle('bi-eye', !show);
            btn.querySelector('i')?.classList.toggle('bi-eye-slash', show);
        });
    });
});
