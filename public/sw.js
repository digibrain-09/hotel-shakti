self.addEventListener('push', function(event) {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        console.error('Push event data parse error', e);
    }

    const title = data.title || 'New Notification';
    const options = {
        body: data.body || '',
        icon: data.icon || '/images/order-icon.png',
        badge: data.badge || '/images/badge.png',
        data: {
            url: data.url || '/'
        },
        requireInteraction: data.requireInteraction || false
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const urlToOpen = event.notification.data && event.notification.data.url ? event.notification.data.url : '/';
    event.waitUntil(clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
        // If page is already open, focus it; otherwise open new window
        for (const client of clientList) {
            if (client.url === urlToOpen && 'focus' in client) {
                return client.focus();
            }
        }
        if (clients.openWindow) {
            return clients.openWindow(urlToOpen);
        }
    }));
});
