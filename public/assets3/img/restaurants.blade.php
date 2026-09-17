@include('front.header')
<!-- Start Breadcrumb 
    ============================================= -->
<div class="breadcrumb-area shadow text-center dark bg-fixed text-light" style="background-image: url(assets3/img/menu-banner.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Restaurants</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                    <li class="active">restaurants</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Food Menu
    ============================================= -->
<div class="default-info default-padding bg-fixed">
    <div class="container">
        <div class="row">
            <div class="info-items text-center">
                @foreach($restaurants as $restaurant)
                <!-- Single Item -->
                <div class="col-md-4 single-item">
                    <div class="item">
                        <img src="{{ ('restaurant_logo/' . $restaurant->logo) }}" alt="Thumb">
                        <h4>{{$restaurant->restaurant_name}}</h4>
                        <p>
                            Belonging sir curiosity discovery extremity yet forfeited prevailed own off. Travelling by introduced of mr terminated.
                        </p>
                        <a class="btn circle btn-theme effect btn-sm" href="#">Read More</a>
                    </div>
                </div>
                <!-- End Single Item -->
                @endforeach
            </div>
        </div>
    </div>
</div>
<!-- End Food Menu -->
@include('front.footer')