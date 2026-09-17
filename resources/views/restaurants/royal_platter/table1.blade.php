@include('restaurants.royal_platter.nav')
@include('restaurants.royal_platter.menu')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Monsieur+La+Doulaise&family=Parisienne&display=swap" rel="stylesheet">


<div class="banner">
    <img src="{{asset('assets/img/scantable-banner.jpg')}}" alt="scantable-banner" style="width:100%;">
    <!-- <h2 class="centered"><b>Its Time To Taste</b></h2> -->
    <!-- <h2 class="centered" style="font-size:7vw;">Its Time To Taste</h2> -->
    {{-- <a href="{{ url('#main_section') }}"><button type="button" class="read_more btn btn-danger rounded-pill">Read More</button></a> --}}
</div>
<div id="main_section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h4 class="page-header mt-3 text-center" style="font-family: 'Parisienne', serif;font-weight: 400;font-style: normal;margin-bottom:-10px">Its Time To Taste</h4><br>
            </div>
        </div>
    </div>


    <!-- tabs.blade.php -->
    <div class="d-flex nav-tabs-scroll">
        <ul class="nav nav-tabs justify-content-between nav-fill d-flex flex-nowrap border-0" id="myTabs">
            @foreach ($categories as $category)
            <li class="nav-item">
                <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab_{{ $category->id }}" data-toggle="tab" href="#content_{{ $category->id }}">{{ $category->category_name }}</a>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-12">

                <div class="container p-3" id="tab-content">
                    <div class="tab-content">
                        @foreach ($categories as $category)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="content_{{ $category->id }}">
                            <ul class="list-unstyled category">
                                @foreach ($category->items as $item)
                                @if($item->status == 1)
                                <div class="row justify-content-between mt-4">
                                    <div class="col-4">
                                        <div class="text">
                                            <b>
                                                <spam class="item">{{ $item->item_name}}</spam>
                                            </b><br>
                                            <span class="price">₹ {{ $item->price}}</span><br>
                                            <div class="btn-group mt-1">
                                                <!-- @if (empty(session()->has('session_code')))
                                                <a type="button" href="javascript:void(0);" onclick="formToggle();" class="btn btn-danger add_to_cart btn-sm" name="add_to_cart" id="{{$item->id}}">
                                                    Add</a>
                                                @else
                                                <a type="button" href="{{ route('add_to_cart', $item->id) }}" class="btn btn-danger add_to_cart btn-sm" name="add_to_cart" id="{{$item->id}}">
                                                    Add</a>
                                                @endif -->

                                                <a type="button" href="{{ route('royalplatter.add_to_cart', $item->id) }}" class="btn btn-danger add_to_cart btn-sm" name="add_to_cart" id="{{$item->id}}">
                                                    Add</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="image">
                                            <a href="{{ route('royalplatter.view', $item->id) }}"><img class="product_image float-right" src="{{ url('items/' . $item->picture) }}"></a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                    <div class="cart_icon">
                        <a href="{{ route('royalplatter.cart') }}" class="cart_link"><i class="fa fa-shopping-cart fa-2x" aria-hidden="true"></i></a>
                        <span class='badge badge-warning' id='lblCartCount'>
                            {{$cart}}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('restaurants.royal_platter.footer')


<script type="text/javascript">
    $(document).ready(function() {
        $('#myTabs a').click(function(e) {
            e.preventDefault()
            $(this).tab('show')
        });
    });
    $(document).ready(function() {
        $("#myModal").modal('show');
    });
</script>

<!-- JS CDN links -->
<script src="https://cdn.jsdelivr.net/npm/cdbootstrap/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cdbootstrap/js/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cdbootstrap/js/cdb.min.js"></script>


<script>
    function openNav() {
        document.getElementById("sidebar").style.width = "250px";
        $("#sidebar").css("display", "block");
        document.body.style.backgroundColor = "rgba(0,0,0,0.3)";
    }

    function closeNav() {
        document.getElementById("sidebar").style.width = "0";
        document.body.style.backgroundColor = "white";
    }

    $(function() {
        $("a[rel='tab']").click(function(e) {
            //e.preventDefault(); 
            pageurl = $(this).attr('href');

            $.ajax({
                url: pageurl,
                success: function(data) {
                    $('body').html(data);
                    window.location.reload();
                }
            });

            //to change the browser URL to 'pageurl'
            if (pageurl != window.location) {
                window.history.pushState({
                    path: pageurl
                }, '', pageurl);
            }
            return false;
        });
    });

    $(function() {
        $(".add_to_cart").click(function(e) {
            pageurl = $(this).attr('href');
            $.ajax({
                url: pageurl,

                success: function(data) {
                    $('body').html(data);
                    window.location.reload();
                    // alert('Your Product Added in Cart');
                    //location.reload();
                }
            });

            return false;
        });
    });

    function formToggle() {
        <?php if (Session::get('session_code')) { ?>

        <?php } else { ?>
            var url = "{{ route('custom_login') }}";
            alert('You are not logged in. Please log in.');
            window.location.href = url;
        <?php } ?>
    }

    window.onbeforeunload = function() {
        // $.ajax({
        //     url: "{{ route('royalplatter.tab_close',session('table_id')) }}",
        //     method: "delete",
        //     success: function(response) {
        //         window.location.reload();
        //     }
        // });
        alert('hii');
    }
</script>