import './bootstrap';

import Alpine from 'alpinejs';

import Lenis from 'lenis';

window.Alpine = Alpine;

/* Lenis — smooth scrolling (hanya untuk halaman yang mengaktifkan, mis. kiosk).
   Halaman admin/dashboard memakai scroll native browser agar lebih ringan. */
if (document.body.hasAttribute('data-lenis')) {
    const lenis = new Lenis({
        autoRaf: true,
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    });
}

Alpine.start();
