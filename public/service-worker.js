var staticCacheName = 'pwa-v' + new Date().getTime();
var filesToCache = [
  '/offline',
  '/css/app.css',
  '/js/app.js',
  '/images/icons/icon-72x72.png',
  '/images/icons/icon-96x96.png',
  '/images/icons/icon-128x128.png',
  '/images/icons/icon-144x144.png',
  '/images/icons/icon-152x152.png',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-384x384.png',
  '/images/icons/icon-512x512.png'
];

// Cache on install - skipWaiting untuk langsung aktif
self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(staticCacheName).then(cache => {
      return cache.addAll(filesToCache);
    })
  );
});

// Clear cache on activate - claim clients untuk auto update
self.addEventListener('activate', event => {
  event.waitUntil(
    Promise.all([
      // Hapus cache lama
      caches.keys().then(cacheNames => {
        return Promise.all(
          cacheNames
            .filter(cacheName => cacheName.startsWith('pwa-'))
            .filter(cacheName => cacheName !== staticCacheName)
            .map(cacheName => caches.delete(cacheName))
        );
      }),
      // Ambil kontrol semua client tanpa perlu refresh
      self.clients.claim()
    ])
  );
});

// Serve from Cache - Network First untuk HTML, Cache First untuk assets
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  // Skip non-GET requests
  if (event.request.method !== 'GET') return;

  // Network first untuk HTML pages (agar selalu dapat update terbaru)
  if (event.request.mode === 'navigate' || event.request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          return response;
        })
        .catch(() => {
          return caches.match('/offline');
        })
    );
    return;
  }

  // Cache first untuk static assets
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        return response || fetch(event.request);
      })
      .catch(() => {
        return caches.match('/offline');
      })
  );
});

// Listen for message to force update
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

self.addEventListener('push', event => {
  const data = event.data.json();
  event.waitUntil(
    self.registration.showNotification(data.title, {
      body: data.body,
      icon: '/icons/icon-192x192.png',
      badge: '/icons/badge-72x72.png' // icon kecil di pojok
    })
  );

  // Opsional update badge di homescreen
  if ('setAppBadge' in navigator) {
    navigator.setAppBadge(data.count).catch(console.error);
  }
});

