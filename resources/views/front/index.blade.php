@include('front.header')

<!-- Start Banner 
    ============================================= -->
<div class="banner-area content-less responsive-auto-height">
    <div id="bootcarousel" class="carousel inc-top-heading slide carousel-fade animate_text" data-ride="carousel">
        <!-- Wrapper for slides -->
        <div class="carousel-inner text-light carousel-zoom">
            <div class="item active">
                <div class="slider-thumb bg-cover" style="background-image: url(assets3/img/gallery-5.jpg);"></div>
                <div class="box-table shadow dark">
                    <div class="box-cell">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="content default-padding" style="margin-top: 80px;margin-bottom:80px">
                                        <h3 data-animation="animated slideInDown">Welcome to ScanTable</h3>
                                        <h1 data-animation="animated slideInLeft">GET YOUR DIGITAL QR CODE MENU</h1>
                                        <a data-animation="animated slideInUp" class="btn btn-light effect btn-md" href="{{ route('contact') }}">LET’S GET STARTED </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Banner -->

<!-- Start About
    ============================================= -->
<div class="about-area default-padding">
    <div class="container">
        <div class="row">
            <div class="about-items">
                <div class="col-md-7 info">
                    <h3>About Us</h3>
                    <h2>Upgrade your business to the next level with <b>Scantable</b></h2>
                    <p>
                        A complete food order management solution for restaurants, hotels, cafes, food courts, and corporates in one website.
                    </p>
                    <p>
                        We accelerate your company’s success by solving challenging technical problems and providing coherent services.
                        ScanTable understands your business needs and we bridge the gap between technology and your business to make it more value-driven and profitable.
                    </p>
                    <ul>
                        <li>
                            <div class="icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info">
                                <h4>Phone</h4>
                                <a href="https://api.whatsapp.com/send?phone=4407551439414&text=Hi"><span>+44 07551 439414</span></a>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <i class="fas fa-envelope-open"></i>
                            </div>
                            <div class="info">
                                <h4>Email</h4>
                                <a href="mailto:info@scantable.online" bis_skin_checked="1"><span>info@scantable.online</span></a>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- Start Form -->
                <div class="col-md-5 form">
                    <img src="{{asset('assets3/img/about-us.jpg')}}" alt="Thumb">
                </div>
                <!-- End Form -->
            </div>
        </div>
    </div>
</div>
<!-- End About -->

<!-- Start Services
    ============================================= -->

<!-- End Services -->

<!-- <div class="chef-area" style="margin-top: 100px;">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="site-heading text-center">
                    <h2>Our Restaurant</h2>
                </div>
            </div>
        </div>
    </div>
</div> -->


<!-- <div class="default-info default-padding bg-fixed" style="background-image: url(assets3/img/gallery-1.jpg);">
    <div class="container">
        <div class="row">
            <div class="info-items text-center">
                @foreach($restaurants as $restaurant)
                <div class="col-md-4 single-item">
                    <div class="item">
                        <img src="{{ ('restaurant_logo/' . $restaurant->logo) }}" alt="Thumb">
                        <h4>{{$restaurant->restaurant_name}}</h4>
                        <p>
                            Belonging sir curiosity discovery extremity yet forfeited prevailed own off. Travelling by introduced of mr terminated.
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div> -->


<!-- <div class="book-table bg-gray about-area bg-cover" style="margin-top: 100px;">
    <div class="container-full">
        <div class="row">
            <div class="form-items">
                <div class="col-md-6 thumb bg-cover" style="background-image: url(assets3/img/person-scanning-qr-code.jpg);"></div>
                <div class="col-md-6 form">
                    <h2>Scan & Order</h2>
                    <p>Customers scan the QR code with their phone camera to access the food digital menu without downloading any app. Then the customers can place their order and pay instantly in a few clicks and enjoy their food delivered directly to their table.</p>
                </div>
            </div>
        </div>
    </div>
</div> -->

<div class="about-area default-padding">
    <div class="container">
        <div class="row">
            <div class="about-items">
                <div class="col-md-7 info">
                    <h2>How Customers Use QR Codes</h2>
                    <p>
                        Today's smartphones have built-in QR Code readers with the camera, so there's no special app to install. This creates very simple steps for customers:
                    </p>
                    <p>1. Point a phone camera at a QR code and wait for just a few seconds.</p>
                    <p>2. A notification will pop up that, when clicked, will send you to a web page.</p>
                    <p>3. Explore the menu on your phone, then enjoy your meal!</p>
                </div>

                <div class="col-md-5 form">
                    <img src="{{asset('assets3/img/person-scanning-qr-code.jpg')}}" alt="Thumb">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="chef-area" style="margin-top: 100px;">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="site-heading text-center">
                    <h2>Pricing Plans</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pricingTable">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="inner1 d-flex tabsBtnHolder">
                    <ul>
                        <li>
                            <p id="monthly" class="active">Monthly</p>
                        </li>
                        <li>
                            <p id="yearly" class="">Yearly</p>
                        </li>

                        <li class="indicator"></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row monthlyPriceList animated">
            <div class="col-md-4">
                <div class="inner holder">
                    <div class="hdng">
                        <p>Standard</p>
                    </div><br>
                    <div class="price mt-5">
                        <p><b>₹1499 </b><span> / every 3 mo</span></p>
                    </div>
                    <div class="info">
                        <p>5 tables/ QR codes per store</p>
                        <p>Apply brand logos</p>
                        <p>Create your own menu</p>
                        <p>Order Management</p>
                        <!--<p>10 Free Optimization</p>
                        <p>24/7 Support</p> -->
                    </div>
                    <div class="btn">
                        <a href="{{ route('contact') }}" class="readon">Contact Us</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="inner holder active">
                    <div class="hdng">
                        <p>Pro</p>
                    </div><br>
                    <div class="price mt-5">
                        <p><b>₹2499 </b><span> / every 3 mo</span></p>
                    </div>
                    <div class="info">
                        <p>15 tables/ QR codes per store</p>
                        <p>Apply brand logos</p>
                        <p>Create your own menu</p>
                        <p>Order Management</p>
                        <!--<p>Organic Trafic 215%</p>
                        <p>10 Free Optimization</p>
                        <p>24/7 Support</p> -->
                    </div>
                    <div class="btn">
                        <a href="{{ route('contact') }}" class="readon">Contact Us</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="inner holder">
                    <div class="hdng">
                        <p>Enterprise</p>
                    </div><br>
                    <div class="price mt-5">
                        <p><b>₹3999 </b><span> / every 3 mo</span></p>
                    </div>
                    <div class="info">
                        <p>More than 15 tables/ QR codes per store</p>
                        <p>Apply brand logos</p>
                        <p>Create your own menu</p>
                        <p>Order Management</p>
                        <!--<p>Organic Trafic 215%</p>
                        <p>10 Free Optimization</p>
                        <p>24/7 Support</p> -->
                    </div>
                    <div class="btn">
                        <a href="{{ route('contact') }}" class="readon">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row yearlyPriceList d-none animated" style="display:none">
            <div class="col-md-4">
                <div class="inner holder">
                    <div class="hdng">
                        <p>Standard</p>
                    </div><br>
                    <div class="price mt-5">
                        <p><b>₹4499</b><span> / every year</span></p>
                    </div>
                    <div class="info">
                        <p>5 tables/ QR codes per store</p>
                        <p>Apply brand logos</p>
                        <p>Create your own menu</p>
                        <p>Order Management</p>
                        <!--<p>Organic Trafic 215%</p>
                        <p>10 Free Optimization</p>
                        <p>24/7 Support</p> -->
                    </div>
                    <div class="btn">
                        <a href="{{ route('contact') }}" class="readon">Contact Us</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="inner holder active">
                    <div class="hdng">
                        <p>Pro</p>
                    </div><br>
                    <div class="price mt-5">
                        <p><b>₹8499 </b><span> / every year</span></p>
                    </div>
                    <div class="info">
                        <p>15 tables/ QR codes per store</p>
                        <p>Apply brand logos</p>
                        <p>Create your own menu</p>
                        <p>Order Management</p>
                        <!--<p>Organic Trafic 215%</p>
                        <p>10 Free Optimization</p>
                        <p>24/7 Support</p> -->
                    </div>
                    <div class="btn">
                        <a href="{{ route('contact') }}" class="readon">Contact Us</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="inner holder">
                    <div class="hdng">
                        <p>Enterprise</p>
                    </div><br>
                    <div class="price mt-5">
                        <p><b>₹14499</b><span> / every year</span></p>
                    </div>
                    <div class="info">
                        <p>More than 15 tables/ QR codes per store</p>
                        <p>Apply brand logos</p>
                        <p>Create your own menu</p>
                        <p>Order Management</p>
                        <!-- <p>Organic Trafic 215%</p>
                        <p>10 Free Optimization</p>
                        <p>24/7 Support</p> -->
                    </div>
                    <div class="btn">
                        <a href="{{ route('contact') }}" class="readon">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="banner-area responsive-auto-height heading-exchange bg-gray text-dark" style="margin-top: 100px;background-color:#e7272d">
    <div id="bootcarousel" class="carousel slide carousel-fade animate_text" data-ride="carousel">
        <!-- Wrapper for slides -->
        <div class="carousel-inner transparent-nav">
            <!-- Single Item -->
            <div class="item active">
                <div class="box-table">
                    <div class="box-cell">
                        <div class="container">
                            <div class="row" id="row" style="margin-left: 200px;">
                                <div class="double-items content">
                                    <div class="col-md-6 col-sm-7 info" style="width:350px">
                                        <!-- <h1 data-animation="animated fadeInUp">Scan & Order</h1> -->
                                        <h3 data-animation="animated fadeInDown" style="color: #fff;">What is your
                                            Digital Menu
                                            going to look like?</h3>
                                        <!-- <a class="btn btn-light border btn-sm" href="{{ route('demo') }}">Click Here to Find Out!</a> -->
                                    </div>
                                    <div class="col-md-6 col-sm-5 thumb" data-animation="animated slideInRight">
                                        <img src="{{asset('assets3/img/scantable-in-table-1.jpeg')}}" style="border: 10px solid #fff;" alt="scantable qrcode" width="300px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Single Item -->

        </div>
        <!-- End Wrapper for slides -->
    </div>
</div>



@include('front.footer')

<script>
    $(document).ready(function() {
        $("#monthly").click(function() {
            $(this).addClass('active');
            $("#yearly").removeClass('active')

            $(".monthlyPriceList").css('display', 'block');
            $(".monthlyPriceList").addClass('fadeIn');
            $(".yearlyPriceList").css('display', 'none');

            $(".indicator").css("left", "2px");
        })

        $("#yearly").click(function() {
            $(this).addClass('active');
            $("#monthly").removeClass('active');

            $(".yearlyPriceList").css('display', 'block');
            $(".yearlyPriceList").addClass('fadeIn');
            $(".monthlyPriceList").css('display', 'none');

            $(".indicator").css("left", "163px");
        })
    })
</script>