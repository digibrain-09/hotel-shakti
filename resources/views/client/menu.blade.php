@include('client.nav')
<div class="wrapper">
    <!-- Sidebar  -->
    <nav id="sidebar" style="display: none;">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div class="sidebarHeader">
        </div>

        <ul class="list-unstyled components">
            <li>
                <a href="{{ route('scantable.index', ['table' => session('table_name')]) }}"><i class="fa fa-home" style="margin-right: 10px;" aria-hidden="true"></i> Home</a>
            </li>
            <!-- @if (Session::get('session_code'))
            <li>
                <a href="{{ route('myorder') }}"><i class='fas fa-shopping-bag' style="margin-right: 10px;"></i> My Orders</a>
            </li>
            @endif -->
            <li>
                <a href="{{ route('scantable.myorder') }}"><i class='fas fa-shopping-bag' style="margin-right: 10px;"></i> My Orders</a>
            </li>
            <!-- <li>
                @if (Session::get('session_code')) 
                    <a href="{{ route('custom_logout') }}"><i class="fa fa-user" style="margin-right: 10px;" aria-hidden="true"></i> Log Out</a>
                @else
                    <a href="{{ route('custom_login') }}"><i class="fa fa-user" style="margin-right: 10px;" aria-hidden="true"></i> Login</a>

                @endif
            </li> -->

        </ul>
    </nav>

    <!-- Main Content  -->
    <div id="main">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <!-- <a href="{{ route('scantable.index', ['table' => session('table_name')]) }}">
                    <img src="{{asset('assets/img/final.jpg')}}" height="70px" width="90px" alt="Smart-BI">
                </a> -->
                <?php if (Session::get('logo')) { ?>
                    <a href="{{route('scantable.index', ['table' => session('table_name')])}}"><img src="{{ url('restaurant_logo/' . Session::get('logo')) }}" height="70px" width="90px" /></a>

                <?php } else { ?>
                    <a href="{{route('scantable.index', ['table' => session('table_name')])}}"><img src="{{asset('assets/img/final.jpg')}}" height="70px" width="90px" alt="scantable"></a>
                <?php } ?>
                <button type="button" id="sidebarCollapse" class="btn btn-defult" onclick="openNav()">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="nav navbar-nav navbar-right ml-auto">
                        <li class="nav-item active">

                            <!-- @if (Session::get('session_code'))
                            <div class="hidden fixed mt-2">
                                <a href="{{ route('custom_logout') }}" class="nav-link text-danger">Log Out</a>
                                @else
                                <a href="{{ route('custom_login') }}" class="nav-link text-danger">Log In</a>

                            </div>
                            @endif -->
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>

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
</script>