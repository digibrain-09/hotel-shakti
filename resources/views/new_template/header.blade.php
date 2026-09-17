<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Shakti</title>
    <link rel="shortcut icon" href="{{asset('assets/img/final_png.png')}}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    <link rel="stylesheet" type="text/css" href="{{ asset('slick/slick/slick.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('slick/slick/slick-theme.css')}}" />

    <script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="{{ asset('slick/slick/slick.min.js')}}"></script>



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('new_template_data/style.css')}}" />
    <!-- <script type="text/javascript" src="{{ asset('new_template_data/script.js')}}"></script> -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<!-- ==================== LOCATION GATE ==================== -->
<style>
    .location-gate {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(17, 17, 17, .72);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
    }

    .location-gate.is-open {
        display: flex;
    }

    .location-gate__card {
        background: #fff;
        border-radius: 18px;
        max-width: 380px;
        width: 100%;
        padding: 32px 26px 26px;
        text-align: center;
        box-shadow: 0 18px 50px rgba(0, 0, 0, .3);
        animation: gateIn .28s ease-out;
    }

    @keyframes gateIn {
        from {
            opacity: 0;
            transform: translateY(14px) scale(.97);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    .location-gate__icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(231, 39, 45, .1);
        color: #e7272d;
        font-size: 28px;
    }

    .location-gate__icon.is-pulsing {
        animation: gatePulse 1.6s ease-in-out infinite;
    }

    @keyframes gatePulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(231, 39, 45, .35);
        }

        50% {
            box-shadow: 0 0 0 14px rgba(231, 39, 45, 0);
        }
    }

    .location-gate__title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #1a1a1a;
    }

    .location-gate__message {
        font-size: 14.5px;
        line-height: 1.55;
        color: #666;
        margin-bottom: 22px;
    }

    .location-gate__btn {
        width: 100%;
        border: none;
        border-radius: 10px;
        padding: 13px;
        font-size: 15px;
        font-weight: 600;
        color: #fff;
        background: #e7272d;
    }

    .location-gate__btn:disabled {
        background: #d9a4a6;
    }

    .location-gate__hint {
        margin-top: 16px;
        font-size: 12.5px;
        color: #9a9a9a;
        line-height: 1.5;
    }

    .location-gate__spinner {
        width: 26px;
        height: 26px;
        border: 3px solid rgba(231, 39, 45, .25);
        border-top-color: #e7272d;
        border-radius: 50%;
        animation: gateSpin .8s linear infinite;
    }

    @keyframes gateSpin {
        to {
            transform: rotate(360deg);
        }
    }
</style>


<style>
    .navbar-background {
        background: url("{{ asset('assets/img/scantable-banner.jpg') }}") no-repeat center center;
        background-size: cover;
        position: relative;
        height: 300px;
    }

    .navbar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        /* Dark overlay for contrast */
    }

    .navbar-content {
        position: relative;
        z-index: 2;
    }


    .navbar-toggler {
        background-color: rgba(255, 255, 255, 0.7);
    }

    .slider {
        width: 100%;
        text-align: center;
    }

    .slider-section {
        background-color: rgb(248, 248, 248);
        padding-top: 50px;
        padding-bottom: 50px;
    }

    .category_img {
        border-radius: 50%;
        height: 160px;
        width: 160px;
    }

    .slide h5 {
        width: 160px;
    }

    .search-form {
        position: relative;
    }

    .search-input {
        padding-left: 2.5rem;
        transition: all 0.3s ease;
    }

    .search-icon {
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        color: #6c757d;
    }

    /* Hide full input on mobile by default */
    .mobile-search {
        display: none;
    }

    .custom-prev,
    .custom-next {
        position: absolute;
        top: 40%;
        transform: translateY(-50%);
        background: rgb(255, 255, 255);
        box-shadow: rgba(0, 0, 0, 0.2) 0px 3px 5px -1px, rgba(0, 0, 0, 0.14) 0px 6px 10px 0px, rgba(0, 0, 0, 0.12) 0px 1px 18px;
        border: none;
        border-radius: 50%;
        font-size: 24px;
        z-index: 2;
        height: 40px;
        width: 40px;
    }

    .custom-prev {
        left: -20px;
    }

    .custom-next {
        right: 0px;
    }

    .item_image {
        height: 200px;
        width: 200px;
        float: right;
        border-radius: 20px;
    }

    #myTabs a {
        color: #000;
    }

    #myTabs .active {
        color: #e7272d;
    }

    .add_to_cart {
        position: absolute;
        top: 80%;
        left: 82%;
        transform: translate(-50%, -50%);
        padding: 10px 20px;
        font-weight: 700;
        background-color: #e7272d;
        color: #fff !important;
    }


    .image-container {
        position: relative;
        min-height: 250px;
        /* adjust as needed */
    }

    .item-section small {
        color: grey;
    }

    .horizontal-scroll-wrapper {
        display: flex;
        overflow-x: auto;
        gap: 0px;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 10px;
    }

    .horizontal-scroll-wrapper::-webkit-scrollbar {
        height: 6px;
    }

    .horizontal-scroll-wrapper::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 10px;
    }

    .horizontal-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        cursor: pointer;
    }

    .bottom-tabbar {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #fff;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        z-index: 1030;
    }

    .bottom-tabbar .nav-link {
        padding: 10px 5px;
        font-size: 14px;
        color: #999;
        text-align: center;
    }

    .bottom-tabbar .nav-link.active {
        color: #e7272d;
        /* purple active color */
    }

    .bottom-tabbar .cart-btn {
        background: #e7272d;
        color: #fff;
        border-radius: 15px;
    }

    .bottom-tabbar .cart-icon-wrapper {
        text-align: center;
    }

    .bottom-tabbar .cart-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: red;
        color: #fff;
        font-size: 10px;
        border-radius: 50%;
        padding: 2px 5px;
    }

    .bottom-tabbar .fa {
        font-size: 25px;
    }

    .bottom-tabbar span {
        font-size: 20px;
        margin-left: 5px;
    }

    .offcanvas-image {
        height: 50px;
        width: 50px;
        border-radius: 10px;
    }

    .offcanvas {
        background-color: rgb(248, 248, 248);
    }

    .offcanvas-header {
        background-color: #fff;
    }

    .checkbox-lg .custom-control-label::before,
    .checkbox-lg .custom-control-label::after {
        width: 23px;
        height: 23px;
    }

    .checkbox-lg .custom-control-label {
        padding-top: 5px;
        padding-left: 6px;
    }

    .quantity-wrapper {
        display: flex;
        align-items: center;
        width: 50%;
        border: 2px solid #e7272d;
        border-radius: 5px;
        overflow: hidden;
        margin-top: 3px;
    }

    .qty-btn {
        flex: 0 0 30%;
        background-color: #fff;
        border: none;
        padding: 8px 0;
        cursor: pointer;
        color: #000;
    }

    .qty-btn .fa {
        font-size: 15px !important;
        color: #e7272d;
    }

    .qty-input {
        width: 50%;
        text-align: center;
        border: none;
        outline: none;
    }

    .offcanvas-footer {
        padding: 1rem;
        background-color: #fff;
    }

    .offcanvas-add-btn {
        float: right;
    }

    .item-details-image {
        border-radius: 20px;
        width: 300px;
        height: 300px;
    }

    .custom-control-input:checked~.custom-control-label::before {
        background-color: #e7272d !important;
        border-color: #e7272d !important;
    }

    .custom-control-input:checked~.custom-control-label::after {
        color: #fff;
    }

    .add-more-items {
        color: #e7272d;
        font-weight: 700;
        text-decoration: none;
        padding: 20px;
    }

    .edit-item {
        color: #e7272d;
        font-weight: 700;
        text-decoration: none;
    }

    .edit-item:hover {
        color: #e7272d;
        text-decoration: none;
    }

    .add-more-items:hover {
        color: #e7272d;
        text-decoration: none;
    }



    @media (max-width: 767.98px) {

        .desktop-search {
            display: none;
        }

        .search-toggle {
            display: inline-block;
            cursor: pointer !important;
            color: #fff !important;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .mobile-search {
            display: none;
            width: 100%;
        }

        .mobile-search.show {
            display: block;
            margin-top: 0.5rem;
        }
    }

    @media (max-width: 768px) {

        .slider-section {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .slider {
            padding: 0px;
        }

        .slider-section h3 {
            font-size: 20px;
        }

        .item-section h3 {
            font-size: 25px;
        }

        .category_img {
            height: 70px;
            width: 70px;
        }

        .slide h5 {
            width: 70px;
            font-size: 13px;
        }

        .custom-prev,
        .custom-next {
            top: 30% !important;
            background: none !important;
            box-shadow: none !important;
            height: none !important;
            width: none !important;
        }

        .item_image {
            height: 150px !important;
            width: 150px !important;
        }

        .item-section h4 {
            font-size: 20px;
        }

        .item-section h5 {
            font-size: 15px;
        }

        .item-section p {
            font-size: 13px;
        }

        .item-section small {
            font-size: 12px;
        }

        .nav-link {
            padding: 0;
        }

        .add_to_cart {
            top: 60% !important;
            left: 50% !important;
            transform: translate(-50%, -50%);
            padding-left: 20px;
            padding-right: 20px;
            font-weight: 700;
        }

        .horizontal-scroll-wrapper {
            gap: 10px;
        }

        .horizontal-scroll-wrapper::-webkit-scrollbar {
            height: 3px;
        }

        .checkbox-lg .custom-control-label::before,
        .checkbox-lg .custom-control-label::after {
            width: 20px;
            height: 20px;
        }

        .checkbox-lg .custom-control-label {
            padding-top: 5px;
            padding-left: 1px;
        }

        .offcanvas-add-btn {
            width: 100%;
        }

        .quantity-wrapper {
            width: 100%;
            border: 2px solid #e7272d;
        }

        .qty-input {
            width: 100%;
        }

        .qty-btn {
            background-color: #fff;
        }

        .qty-btn .fa {
            color: #e7272d;
        }

        .item-details-image {
            width: 100%;
        }

        .add-more-items {
            padding: 10px;
        }
    }
</style>
<script>
    $(document).ready(function() {
        let currentUrl = window.location.href;

        $('.bottom-tabbar a.bottom-tab-link').each(function() {
            if (this.href === currentUrl) {
                $('.bottom-tabbar a.bottom-tab-link').removeClass('active'); // remove previous active
                $(this).addClass('active'); // add to current
            }
        });
    });
</script>
<script>
    // false until the server confirms the customer is inside the radius
    window.GEO_OK = false;

    var geoChecking = false;
    var geoWatchdog = null;
    var geoAfterSuccess = null; // optional callback, used to resume a blocked form

    function requestLocation(showUi, onSuccess) {
        if (geoChecking) return;
        geoChecking = true;
        geoAfterSuccess = onSuccess || null;

        if (showUi) gateLoading();

        if (!navigator.geolocation) {
            geoChecking = false;
            gateBlocked('Your browser does not support location. Please ask our staff for help.');
            return;
        }

        // Some browsers call NEITHER callback when the prompt is dismissed.
        geoWatchdog = setTimeout(function() {
            if (!geoChecking) return;
            geoChecking = false;
            window.GEO_OK = false;
            gateBlocked('We could not read your location. Please turn on GPS / Location Services, allow the permission, and tap Try Again.');
        }, 18000);

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                clearTimeout(geoWatchdog);
                $.ajax({
                    url: '/hotelshakti/verify-location',
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude,
                        accuracy: pos.coords.accuracy
                    },
                    success: function() {
                        geoChecking = false;
                        window.GEO_OK = true;
                        gateClose();
                        if (geoAfterSuccess) {
                            var cb = geoAfterSuccess;
                            geoAfterSuccess = null;
                            cb();
                        }
                    },
                    error: function(xhr) {
                        geoChecking = false;
                        window.GEO_OK = false;
                        var m = (xhr.responseJSON && xhr.responseJSON.message);
                        gateBlocked(m || 'You seem to be away from the restaurant. Ordering is available only on the premises.');
                    }
                });
            },
            function(err) {
                clearTimeout(geoWatchdog);
                geoChecking = false;
                window.GEO_OK = false;

                var msg = 'We need your location to take the order.';
                if (err.code === 1) msg = 'Location permission is blocked. Please enable it in your browser settings and tap Try Again.';
                else if (err.code === 2) msg = 'Your location is switched off. Please turn on GPS / Location Services and tap Try Again.';
                else if (err.code === 3) msg = 'Getting your location took too long. Please move near a window and tap Try Again.';
                gateBlocked(msg);
            }, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 30000
            }
        );
    }

    function gateLoading() {
        $('#locationGate').addClass('is-open');
        $('#locationGateIcon').addClass('is-pulsing').html('<div class="location-gate__spinner"></div>');
        $('#locationGateTitle').text('Checking your location');
        $('#locationGateMessage').text("Hold on a moment while we confirm you're at the restaurant.");
        $('#locationGateBtn').prop('disabled', true).text('Please wait...');
    }

    function gateBlocked(message) {
        $('#locationGate').addClass('is-open');
        $('#locationGateIcon').removeClass('is-pulsing').html('<i class="fa fa-map-marker"></i>');
        $('#locationGateTitle').text('Location needed');
        $('#locationGateMessage').text(message);
        $('#locationGateBtn').prop('disabled', false).text('Try Again');
    }

    function gateClose() {
        $('#locationGate').removeClass('is-open');
        $('#locationGateIcon').removeClass('is-pulsing').html('<i class="fa fa-map-marker"></i>');
        $('#locationGateBtn').prop('disabled', false).text('Try Again');
    }

    // Silent check on every page load. Popup only on failure.
    $(document).ready(function() {
        requestLocation(false);
    });

    /* ---- Block order / payment forms until the check passes ---- */
    var GEO_GUARDED = ['/order', '/payment', '/case_on_delivery'];

    $(document).on('submit', 'form', function(e) {
        var action = $(this).attr('action') || '';
        var guarded = false;

        for (var i = 0; i < GEO_GUARDED.length; i++) {
            if (action.indexOf(GEO_GUARDED[i]) !== -1) {
                guarded = true;
                break;
            }
        }
        if (!guarded || window.GEO_OK === true) return;

        e.preventDefault();
        var form = this;

        // Re-check; if it passes, send the form through automatically.
        requestLocation(true, function() {
            form.submit(); // native submit, skips this handler
        });
    });
</script>


<body>
    <div id="locationGate" class="location-gate">
        <div class="location-gate__card">
            <div class="location-gate__icon" id="locationGateIcon"><i class="fa fa-map-marker"></i></div>
            <div class="location-gate__title" id="locationGateTitle">Location needed</div>
            <p class="location-gate__message" id="locationGateMessage">
                Please turn on your location to place an order.
            </p>
            <button type="button" class="location-gate__btn" id="locationGateBtn" onclick="requestLocation(true)">
                Try Again
            </button>
            <p class="location-gate__hint">
                Ordering is available only inside the restaurant.
            </p>
        </div>
    </div>


    <!-- Bottom Tab Bar -->
    <nav class="bottom-tabbar pt-2 pb-2 d-flex justify-content-around align-items-center border-top">
        <a class="nav-link bottom-tab-link active" href="{{route('scantable.index', ['table' => session('table_name')])}}"><i class="fa fa-home fa-lg"></i>
        </a>
        <a class="nav-link bottom-tab-link" href="{{ route('scantable.myorder') }}"><i class="fa fa-list fa-lg"></i>
        </a>
        <div class="cart-icon-wrapper">
            <?php if ($cart_count) { ?>
                <a type="button" href="{{ route('scantable.cart') }}" class="btn btn-md cart-btn bottom-tab-link" id="#">
                    <i class="fa fa-shopping-cart"></i> <span>{{$cart_count}}</span></a>
            <?php } else { ?>
                <a class="nav-link bottom-tab-link" href="{{ route('scantable.cart') }}"><i class="fa fa-shopping-cart fa-lg"></i>
                </a>
            <?php } ?>
        </div>
    </nav>