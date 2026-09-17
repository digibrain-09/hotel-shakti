<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ========== Meta Tags ========== -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Rescaf - Food & Restauratn Template">

    <!-- ========== Page Title ========== -->
    <title>Scantable</title>

    <!-- ========== Favicon Icon ========== -->
    <link rel="shortcut icon" href="{{asset('assets3/img/final png.png')}}" type="image/x-icon">

    <!-- ========== Start Stylesheet ========== -->
    <link href="{{asset('assets3/css/bootstrap.min.css')}}" rel="stylesheet" />
    <link href="{{asset('assets3/css/font-awesome.min.css')}}" rel="stylesheet" />
    <link href="{{asset('assets3/css/flaticon-set.css')}}" rel="stylesheet" />
    <link href="{{asset('assets3/css/magnific-popup.css')}}" rel="stylesheet" />
    <link href="{{asset('assets3/css/owl.carousel.min.css')}}" rel="stylesheet" />
    <link href="{{asset('assets3/css/owl.theme.default.min.css')}}" rel="stylesheet" />
    <link href="{{asset('assets3/css/animate.css')}}" rel="stylesheet" />
    <link href="{{asset('assets3/css/bootsnav.css')}}" rel="stylesheet" />
    <link href="{{asset('assets3/css/style.css')}}" rel="stylesheet" />
    <link href="{{asset('assets3/css/responsive.css')}}" rel="stylesheet" />
    <!-- ========== End Stylesheet ========== -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5/html5shiv.min.js"></script>
      <script src="assets/js/html5/respond.min.js"></script>
    <![endif]-->

    <!-- ========== Google Fonts ========== -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,500,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script:400,700" rel="stylesheet">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TP22GMFC5Q"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-TP22GMFC5Q');
    </script>

</head>
<style>
    /* ------------------------------------
02. Global CSS
---------------------------------------*/
    .readon {
        position: relative;
        display: inline-block !important;
        background: #e7272d;
        padding: 14px 30px;
        line-height: normal;
        color: #ffffff !important;
        transition: all 0.3s ease 0s;
        border-radius: 30px;
        text-transform: capitalize !important;
        cursor: pointer;
        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
        -ms-box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
        -webkit-box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
        -moz-box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
    }

    .readon:hover,
    .readon:focus {
        background: #fff;
        color: #e7272d !important;
        border: 1px solid #e7272d;
    }

    .inner {
        width: 100%;
        /* float: left; */
        position: relative;
    }



    .pricingTable .holder {
        background: #fff;
        box-shadow: 1px 20px 12px -15px rgba(0, 0, 0, 0.2);
        padding: 40px 15px;
        text-align: center;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 20px;
        transition: 0.5s ease;
    }

    .pricingTable .holder:hover {
        transform: translateY(-5px);

    }



    .pricingTable .holder .hdng p {
        font-size: 28px;
        font-weight: bold;
        color: #242526;
    }

    .pricingTable .holder .img img {
        width: 70%;
    }

    .pricingTable .holder .price p {
        color: #e7272d;
        margin-bottom: 25px;
    }

    .pricingTable .holder .price p b {
        font-size: 40px;
        font-weight: bold;
    }

    .pricingTable .holder .price p span {
        font-size: 18px;
    }

    .pricingTable .holder .info p {
        margin-bottom: 15px;
        color: #242526;
        font-weight: 14px;
    }

    .pricingTable .holder.active {
        background: #e7272d;
    }

    .pricingTable .holder.active .hdng p,
    .pricingTable .holder.active .price p,
    .pricingTable .holder.active .info p {
        color: #fff;
    }

    .pricingTable .holder.active .readon {
        background: #fff;
        color: #e7272d !important;
    }


    .pricingTable .inner1 {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }


    .pricingTable .tabsBtnHolder ul {
        float: left;
        display: block;
        width: 100%;
        max-width: 326px;
        border-radius: 1.6666666667rem;
        margin: 0px auto;
        margin-bottom: 40px;
        background: #e7272d;
        text-align: center;
        position: relative;
    }

    .pricingTable .tabsBtnHolder ul li {
        float: left;
        width: calc(100% / 2);
        display: inline-block;
        transition: 0.4s ease;
    }

    .pricingTable .tabsBtnHolder ul li p {
        color: #fff;
        padding: 18px 18px;
        z-index: 10;
        position: relative;
        cursor: pointer;
        margin: 0;
    }

    .pricingTable .tabsBtnHolder ul li p.active {
        color: #e7272d;
    }

    .pricingTable .tabsBtnHolder ul li.indicator {
        position: absolute;
        top: 50%;
        left: 2px;
        /*163px*/
        background: #fff;
        height: calc(100% - 4px);
        transform: translateY(-50%);
        border-radius: 1.5333333333rem;
        width: 161px;
        z-index: 9
    }
</style>

<body>

    <!-- Preloader Start -->
    <div class="se-pre-con"></div>
    <!-- Preloader Ends -->

    <!-- Start Header Top 
    ============================================= -->
    <div class="top-bar-area inline bg-theme">
        <div class="container">
            <div class="row">
                <div class="col-md-8 address-info text-left">
                    <div class="info box">
                        <ul>
                            <li>
                                <i class="fas fa-map-marker-alt"></i> s7, Shiv Kuber, Uganda Road Porbandar
                            </li>
                            <li>
                                <a style="color: #fff;" href="https://api.whatsapp.com/send?phone=4407551439414&text=Hi"><i class="fas fa-phone"></i> +44 07551 439414 </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 social text-right">
                    <ul>
                        <li>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </li>
                        <li>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </li>
                        <li>
                            <a href="mailto:info@scantable.online"><i class="fa fa-envelope"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Header Top -->

    <!-- Header 
    ============================================= -->
    <header id="home">

        <!-- Start Navigation -->
        <nav class="navbar navbar-default attr-border navbar-sticky bootsnav">

            <!-- Start Top Search -->
            <div class="container">
                <div class="row">
                    <div class="top-search">
                        <div class="input-group">
                            <form action="#">
                                <input type="text" name="text" class="form-control" placeholder="Search">
                                <button type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Top Search -->

            <div class="container">

                <!-- Start Atribute Navigation -->
                <!-- <div class="attr-nav">
                    <ul>
                        <li class="search"><a href="#"><i class="fa fa-search"></i></a></li>
                    </ul>
                </div> -->
                <!-- End Atribute Navigation -->

                <!-- Start Header Navigation -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                        <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand" href="{{ route('front') }}">
                        <img src="{{asset('assets3/img/final png.png')}}" class="logo" alt="Logo" width="100px">
                    </a>
                </div>
                <!-- End Header Navigation -->

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <ul class="nav navbar-nav navbar-right" data-in="#" data-out="#">
                        <li><a href="{{ route('front') }}">Home</a></li>
                        </li>
                        <!-- <li class="dropdown">
                        <li><a href="{{ route('demo') }}">Demo</a></li>
                        </li> -->
                        <li>
                            <a href="{{ route('about') }}">About Us</a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}">contact</a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}">Login</a>
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div>

        </nav>
        <!-- End Navigation -->

    </header>
    <!-- End Header -->

    <body>