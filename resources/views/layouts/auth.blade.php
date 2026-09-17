<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title')</title>
    <link rel="shortcut icon" href="{{asset('assets/img/final_png.png')}}">  
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/components.css')}}">
    <style>
        :root {
            --background: red;
            --background-color:red;
            --color: #5c6874;
        }
          
    </style>
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">
</head>
<body>
    <div id="app">
        <section class="section login-page">
            <div class="container h-100">
                @yield('body-content')
                <div class="simple-footer">
                    Copyright &copy; DigiBrain 2023
                </div>
            </div>
        </section>
    </div>
    <!-- General JS Scripts -->
    <script src="{{asset('assets/js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{asset('assets/js/popper.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.nicescroll.min.js')}}"></script>
    <script src="{{asset('assets/js/stisla.js')}}"></script>
    <!-- JS Libraies -->
    <!-- Template JS File -->
    <script src="{{asset('assets/js/scripts.js')}}"></script>
    <!-- Page Specific JS File -->
    @yield('external-js')
</body>
</html>