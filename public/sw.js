self.addEventListener('install', (event) => {
    event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('push', (event) => {
    const fallback = {
        title: 'Pindang OI',
        body: 'Notifikasi untuk kamu hehe.',
        icon: '/images/logo/logo-icon.svg',
        badge: '/images/logo/logo-icon.svg',
        data: { url: '/' },
    };

    let payload = fallback;

    if (event.data) {
        try {
            payload = { ...fallback, ...event.data.json() };
        } catch (error) {
            payload = { ...fallback, body: event.data.text() };
        }
    }

    const title = payload.title || fallback.title;
    const options = {
        body: payload.body || fallback.body,
        icon: payload.icon || fallback.icon,
        badge: payload.badge || fallback.badge,
        tag: payload.tag || 'pindang-oi',
        data: payload.data || fallback.data,
        actions: payload.actions || [],
        requireInteraction: payload.requireInteraction || false,
    };

    event.waitUntil(
        self.registration.showNotification(title, options).then(() => {
            const notificationId = options.data?.notification_id;

            if (!notificationId) {
                return null;
            }

            return clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
                for (const client of clientList) {
                    client.postMessage({
                        type: 'pindang-oi-notification-shown',
                        notificationId,
                    });
                }
            });
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = new URL(event.notification.data?.url || '/', self.location.origin).href;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
