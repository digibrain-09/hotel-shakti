@include('front.header')
<!-- Start Breadcrumb 
    ============================================= -->
<div class="breadcrumb-area shadow text-center dark bg-fixed text-light" style="background-image: url(assets3/img/menu-banner.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Food Menus</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                    <li class="active">Menus</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Food Menu
    ============================================= -->
<div class="food-menu-area inc-isotop default-padding">
    <div class="container">
        <div class="food-menu-area text-center">
            <div class="row">
                <div class="col-md-12 food-menu-content">
                    <div class="mix-item-menu text-center">
                        <button class="active" data-filter="*">All</button>
                        <button data-filter=".salads">Salads</button>
                        <button data-filter=".desserts">Desserts</button>
                        <button data-filter=".drinks">Drinks</button>
                    </div>
                    <!-- End Mixitup Nav-->

                    <div class="row text-center masonary">
                        <div id="portfolio-grid" class="menu-lists text-center col-3">
                            <!-- Single Item -->
                            <div class="item-single pf-item salads">
                                <div class="item">
                                    <div class="thumb">
                                        <a href="#">
                                            <img src="{{asset('assets3/img/202302100929TFL Salad.jpg')}}" alt="Thumb">
                                        </a>
                                        <div class="price">
                                            <h5>$200</h5>
                                        </div>
                                    </div>
                                    <div class="info">
                                        <h4><a href="#">TFL Salad</a></h4>
                                        <!-- <span>Mutton / Olive Oil / Salt</span> -->
                                        <p>
                                            Considered introduced themselves mr to discretion at. Means among saw hopes
                                            for. Death mirth in oh learn he equal on.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Item -->
                            <!-- Single Item -->
                            <div class="item-single pf-item salads">
                                <div class="item">
                                    <div class="thumb">
                                        <a href="#">
                                            <img src="{{asset('assets3/img/202302100922db3b5177-d2d6-4d30-8728-e4dddd1bc58f.jpg')}}" alt="Thumb">
                                        </a>
                                        <div class="price">
                                            <h5>$350</h5>
                                        </div>
                                    </div>
                                    <div class="info">
                                        <h4><a href="#">Veggie Lemon Salad</a></h4>
                                        <p>
                                            Considered introduced themselves mr to discretion at. Means among saw hopes
                                            for. Death mirth in oh learn he equal on.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Item -->
                            <!-- Single Item -->
                            <div class="item-single pf-item salads">
                                <div class="item">
                                    <div class="thumb">
                                        <a href="#">
                                            <img src="{{asset('assets3/img/202302100924Fresh Chicken Salad.jpg')}}" alt="Thumb">
                                        </a>
                                        <div class="price">
                                            <h5>$350</h5>
                                        </div>
                                    </div>
                                    <div class="info">
                                        <h4><a href="#">Fresh Chicken Salad</a></h4>
                                        <p>
                                            Considered introduced themselves mr to discretion at. Means among saw hopes
                                            for. Death mirth in oh learn he equal on.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Item -->
                            <!-- Single Item -->
                            <div class="item-single pf-item desserts">
                                <div class="item">
                                    <div class="thumb">
                                        <a href="#">
                                            <img src="{{asset('assets3/img/202302100925Blueberry cheesecake.jpg')}}" alt="Thumb">
                                        </a>
                                        <div class="price">
                                            <h5>$300</h5>
                                        </div>
                                    </div>
                                    <div class="info">
                                        <h4><a href="#">Blueberry cheesecake</a></h4>
                                        <p>
                                            Considered introduced themselves mr to discretion at. Means among saw hopes
                                            for. Death mirth in oh learn he equal on.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Item -->
                            <!-- Single Item -->
                            <div class="item-single pf-item desserts">
                                <div class="item">
                                    <div class="thumb">
                                        <a href="#">
                                            <img src="{{asset('assets3/img/202302100925Brownies.jpg')}}" alt="Thumb">
                                        </a>
                                        <div class="price">
                                            <h5>$350</h5>
                                        </div>
                                    </div>
                                    <div class="info">
                                        <h4><a href="#">Brownies</a></h4>
                                        <p>
                                            Considered introduced themselves mr to discretion at. Means among saw hopes
                                            for. Death mirth in oh learn he equal on.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Item -->
                            <!-- Single Item -->
                            <div class="item-single pf-item desserts">
                                <div class="item">
                                    <div class="thumb">
                                        <a href="#">
                                            <img src="{{asset('assets3/img/202302100926Apple Pie with Cinnamon.jpg')}}" alt="Thumb">
                                        </a>
                                        <div class="price">
                                            <h5>$400</h5>
                                        </div>
                                    </div>
                                    <div class="info">
                                        <h4><a href="#">Apple Pie with Cinnamon</a></h4>
                                        <p>
                                            Considered introduced themselves mr to discretion at. Means among saw hopes
                                            for. Death mirth in oh learn he equal on.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="item-single pf-item drinks">
                                <div class="item">
                                    <div class="thumb">
                                        <a href="#">
                                            <img src="{{asset('assets3/img/202302100927Citrus Juice.jpg')}}" alt="Thumb">
                                        </a>
                                        <div class="price">
                                            <h5>$200</h5>
                                        </div>
                                    </div>
                                    <div class="info">
                                        <h4><a href="#">Citrus Juice</a></h4>
                                        <p>
                                            Considered introduced themselves mr to discretion at. Means among saw hopes
                                            for. Death mirth in oh learn he equal on.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="item-single pf-item drinks">
                                <div class="item">
                                    <div class="thumb">
                                        <a href="#">
                                            <img src="{{asset('assets3/img/202302100928Iced Tea.jpg')}}" alt="Thumb">
                                        </a>
                                        <div class="price">
                                            <h5>$250</h5>
                                        </div>
                                    </div>
                                    <div class="info">
                                        <h4><a href="#">Iced Tea</a></h4>
                                        <p>
                                            Considered introduced themselves mr to discretion at. Means among saw hopes
                                            for. Death mirth in oh learn he equal on.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Single Item -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Food Menu -->
@include('front.footer')