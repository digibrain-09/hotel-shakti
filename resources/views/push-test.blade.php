<!DOCTYPE html>
<html>
<head>
    <title>Push Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Push Notification Test</h1>
    <button id="subscribe">Subscribe for Push</button>
    <button onclick="window.location.href='/test-push'">Send Test Notification</button>

    <script>
    document.getElementById('subscribe').addEventListener('click', function () {
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("/sw.js").then(function(registration) {
                console.log("Service Worker registered:", registration);

                Notification.requestPermission().then(function(permission) {
                    if (permission === "granted") {
                        registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array("{{ env('VAPID_PUBLIC') }}")
                        }).then(function(subscription) {
                            console.log("Got subscription:", subscription);

                            fetch("/save-subscription", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                                },
                                body: JSON.stringify(subscription)
                            }).then(r => r.json()).then(data => {
                                console.log("Saved:", data);
                            });
                        });
                    }
                });
            });
        }
    });

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = atob(base64);
        return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
    }
    </script>
</body>
</html>
