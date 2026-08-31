import './bootstrap';

import Alpine from 'alpinejs';
import Lenis from 'lenis';

window.Alpine = Alpine;
Alpine.start();

const createToast = ({ title, description, variant = 'default', duration = 3200 }) => {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    const tone = {
        default: 'toast-tone-default',
        success: 'toast-tone-success',
        error: 'toast-tone-error',
        warning: 'toast-tone-warning',
    }[variant] || 'toast-tone-default';

    toast.className = `toast-item toast-glass pointer-events-auto w-[min(360px,calc(100vw-2.5rem))] rounded-2xl border ${tone} p-4`;
    toast.setAttribute('role', 'status');
    toast.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-white/10 text-xs font-bold">
                ${variant === 'error' ? '!' : variant === 'success' ? '✓' : variant === 'warning' ? '!' : 'i'}
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-semibold leading-5">${title}</div>
                ${description ? `<div class="mt-1 text-xs opacity-80 leading-5">${description}</div>` : ''}
            </div>
            <button type="button" class="toast-close ml-2 rounded-md p-1 text-slate-300 transition hover:bg-white/5 hover:text-white" aria-label="Tutup notifikasi">✕</button>
        </div>
    `;

    const close = () => {
        toast.classList.add('opacity-0', 'translate-x-2');
        setTimeout(() => toast.remove(), 180);
    };

    toast.querySelector('.toast-close').addEventListener('click', close);
    container.appendChild(toast);
    setTimeout(close, duration);
};

window.showToast = createToast;
window.BukuTamuToast = { show: createToast };

const openAlertDialog = ({ title = 'Konfirmasi', description = '', confirmText = 'Ya, lanjutkan', cancelText = 'Batal', variant = 'default', onConfirm = null, onCancel = null }) => {
    const existing = document.getElementById('shadcn-dialog');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.id = 'shadcn-dialog';
    overlay.className = 'dialog-backdrop show';

    const iconBg = {
        default: 'bg-slate-200/10 text-slate-100',
        danger: 'bg-red-500/15 text-red-300',
        success: 'bg-emerald-500/15 text-emerald-300',
        warning: 'bg-amber-500/15 text-amber-300',
    }[variant] || 'bg-slate-200/10 text-slate-100';

    const confirmTone = {
        default: 'bg-emerald-400 text-slate-950 hover:bg-emerald-300',
        danger: 'bg-red-600 text-white shadow-lg shadow-red-600/25 hover:bg-red-500',
        success: 'bg-emerald-400 text-slate-950 hover:bg-emerald-300',
        warning: 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-500/20 hover:bg-amber-300',
    }[variant] || 'bg-emerald-400 text-slate-950 hover:bg-emerald-300';

    overlay.innerHTML = `
        <div class="dialog-panel text-center text-slate-800">
            <div class="flex flex-col items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full ${iconBg} text-lg font-bold">
                    ${variant === 'danger' ? '!' : variant === 'success' ? '✓' : variant === 'warning' ? '!' : 'i'}
                </div>
                <div class="min-w-0">
                    <h3 class="text-xl font-semibold text-slate-900">${title}</h3>
                    ${description ? `<p class="mt-2 text-sm leading-6 text-slate-600">${description}</p>` : ''}
                </div>
            </div>
            <div class="mt-6 flex justify-center gap-3">
                <button type="button" class="dialog-confirm min-w-[128px] rounded-full px-4 py-2.5 text-base font-semibold shadow-lg ${confirmTone}">${confirmText}</button>
                <button type="button" class="dialog-cancel min-w-[128px] rounded-full border-0 bg-slate-200 px-4 py-2.5 text-base font-semibold text-slate-700 shadow-sm hover:bg-slate-300">${cancelText}</button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    return new Promise((resolve) => {
        const close = (result) => {
            overlay.remove();
            resolve(result);
        };

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) close(false);
        });

        overlay.querySelector('.dialog-cancel').addEventListener('click', () => {
            if (typeof onCancel === 'function') onCancel();
            close(false);
        });

        overlay.querySelector('.dialog-confirm').addEventListener('click', () => {
            if (typeof onConfirm === 'function') onConfirm();
            close(true);
        });
    });
};

window.BukuTamuDialog = { confirm: openAlertDialog, alert: ({ title, description, variant = 'default' }) => openAlertDialog({ title, description, confirmText: 'OK', cancelText: 'Tutup', variant, onConfirm: () => {} }) };

window.addEventListener('toast:show', (event) => {
    const { title, description, variant = 'default', duration = 3200 } = event.detail || {};
    if (title) {
        createToast({ title, description, variant, duration });
    }
});

/* Lenis — smooth scrolling (hanya untuk halaman yang mengaktifkan, mis. kiosk). */
if (document.body.hasAttribute('data-lenis')) {
    const lenis = new Lenis({
        autoRaf: true,
        duration: 0.9,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    });
}

const initSoftControls = () => {
    document.querySelectorAll('.soft-search, .soft-select, .soft-date').forEach((input) => {
        const base = input.classList.contains('soft-search') ? 'soft-search ' : '';
        input.classList.add('transition', 'duration-200', 'ease-out');
        input.addEventListener('focus', () => {
            input.classList.add('ring-2', 'ring-bank-blue/15', 'border-bank-blue');
        });
        input.addEventListener('blur', () => {
            input.classList.remove('ring-2', 'ring-bank-blue/15', 'border-bank-blue');
        });
    });
};

document.addEventListener('DOMContentLoaded', initSoftControls);

const confirmSubmit = async (form) => {
    const hasConfirm = form.dataset.confirm;
    if (!hasConfirm) return true;

    const result = await openAlertDialog({
        title: form.dataset.confirmTitle || 'Konfirmasi',
        description: form.dataset.confirm,
        confirmText: form.dataset.confirmAction || 'Ya, lanjutkan',
        cancelText: 'Batal',
        variant: form.dataset.confirmVariant || 'default',
    });

    return result === true;
};

const confirmLink = async (trigger) => {
    const href = trigger.getAttribute('href');
    if (!href) return false;

    const result = await openAlertDialog({
        title: trigger.dataset.confirmTitle || 'Konfirmasi',
        description: trigger.dataset.confirm || 'Apakah Anda yakin ingin melanjutkan?',
        confirmText: trigger.dataset.confirmAction || 'Ya, lanjutkan',
        cancelText: 'Batal',
        variant: trigger.dataset.confirmVariant || 'default',
    });

    if (result) {
        window.location.href = href;
    }

    return result === true;
};

const submitExportForm = (form) => {
    const button = form.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-ring mr-2"></span>Memproses...';
    }
};

const ensureSubmitLoader = () => {
    let loader = document.getElementById('page-submit-loader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'page-submit-loader';
        loader.className = 'page-submit-loader';
        loader.innerHTML = '<div class="page-submit-loader-inner"></div>';
        document.body.appendChild(loader);
    }

    return loader;
};

const showSubmitLoader = (label = 'Memproses...') => {
    const loader = ensureSubmitLoader();
    loader.setAttribute('aria-live', 'polite');
    loader.setAttribute('aria-label', label);
    loader.classList.add('show');
};

const submitWithConfirmation = async (event) => {
    if (event.target instanceof HTMLFormElement) {
        const form = event.target;

        if (form.dataset.exportForm === 'true') {
            submitExportForm(form);
            return;
        }

        if (form.dataset.confirm) {
            event.preventDefault();
            const confirmed = await confirmSubmit(form);
            if (confirmed) {
                showSubmitLoader(form.dataset.loadingText || 'Menyimpan...');
                form.submit();
            }
            return;
        }

        showSubmitLoader(form.dataset.loadingText || 'Menyimpan...');
    }
};

document.addEventListener('submit', async (event) => {
    await submitWithConfirmation(event);
});

document.addEventListener('click', async (event) => {
    const trigger = event.target.closest('[data-confirm-link]');
    if (!trigger) return;

    event.preventDefault();
    await confirmLink(trigger);
});
