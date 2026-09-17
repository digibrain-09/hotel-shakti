<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>


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
<link rel="shortcut icon" href="{{asset('assets3/img/final png.png')}}" type="image/x-icon">
<style>
    div.sticky {
        position: -webkit-sticky;
        position: sticky;
        bottom: 0;
        background-color: #e7272d;
        padding: 10px;
        font-size: 20px;
    }

    .tab-div {
        box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        color: black;
    }

    a,
    a:hover,
    a:focus {
        color: inherit;
        text-decoration: none;
        transition: all 0.3s;
    }

    #main {
        width: 100%;
        padding: 10px;
        /* min-height: 100vh; */
        transition: all 0.3s;
    }


    #tabs {
        background: #007b5e;
        color: #dc3545;
    }

    #tabs h6.section-title {
        color: #dc3545;
    }

    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #dc3545;
        background-color: transparent;
        border-color: transparent transparent #dc3545;
        border-bottom: 4px solid !important;
        font-size: 20px;
        font-weight: bold;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        color: #000;
        font-size: 18px !important;
    }



    .product_image {
        width: 300px;
        margin-top: 0;
    }

    .logo {
        height: 60px;
        width: 80px;
    }


    .product_detail_image {
        position: relative;
    }

    .top-left {
        position: absolute;
        top: 0px;
        left: 6px;
    }

    .product-price {
        font-size: 20px;
        font-weight: bold;
    }

    .item {
        font-size: 18px;
    }

    .add_to_cart {
        font-size: 15px;
        padding-right: 30px;
        padding-left: 30px;
    }

    .cart_tr {
        background-color: red;
        color: white;
    }

    .cart_icon {
        text-align: right;
    }

    .cart_link {
        display: inline-block;
        border-radius: 50px;
        background-color: #dc3545;
        color: white;
        box-shadow: 0 0 2px #888;
        padding: 0.5em 0.6em;
    }

    .cart_link:hover {
        color: white;
    }

    .badge {
        padding-left: 9px;
        padding-right: 9px;
        -webkit-border-radius: 9px;
        -moz-border-radius: 9px;
        border-radius: 9px;
    }

    .label-warning[href],
    .badge-warning[href] {
        background-color: #c67605;
    }

    #lblCartCount {
        font-size: 12px;
        background: black;
        color: #fff;
        padding: 0 5px;
        vertical-align: top;
        margin-left: -10px;
    }

    @media only screen and (max-width: 600px) {

        .product_view_iamge {
            height: 250px;
        }

        .read_more {
            top: 70%;
            font-weight: 500;
        }

        .text {
            width: 200px;
        }

        .product_image {
            width: 80px;
        }

        .chekout_btn {
            position: relative;
            /* margin-top: 10%; */
        }

        .item {
            font-size: 15px;
        }

    }

    @media screen and (min-width: 768px) and (max-width: 1180px) {

        .product_view_iamge {
            height: 350px;
        }

        .text {
            width: 200px;
        }

        .product_image {
            width: 150px;
        }

        .chekout_btn {
            position: relative;
            /* margin-top: 10%; */
        }


        .item {
            font-size: 15px;
        }

    }
</style>
<!-- Start Breadcrumb 
    ============================================= -->
<div class="breadcrumb-area shadow text-center dark bg-fixed text-light" style="background-image: url(assets3/img/banner.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Demo</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                    <li class="active">Demo</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<div id="main_section">



    <div class="tab-div">
        <ul class="nav nav-tabs justify-content-between" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="salads-tab-fill" data-bs-toggle="tab" href="#salads-fill" role="tab" aria-controls="salads-fill" aria-selected="false">Salads</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="desserts-tab-fill" data-bs-toggle="tab" href="#desserts-fill" role="tab" aria-controls="desserts-fill" aria-selected="true">Desserts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="drinks-tab-fill" data-bs-toggle="tab" href="#drinks-fill" role="tab" aria-controls="drinks-fill" aria-selected="false">Drinks</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pizzas-tab-fill" data-bs-toggle="tab" href="#pizzas-fill" role="tab" aria-controls="pizzas-fill" aria-selected="false">Pizzas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="burgers-tab-fill" data-bs-toggle="tab" href="#burgers-fill" role="tab" aria-controls="burgers-fill" aria-selected="false">Burgers</a>
            </li>
        </ul>
    </div>
    <div class="container">
        <div class="tab-content pt-1">
            <div class="tab-pane active" id="salads-fill" role="tabpanel" aria-labelledby="salads-tab-fill">
                <ul class="list-unstyled category">
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">TFL Salad</spam>
                                </b><br>
                                <span class="">₹ 200</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/202302100929TFL Salad.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Veggie Lemon Salad</spam>
                                </b><br>
                                <span class="">₹ 350</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/202302100922db3b5177-d2d6-4d30-8728-e4dddd1bc58f.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Fresh Chicken Salad</spam>
                                </b><br>
                                <span class="">₹ 350</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/202302100924Fresh Chicken Salad.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
            <div class="tab-pane" id="desserts-fill" role="tabpanel" aria-labelledby="desserts-tab-fill">
                <ul class="list-unstyled category">
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Blueberry cheesecake</spam>
                                </b><br>
                                <span class="">₹ 300</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/202302100925Blueberry cheesecake.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Brownies</spam>
                                </b><br>
                                <span class="">₹ 350</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/202302100925Brownies.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Apple Pie with Cinnamon</spam>
                                </b><br>
                                <span class="">₹ 400</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/202302100926Apple Pie with Cinnamon.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
            <div class="tab-pane" id="drinks-fill" role="tabpanel" aria-labelledby="drinks-tab-fill">
                <ul class="list-unstyled category">
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Citrus Juice</spam>
                                </b><br>
                                <span class="">₹ 200</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/202302100927Citrus Juice.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Iced Tea</spam>
                                </b><br>
                                <span class="">₹ 250</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/202302100928Iced Tea.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
            <div class="tab-pane" id="pizzas-fill" role="tabpanel" aria-labelledby="pizzas-tab-fill">
                <ul class="list-unstyled category">
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Margherita Pizza</spam>
                                </b><br>
                                <span class="">₹ 200</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/PRD_5cx5311q580_0.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Double Chicken Pizza</spam>
                                </b><br>
                                <span class="">₹ 350</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/pizzas.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Pepperoni Pizza</spam>
                                </b><br>
                                <span class="">₹ 300</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/PRD_6jw6fbck567_0.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
            <div class="tab-pane" id="burgers-fill" role="tabpanel" aria-labelledby="burgers-tab-fill">
                <ul class="list-unstyled category">
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Chicken Burger</spam>
                                </b><br>
                                <span class="">₹ 350</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/PRD_1b53031i778_1616730777523.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-between mt-5">
                        <div class="col-4">
                            <div class="text">
                                <b>
                                    <spam class="item">Beef Burger</spam>
                                </b><br>
                                <span class="">₹ 400</span><br>
                                <div class="btn-group mt-2">
                                    <a type="button" href="" class="btn-theme btn-sm" name="add_to_cart" id="">
                                        Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="image">
                                <a href=""><img class="product_image float-right" src="{{asset('assets3/img/PRD_50y48c9j73e_1611724233223.jpg')}}"></a>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </div>

    <div class="sticky">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mt-4">
                    <p style="color: #fff;">Like what you see? Get your menu now</p>
                </div>
                <div class="col-md-4 mt-3">
                    <a class="btn btn-light border btn-sm" style="float: right;" href="{{ route('contact') }}">LET’S GET STARTED </a>
                </div>
            </div>
        </div>
    </div>
</div>