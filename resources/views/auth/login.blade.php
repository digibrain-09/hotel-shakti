<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scantable - Login</title>
    <link rel="shortcut icon" href="{{asset('assets/img/final_png.png')}}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
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
  .alert {
        padding: 15px;
        background-color: green;
        color: #fff;
        margin-bottom: 20px;
        border-radius: 8px;
        text-align: center;
    }
</style>
</head>

<body class="font-sans bg-white min-h-screen overflow-x-hidden">

    <div class="flex min-h-screen">
        <!-- LEFT SIDE - Kitchen Image -->
        <div class="hidden lg:flex lg:w-1/2 relative">
            <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=1000&fit=crop" alt="Professional kitchen" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute bottom-8 left-8 text-white">
                <h2 class="text-3xl font-bold mb-2">Welcome to Scantable</h2>
                <p class="text-lg opacity-90">Smart dashboard for order management</p>
            </div>
        </div>

        <!-- RIGHT SIDE - Login Form -->
        <div class="flex-1 lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">

                <!-- Login Card -->
                <div class="bg-white border border-border rounded-card p-8 shadow-lg">
                    <!-- Chef Icon -->
                    <div class="text-center mb-6">
                        <div class="w-50 h-30 flex items-center justify-center mx-auto mb-4">
                            <img src="{{asset('assets/img/final_png.png')}}" alt="logo">
                        </div>
                    </div>

                    @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif



                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label class="block text-foreground text-sm font-medium mb-2">
                                Email Address
                            </label>
                            <input
                                type="text"
                                name="username"
                                value="{{ old('username') }}"
                                required
                                autofocus
                                placeholder="Enter your email"
                                class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary
                   @error('username') border-error @enderror">

                            @error('username')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-foreground text-sm font-medium mb-2">
                                Password
                            </label>

                            <div class="flex input-group" id="show_hide_password">
                                <input id="password" type="password" placeholder="Enter your password"
                                    class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary
                   @error('password') border-error @enderror"
                                    name="password" tabindex="2" required autocomplete="off">
                                <div class="input-group-append px-4 py-3" id="toggle_password" style="margin-left: -50px;">
                                    <span class="input-group-text" style="cursor:pointer;">
                                        <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>

                            @error('password')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- User Type --}}
                        <div class="mb-2">
                            <label class="block text-foreground text-sm font-medium mb-2">
                                Login As
                            </label>
                            <select
                                name="type"
                                class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="admin">Admin</option>
                                <option value="restaurant_admin">Restaurant Owner</option>
                                <option value="kitchen_owner">Kitchen Owner</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between" bis_skin_checked="1">
                            <a href="{{route('password.request')}}" class="text-sm text-primary hover:text-primary-hover cursor-pointer ml-auto">Forgot password?</a>
                        </div>

                        {{-- Submit --}}
                        <button
                            type="submit"
                            class="w-full bg-primary text-white py-3 px-4 rounded-button
               font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                            Sign In
                        </button>
                    </form>

                </div>

                <!-- Footer -->
                <div class="text-center mt-8">
                    <p class="text-gray-400 text-xs">Copyright © DigiBrain 2026</p>
                </div>
            </div>
        </div>
    </div>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            var $passwordInput = $('#password');
            var $toggleBtn = $('#toggle_password');
            var $icon = $('#toggle_password i');

            // Show/hide eye icon based on input
            $passwordInput.on('input', function() {
                if ($(this).val().length > 0) {
                    // $toggleBtn.removeClass('d-none');
                } else {
                    // $toggleBtn.addClass('d-none');
                    $passwordInput.attr('type', 'password');
                    $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });

            // Toggle password visibility
            $toggleBtn.on('click', function(e) {
                e.preventDefault();
                if ($passwordInput.attr("type") === "password") {
                    $passwordInput.attr("type", "text");
                    $icon.removeClass("fa-eye-slash").addClass("fa-eye");
                } else {
                    $passwordInput.attr("type", "password");
                    $icon.removeClass("fa-eye").addClass("fa-eye-slash");
                }
            });
        });
    </script>
</body>

</html>