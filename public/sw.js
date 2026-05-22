const CACHE_NAME = 'ninja-park-v2'; // Incrementamos versión para forzar limpieza
const ASSETS_TO_CACHE = [
  '/manifest.json',
  '/img/logo.png',
  '/img/icon-192.png',
  '/img/icon-512.png',
  '/vendor/bootstrap/css/bootstrap.min.css',
  '/vendor/bootstrap/js/bootstrap.bundle.min.js',
  '/vendor/bootstrap-icons/bootstrap-icons.css',
  '/vendor/flatpickr/css/flatpickr.min.css',
  '/vendor/flatpickr/js/flatpickr.js',
  '/vendor/flatpickr/js/es.js',
  '/fonts/outfit/outfit.css',
  '/fonts/outfit/outfit.woff2'
];

// Install: Cache critical assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      // Solo precacheamos assets estáticos, NO el Root '/' porque contiene CSRF tokens dinámicos
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
  self.skipWaiting();
});

// Activate: Clean old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log('Borrando cache antiguo:', cache);
            return caches.delete(cache);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch: Strategy for Assets vs Pages
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // SI ES UN ASSET ESTÁTICO (CSS, JS, Imagen, Font) -> Cache First / Network Fallback
  if (ASSETS_TO_CACHE.some(asset => url.pathname.endsWith(asset) || url.pathname.includes('/vendor/') || url.pathname.includes('/fonts/'))) {
    event.respondWith(
      caches.match(event.request).then((response) => {
        return response || fetch(event.request).then((fetchRes) => {
          return caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, fetchRes.clone());
            return fetchRes;
          });
        });
      })
    );
    return;
  }

  // SI ES UNA PÁGINA HTML (Rutas de Laravel) -> Network ONLY o Network First SIN guardar en cache
  // Nunca guardamos HTML en cache para evitar que el CSRF Token se quede "pegado" (Error 419)
  event.respondWith(
    fetch(event.request).catch(() => {
      // Solo si no hay internet absoluto, intentamos buscar si por error quedó algo en cache
      return caches.match(event.request);
    })
  );
});
