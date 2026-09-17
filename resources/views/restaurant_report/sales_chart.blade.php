@extends('layouts.admin')
<style>
    @media screen and (max-width: 768px) {
        #salesOverTimeChart {
            width: 0;
            height: 200px;
        }

        #ordersByHourChart,
        #tableOccupancyChart,
        #dailyRevenueChart,
        #revenueChart,
        #topItemsChart {
            width: 0;
        }

        #container .container-div {
            margin-bottom: 20px;
        }
    }

    #cancelledRejectedChart {
        height: 300px !important;
        width: 300px !important;
    }
</style>
@section('content')

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Restaurant Report Management</h1>
        <p class="text-gray-500 text-sm md:text-base">View and track your restaurant’s performance.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 md:ml-auto md:ml-0">
        <a href="{{ route('sales.chart.export', request()->query()) }}">
            <button class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export CSV</span>
            </button>
        </a>
    </div>
</div>

<!-- Alerts -->
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

<div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
    <div class="bg-white rounded-card p-4">
        <div class="md:items-center gap-3">
            <!-- Search Input -->
            <form action="{{ route('sales.chart') }}" method="GET" id="reportForm">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <div class="form-group mb-4 flex-1">
                        <label class="block text-foreground text-sm font-medium mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') ?? \Carbon\Carbon::now()->subDays(6)->toDateString() }}"
                            class="px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary w-full">
                    </div>
                    <div class="form-group mb-4 flex-1">
                        <label class="block text-foreground text-sm font-medium mb-2">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') ?? \Carbon\Carbon::now()->toDateString() }}"
                            class="px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary w-full">
                    </div>
                    <div class="mt-4 text-right flex gap-2">
                        <button type="submit" class="items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                            <span>Apply Filters</span>
                        </button>
                        <a href="{{ route('sales.chart') }}">
                            <button type="button" class="items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                                <span>Reset</span>
                            </button>
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
    <!-- Sales Over Time -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Sales Over Time</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="salesOverTimeChart" height="150"></canvas>
            </div>
        </div>
    </div>



    <!-- Daily Revenue Comparison -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Daily Revenue Comparisons</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="dailyRevenueChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
    <!-- Top Selling Items -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Top Selling Items</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="topItemsChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue by Category -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Revenue by Category</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="revenueChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
    <!-- Orders by Times -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Orders by Times</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="ordersByHourChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Table Occupancy Rate -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Table Occupancy Rate</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="tableOccupancyChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
    <!-- Payment Method Distribution -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Payment Method Distribution</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="paymentMethodChart" width="150" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Cancelled or Rejected Orders -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Cancelled or Rejected Orders</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full overflow-x-auto">
                <canvas id="cancelledRejectedChart"></canvas>
            </div>
        </div>
    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    // 1. Daily Sales + Order Count
    const ctx = document.getElementById('salesOverTimeChart').getContext('2d');

    const salesOverTimeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels) ?>,
            datasets: [{
                    label: 'Total Sales (₹)',
                    data: <?php echo json_encode($salesTotals) ?>,
                    yAxisID: 'ySales',
                    fill: false,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    tension: 0.3
                },
                {
                    label: 'Total Orders',
                    data: <?php echo json_encode($orderTotals) ?>,
                    yAxisID: 'yOrders',
                    fill: false,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                ySales: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Sales (₹)'
                    }
                },
                yOrders: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: true,
                        text: 'Orders Count'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Sales & Orders from {{ request("start_date") ?? now()->subDays(6)->format("Y-m-d") }} to {{ request("end_date") ?? now()->format("Y-m-d") }}'
                }
            }
        }
    });

    // 3. Revenue by Category
    const categoryNames = <?php echo json_encode($categoryNames) ?>;
    const categoryRevenues = <?php echo json_encode($categoryRevenues) ?>;

    const revenue_by_category = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenue_by_category, {
        type: 'bar',
        data: {
            labels: categoryNames,
            datasets: [{
                label: 'Revenue by Category (₹)',
                data: categoryRevenues,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Revenue'
                    }
                },
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Category from {{ request("start_date") ?? now()->subDays(6)->format("Y-m-d") }} to {{ request("end_date") ?? now()->format("Y-m-d") }}'
                }
            }
        }
    });


    // 2. Top Selling Items
    const itemNames = <?php echo json_encode($itemNames); ?>;
    const itemQuantities = <?php echo json_encode($itemQuantities); ?>;

    const topItemsCtx = document.getElementById('topItemsChart').getContext('2d');

    const topItemsChart = new Chart(topItemsCtx, {
        type: 'bar', // Change to 'horizontalBar' for horizontal chart
        data: {
            labels: itemNames,
            datasets: [{
                label: 'Top Selling Items (Qty)',
                data: itemQuantities,
                backgroundColor: 'rgba(255, 159, 64, 0.6)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // make horizontal bar chart, remove if you prefer vertical
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantity Sold'
                    }
                },
                // y: {
                //     title: {
                //         display: true,
                //         text: 'Item Name'
                //     }
                // }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Top 10 Selling Items from {{ request("start_date") ?? now()->subDays(6)->format("Y-m-d") }} to {{ request("end_date") ?? now()->format("Y-m-d") }}'
                },
                legend: {
                    display: true
                }
            }
        }
    });

    // 4. Payment Method Distribution
    const methodLabels = <?php echo json_encode($methodLabels) ?>;
    const methodCounts = <?php echo json_encode($methodCounts) ?>;
    const methodTotalSale = <?php echo json_encode($methodTotalSale) ?>;

    const paymentMethod = document.getElementById('paymentMethodChart').getContext('2d');
    const paymentMethodChart = new Chart(paymentMethod, {
        type: 'bar', // switch from pie to bar
        data: {
            labels: methodLabels,
            datasets: [{
                    label: 'Order Count',
                    data: methodCounts,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Total Sale (₹)',
                    data: methodTotalSale,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    yAxisID: 'y1' // separate axis for sales so scale looks correct
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Payment Method: Orders & Sales from {{ request("start_date") ?? now()->subDays(6)->format("Y-m-d") }} to {{ request("end_date") ?? now()->format("Y-m-d") }}'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Order Count'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: true,
                        text: 'Total Sale (₹)'
                    }
                }
            }
        }
    });


    // 5. Orders by Time of Day
    const hourLabels = <?php echo json_encode(($hourLabels)) ?>;
    const orderCounts = <?php echo json_encode(($orderCounts)) ?>;

    const ordersByHour = document.getElementById('ordersByHourChart').getContext('2d');
    const ordersByHourChart = new Chart(ordersByHour, {
        type: 'bar',
        data: {
            labels: hourLabels,
            datasets: [{
                label: 'Orders',
                data: orderCounts,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Orders by Time from {{ request("start_date") ?? now()->subDays(6)->format("Y-m-d") }} to {{ request("end_date") ?? now()->format("Y-m-d") }}'
                },
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Orders'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Time of Day'
                    }
                }
            }
        }
    });

    // 6. Table Occupancy Rate
    const occupancyLabels = <?php echo json_encode(($dates)) ?>;
    const occupancyRates = <?php echo json_encode(($occupancyRates)) ?>;

    const ctxOccupancy = document.getElementById('tableOccupancyChart').getContext('2d');
    new Chart(ctxOccupancy, {
        type: 'line',
        data: {
            labels: occupancyLabels,
            datasets: [{
                label: 'Table Occupancy Rate (%)',
                data: occupancyRates,
                backgroundColor: 'rgba(75, 192, 192, 0.3)',
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: true,
                tension: 0.3,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Table Occupancy Rate from {{ request("start_date") ?? now()->subDays(6)->format("Y-m-d") }} to {{ request("end_date") ?? now()->format("Y-m-d") }}'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Occupancy Rate (%)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            }
        }
    });

    // 7. Cancelled / Rejected
    const cancelledRejectedLabels = <?php echo json_encode($cancelledRejectedLabels); ?>;
    const cancelledRejectedCounts = <?php echo json_encode($cancelledRejectedCounts); ?>;

    const ctxCancelled = document.getElementById('cancelledRejectedChart').getContext('2d');
    const cancelledRejectedChart = new Chart(ctxCancelled, {
        type: 'doughnut',
        data: {
            labels: cancelledRejectedLabels,
            datasets: [{
                data: cancelledRejectedCounts,
                backgroundColor: ['#f87171', '#facc15'],
                borderColor: ['#ef4444', '#eab308'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Cancelled or Rejected Orders from {{ request("start_date") ?? now()->subDays(6)->format("Y-m-d") }} to {{ request("end_date") ?? now()->format("Y-m-d") }}'
                }
            }
        }
    });

    // 8. Daily Revenue
    const revenueDates = <?php echo json_encode($revenueDates); ?>;
    const revenueValues = <?php echo json_encode($revenueValues); ?>;

    const ctxRevenue = document.getElementById('dailyRevenueChart').getContext('2d');
    const dailyRevenueChart = new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: revenueDates,
            datasets: [{
                label: 'Daily Revenue (₹)',
                data: revenueValues,
                fill: true,
                tension: 0.4,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#1e40af',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Daily Revenue Comparison from {{ request("start_date") ?? now()->subDays(6)->format("Y-m-d") }} to {{ request("end_date") ?? now()->format("Y-m-d") }}'
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value;
                        }
                    }
                }
            }
        }
    });

    $(document).ready(function() {
        $('.main-sidebar').attr('tabindex', '1');
        $('.main-sidebar').css({
            'overflow': 'auto',
            'outline': 'none'
        });

        // Run on load
        adjustSidebar();

        // Also run on window resize to keep it responsive
        $(window).resize(function() {
            adjustSidebar();
        });

        function adjustSidebar() {
            if ($(window).width() < 768) {
                $('body').addClass('sidebar-gone');
                localStorage.setItem('toggle', 'false');
                const toggle = localStorage.getItem('toggle');

                if (toggle === 'false') {
                    $('body').removeClass('sidebar-mini');
                    localStorage.setItem('toggle', 'false');
                    $('.main-sidebar').css('left', '-250px');
                }

                $('[data-toggle="sidebar"]').on('click', function() {
                    $('body').removeClass('sidebar-mini');
                    localStorage.setItem('toggle', 'false');
                    $('.main-sidebar').css('left', '0');
                });

                console.log('Sidebar toggle on load:', toggle);
            } else {
                $('.main-sidebar').css('left', '0'); // Reset for desktop
            }


            if ($(window).width() > 992) {
                $('[data-toggle="sidebar"]').on('click', function() {
                    $('body').toggleClass('sidebar-mini'); // Toggle the class immediately

                    // Save new state to localStorage
                    if ($('body').hasClass('sidebar-mini')) {
                        localStorage.setItem('toggle', 'true');
                    } else {
                        localStorage.setItem('toggle', 'false');
                    }
                });

            }

            $("[data-toggle='sidebar']").click(function(e) {
                e.stopPropagation(); // Prevent this click from bubbling to the document

                var body = $("body"),
                    w = $(window);

                body.removeClass("search-show search-gone");

                if (w.outerWidth() <= 1024) {
                    if (body.hasClass("sidebar-gone")) {
                        body.removeClass("sidebar-gone").addClass("sidebar-show");
                    } else {
                        body.removeClass("sidebar-show").addClass("sidebar-gone");
                    }
                    update_sidebar_nicescroll();
                } else {
                    if (body.hasClass("sidebar-mini")) {
                        toggle_sidebar_mini(false);
                    } else {
                        toggle_sidebar_mini(true);
                    }
                }

                return false;
            });

            // ✅ Hide sidebar on outside click
            $(document).on("click", function(e) {

                var body = $("body");
                var target = $(e.target);

                // For mobile (sidebar-show)
                if (body.hasClass("sidebar-show") && !target.closest(".main-sidebar, [data-toggle='sidebar']").length) {
                    body.removeClass("sidebar-show").addClass("sidebar-gone");
                    $('.main-sidebar').css('left', '-250px');
                }

                // For desktop (sidebar-mini)
                if (body.hasClass("sidebar-mini") && !target.closest(".main-sidebar, [data-toggle='sidebar']").length) {
                    toggle_sidebar_mini(false);
                }
            });

        }
    });
</script>
@endsection