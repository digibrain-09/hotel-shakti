<?php
if (Auth::check()) {
    $platform = auth()->user()->plat_form;
} else {
?>
    <script>
        window.location.href = "{{ route('login') }}"; // Redirect to login page if session is expired
    </script>
<?php
}
?>

<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand1">
            <?php if (Session::get('logo')) { ?>
                <a href="{{route('home')}}"><img src="{{ url('restaurant_logo/' . Session::get('logo')) }}" /></a>

            <?php } else { ?>
                <a href="{{route('home')}}"><img src="{{asset('assets/img/final.jpg')}}" alt="scantable"></a>
            <?php } ?>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">S<span>T</span></a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">{{trans('common.dashboard')}}</li>

            <li class="nav-item {{getActiveClass(['home'])}}">
                <a href="{{route('home')}}" class="nav-link">
                    <div class="sidebar-menu-img"><svg xmlns="http://www.w3.org/2000/svg" width="20.128" height="16.433" viewBox="0 0 20.128 16.433">
                            <path d="M20.128,10.059A9.969,9.969,0,0,1,18.1,16.121a.786.786,0,0,1-1.254-.949A8.49,8.49,0,1,0,1.573,10.059a8.409,8.409,0,0,0,1.714,5.113.786.786,0,0,1-1.254.949A10.059,10.059,0,0,1,10.064,0,10.056,10.056,0,0,1,20.128,10.059ZM15.046,5.324a.786.786,0,0,1,0,1.112L12.483,9a2.752,2.752,0,1,1-1.112-1.112l2.563-2.563a.786.786,0,0,1,1.112,0Zm-3.8,4.982a1.178,1.178,0,1,0-1.178,1.178A1.179,1.179,0,0,0,11.242,10.306Zm0,0" transform="translate(0 0)" fill="#5c6874" />
                        </svg></div><span>{{trans('common.dashboard')}}</span>
                </a>
            </li>

            <li class="menu-header">{{trans('common.other')}}</li>

            <?php if (Session::get('test') == 'admin') { ?>
                <li class="nav-item {{getActiveClass(['restaurant.index'])}}">
                    <a href="{{route('restaurant.index')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class='fas fa-bread-slice'></i></div><span>Restaurant</span>
                    </a>
                </li>

                <li class="nav-item {{getActiveClass(['restaurant_user.index'])}}">
                    <a href="{{route('restaurant_user.index')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class='fa fa-user'></i></div><span>Restaurant Employee</span>
                    </a>
                </li>

                <li class="nav-item {{getActiveClass(['table.index'])}}">
                    <a href="{{route('table.index')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa fa-table"></i></div><span>Table</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'restaurant_admin') { ?>
                <li class="nav-item {{getActiveClass(['restaurant.index'])}}">
                    <a href="{{route('restaurant.index')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class='fas fa-user'></i></div><span>Account</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'admin' || Session::get('test') == 'restaurant_manager' || Session::get('test') == 'restaurant_admin' || Session::get('test') == 'kitchen_owner') { ?>

                <li class="nav-item {{getActiveClass(['category.index'])}}">
                    <a href="{{route('category.index')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fa fa-list-alt"></i></div><span>category</span>
                    </a>
                </li>

                <li class="nav-item {{getActiveClass(['item.index'])}}">
                    <a href="{{route('item.index')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-cart-plus"></i></div><span>Items</span>
                    </a>
                </li>

            <?php } ?>

            <?php if (Session::get('test') == 'restaurant_manager' || Session::get('test') == 'restaurant_admin') { ?>
                <li class="nav-item {{getActiveClass(['add_on.index'])}}">
                    <a href="{{route('add_on.index')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-cheese"></i></div><span>Add-on</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'restaurant_manager' || Session::get('test') == 'restaurant_admin') { ?>
                <li class="nav-item {{getActiveClass(['coupons.index'])}}">
                    <a href="{{route('coupons.index')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-tag"></i></div><span>Coupons</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'restaurant_admin') { ?>
                <li class="nav-item {{getActiveClass(['admin_order'])}} {{getActiveClass(['getOrders'])}}">
                    <a href="{{route('admin_order')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-utensils"></i></div><span>Orders</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'restaurant_manager') { ?>
                <li class="nav-item {{getActiveClass(['manager_order'])}} {{getActiveClass(['getManagerOrders'])}}">
                    <a href="{{route('manager_order')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-utensils"></i></div><span>Orders</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'kitchen_owner') { ?>
                <li class="nav-item {{getActiveClass(['kitchen_order'])}} {{getActiveClass(['getKitchenOrders'])}}">
                    <a href="{{route('kitchen_order')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-utensils"></i></div><span>Orders</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'restaurant_admin' ||  Session::get('test') == 'restaurant_manager') { ?>
                <li class="nav-item {{getActiveClass(['Invoice'])}} {{getActiveClass(['InvoiceData'])}}">
                    <a href="{{route('Invoice')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-file-invoice"></i></div><span>Invoice</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'restaurant_admin' ||  Session::get('test') == 'restaurant_manager') { ?>
                <li class="nav-item {{getActiveClass(['KOT'])}} {{getActiveClass(['KOTData'])}} {{getActiveClass(['KOTOrderData'])}}">
                    <a href="{{route('KOT')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-book"></i></div><span>KOT</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'restaurant_admin') { ?>
                <li class="nav-item {{getActiveClass(['report'])}}">
                    <a href="{{route('report')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-list"></i></div><span>Report</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'restaurant_admin') { ?>
                <li class="nav-item {{getActiveClass(['sales.chart'])}}">
                    <a href="{{route('sales.chart')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-list-alt"></i></div><span>Report Chart</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (Session::get('test') == 'restaurant_waiter') { ?>
                <li class="nav-item {{getActiveClass(['OrderMenu'])}} {{getActiveClass(['getMenu'])}} {{getActiveClass(['getItems'])}}">
                    <a href="{{route('OrderMenu')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-cart-plus"></i></div><span>Order Menu</span>
                    </a>
                </li>

                <li class="nav-item {{getActiveClass(['OrderDetails'])}} {{getActiveClass(['getOrderDetails'])}}">
                    <a href="{{route('OrderDetails')}}" class="nav-link">
                        <div class="sidebar-menu-img"><i class="fas fa-list"></i></div><span>Order Details</span>
                    </a>
                </li>
            <?php } ?>


            <li class="nav-item p-2 mt-5 absolute-bottom-left w-100 nav-logout">
                <a href="{{route('logout')}}" class="btn btn-primary btn-lg btn-block btn-icon-split">
                    <div class="sidebar-menu-img">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17.726" height="19.098" viewBox="0 0 17.726 19.098">
                            <g transform="translate(-17.6)">
                                <g transform="translate(17.6 0)">
                                    <g transform="translate(0 0)">
                                        <path d="M133.193,133.823l.008-.008a.3.3,0,0,0,.031-.043s0-.008.008-.012.019-.031.027-.047a.014.014,0,0,1,0-.008c.008-.016.016-.031.023-.051,0,0,0,0,0-.008s.012-.035.02-.055c0,0,0-.008,0-.008a.273.273,0,0,0,.012-.055.035.035,0,0,1,0-.019c0-.016,0-.031.008-.047a.566.566,0,0,0,0-.133.2.2,0,0,0-.008-.047.035.035,0,0,0,0-.02c0-.019-.008-.035-.012-.055,0,0,0-.008,0-.008a.3.3,0,0,0-.02-.055s0,0,0-.008-.016-.035-.023-.051a.014.014,0,0,0,0-.008.291.291,0,0,0-.027-.047s0-.008-.008-.012a.408.408,0,0,0-.031-.043l-.008-.008a.555.555,0,0,0-.047-.051l-3.855-3.852a.67.67,0,0,0-.947.947l2.713,2.713H120.271a.669.669,0,1,0,0,1.337h10.791l-2.694,2.694a.668.668,0,1,0,.943.947l3.832-3.832C133.162,133.854,133.178,133.839,133.193,133.823Z" transform="translate(-115.624 -123.851)" fill="#fff" />
                                        <path d="M21.229,1.337h4.99a.669.669,0,1,0,0-1.337h-4.99A3.635,3.635,0,0,0,17.6,3.629V15.469A3.635,3.635,0,0,0,21.229,19.1h4.908a.669.669,0,1,0,0-1.337H21.229a2.3,2.3,0,0,1-2.292-2.292V3.629A2.3,2.3,0,0,1,21.229,1.337Z" transform="translate(-17.6 0)" fill="#fff" />
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <span>@lang('common.logout')</span>
                </a>
            </li>
        </ul>
    </aside>
</div>