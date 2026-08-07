// Paperwork PWA & Firebase Cloud Messaging Service Worker
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

const CACHE_NAME = 'paperwork-pwa-v3';
const ASSETS_TO_CACHE = [
  '/',
  '/manifest.json',
  '/favicon.png',
  '/apple-touch-icon.png',
  '/favicon.ico',
  '/images/logo/paperwork-logo.png'
];

// Initialize Firebase App in Service Worker if config exists
try {
  if (firebase.apps.length === 0) {
    firebase.initializeApp({
      apiKey: self.FIREBASE_API_KEY || "",
      authDomain: self.FIREBASE_AUTH_DOMAIN || "",
      projectId: self.FIREBASE_PROJECT_ID || "",
      storageBucket: self.FIREBASE_STORAGE_BUCKET || "",
      messagingSenderId: self.FIREBASE_MESSAGING_SENDER_ID || "",
      appId: self.FIREBASE_APP_ID || "",
    });
  }

  const messaging = firebase.messaging();
  messaging.onBackgroundMessage((payload) => {
    console.log('[sw.js] Received Firebase background message: ', payload);
    const title = payload.notification?.title || payload.data?.title || 'Notifikasi Paperwork';
    const options = {
      body: payload.notification?.body || payload.data?.body || 'Anda memiliki notifikasi baru.',
      icon: payload.notification?.icon || '/img/logo/logo.png',
      badge: '/img/logo/logo.png',
      data: payload.data || {},
    };
    self.registration.showNotification(title, options);
  });
} catch (err) {
  console.log('[sw.js] Firebase SDK init skipped or fallback to standard Web Push:', err);
}

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch(() => {});
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;
  // Skip caching for API requests or admin endpoints
  if (event.request.url.includes('/api/') || event.request.url.includes('/livewire/')) return;

  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request);
    })
  );
});

// Standard Web Push Listener fallback
self.addEventListener('push', (event) => {
  if (event.data) {
    try {
      const data = event.data.json();
      const title = data.title || data.notification?.title || 'Notifikasi Paperwork';
      const options = {
        body: data.body || data.notification?.body || 'Anda memiliki notifikasi baru.',
        icon: data.icon || '/img/logo/logo.png',
        badge: '/img/logo/logo.png',
        data: data.data || {},
      };
      event.waitUntil(self.registration.showNotification(title, options));
    } catch (e) {
      console.error('Error parsing push data', e);
    }
  }
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then((clientList) => {
      for (const client of clientList) {
        if (client.url === '/' && 'focus' in client) return client.focus();
      }
      if (clients.openWindow) return clients.openWindow('/');
    })
  );
});
