// Service Worker untuk Admin Panel Desa Cibatu - No Caching Version
const CACHE_NAME = 'desa-cibatu-admin-v2.0.0';

// Install event - cache offline page only
self.addEventListener('install', event => {
    console.log('Service Worker: Installing (No Caching Version)...');

    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.add('/offline.html');
        }).then(() => {
            return self.skipWaiting();
        })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker: Activating (No Caching Version)...');

    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        console.log('Service Worker: Deleting cache:', cacheName);
                        return caches.delete(cacheName);
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activated successfully - No caching mode');
                return self.clients.claim();
            })
    );
});

// Fetch event - Always fetch from network with offline fallback
self.addEventListener('fetch', event => {
    const { request } = event;

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip external requests
    const url = new URL(request.url);
    if (url.origin !== location.origin) {
        return;
    }

    // Always fetch from network for real-time data
    event.respondWith(
        fetch(request).catch(() => {
            // Return offline page for HTML requests
            if (request.destination === 'document' ||
                request.headers.get('accept').includes('text/html')) {
                return caches.match('/offline.html');
            }

            // Return error for other requests
            return new Response('Offline content not available', {
                status: 503,
                statusText: 'Service Unavailable'
            });
        })
    );
});

// Background sync untuk offline functionality (tanpa caching)
self.addEventListener('sync', event => {
    console.log('Service Worker: Background sync triggered:', event.tag);

    if (event.tag === 'form-sync') {
        event.waitUntil(syncFormData());
    }
});

// Push notifications
self.addEventListener('push', event => {
    console.log('Service Worker: Push notification received');

    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.body || 'Notifikasi dari Admin Panel Desa Cibatu',
            icon: '/logo-desa-cibatu.png',
            badge: '/logo-desa-cibatu.png',
            vibrate: [100, 50, 100],
            data: {
                url: data.url || '/',
                timestamp: Date.now()
            },
            actions: [
                {
                    action: 'open',
                    title: 'Buka',
                    icon: '/logo-desa-cibatu.png'
                },
                {
                    action: 'close',
                    title: 'Tutup',
                    icon: '/logo-desa-cibatu.png'
                }
            ]
        };

        event.waitUntil(
            self.registration.showNotification(data.title || 'Admin Panel Desa Cibatu', options)
        );
    }
});

// Notification click handler
self.addEventListener('notificationclick', event => {
    console.log('Service Worker: Notification clicked');

    event.notification.close();

    if (event.action === 'open' || !event.action) {
        event.waitUntil(
            self.clients.openWindow(event.notification.data?.url || '/')
        );
    }
});

// Background sync functions
async function syncFormData() {
    console.log('Service Worker: Syncing form data...');
    // Implement form data sync logic here
}

console.log('Service Worker: Loaded - No caching version for real-time admin panel');
