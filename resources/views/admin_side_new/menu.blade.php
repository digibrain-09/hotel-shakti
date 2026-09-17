<aside id="sidebar"
    class="w-64 bg-muted fixed inset-y-0 left-0 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

    <!-- Logo -->
    <div class="px-6 py-4 border-b border-border">
        <div class="flex items-center justify-between">
            <div class=" items-center gap-4">
                <div class="w-50 h-32  items-center justify-center">
                    <?php if (Session::get('logo')) { ?>
                        <a href="{{route('home')}}"><img src="{{ url('restaurant_logo/' . Session::get('logo')) }}" /></a>

                    <?php } else { ?>
                        <a href="{{route('home')}}"><img src="{{asset('assets/img/final.png')}}" alt="scantable"></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto p-4 sidebar-nav">

        <p class="text-gray-500 text-xs font-semibold mb-3">Dashboard</p>

        <a href="{{route('home')}}"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 {{getActiveClass(['home'])}}">
            <i data-lucide="home" class="w-5 h-5"></i>
            <span>{{trans('common.dashboard')}}</span>
        </a>

        <p class="text-gray-500 text-xs font-semibold mt-6 mb-3">Other</p>

        <!-- ADMIN MENU -->
        @if(Session::get('test') == 'admin')
        <a href="{{route('restaurant.index')}}" class="menu-item {{getActiveClass(['restaurant.index'])}}">
            <i data-lucide="building" class="icon"></i><span>Restaurant</span>
        </a>

        <a href="{{route('restaurant_user.index')}}" class="menu-item {{getActiveClass(['restaurant_user.index'])}}">
            <i data-lucide="users" class="icon"></i><span>Restaurant Employee</span>
        </a>

        <a href="{{route('table.index')}}" class="menu-item {{getActiveClass(['table.index'])}}">
            <i data-lucide="table" class="icon"></i><span>Table</span>
        </a>
        @endif

        <!-- RESTAURANT ADMIN -->
        @if(Session::get('test') == 'restaurant_admin')
        <a href="{{route('restaurant.index')}}" class="menu-item {{getActiveClass(['restaurant.index'])}}">
            <i data-lucide="user" class="icon"></i><span>Account</span>
        </a>
        @endif

        <!-- COMMON FOR ADMIN + MANAGER + RESTAURANT ADMIN -->
        @if(in_array(Session::get('test'), ['admin','restaurant_manager','restaurant_admin','kitchen_owner']))

        <a href="{{route('category.index')}}" class="menu-item {{getActiveClass(['category.index'])}}">
            <i data-lucide="list" class="icon"></i><span>Category</span>
        </a>

        <a href="{{route('item.index')}}" class="menu-item {{getActiveClass(['item.index'])}}">
            <i data-lucide="shopping-cart" class="icon"></i><span>Items</span>
        </a>
        @endif

        <!-- ONLY MANAGER + ADMIN -->
        @if(in_array(Session::get('test'), ['restaurant_manager','restaurant_admin']))

        <a href="{{route('add_on.index')}}" class="menu-item {{getActiveClass(['add_on.index'])}}">
            <i data-lucide="plus-circle" class="icon"></i><span>Add-on</span>
        </a>

        <a href="{{route('coupons.index')}}" class="menu-item {{getActiveClass(['coupons.index'])}}">
            <i data-lucide="badge-percent" class="icon"></i><span>Coupons</span>
        </a>
        @endif

        <!-- ORDERS -->
        @if(Session::get('test') == 'restaurant_admin')
        <a href="{{route('admin_order')}}" class="menu-item {{getActiveClass(['admin_order'])}} {{getActiveClass(['getOrders'])}} {{getActiveClass(['TakeAwayOrder'])}} 
        {{getActiveClass(['getOrderItems'])}} {{getActiveClass(['ViewOrder'])}}">
            <i data-lucide="utensils" class="icon"></i><span>Orders</span>
        </a>
        <a href="{{route('OrderMenu')}}" class="menu-item {{getActiveClass(['OrderMenu'])}} {{getActiveClass(['getMenu'])}} {{getActiveClass(['getItems'])}}">
            <i data-lucide="shopping-basket" class="icon"></i><span>Add New Order</span>
        </a>
        @endif

        @if(Session::get('test') == 'restaurant_manager')
        <a href="{{route('manager_order')}}" class="menu-item {{getActiveClass(['manager_order'])}} {{getActiveClass(['getManagerOrders'])}}">
            <i data-lucide="utensils" class="icon"></i><span>Orders</span>
        </a>
        @endif

        <?php if (Session::get('test') == 'kitchen_owner') { ?>
            <a href="{{route('kitchen_order')}}" class="menu-item {{getActiveClass(['kitchen_order'])}} {{getActiveClass(['getKitchenOrders'])}}">
                <i data-lucide="utensils" class="icon"></i><span>Orders</span>
            </a>
        <?php } ?>


        <!-- Invoice -->
        @if(in_array(Session::get('test'), ['restaurant_admin','restaurant_manager']))
        <a href="{{route('Invoice')}}" class="menu-item {{getActiveClass(['Invoice'])}} {{getActiveClass(['InvoiceData'])}} {{getActiveClass(['ViewTakeAwayInvoice'])}}">
            <i data-lucide="file-text" class="icon"></i><span>Invoice</span>
        </a>
        @endif

        <!-- KOT -->
        <!-- @if(in_array(Session::get('test'), ['restaurant_admin','restaurant_manager','kitchen_owner']))
        <a href="{{route('KOT')}}" class="menu-item {{getActiveClass(['KOT'])}} {{getActiveClass(['KOTData'])}} {{getActiveClass(['KOTOrderData'])}}">
            <i data-lucide="book-open" class="icon"></i><span>KOT</span>
        </a>
        @endif -->

        <!-- Report -->
        @if(Session::get('test') == 'restaurant_admin')
        <a href="{{route('report')}}" class="menu-item {{getActiveClass(['report'])}}">
            <i data-lucide="bar-chart-2" class="icon"></i><span>Report</span>
        </a>

        <a href="{{route('sales.chart')}}" class="menu-item {{getActiveClass(['sales.chart'])}}">
            <i data-lucide="pie-chart" class="icon"></i><span>Report Chart</span>
        </a>
        @endif

        <!-- WAITER -->
        @if(Session::get('test') == 'restaurant_waiter')
        <a href="{{route('OrderMenu')}}" class="menu-item {{getActiveClass(['OrderMenu'])}} {{getActiveClass(['getMenu'])}} {{getActiveClass(['getItems'])}}">
            <i data-lucide="shopping-basket" class="icon"></i><span>Order Menu</span>
        </a>

        <a href="{{route('OrderDetails')}}" class="menu-item {{getActiveClass(['OrderDetails'])}} {{getActiveClass(['getOrderDetails'])}}">
            <i data-lucide="list-checks" class="icon"></i><span>Order Details</span>
        </a>
        @endif

    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-border">
        <a href="{{route('logout')}}" class="w-full flex items-center justify-center gap-2 py-3 bg-primary text-white rounded-lg">
            <i data-lucide="log-out" class="w-5 h-5"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        renderTables(currentFloor);

        // Show modal when clicking any navigation link
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('page-not-found-modal').classList.remove('hidden');
            });
        });

        // Accordion toggle
        document.querySelectorAll('[data-accordion]').forEach(button => {
            button.addEventListener('click', function() {
                const target = document.getElementById(this.dataset.accordion);
                const chevron = this.querySelector('[data-lucide="chevron-down"]');
                target.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
            });
        });
    });


    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
    }
</script>
@endpush