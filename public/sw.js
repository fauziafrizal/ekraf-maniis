// Cache only an explicit, static offline fallback. Never cache private pages or submissions.
const CACHE = 'ekraf-offline-v2';
const FALLBACK = new URL('offline.html', self.registration.scope).href;
self.addEventListener('install', (event) => event.waitUntil(caches.open(CACHE).then((cache) => cache.add(FALLBACK))));
self.addEventListener('activate', (event) =>
    event.waitUntil(
        caches
            .keys()
            .then((keys) =>
                Promise.all(
                    keys
                        .filter((key) => key.startsWith('ekraf-offline-') && key !== CACHE)
                        .map((key) => caches.delete(key))
                )
            )
    )
);
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET' || event.request.mode !== 'navigate') return;
    const url = new URL(event.request.url);
    if (
        url.origin !== self.location.origin ||
        url.pathname.includes('/pengurus') ||
        url.pathname.includes('/verifikasi')
    )
        return;
    event.respondWith(fetch(event.request, { cache: 'no-store' }).catch(() => caches.match(FALLBACK)));
});
