const PUSH_BUTTON_ID = 'pushNotificationToggle';
const POLL_INTERVAL_MS = 15000;
const SHOWN_STORAGE_KEY = 'pindang_oi_shown_local_notification_ids';
let latestNotificationPollStarted = false;
let latestNotificationSince = new Date().toISOString();

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; i += 1) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
}

function subscriptionToPayload(subscription) {
    const json = subscription.toJSON();

    return {
        endpoint: json.endpoint,
        keys: json.keys,
        contentEncoding: PushManager.supportedContentEncodings?.includes('aes128gcm')
            ? 'aes128gcm'
            : 'aesgcm',
    };
}

function setButtonState(button, enabled, title) {
    const thumb = button.querySelector('[data-push-toggle-thumb]');
    const status = document.querySelector('[data-push-status]');

    button.dataset.enabled = enabled ? 'true' : 'false';
    button.title = title;
    button.setAttribute('aria-label', title);
    button.classList.toggle('text-brand-600', enabled);
    button.classList.toggle('dark:text-brand-400', enabled);
    button.classList.toggle('bg-brand-500', enabled);
    button.classList.toggle('bg-gray-300', !enabled);
    button.classList.toggle('dark:bg-gray-700', !enabled);

    if (thumb) {
        thumb.classList.toggle('translate-x-5', enabled);
        thumb.classList.toggle('translate-x-0.5', !enabled);
    }

    if (status) {
        status.textContent = title;
    }

    window.dispatchEvent(new CustomEvent('push-status-updated', {
        detail: { enabled, title },
    }));
}

async function getVapidPublicKey() {
    const response = await window.axios.get('/push-notifications/public-key');
    return response.data.publicKey;
}

async function getRegistration() {
    const registration = await navigator.serviceWorker.register('/sw.js');
    registration.update().catch(() => {});

    return registration;
}

async function syncSubscription(subscription) {
    await window.axios.post('/push-notifications/subscribe', subscriptionToPayload(subscription));
}

async function subscribe(button) {
    const publicKey = await getVapidPublicKey();

    if (!publicKey) {
        setButtonState(button, false, 'VAPID public key belum dikonfigurasi');
        return;
    }

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        setButtonState(button, false, 'Notifikasi browser diblokir');
        return;
    }

    const registration = await getRegistration();
    let subscription = await registration.pushManager.getSubscription();

    if (!subscription) {
        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(publicKey),
        });
    }

    await syncSubscription(subscription);
    setButtonState(button, true, 'Notifikasi browser aktif');
}

async function unsubscribe(button) {
    const registration = await getRegistration();
    const subscription = await registration.pushManager.getSubscription();

    if (subscription) {
        await window.axios.delete('/push-notifications/subscribe', {
            data: { endpoint: subscription.endpoint },
        });
        await subscription.unsubscribe();
    }

    setButtonState(button, false, 'Aktifkan notifikasi browser');
}

async function sendTest(button) {
    const registration = await getRegistration();
    const subscription = await registration.pushManager.getSubscription();

    await window.axios.post('/push-notifications/test', {
        endpoint: subscription?.endpoint,
    });

    setButtonState(button, button.dataset.enabled === 'true', 'Tes push dikirim dari server');
}

function shownNotificationIds() {
    try {
        return new Set(JSON.parse(window.localStorage.getItem(SHOWN_STORAGE_KEY) || '[]'));
    } catch (error) {
        return new Set();
    }
}

function rememberShownNotificationIds(ids) {
    window.localStorage.setItem(SHOWN_STORAGE_KEY, JSON.stringify([...ids].slice(-50)));
}

function markNotificationAsShown(notificationId) {
    if (!notificationId) {
        return;
    }

    const shownIds = shownNotificationIds();
    shownIds.add(notificationId);
    rememberShownNotificationIds(shownIds);
}

async function showLocalDatabaseNotification(registration, notification) {
    const shownIds = shownNotificationIds();

    if (shownIds.has(notification.id)) {
        return;
    }

    shownIds.add(notification.id);
    rememberShownNotificationIds(shownIds);

    await registration.showNotification(notification.title || 'Notifikasi', {
        body: notification.body || 'Ada pembaruan baru.',
        icon: '/images/logo/logo-icon.svg',
        badge: '/images/logo/logo-icon.svg',
        tag: `${notification.tag || 'pindang-oi'}-${notification.id}`,
        data: { url: notification.read_url || '/' },
    });
}

async function pollLatestUnreadNotifications() {
    if (Notification.permission !== 'granted') {
        return;
    }

    const registration = await getRegistration();
    const response = await window.axios.get('/notifications/latest-unread', {
        params: { since: latestNotificationSince },
    });

    const notifications = response.data.notifications || [];

    for (const notification of notifications) {
        await showLocalDatabaseNotification(registration, notification);

        if (notification.created_at && notification.created_at > latestNotificationSince) {
            latestNotificationSince = notification.created_at;
        }
    }

    latestNotificationSince = response.data.server_time || latestNotificationSince;
}

function startLatestNotificationPolling() {
    if (latestNotificationPollStarted || !window.axios) {
        return;
    }

    latestNotificationPollStarted = true;

    window.setInterval(() => {
        pollLatestUnreadNotifications().catch((error) => {
            console.error('Latest notification polling failed:', error);
        });
    }, POLL_INTERVAL_MS);
}

function listenForServiceWorkerNotifications() {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    navigator.serviceWorker.addEventListener('message', (event) => {
        if (event.data?.type === 'pindang-oi-notification-shown') {
            markNotificationAsShown(event.data.notificationId);
        }
    });
}

async function hydrateButton(button) {
    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
        button.hidden = true;
        return;
    }

    const registration = await getRegistration();
    const subscription = await registration.pushManager.getSubscription();
    const enabled = Boolean(subscription) && Notification.permission === 'granted';

    if (enabled) {
        await syncSubscription(subscription);
    }

    setButtonState(
        button,
        enabled,
        enabled ? 'Notifikasi browser aktif' : 'Aktifkan notifikasi browser'
    );

    if (enabled) {
        startLatestNotificationPolling();
    }
}

export function initPushNotifications() {
    const button = document.getElementById(PUSH_BUTTON_ID);
    const testButton = document.querySelector('[data-push-test]');

    if (!button || !window.axios) {
        return;
    }

    listenForServiceWorkerNotifications();

    hydrateButton(button).catch(() => {
        setButtonState(button, false, 'Notifikasi browser belum siap');
    });

    button.addEventListener('click', async () => {
        button.disabled = true;

        try {
            if (button.dataset.enabled === 'true') {
                await unsubscribe(button);
            } else {
                await subscribe(button);
                startLatestNotificationPolling();
            }
        } catch (error) {
            const statusCode = error?.response?.status;
            const message = statusCode === 419
                ? 'Sesi CSRF kedaluwarsa, refresh halaman'
                : statusCode === 422
                    ? 'Data subscription tidak valid'
                    : statusCode === 500
                        ? 'Server gagal menyimpan subscription'
                        : error?.name === 'NotAllowedError'
                            ? 'Izin notifikasi diblokir browser'
                            : 'Gagal mengubah notifikasi browser';

            setButtonState(button, false, message);
            console.error('Push notification toggle failed:', error);
        } finally {
            button.disabled = false;
        }
    });

    if (testButton) {
        testButton.addEventListener('click', async () => {
            testButton.disabled = true;

            try {
                await sendTest(button);
            } catch (error) {
                setButtonState(button, button.dataset.enabled === 'true', 'Tes push gagal, cek log Laravel');
                console.error('Push notification test failed:', error);
            } finally {
                testButton.disabled = false;
            }
        });
    }
}
