<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scantable</title>

    <link rel="shortcut icon" href="{{ asset('assets/img/final_png.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> -->
    <script src="{{ asset('js/browser@4.js') }}"></script>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    @stack('styles')
    <style type="text/tailwindcss">
        @theme inline {
    --color-primary: var(--primary);
    --color-primary-hover: var(--primary-hover);
    --color-foreground: var(--foreground);
    --color-muted: var(--muted);
    --color-border: var(--border);
    --color-accent-lime: var(--accent-lime);
    --color-accent-teal: var(--accent-teal);
    --color-accent-peach: var(--accent-peach);
    --color-success: var(--success);
    --color-success-light: var(--success-light);
    --color-success-dark: var(--success-dark);
    --color-error: var(--error);
    --color-error-light: var(--error-light);
    --color-error-lighter: var(--error-lighter);
    --color-error-dark: var(--error-dark);
    --color-warning: var(--warning);
    --color-warning-light: var(--warning-light);
    --color-warning-dark: var(--warning-dark);
    --color-info: var(--info);
    --color-info-light: var(--info-light);
    --color-info-dark: var(--info-dark);
    --color-alert: var(--alert);
    --color-alert-light: var(--alert-light);
    --color-alert-dark: var(--alert-dark);
    --color-gray-50: var(--gray-50);
    --color-gray-100: var(--gray-100);
    --color-gray-200: var(--gray-200);
    --color-gray-500: var(--gray-500);
    --color-gray-600: var(--gray-600);
    --color-gray-700: var(--gray-700);
    --font-sans: var(--font-sans);
    --radius-card: 20px;
    --radius-button: 16px;
    --radius-icon: 22px;
  }
  :root {
    --primary: #fe0000;
    --primary-hover: #fe0000;
    --foreground: #0C1C3C;
    --muted: #F7F7F7;
    --border: #DCDEDD;
    --accent-lime: #C5E151;
    --accent-teal: #82D9D7;
    --accent-peach: #FAAC7B;
    --success: #22C55E;
    --success-light: #DCFCE7;
    --success-dark: #166534;
    --error: #EF4444;
    --error-light: #FEE2E2;
    --error-lighter: #FEF2F2;
    --error-dark: #991B1B;
    --warning: #EAB308;
    --warning-light: #FEF9C3;
    --warning-dark: #854D0E;
    --info: #3B82F6;
    --info-light: #DBEAFE;
    --info-dark: #1E40AF;
    --alert: #F97316;
    --alert-light: #FFEDD5;
    --alert-dark: #9A3412;
    --gray-50: #F9FAFB;
    --gray-100: #F3F4F6;
    --gray-200: #E5E7EB;
    --gray-500: #6B7280;
    --gray-600: #4B5563;
    --gray-700: #374151;
    --font-sans: 'Plus Jakarta Sans', sans-serif;
  }
  select {
    @apply appearance-none bg-no-repeat cursor-pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-position: right 10px center;
    padding-right: 40px;
  }
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

  .menu-item {
        @apply flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 text-gray-700 mb-1;
    }
    .icon {
        @apply w-5 h-5;
    }

    .sidebar-nav a.active{
        color: #fff;
        background: #fe0000;
        border-radius: 20px;
    }

    .custom-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
}

.custom-modal.hidden {
    display: none;
}

.custom-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.5);
}

.custom-modal-box {
    position: relative;
    background: #fff;
    width: 100%;
    max-width: 600px;
    margin: 5% auto;
    padding: 24px;
    border-radius: 12px;
    max-height: 85vh;
    overflow-y: auto;
}

.alert {
        padding: 15px;
        background-color: #d9edf7;
        color: #31708f;
        margin-bottom: 20px;
        border-radius: 8px;
    }

    .closebtn {
        margin-left: 15px;
        color: #31708f;
        font-weight: bold;
        float: right;
        font-size: 25px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

</style>
</head>

<body class="font-sans bg-white min-h-screen overflow-x-hidden">

    <?php if (Auth::check()) { ?>
        <!-- Mobile Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

        <div class="flex min-h-screen">

            {{-- SIDEBAR --}}
            @include('admin_side_new.menu')

            {{-- MAIN CONTENT --}}
            <main class="flex-1 lg:ml-64 p-4 md:p-5 bg-white min-h-screen overflow-x-hidden">
                <!-- Mobile Header -->
                <div class="lg:hidden flex items-center justify-between mb-6 bg-muted rounded-card p-4">
                    <button onclick="toggleSidebar()" aria-label="Open menu" class="p-2 rounded-lg hover:bg-gray-200 cursor-pointer transition-all duration-200">
                        <i data-lucide="menu" class="w-6 h-6 text-foreground"></i>
                    </button>
                    @if(Session::get('logo'))
                    <a href="{{route('home')}}"><img height="100px" width="100px" src="{{ url('restaurant_logo/' . Session::get('logo')) }}" /></a>
                    @else
                    <a href="{{route('home')}}"><img height="100px" width="100px" src="{{asset('assets/img/final.png')}}" alt="scantable"></a>
                    @endif
                    <div class="w-10"></div>
                </div>


                {{-- ALERTS --}}
                @include('alert.order-manager-alert')
                @include('alert.order-alert')
                @include('alert.waiter-payment-alert')
                @include('alert.kitchen-order-alert')

                {{-- PAGE CONTENT --}}
                @yield('content')

            </main>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                lucide.createIcons();
            });

            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            }
        </script>

    <?php } else { ?>
        <script>
            window.location.href = "{{ route('login') }}"; // Redirect to login page if session is expired
        </script>
    <?php } ?>
    <script>
        const role = "{{ Session::get('test') }}";

        if (role === "restaurant_admin" || role === "restaurant_manager" || role === "restaurant_waiter" || role === "kitchen_owner") {
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
        }
    </script>
    @stack('scripts')
</body>

</html>