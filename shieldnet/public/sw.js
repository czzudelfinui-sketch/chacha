const CACHE_NAME = 'shieldnet-v1';
const ASSETS = [
  'login.php',
  'style.css',
  'assets/1-removebg-preview.png',
  'assets/2.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => response || fetch(event.request))
  );
});
