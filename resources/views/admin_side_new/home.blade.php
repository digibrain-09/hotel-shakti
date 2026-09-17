@extends('layouts.admin')
@push('styles')
<style>
    @media screen and (max-width: 768px) {
        #salesOverTimeChart {
            width: 100%;
            height: 200px;
        }
    }
</style>
@endpush
@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <?php if (Session::get('test') == 'admin') { ?>
            <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Welcome Admin</h1>
        <?php } else if (Session::get('test') == 'restaurant_manager') { ?>
            <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Welcome Restaurant Manger</h1>
        <?php } else if (Session::get('test') == 'restaurant_admin') { ?>
            <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Welcome Restaurant Owner</h1>
        <?php } else if (Session::get('test') == 'restaurant_waiter') { ?>
            <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Welcome Restaurant Waiter</h1>
        <?php } else if (Session::get('test') == 'kitchen_owner') { ?>
            <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Welcome Restaurant Kitchen Owner</h1>
        <?php } ?>
        <p class="text-gray-500 text-sm md:text-base">A quick overview of real-time metrics and system activity.</p>
    </div>
</div>

@if(Session::get('test') == 'restaurant_admin')
<div class="alertordermsg"></div>
<div id="alertSoundContainer"></div>
<div id="PrintalertSoundContainer"></div>
<div class="alertpaymentmsg"></div>
<div class="alertTakeAwayordermsg"></div>
<div id="alertTakeAwaySound"></div>
<div class="alertreadytoservedmsg"></div>
<div id="ReadyToservedalertSoundContainer"></div>
<div class="Waiteralertordermsg"></div>
<div id="alertSoundContainer_waiter"></div>
<div class="alertreadytoservedWaitermsg"></div>
<div id="ReadyToservedWaiteralertSoundContainer"></div>
@endif
@if(Session::get('test') == 'restaurant_manager')
<div class="Manageralertmsg"></div>
<div id="alertSoundContainer_manager"></div>
<div class="alertreadytoservedmsg"></div>
<div id="ReadyToservedalertSoundContainer"></div>
@endif
@if(Session::get('test') == 'restaurant_waiter')
<div class="Waiteralertordermsg"></div>
<div id="alertSoundContainer_waiter"></div>
<div class="alertreadytoservedWaitermsg"></div>
<div id="ReadyToservedWaiteralertSoundContainer"></div>
@endif
@if(Session::get('test') == 'kitchen_owner')
<div class="Kitchenalertmsg"></div>
<div id="KitchenalertSoundContainer"></div>
<div class="KitchenTakeAwayalertmsg"></div>
<div id="KitchenTakeAwayalertSoundContainer"></div>
@endif

@if(Session::get('test') == 'restaurant_admin')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
    <!-- Left Column - Restaurant Info & Floor Selection -->
    <div class="lg:col-span-12 space-y-4 md:space-y-6">
        <!-- Restaurant Information -->
        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Restaurant Information</h3>
            <div class="bg-white rounded-card p-5">
                @foreach($restaurant as $data)
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ ('restaurant_logo/' . $data->logo) }}"
                        alt="Restaurant interior" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-foreground text-lg font-bold mb-1" style="text-transform: capitalize;">{{ $data->restaurant_name }}</h4>
                        <p class="text-gray-500 text-sm">{{ $data->address }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-card">
                        <p class="text-foreground text-2xl font-bold">{{ $tableCount ?? 0 }}</p>
                        <p class="text-gray-500 text-xs">Total Tables</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-card">
                        <p class="text-foreground text-2xl font-bold">{{ $availableTableCount ?? 0 }}</p>
                        <p class="text-gray-500 text-xs">Available</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Stats Summary Card -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6 md:mb-8 mt-5">
    <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Orders Summary</h3>
    <div class="bg-white rounded-card p-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-lime rounded-icon flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="shopping-bag" class="w-7 h-7 text-foreground"></i>
                </div>
                <p class="text-foreground text-3xl font-extrabold mb-1">{{ $totalOrders }}</p>
                <p class="text-gray-500 text-sm">Total Orders</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-teal rounded-icon flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="trending-up" class="w-7 h-7 text-foreground"></i>
                </div>
                <p class="text-foreground text-3xl font-extrabold mb-1"> ₹{{ number_format($totalRevenue, 2) }}</p>
                <p class="text-gray-500 text-sm">Total Revenue</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-peach rounded-icon flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="bar-chart-3" class="w-7 h-7 text-foreground"></i>
                </div>
                <p class="text-foreground text-3xl font-extrabold mb-1">₹{{ number_format($averageOrderValue, 2) }}</p>
                <p class="text-gray-500 text-sm">Average Order Value</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-1 gap-4 md:gap-6 mb-6 md:mb-8 mt-5">
    <!-- Sales Over Time -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Sales Over Time</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="salesOverTimeChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@elseif(Session::get('test') == 'restaurant_manager')

<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
    <!-- Left Column - Restaurant Info & Floor Selection -->
    <div class="lg:col-span-12 space-y-4 md:space-y-6">
        <!-- Restaurant Information -->
        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Restaurant Information</h3>
            <div class="bg-white rounded-card p-5">
                @foreach($restaurant as $data)
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ ('restaurant_logo/' . $data->logo) }}"
                        alt="Restaurant interior" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-foreground text-lg font-bold mb-1" style="text-transform: capitalize;">{{ $data->restaurant_name }}</h4>
                        <p class="text-gray-500 text-sm">{{ $data->address }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-card">
                        <p class="text-foreground text-2xl font-bold">{{ $tableCount ?? 0 }}</p>
                        <p class="text-gray-500 text-xs">Total Tables</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-card">
                        <p class="text-foreground text-2xl font-bold">{{ $availableTableCount ?? 0 }}</p>
                        <p class="text-gray-500 text-xs">Available</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Stats Summary Card -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6 md:mb-8 mt-5">
    <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Orders Summary</h3>
    <div class="bg-white rounded-card p-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-lime rounded-icon flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="shopping-bag" class="w-7 h-7 text-foreground"></i>
                </div>
                <p class="text-foreground text-3xl font-extrabold mb-1">{{ $totalOrders }}</p>
                <p class="text-gray-500 text-sm">Total Orders</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-teal rounded-icon flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="trending-up" class="w-7 h-7 text-foreground"></i>
                </div>
                <p class="text-foreground text-3xl font-extrabold mb-1"> ₹{{ number_format($totalRevenue, 2) }}</p>
                <p class="text-gray-500 text-sm">Total Revenue</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-peach rounded-icon flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="bar-chart-3" class="w-7 h-7 text-foreground"></i>
                </div>
                <p class="text-foreground text-3xl font-extrabold mb-1">₹{{ number_format($averageOrderValue, 2) }}</p>
                <p class="text-gray-500 text-sm">Average Order Value</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-1 gap-4 md:gap-6 mb-6 md:mb-8 mt-5">
    <!-- Sales Over Time -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Sales Over Time</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="salesOverTimeChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

@elseif(Session::get('test') == 'restaurant_waiter')

<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
    <!-- Left Column - Restaurant Info & Floor Selection -->
    <div class="lg:col-span-12 space-y-4 md:space-y-6">
        <!-- Restaurant Information -->
        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Restaurant Information</h3>
            <div class="bg-white rounded-card p-5">
                @foreach($restaurant as $data)
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ ('restaurant_logo/' . $data->logo) }}"
                        alt="Restaurant interior" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-foreground text-lg font-bold mb-1" style="text-transform: capitalize;">{{ $data->restaurant_name }}</h4>
                        <p class="text-gray-500 text-sm">{{ $data->address }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-card">
                        <p class="text-foreground text-2xl font-bold">{{ $tableCount ?? 0 }}</p>
                        <p class="text-gray-500 text-xs">Total Tables</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-card">
                        <p class="text-foreground text-2xl font-bold">{{ $availableTableCount ?? 0 }}</p>
                        <p class="text-gray-500 text-xs">Available</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 mt-5">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <a href="{{route('OrderMenu')}}">
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-lime rounded-icon flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="shopping-bag" class="w-7 h-7 text-foreground"></i>
                </div>
                <p class="text-foreground text-lg font-extrabold mb-1">Add Order</p>
            </div>
        </a>
    </div>

    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <a href="{{route('OrderDetails')}}">
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-teal rounded-icon flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="list" class="w-7 h-7 text-foreground"></i>
                </div>
                <p class="text-foreground text-lg font-extrabold mb-1">View Order</p>
            </div>
        </a>
    </div>

    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <a href="{{route('OrderDetails')}}">
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-peach rounded-icon flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="file-text" class="w-7 h-7 text-foreground"></i>
                </div>
                <p class="text-foreground text-lg font-extrabold mb-1">Generate Bill</p>
            </div>
        </a>
    </div>

</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-1 gap-4 md:gap-6 mb-6 md:mb-8 mt-5">
    <!-- Sales Over Time -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Sales Over Time</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="salesOverTimeChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>


@elseif(Session::get('test') == 'kitchen_owner')

<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
    <!-- Left Column - Restaurant Info & Floor Selection -->
    <div class="lg:col-span-12 space-y-4 md:space-y-6">
        <!-- Restaurant Information -->
        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Restaurant Information</h3>
            <div class="bg-white rounded-card p-5">
                @foreach($restaurant as $data)
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ ('restaurant_logo/' . $data->logo) }}"
                        alt="Restaurant interior" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-foreground text-lg font-bold mb-1" style="text-transform: capitalize;">{{ $data->restaurant_name }}</h4>
                        <p class="text-gray-500 text-sm">{{ $data->address }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-card">
                        <p class="text-foreground text-2xl font-bold">{{ $tableCount ?? 0 }}</p>
                        <p class="text-gray-500 text-xs">Total Tables</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-card">
                        <p class="text-foreground text-2xl font-bold">{{ $availableTableCount ?? 0 }}</p>
                        <p class="text-gray-500 text-xs">Available</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-1 gap-4 md:gap-6 mb-6 md:mb-8 mt-5">
    <!-- Sales Over Time -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Sales Over Time</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="salesOverTimeChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

@elseif(Session::get('test') == 'admin')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total Restaurants</h3>
        <div class="bg-white rounded-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-foreground text-4xl font-extrabold mb-1">{{ $restaurantCount ?? 0 }}</p>
                </div>
                <div class="w-16 h-16 bg-accent-lime rounded-icon flex items-center justify-center">
                    <i data-lucide="store" class="w-7 h-7 text-foreground"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total Tables</h3>
        <div class="bg-white rounded-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-foreground text-4xl font-extrabold mb-1">{{ $TableCount ?? 0 }}</p>
                </div>
                <div class="w-16 h-16 bg-error-light rounded-icon flex items-center justify-center">
                    <i data-lucide="table" class="w-7 h-7 text-error-dark"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total Categories</h3>
        <div class="bg-white rounded-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-foreground text-4xl font-extrabold mb-1">{{ $CategoryCount ?? 0 }}</p>
                </div>
                <div class="w-16 h-16 bg-accent-teal rounded-icon flex items-center justify-center">
                    <i data-lucide="list" class="w-7 h-7 text-foreground"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total Items</h3>
        <div class="bg-white rounded-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-foreground text-4xl font-extrabold mb-1">{{ $ItemCount ?? 0 }}</p>
                </div>
                <div class="w-16 h-16 bg-accent-peach rounded-icon flex items-center justify-center">
                    <i data-lucide="shopping-cart" class="w-7 h-7 text-foreground"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Growth Chart -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
    <h3 class="text-foreground text-lg font-bold ml-3 mb-4">
        Restaurant Growth Over Time
    </h3>

    <div class="bg-white rounded-card p-4 md:p-5">
        <div class="w-full overflow-x-auto">
            <div class="min-w-70 h-55 sm:h-62 md:h-70">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Restaurant Table (Desktop) -->
<div class="hidden md:block">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
            <h3 class="text-foreground text-lg font-bold">Restaurant Locations</h3>
            <a href="{{route('restaurant.index')}}" class="text-primary hover:text-primary-hover text-sm font-medium cursor-pointer">View All</a>
        </div>
        <div class="bg-white rounded-card overflow-hidden">
            <table id="dataTable" class="w-full table-fixed">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Restaurant</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Location</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Mobile No.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($allRestaurant as $index => $restaurant)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ ('restaurant_logo/' . $restaurant->logo) }}" alt="restaurant image" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-foreground truncate" style="text-transform: capitalize;">{{ $restaurant->restaurant_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell"><span class="truncate block">{{ $restaurant->address }}</span></td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $restaurant->email }}</td>
                        <td class="px-4 py-4">{{ $restaurant->phone }}</td>
                    </tr>
                    @endforeach
                    @if (!$matchFound)
                    <tr>
                        <td class="text-center pt-3 pb-3" colspan="6" style="background-color:yellow">Restaurant not found.</td>
                    </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mobile Cards -->
<div id="mobileCards" class="md:hidden space-y-3">
    @php
    $matchFound = false; // Flag to track if a match is found
    @endphp
    @foreach ($allRestaurant as $index => $restaurant)
    @php
    $matchFound = true; // Set the flag to true
    @endphp
    <div class="bg-white rounded-2xl p-4 border border-gray-100">
        <div class="flex items-center gap-3 mb-3 min-w-0">
            <img src="{{ ('restaurant_logo/' . $restaurant->logo) }}" alt="restaurant image" class="w-12 h-12 rounded-button object-cover flex-shrink-0">
            <div class="flex-1 min-w-0">
                <h4 class="text-foreground font-semibold truncate" style="text-transform: capitalize;">{{ $restaurant->restaurant_name }}</h4>
                <p class="text-gray-500 text-xs truncate">{{ $restaurant->address }}</p>
            </div>
        </div>
    </div>
    @endforeach
    @if (!$matchFound)
    <p class="text-center pt-3 pb-3" style="background-color:yellow">Restaurant not found.</p>
    @endif
</div>

@endif

@if(Session::get('test') == 'restaurant_admin' || Session::get('test') == 'restaurant_manager' || Session::get('test') == 'restaurant_waiter' || Session::get('test') == 'kitchen_owner')
@push('scripts')
<script>
    // 1. Daily Sales + Order Count
    const ctx = document.getElementById('salesOverTimeChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels) ?>,
            datasets: [{
                    label: 'Total Sales (₹)',
                    data: <?php echo json_encode($salesTotals) ?>,
                    yAxisID: 'ySales',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 3
                },
                {
                    label: 'Total Orders',
                    data: <?php echo json_encode($orderTotals) ?>,
                    yAxisID: 'yOrders',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // 🔥 IMPORTANT
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Sales & Orders'
                }
            },
            scales: {
                ySales: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                yOrders: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    }
                },
                x: {
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 6 // 🔥 prevents label crowding on mobile
                    }
                }
            }
        }
    });
</script>
@endpush
@endif
@if(Session::get('test') == 'admin')
@push('scripts')
<script>
    const growthCtx = document.getElementById('growthChart').getContext('2d');

    new Chart(growthCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($Reslabels) ?>,
            datasets: [{
                label: 'Total Restaurants',
                data: <?php echo json_encode($Resdata) ?>,
                borderColor: '#EF3F09',
                backgroundColor: 'rgba(239, 63, 9, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#EF3F09',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush
@endif
@endsection