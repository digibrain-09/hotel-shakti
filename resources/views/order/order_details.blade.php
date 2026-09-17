@extends('layouts.admin')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .customer_tr span,
    .customer_tr p {
        color: red;
    }

    .promo-container {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 25px;
        padding: 5px 5px 5px 15px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);

    }

    .coupon-notice {
        background: #f9f9f9;
        padding: 15px;
        /* border: 1px solid #eee; */
        border-radius: 5px;
        font-size: 14px;
        color: #333;
        margin-top: 10px;
    }

    .coupon-notice a {
        color: #e7272d !important;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
    }

    .coupon-form {
        /* display: none; */
        margin-top: 15px;
    }

    .coupon-input {
        border: none;
        outline: none;
        font-size: 14px;
        flex: 1;
        padding: 10px;
        background: transparent;
        color: #555;
    }


    .apply-btn {
        border: none;
        outline: none;
        padding: 10px 20px;
        border-radius: 20px;
        color: #fff;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #28a745;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }
</style>

@section('content')
<!-- Breadcrumb Navigation -->
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{route('admin_order')}}" class="cursor-pointer">
        <span class="text-gray-500 hover:text-primary transition-all duration-200">Orders</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
    <span class="text-foreground font-medium">Order Details</span>
</div>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Order Management</h1>
        <p class="text-gray-500 text-sm md:text-base">Manage restaurant order and reservations</p>
    </div>
</div>

<!-- Alerts -->
@if(Session::get('test') == 'restaurant_admin')
<div class="alertordermsg"></div>
<div id="alertSoundContainer"></div>
<div class="alertpaymentmsg"></div>
<div id="PrintalertSoundContainer"></div>
<div class="alertTakeAwayordermsg"></div>
<div id="alertTakeAwaySound"></div>
<div class="alertreadytoservedmsg"></div>
<div id="ReadyToservedalertSoundContainer"></div>
<div class="Waiteralertordermsg"></div>
<div id="alertSoundContainer_waiter"></div>
<div class="alertreadytoservedWaitermsg"></div>
<div id="ReadyToservedWaiteralertSoundContainer"></div>
@endif

<!-- Orders Table -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">All Orders</h3>
    </div>
    <div class="bg-white rounded-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]" id="orderTable">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Customer ID</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Created</th>
                        <!-- <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Updated</th> -->
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Status</th>
                        <!-- <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Send To Kitchen</th> -->
                        <!-- <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Confirm</th> -->
                        <!-- <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Confirm By</th> -->
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Service</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Payment Mode</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="pagination-{{ request()->table_id }}"></div>

<!-- Order Modal -->
@foreach ($orders as $index => $order)
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
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Department</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Add-ons</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Note</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Date & Time</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Sub</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Ready To Served</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">

            </tbody>
            <tfoot>

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

<div id="CompleteOrderModal" class="custom-modal hidden CompleteOrderModal">
    <div class="custom-modal-backdrop" onclick="closeCompleteOrderModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Complete Order</h2>
        <form id="couponForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod">
            @foreach ($orders2 as $val)
            <input type="hidden" name="total" value="{{ $val->total_amount ?? 0 }}"> {{-- current order/cart total --}}
            <input type="hidden" name="data" value="{{ json_encode($val->data) }}">
            @endforeach

            @if($GST_status)
            <input type="hidden" name="cgst" value="{{ $cgst ?? 0 }}">
            <input type="hidden" name="sgst" value="{{ $sgst ?? 0 }}">
            <input type="hidden" name="cgst_rate" value="{{ $cgst_rate ?? 0 }}">
            <input type="hidden" name="sgst_rate" value="{{ $sgst_rate ?? 0 }}">
            @else
            <input type="hidden" name="cgst" value="0">
            <input type="hidden" name="sgst" value="0">
            <input type="hidden" name="cgst_rate" value="0">
            <input type="hidden" name="sgst_rate" value="0">
            @endif
            <div class="coupon-notice">
                <span>
                    Have a coupon? <a id="toggleCoupon">Enter your code here</a>
                </span>
                <div class="promo-container mt-3">
                    <input type="text" name="code" class="coupon-input" placeholder="Enter your coupon code" required>
                    <button type="submit" class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button hover:bg-primary-hover transition-all duration-200 cursor-pointer apply-btn">Apply</button>
                </div>
            </div>
        </form>

        <div class="mt-4 text-right">
            <h4>
                @foreach ($orders2 as $val)
                <strong>Sub-Total: ₹{{ $val->total_amount ?? 0 }}</strong>
                @endforeach
            </h4>
        </div>

        <div class="text-right" id="discountRow" style="display:none"></div>
        <div class="text-right" id="grandTotalRow"></div>
        <div class="text-right" id="cgst"></div>
        <div class="text-right" id="sgst"></div>
        <div class="text-right" id="total_with_tax"></div>
        <div class="text-right" id="final_total"></div>

        @if($GST_status)
        <div class="text-right taxRows">
            <h4><strong>CGST ({{ $cgst_rate }}%): ₹{{ number_format(session('cgst', $cgst),  2, '.', '') }}</strong></h4>
        </div>
        <div class="text-right taxRows">
            <h4><strong>SGST ({{ $sgst_rate }}%): ₹{{ number_format(session('sgst', $sgst),  2, '.', '') }}</strong></h4>
        </div>
        <div class="text-right taxRows">
            <h4><strong>To Pay: ₹{{ number_format(session('total_with_tax', $total_with_tax),  2, '.', '') }}</strong></h4>
        </div>
        @else
        <div class="text-right taxRows">
            <h4><strong>To Pay: ₹{{ number_format(session('final_total', $total),  2, '.', '') }}</strong></h4>
        </div>
        @endif

        <div class="mt-4 text-right">

            <form method="POST" action="{{ route('CompleteOrder') }}">
                @csrf

                @foreach ($orders as $index => $detail)
                @foreach ($data as $value)
                <?php $json = json_decode($value->data); ?>
                @if($detail->customer_code == $value->customer_code && $value->status == 'processed')
                @foreach ($json as $item)
                <input type="hidden" name="product_name[]" value="{{ $item->name }}">
                <input type="hidden" name="product_price[]" value="{{ $item->price }}">
                <input type="hidden" name="product_quantity[]" value="{{ $item->quantity }}">

                <input type="hidden" name="addons[]" value="{{ json_encode($item->addons) }}">

                <input type="hidden" name="customer_code" value="{{ $value->customer_code }}">
                <input type="hidden" name="order_id" value="{{ $value->id }}">
                <input type="hidden" name="table_id" value="{{ $table_id }}">
                @endforeach
                @endif
                @endforeach
                @endforeach

                @if($GST_status == true)
                <input type="hidden" name="cgst" id="form_cgst" value="{{  number_format(session('cgst', $cgst), 2, '.', '') }}">
                <input type="hidden" name="sgst" id="form_sgst" value="{{  number_format(session('sgst', $sgst), 2, '.', '') }}">
                <input type="hidden" name="cgst_rate" value="{{ $cgst_rate }}">
                <input type="hidden" name="sgst_rate" value="{{ $sgst_rate }}">
                <input type="hidden" name="total_with_tax" id="form_total_with_tax" value="{{ number_format(session('total_with_tax', $total_with_tax), 2, '.', '') }}">
                <input type="hidden" name="price" id="payNowPrice" value="{{ number_format(session('total_with_tax', $total_with_tax), 2, '.', '') }}">

                <input type="hidden" name="coupon" id="coupon" value="{{  session('coupon') ? session('coupon') : 'null' }}">
                <input type="hidden" name="discount" id="discount" value="{{  number_format(session('discount', 0), 2, '.', '') }}">

                @else
                <input type="hidden" name="cgst" value="0">
                <input type="hidden" name="sgst" value="0">
                <input type="hidden" name="cgst_rate" value="0">
                <input type="hidden" name="sgst_rate" value="0">
                <input type="hidden" name="total_with_tax" id="form_total_with_tax" value="{{ number_format(session('total_with_tax', $total), 2, '.', '') }}">
                <input type="hidden" name="price" id="payNowPrice" value="{{ number_format(session('total_with_tax', $total), 2, '.', '') }}">

                <input type="hidden" name="coupon" id="coupon" value="{{ session('coupon') ? session('coupon') : 'null' }}">
                <input type="hidden" name="discount" id="discount" value="{{  number_format(session('discount', 0), 2, '.', '') }}">
                @endif
                <button type="button" onclick="closeCompleteOrderModal()" class="px-4 py-2 border rounded mr-1 cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="submit px-4 py-2 bg-primary text-white border rounded cursor-pointer">
                    Complete Order
                </button>
            </form>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function openOrderModal() {
        document.getElementById("OrderModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeOrderModal() {
        document.getElementById("OrderModal").classList.add("hidden");
        document.body.style.overflow = "";
    }

    function fetchRealTimeData() {
        // Send an AJAX request to the server-side PHP endpoint
        $.ajax({
            url: '{{ url("check_condition") }}',
            type: 'GET',
            success: function(response) {
                if (response.condition) {
                    $('#tables-container #table_td').each(function() {
                        let $this = $(this);
                        var tdText = $this.find('.table_name').attr('data-table-name');

                        // 🔥 RESET COLORS FIRST
                        $this.removeClass('td-text-orange td-text-blue');
                        $this.addClass('td-text-green');

                        response.customer.forEach((customer) => {
                            if (tdText === customer.table_name) {
                                $(this).addClass('td-text-orange');

                                // Update only the .createTime inside this matched table block
                                $(this).find('.createTime').html(customer.time_diff);
                                $(this).removeClass('td-text-green');
                                $(this).removeClass('td-text-blue');
                            }
                        });

                        response.invoice_data.forEach((invoice_data) => {
                            if (tdText === invoice_data.table_name) {
                                $(this).addClass('td-text-blue');
                                $(this).removeClass('td-text-orange');
                                $(this).removeClass('td-text-green');
                            }
                        });


                    });
                }
            },
            error: function() {
                console.log('Error occurred during AJAX request');
            }
        });


    }

    // Call the function every second
    setInterval(fetchRealTimeData, 1000);

    $(document).on('click', '.pagination-link', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        fetchLatestOrders(url);
    });

    let currentPageUrl = null;

    function fetchLatestOrders(url = null) {
        // let fetchUrl = currentPageUrls[tableId] || `/get-latest-orders/${tableId}`;
        var tableId = "{{ request()->table_id ?? 'null' }}";

        // If URL passed → store it
        if (url) {
            currentPageUrl = url;
        }


        let fetchUrl = currentPageUrl ?
            currentPageUrl :
            "{{ route('getLatestOrders', ':table_id') }}".replace(':table_id', tableId);

        $.ajax({
            url: fetchUrl,
            type: 'GET',
            success: function(response) {


                // Check if there are any orders
                if (response.orders && response.orders.data && response.orders.data.length > 0) {
                    let ordersHTML = '';


                    response.orders.data.forEach((order, index) => {
                        let serviceItemsHTML = '';

                        if (response.orderdata) {

                            response.orderdata.forEach((value) => {

                                // IMPORTANT:
                                // Only process data belonging to the current order/customer
                                if (value.customer_code != order.customer_code) {
                                    return;
                                }

                                try {

                                    const items = JSON.parse(value.item_data || '[]');

                                    items.forEach((item) => {

                                        if (
                                            item.fulfillment_type === 'service' && item.ReadytoServed === 'No'
                                        ) {

                                            serviceItemsHTML += `
                                                <div class="text-sm mb-1">
                                                    <strong>${item.name}</strong>
                                                    × ${item.quantity}
                                                </div>
                                            `;

                                        }

                                    });

                                } catch (e) {
                                    console.log('Invalid order JSON', e);
                                }

                            });
                        }

                        // If no service item exists for this order
                        if (!serviceItemsHTML) {
                            serviceItemsHTML = `
                                <span class="text-gray-400">—</span>
                            `;
                        }


                        let rowClass = '';
                        if (order.status === 'processed') {
                            rowClass += order.customer_code === response.last.customer_code ? 'customer_tr' : '';
                        }
                        let statusColor = order.status === 'processed' ? 'orange' : (order.status === 'completed' ? 'green' : '');
                        let PrintStatus = order.order_print_status === 0 ? '<span class="text-foreground text-sm font-semibold" style="color: red;">No</span>' : (order.order_print_status === 1 ? '<span class="text-foreground text-sm font-semibold" style="color: green;">Yes</span>' : '');
                        let OrderConfirm = order.order_confirm === 0 ? '<span class="text-foreground text-sm font-semibold" style="color: red;">No</span>' : (order.order_confirm === 1 ? '<span class="text-foreground text-sm font-semibold" style="color: green;">Yes</span>' : '');

                        // let invoiceData = response.invoice_data.status === 0 ? 'block' : (response.invoice_data.status === 1 ? 'none' : '');


                        let printButton = order.status === 'completed' ?
                            `<button data-order-id="${order.id}" id="PrintInvoice" class="print-invoice-btn px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer">
                                    Print Invoice
                                </button>` :
                            '';

                        let completOrderButton = order.status === 'processed' ?
                            `<button onclick="openCompleteOrderModal()" data-customer-code="${order.customer_code}" data-order-id="${order.id}" class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button hover:bg-primary-hover transition-all duration-200 cursor-pointer">Complete Order</button>` :
                            '';



                        let payment_mode = '';

                        if (order.status === 'completed') {

                            const invoice = response.invoice_data.find(
                                inv => inv.order_id == order.id
                            );

                            if (invoice) {

                                payment_mode = `
                                <select class="px-3 md:px-4 py-2.5 md:py-3 text-sm md:text-base border border-border rounded-button focus:bg-white hover:border-primary focus:border-primary transition-all duration-300 font-semibold cursor-pointer payment-method-select" name="payment-method-select"
                                aria-label="select"
                                    data-order-id="${order.id}"
                                >
                                    <option value="" ${invoice.payment_mode == null ? 'selected' : ''}>Select</option>
                                    <option value="cash" ${invoice.payment_mode == 'cash' ? 'selected' : ''}>Cash</option>
                                    <option value="card" ${invoice.payment_mode == 'card' ? 'selected' : ''}>Card</option>
                                    <option value="upi" ${invoice.payment_mode == 'upi' ? 'selected' : ''}>UPI</option>
                                </select>
                            `;
                            }

                        } else {
                            payment_mode = '<span>Not Set</span>';
                        }

                        console.log(payment_mode);

                        // alert(response.orderId);

                        // Append the new order row to the table

                        ordersHTML += `<tr class="border-b border-gray-50 hover:bg-gray-50 ${rowClass}">
                            <td class="py-4 px-2">
                                <span class="text-foreground text-sm font-medium">${order.customer_code}</span>
                            </td>
                            <td class="py-4 px-2">
                                <p class="text-foreground text-sm font-medium">${order.latest_created_at}</p>
                            </td>
                            <td class="py-4 px-2">
                                <span class="text-xs text-white font-medium px-2 py-2 rounded-full" style="background-color:${statusColor};">${order.status}</span>
                            </td>
                            <td class="py-4 px-2">
                                ${
                                    serviceItemsHTML
                                    ? `
                                        <div>
                                            <div class="text-xs font-semibold mb-1"
                                                style="color:#856404;">
                                                Service Required
                                            </div>

                                            ${serviceItemsHTML}
                                        </div>
                                    `
                                    : `
                                        <span class="text-gray-400 text-sm">
                                            —
                                        </span>
                                    `
                                }
                            </td>
                            <td class="py-4 px-2">
                               ${payment_mode}
                            </td>
                            <td class="py-4 px-2">
                                <div class="flex items-center gap-2">
                                    <button  data-customer-code="${order.customer_code}" data-order-id="${order.id}" class="text-primary hover:text-primary-hover text-sm font-medium cursor-pointer view-order-btn">View Details</button>

                                    ${printButton}

                                    ${completOrderButton}
                                </div>
                            </td>
                        </tr>`;
                    });

                    // Update the order table
                    $('#orderTable tbody').html(ordersHTML);
                    let paginationHTML = '';

                    if (response.orders.last_page > 1) {

                        paginationHTML += `
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 px-3 pb-4">

                                <div class="text-sm text-gray-600">
                                    Showing ${response.orders.from} to ${response.orders.to}
                                    of ${response.orders.total} entries
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                            `;

                        // Previous
                        if (response.orders.prev_page_url) {
                            paginationHTML += `
                                <a href="${response.orders.prev_page_url}"
                                class="pagination-link px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
                                Previous
                                </a>
                            `;
                        } else {
                            paginationHTML += `
                                <span class="px-3 py-2 border rounded-lg text-sm text-gray-400 cursor-not-allowed">
                                    Previous
                                </span>
                            `;
                        }

                        // Page Numbers
                        for (let i = 1; i <= response.orders.last_page; i++) {

                            if (i === response.orders.current_page) {
                                paginationHTML += `
                                    <span class="px-3 py-2 bg-primary text-white rounded-lg text-sm">
                                        ${i}
                                    </span>
                                `;
                            } else {
                                paginationHTML += `
                                    <a href="${response.orders.path}?page=${i}"
                                    class="pagination-link px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
                                    ${i}
                                    </a>
                                `;
                            }
                        }

                        // Next
                        if (response.orders.next_page_url) {
                            paginationHTML += `
                                <a href="${response.orders.next_page_url}"
                                class="pagination-link px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
                                Next
                                </a>
                            `;
                        } else {
                            paginationHTML += `
                                <span class="px-3 py-2 border rounded-lg text-sm text-gray-400 cursor-not-allowed">
                                    Next
                                </span>
                            `;
                        }

                        paginationHTML += `
                            </div>
                        </div>
                        `;
                    }

                    $('#pagination-' + tableId).html(paginationHTML);



                    // Event delegation for dynamically created rows
                    $(document).on('click', '.view-order-btn', function() {
                        const customerCode = $(this).data('customer-code');
                        const orderId = $(this).data('order-id'); // Get order ID

                        // Call the function to display the order details
                        displayOrderDetails(customerCode, response.orderdata);


                        // Call the updateNotified function to set is_notified to true
                        $.ajax({
                            url: '/update-order-notified/' + orderId, // Trigger the update for this specific order
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                order_notified: true
                            },
                            success: function(response) {
                                if (response.success) {
                                    openOrderModal();
                                    console.log('Order notification status updated to true.');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log('Error updating notification status: ' + error);
                            }
                        });
                    });


                } else {
                    // Show a message if no orders are found
                    $('#orderTable tbody').html('<tr><td colspan="9" class="text-center pt-3 pb-3" style="background-color:yellow">No orders found for this table.</td></tr>');
                    $('#pagination-' + tableId).html('');
                }


            },
            error: function(xhr, status, error) {
                console.log('Error fetching latest orders: ' + error);
            }
        });
    }


    function displayOrderDetails(customerCode, orderData) {
        let modalHTML = '';
        let modalHTML2 = '';
        let total = 0;
        let matchFound = false;

        orderData.forEach((value) => {
            if (customerCode === value.customer_code) {
                matchFound = true;
                const jsonData = JSON.parse(value.data);
                jsonData.forEach((detail) => {
                    // total += detail.price * detail.quantity;

                    let itemPrice = parseFloat(detail.price);
                    let itemQty = parseInt(detail.quantity);
                    let itemTotal = itemPrice * itemQty;
                    let note_data = '';

                    let addonNames = [];
                    let addonTotal = 0;

                    let addonData = JSON.parse(detail.addons); // assuming `details.addons` is a JSON string

                    if (Array.isArray(addonData)) {
                        addonData.forEach(function(addon) {
                            if (addon.name !== undefined && addon.price !== undefined) {
                                addonNames.push(addon.name);
                                addonTotal += addon.price * detail.quantity;
                            }
                        });
                    }

                    let totalWithAddons = itemTotal + addonTotal;
                    total += totalWithAddons;

                    note_data += detail.note == null ? '-' : detail.note;

                    let addon = addonData == null ? '-' : addonNames;

                    let ServedStatus = '';

                    const jsonItemData = JSON.parse(value.item_data);
                    const currentItem = jsonItemData.find(
                        item => item.item_id == detail.item_id
                    );

                    if (currentItem && currentItem.ReadytoServed === 'Yes') {
                        ServedStatus = '<span class="text-foreground text-sm font-semibold" style="color: green;">Yes</span>';
                    } else {
                        ServedStatus = '<span class="text-foreground text-sm font-semibold" style="color: red;">No</span>';
                    }

                    const fulfillmentType = detail.fulfillment_type ?? 'kitchen';

                    let fulfillmentHTML = '';
                    let toggleStatusHTML = '';

                    if (fulfillmentType === 'service') {
                        fulfillmentHTML = `
                            <span class="text-xs font-semibold px-2 py-1 rounded-full"
                                style="background:#fff3cd;color:#856404;">
                                Service
                            </span>
                        `;
                        toggleStatusHTML = `
                           <label class="switch">
                                <input type="checkbox"
                                    class="toggle-status"
                                    data-order-id="${value.id}"
                                    data-item-id="${detail.item_id}"
                                    data-fulfillment-type="${fulfillmentType}"
                                    ${currentItem && currentItem.ReadytoServed === 'Yes' ? 'checked' : ''}>
                                <span class="slider"></span>
                            </label>
                        `;
                    } else {
                        fulfillmentHTML = `
                            <span class="text-xs font-semibold px-2 py-1 rounded-full"
                                style="background:#e8f5e9;color:#2e7d32;">
                                Kitchen
                            </span>
                        `;
                        toggleStatusHTML = '';
                    }


                    modalHTML += `<tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-4 px-2">
                            <div>
                                <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">${detail.name}</p>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">${detail.price}</span>
                        </td>
                        <td class="py-4 px-2">
                            <p class="text-foreground text-sm font-medium">${detail.quantity}</span>
                        </td>
                         <td class="py-4 px-2">
                            ${fulfillmentHTML}
                        </td>
                        <td class="py-4 px-2">
                            <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">${addon}</p>
                        </td>
                        <td class="py-4 px-2">
                            <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">${note_data}</p>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">${detail.created_at}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold text-success">₹${totalWithAddons}</span>
                        </td>
                        <td class="py-4 px-2">
                            ${ServedStatus}
                        </td>
                        <td class="py-4 px-2">
                            ${toggleStatusHTML}
                        </td>

                    </tr>`;
                });
            }
        });

        if (!matchFound) {
            modalHTML = `<tr><td class="text-center" colspan="9">A customer has recently logged into their account but has not placed an order yet.</td></tr>`;
        }

        modalHTML2 = `
            <tr>
                <td colspan="8" class="text-right"><h3><strong>Total:</strong></h3></td>
                <td align="right" class=""><h3><strong>₹${total.toFixed(2)}</strong></h3></td>
            </tr>
        `;

        // Update the modal content
        $('.OrderModal tbody').html(modalHTML);
        $('.OrderModal tfoot').html(modalHTML2);
    }

    $(document).on('click', '.pagination-links a', function(e) {
        e.preventDefault();
        let newPageUrl = $(this).attr('href');
        let tableId = $(this).closest('.pagination-links').attr('data-table-id');

        if (newPageUrl && tableId) {
            currentPageUrls[tableId] = newPageUrl; // Update pagination URL for this specific table
            fetchLatestOrders(tableId); // Fetch data for the new page
        }
    });


    // Fetch orders every 2 seconds for a specific table
    setInterval(function() {
        // let urlParams = new URLSearchParams(window.location.search);
        // let tableId = urlParams.get('id');
        fetchLatestOrders();
    }, 3000); // Adjust the interval as needed


    $(document).on('click', '.print-invoice-btn', function() {

        const orderId = $(this).data('order-id');

        console.log('Removing print alert for order:', orderId);

        // ✅ Open invoice
        const url = "{{ url('/print-invoice-order') }}/" + orderId;
        window.open(url, "_blank");
    });

    function openCompleteOrderModal() {
        document.getElementById("CompleteOrderModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeCompleteOrderModal() {
        document.getElementById("CompleteOrderModal").classList.add("hidden");
        document.body.style.overflow = "";
    }

    $(document).ready(function() {
        $(document).on('change', '.payment-method-select', function() {

            var payment_mode = $(this).val();
            var orderId = $(this).data('order-id');

            // ✅ Remove alert
            $('#order-alert-' + orderId).fadeOut(200, function() {
                $(this).remove();

                // 🛑 Stop sound if no print alerts left
                if ($('#Waiteralertordermsg .alert').length === 0) {
                    stopManagerorderAlertSound();
                }
            });


            $.ajax({
                url: "{{ route('updatePaymentMethod') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: orderId,
                    payment_mode: payment_mode
                },
                success: function(response) {
                    alert('Payment method updated successfully!');
                },
                error: function() {
                    alert('Something went wrong while updating.');
                }
            });
        });
    });

    $(document).on('change', '.toggle-status', function() {
        let isChecked = $(this).is(':checked');
        const orderId = $(this).data('order-id');
        const itemId = $(this).data('item-id');

        const url = "{{ url('/item-ready-to-served') }}/" + orderId + "/" + itemId;

        $.ajax({
            url: url,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                status: isChecked ? 'Yes' : 'No'
            },
            success: function(res) {
                if (res.success) {
                    alert(res.message);
                } else {
                    alert('Unexpected response');
                }
            },
            error: function(xhr) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                alert('Server error occurred');
            }
        });

    });
</script>
@endsection