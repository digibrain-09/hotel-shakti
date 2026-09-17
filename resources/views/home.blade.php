@extends('layouts.base',['nav_title'=>"Dashboard"])
@include('alert.order-manager-alert')
@include('alert.order-alert')
@include('alert.waiter-payment-alert')

@section('page-title')
Dashboard | {{ config('app.name', 'Scantable') }}
@endsection

@section('body-content')
@component('components.alert')

@endcomponent
<style>
    .alert {
        padding: 15px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .alert-info {
        background-color: #d9edf7;
        border-left: 5px solid #31708f;
        color: #31708f;
    }
</style>
<script>
    if ("serviceWorker" in navigator && "PushManager" in window) {
        navigator.serviceWorker.register("/sw.js")
            .then(function(registration) {
                console.log("Service Worker registered:", registration);

                Notification.requestPermission().then(function(permission) {
                    if (permission === "granted") {
                        subscribeUser(registration);
                    } else {
                        console.warn("Notification permission denied");
                    }
                });
            })
            .catch(function(err) {
                console.error("Service Worker registration failed:", err);
            });
    }

    function subscribeUser(registration) {
        const publicVapidKey = "{{ env('VAPID_PUBLIC') }}";
        const role = "{{ Session::get('test') ?? 'restaurant_manager' }}"; // or whatever default

        registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicVapidKey)
            })
            .then(function(subscription) {
                console.log("Got subscription:", subscription);

                // send subscription + role to server
                fetch("/save-subscription", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            subscription: subscription,
                            role: role
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        console.log("Saved subscription:", data);
                    })
                    .catch(err => console.error("Failed to save subscription:", err));
            })
            .catch(function(err) {
                console.error("Failed to subscribe:", err);
            });
    }

    // helper
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
    }
</script>
@if(Session::get('test') == 'restaurant_manager')
<div class="alertmsg"></div>
<div id="alertSoundContainer"></div>
@endif
@if(Session::get('test') == 'restaurant_admin')
<div class="alertordermsg"></div>
<div id="alertSoundContainer"></div>
<div class="alertpaymentmsg"></div>
@endif
@if(Session::get('test') == 'restaurant_waiter')
<div class="alertordermsg"></div>
<div id="alertSoundContainer_waiter"></div>
@endif
<section class="section">
    <div class="section-body">
        <div class="card mb-0">
            <div class="card-body">
                <?php if (Session::get('test') == 'admin') { ?>
                    <h2>Welcome Admin</h2>
                <?php } else if (Session::get('test') == 'restaurant_manager') { ?>
                    <h2>Welcome Restaurant Manger</h2>
                <?php } else if (Session::get('test') == 'restaurant_admin') { ?>
                    <h2>Welcome Restaurant Owner</h2>
                <?php } else if (Session::get('test') == 'restaurant_waiter') { ?>

                    <h2>Welcome Restaurant Waiter</h2>
                <?php } else if (Session::get('test') == 'kitchen_owner') { ?>

                    <h2>Welcome Kitchen Owner</h2>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
@endsection