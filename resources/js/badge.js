import { animate, createTimeline, stagger } from 'animejs';

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (!reduceMotion) {
    const timeline = createTimeline({
        defaults: {
            ease: 'out(4)',
        },
    });

    timeline
        .add('.badge', {
            opacity: [0, 1],
            translateY: [18, 0],
            scale: [0.96, 1],
            duration: 700,
        })
        .add('.badge-head', {
            opacity: [0.4, 1],
            duration: 350,
        }, '-=420')
        .add('.qr', {
            opacity: [0, 1],
            scale: [0.9, 1],
            duration: 600,
        }, '-=180')
        .add('.meta .row', {
            opacity: [0, 1],
            translateY: [8, 0],
            duration: 320,
            delay: stagger(65),
        }, '-=160');
}

const qrTrigger = document.getElementById('qr-zoom-trigger');
const qrModal = document.getElementById('qr-modal');
const qrModalCard = qrModal?.querySelector('.qr-modal-card');
const qrModalFrame = document.getElementById('qr-modal-frame');
const qrModalClose = document.getElementById('qr-modal-close');

const closeQrModal = () => {
    if (!qrModal) return;
    qrModal.hidden = true;
    document.body.style.overflow = '';
    if (qrModalFrame) qrModalFrame.replaceChildren();
};

qrTrigger?.addEventListener('click', () => {
    if (!qrModal || !qrModalFrame) return;

    const qr = qrTrigger.querySelector('svg')?.cloneNode(true);
    if (!qr) return;

    qrModalFrame.replaceChildren(qr);
    qrModal.hidden = false;
    document.body.style.overflow = 'hidden';

    if (!reduceMotion && qrModalCard) {
        animate(qrModalCard, {
            opacity: [0, 1],
            scale: [0.92, 1],
            translateY: [12, 0],
            duration: 420,
            ease: 'out(4)',
        });
        animate(qr, {
            opacity: [0, 1],
            scale: [0.86, 1],
            duration: 500,
            ease: 'out(4)',
        });
    }

    qrModalClose?.focus();
});

qrModalClose?.addEventListener('click', closeQrModal);
qrModal?.addEventListener('click', (event) => {
    if (event.target === qrModal) closeQrModal();
});
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && qrModal && !qrModal.hidden) closeQrModal();
});
