@extends('layouts.admin')
@section('content')


<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Restaurant Report Management</h1>
        <p class="text-gray-500 text-sm md:text-base">View and track your restaurant’s performance.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 md:ml-auto md:ml-0">
        <form action="{{ route('exportReport') }}" method="GET" id="exportForm">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="order_type" value="{{ request('order_type') }}">
            <input type="hidden" name="payment_mode" value="{{ request('payment_mode') }}">
            <input type="hidden" name="order_confirm_by" value="{{ request('order_confirm_by') }}">
            <button type="submit" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                <i data-lucide="download" class="w-4 h-4 "></i>
                <span>Export</span>
            </button>
        </form>
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

<!-- Filters and Search -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
    <div class="bg-white rounded-card p-4">
        <div class="md:items-center gap-3">
            <!-- Search Input -->
            <form action="{{ route('report') }}" method="GET" id="reportForm">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <div class="form-group mb-4 flex-1">
                        <label class="block text-foreground text-sm font-medium mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" max="{{ now()->toDateString() }}"
                            class="px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary w-full">
                    </div>
                    <div class="form-group mb-4 flex-1">
                        <label class="block text-foreground text-sm font-medium mb-2">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" max="{{ now()->toDateString() }}"
                            class="px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary w-full">
                    </div>
                    <div class="mt-4 text-right flex gap-2">

                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                    <div>
                        <label class="block text-foreground text-sm font-medium mb-2">order Type</label>
                        <!-- <div class="relative">
                            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search table/CustomerId" class="w-full px-4 py-2.5 border border-border rounded-button text-foreground placeholder-gray-500 focus:outline-none focus:border-primary">
                            <i data-lucide="search" class="w-4 h-4 text-gray-500 absolute right-3 top-1/2 transform -translate-y-1/2"></i>
                        </div> -->
                        <select id="order_type" name="order_type" class="w-full px-4 py-2.5 border border-border rounded-button text-foreground focus:outline-none focus:border-primary">
                            <option value="">Select Type</option>
                            <option value="Dine In" {{ request('order_type') == 'Dine In' ? 'selected' : '' }}>Dine In</option>
                            <option value="Take Away" {{ request('order_type') == 'Take Away' ? 'selected' : '' }}>Take Away</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-foreground text-sm font-medium mb-2">Order Status</label>
                        <select id="status" name="status" class="w-full px-4 py-2.5 border border-border rounded-button text-foreground focus:outline-none focus:border-primary">
                            <option value="">Select Status</option>
                            <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-foreground text-sm font-medium mb-2">Payment Mode</label>
                        <select id="payment_mode" name="payment_mode" class="w-full px-4 py-2.5 border border-border rounded-button text-foreground focus:outline-none focus:border-primary">
                            <option value="">Select</option>
                            <option value="cash" {{ request('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="upi" {{ request('payment_mode') == 'upi' ? 'selected' : '' }}>UPI</option>
                            <option value="card" {{ request('payment_mode') == 'card' ? 'selected' : '' }}>Card</option>
                        </select>
                    </div>
                    <!-- <div>
                        <label class="block text-foreground text-sm font-medium mb-2">Order Confirm By</label>
                        <select id="order_confirm_by" name="order_confirm_by" class="w-full px-4 py-2.5 border border-border rounded-button text-foreground focus:outline-none focus:border-primary">
                            <option value="">Select</option>
                            <option value="manager" {{ request('order_confirm_by') == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="waiter" {{ request('order_confirm_by') == 'waiter' ? 'selected' : '' }}>Waiter</option>
                        </select>
                    </div> -->
                </div>

                <div class="flex flex-col md:flex-row md:items-center gap-3 mt-5">
                    <button type="submit" class="w-full items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                        <span>Apply Filters</span>
                    </button>
                    <a href="{{ route('report') }}" type="button" class="w-full text-center items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                        <span>Reset</span>
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Stats Summary Card -->
@if($all_orders->isNotEmpty())
<div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6 md:mb-8">
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
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8 mt-5">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Grand Total</h3>
        <div class="bg-white rounded-card p-5">
            <div class="items-center justify-between">
                <div class="w-16 h-16 bg-accent-lime rounded-icon flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-7 h-7 text-foreground"></i>
                </div>
                <div>
                    <p class="text-foreground text-2xl font-extrabold mt-2">₹{{ number_format($grandTotal, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total CGST</h3>
        <div class="bg-white rounded-card p-5">
            <div class="items-center justify-between">
                <div class="w-16 h-16 bg-accent-teal rounded-icon flex items-center justify-center">
                    <i data-lucide="bar-chart" class="w-7 h-7 text-foreground"></i>
                </div>
                <div>
                    <p class="text-foreground text-2xl font-extrabold mt-2">₹{{ number_format($totalCGST, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total SGST</h3>
        <div class="bg-white rounded-card p-5">
            <div class="items-center justify-between">
                <div class="w-16 h-16 bg-accent-peach rounded-icon flex items-center justify-center">
                    <i data-lucide="bar-chart-2" class="w-7 h-7 text-foreground"></i>
                </div>
                <div>
                    <p class="text-foreground text-2xl font-extrabold mt-2">₹{{ number_format($totalSGST, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total Taxable Amount</h3>
        <div class="bg-white rounded-card p-5">
            <div class="items-center justify-between">
                <div class="w-16 h-16 bg-accent-lime rounded-icon flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-7 h-7 text-foreground"></i>
                </div>
                <div>
                    <p class="text-foreground text-2xl font-extrabold mt-2">₹{{ number_format($totalTaxableAmount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Orders Table -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">All Orders Data</h3>
    </div>
    <div class="bg-white rounded-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Customer</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Type</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Created</th>
                        <!-- <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Confirm By</th> -->
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Status</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Payment Mode</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Total Amount</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">CGST</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">SGST</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Total Tax</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Taxable Amount</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @foreach($all_orders as $index => $data)
                    @php
                    $statusColor = $data->status === 'processed' ? 'orange' : ($data->status === 'completed' ? 'green' : '');
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-4 px-2">
                            <div>
                                <p class="text-foreground text-sm font-medium">{{ $data->customer_code }}</p>
                                <p class="text-gray-500 text-xs">{{ $data->phone }}</p>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-gray-600 text-sm">{{ $data->order_type }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-gray-600 text-sm">{{ $data->latest_created_at }}</span>
                        </td>
                        <!-- <td class="py-4 px-2">
                            <span class="text-gray-600 text-sm" style="text-transform: capitalize;">{{ $data->order_confirm_by }}</span>
                        </td> -->
                        <td class="py-4 px-2">
                            <span class="text-white text-xs font-medium px-2 py-1 rounded-full" style="background-color:{{ $statusColor }};">{{ $data->status }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-gray-600 text-sm" style="text-transform: capitalize;">{{ $data->payment_mode }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">₹{{ number_format($data->total_amount, 2) }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">₹{{ number_format($data->cgst, 2) }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">₹{{ number_format($data->sgst, 2) }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">₹{{ number_format($data->cgst + $data->sgst, 2) }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">₹{{ number_format($data->taxable_amount, 2) }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-2">
                                <button onclick="openOrderModal('{{ $data->customer_code }}')" class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer">
                                    View Order
                                </button>
                                <a href="{{ asset('invoices/invoice_' . $data->invoice_url . '.pdf') }}" target="_blank" class="text-center px-3 py-1.5 border border-border text-foreground text-xs font-medium rounded-button hover:border-primary hover:text-primary transition-all duration-200 cursor-pointer">View Invoice</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Pagination -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 px-3 pb-4">
    <div class="text-sm text-gray-600"> Showing {{ $all_orders->firstItem() }} to {{ $all_orders->lastItem() }}
        of {{ $all_orders->total() }} entries</div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Previous --}}
        @if ($all_orders->onFirstPage())
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Previous
        </span>
        @else
        <a href="{{ $all_orders->previousPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Previous
        </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($all_orders->getUrlRange(1, $all_orders->lastPage()) as $page => $url)
        @if ($page == $all_orders->currentPage())
        <span class="px-3 py-2 bg-primary text-white rounded-lg text-sm">
            {{ $page }}
        </span>
        @else
        <a href="{{ $url }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            {{ $page }}
        </a>
        @endif
        @endforeach

        {{-- Next --}}
        @if ($all_orders->hasMorePages())
        <a href="{{ $all_orders->nextPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Next
        </a>
        @else
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Next
        </span>
        @endif
    </div>

</div>


<!-- Order Modal -->
@foreach ($all_orders as $index => $order)
<div id="OrderModal" class="custom-modal hidden OrderModal">
    <div class="custom-modal-backdrop" onclick="closeOrderModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Order History</h2>

        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Item</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Price</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Qty</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Add-ons</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Note</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Date & Time</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Sub</th>
                </tr>
            </thead>
            <tbody id="orderItemsBody">


            </tbody>
            <tfoot id="orderItemsTfoot">

            </tfoot>
        </table>

        <div class="mt-4 text-right">
            <button type="button" onclick="closeOrderModal()" class="px-4 py-2 border rounded mr-1 cursor-pointer">
                Cancel
            </button>
        </div>
    </div>
</div>
@endforeach


@else
<p colspan="6" class="text-center pt-3 pb-3" style="background-color:yellow">
    You have not any orders yet.
</p>
@endif


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const orders = @json($orders);

    function openOrderModal(customerCode) {
        let tbody = document.getElementById('orderItemsBody');
        let tfoot = document.getElementById('orderItemsTfoot');
        tbody.innerHTML = '';
        tfoot.innerHTML = '';
        let total = 0;
        let matchFound = false;

        orders.forEach(order => {
            if (order.customer_code === customerCode) {
                let items = JSON.parse(order.data);

                items.forEach(item => {
                    matchFound = true;
                    let addons = '-';
                    let addonTotal = 0;

                    if (item.addons) {
                        try {
                            let addonData = JSON.parse(item.addons);

                            if (Array.isArray(addonData) && addonData.length > 0) {
                                addons = addonData.map(a => a.name).join(', ');

                                addonData.forEach(a => {
                                    addonTotal += (parseFloat(a.price) || 0) * item.quantity;
                                });
                            }
                        } catch (e) {
                            console.warn('Invalid addon JSON', item.addons);
                        }
                    }

                    let subTotal = (item.price * item.quantity) + addonTotal;
                    total += subTotal;


                    tbody.innerHTML += `<tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="py-4 px-2">
                                <div>
                                    <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">${item.name}</p>
                                </div>
                            </td>
                            <td class="py-4 px-2">
                                <span class="text-foreground text-sm font-semibold">₹${item.price}</span>
                            </td>
                            <td class="py-4 px-2">
                                <p class="text-foreground text-sm font-medium">${item.quantity}</span>
                            </td>
                            <td class="py-4 px-2">
                                <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">${addons}</p>
                            </td>
                            <td class="py-4 px-2">
                                <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">${item.note ?? '-'}</p>
                            </td>
                            <td class="py-4 px-2">
                                <span class="text-foreground text-sm font-semibold">${item.created_at}</span>
                            </td>
                            <td class="py-4 px-2">
                                <span class="text-foreground text-sm font-semibold text-success">₹${subTotal.toFixed(2)}</span>
                            </td>
                        </tr>`;
                });

                if (!matchFound) {
                    tbody.innerHTML += `<tr><td class="text-center pt-3 pb-3" colspan="7" style="background-color:yellow">A customer has recently logged into their account but has not placed an order yet.</td></tr>`;
                }

                tfoot.innerHTML += `<tr>
                        <td colspan="6" class="text-right">
                            <h3><strong>Total:</strong></h3>
                        </td>
                        <td align="right" class="">
                            <h3><strong>₹${total.toFixed(2)}</strong></h3>
                        </td>
                    </tr>`;
            }
        });

        document.getElementById("OrderModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeOrderModal() {
        document.getElementById("OrderModal").classList.add("hidden");
        document.body.style.overflow = "";
    }
</script>
@endsection