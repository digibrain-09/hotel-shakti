<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scantable</title>
    <link rel="shortcut icon" href="{{asset('assets/img/final_png.png')}}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <link rel="stylesheet" href="{{asset('assets/css/custom.css?version').env('CSS_VERSION',1.0)}}">
    @yield('external-css')
</head>

<body class="font-sans bg-white min-h-screen overflow-x-hidden">
    <?php if (Auth::check()) { ?>
        @if(auth()->user()->t_web_request_status != 0)
        <div class=" d-sm-block d-lg-none nav-extra">
            {{ auth()->user()->t_web_request_status == 1 ? trans('messages.dashboard_reload_label') :
                    trans('messages.dashboard_reload_label_success') }}
        </div>
        @endif
    <?php } else { ?>
        <script>
            window.location.href = "{{ route('login') }}"; // Redirect to login page if session is expired
        </script>
    <?php } ?>
    @component('components.toast')@endcomponent
    @yield('body-content')
    @include('partials.footer')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            renderTables(currentFloor);

            // Show modal when clicking any navigation link
            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('page-not-found-modal').classList.remove('hidden');
                });
            });

            // Accordion toggle
            document.querySelectorAll('[data-accordion]').forEach(button => {
                button.addEventListener('click', function() {
                    const target = document.getElementById(this.dataset.accordion);
                    const chevron = this.querySelector('[data-lucide="chevron-down"]');
                    target.classList.toggle('hidden');
                    chevron.classList.toggle('rotate-180');
                });
            });
        });


        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }
    </script>
    <!-- Page Specific JS File -->
    @yield('external-js')

</body>

</html>