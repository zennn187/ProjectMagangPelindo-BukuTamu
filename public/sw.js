const CACHE_NAME = 'bukutamu-static-v1';
const PRECACHE_URLS = [
    '/offline.html',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key.startsWith('bukutamu-static-') && key !== CACHE_NAME)
                    .map((key) => caches.delete(key)),
            ))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request, { cache: 'no-store' })
                .catch(() => caches.match('/offline.html')),
        );
        return;
    }

    if (url.pathname.startsWith('/icons/') || url.pathname === '/offline.html') {
        event.respondWith(
            caches.match(request)
                .then((cached) => cached || fetch(request)),
        );
    }
});
