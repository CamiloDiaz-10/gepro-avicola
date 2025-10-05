/* Gepro Avícola Service Worker */
const CACHE_NAME = 'gepro-avicola-v2';
const OFFLINE_URL = '/offline';

// Add core routes/assets to pre-cache (customize as needed)
const PRECACHE_URLS = [
  '/',
  '/manifest.webmanifest',
  OFFLINE_URL,
  // CDNs are not cacheable cross-origin here; runtime caching will handle network
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.map((k) => (k !== CACHE_NAME ? caches.delete(k) : null)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // NEVER cache authentication, API, or POST requests
  const skipCache = [
    '/login',
    '/register',
    '/logout',
    '/api/',
    '/admin/',
    '/owner/',
    '/employee/',
    '/_ignition',
  ];

  const shouldSkipCache = skipCache.some(path => url.pathname.includes(path)) || 
                          request.method !== 'GET';

  if (shouldSkipCache) {
    // Pass through to network without caching
    return;
  }

  // Navigation requests: serve offline page when network fails
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          // Only cache successful responses
          if (response.status === 200) {
            const copy = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
          }
          return response;
        })
        .catch(async () => {
          const cached = await caches.match(request);
          return (
            cached ||
            (await caches.match(OFFLINE_URL)) ||
            new Response('Offline', { status: 503, statusText: 'Offline' })
          );
        })
    );
    return;
  }

  // For same-origin GET requests: try cache first, then network
  if (url.origin === self.location.origin) {
    event.respondWith(
      caches.match(request).then((cached) => {
        const networkFetch = fetch(request)
          .then((response) => {
            if (response.status === 200) {
              const copy = response.clone();
              caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
            }
            return response;
          })
          .catch(() => cached || caches.match(OFFLINE_URL));
        return cached || networkFetch;
      })
    );
  }
});
