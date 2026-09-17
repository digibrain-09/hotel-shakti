@extends('layouts.admin')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .customer_tr span,
    .customer_tr p {
        color: red;
    }

    /* Hidden checkbox to control offcanvas */
    #offcanvas-toggle {
        display: none;
    }

    /* Overlay */
    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        visibility: hidden;
        transition: 0.3s ease;
        z-index: 998;
    }

    /* Offcanvas */
    .offcanvas {
        position: fixed;
        left: 0;
        right: 0;
        bottom: -100%;
        background: #fafafa;
        border-radius: 16px 16px 0 0;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.2);
        transition: bottom 0.3s ease;
        z-index: 999;
        max-height: 70vh;
        overflow-y: auto;
        /* padding: 20px; */
    }

    /* Show when checked */
    #offcanvas-toggle:checked~.overlay {
        opacity: 1;
        visibility: visible;
    }

    #offcanvas-toggle:checked~.offcanvas {
        bottom: 0;
    }

    /* Header */
    .offcanvas-header {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 15px;
        /* justify-content: space-between; */
        padding: 20px;
        background-color: #fff;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .offcanvas-header img {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
    }

    .offcanvas-header h2 {
        font-size: 18px;
        margin: 0;
    }

    /* Section */
    .offcanvas-section {
        margin: 1rem;
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #eee;
    }

    .offcanvas-section h3 {
        margin: 0 0 5px;
        font-size: 16px;
    }

    .offcanvas-section p {
        margin: 0 0 10px;
        font-size: 13px;
        color: #555;
    }

    /* Addon */
    .addon {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-top: 1px solid #eee;
    }

    .addon:first-child {
        border-top: none;
    }

    .addon label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .addon input {
        accent-color: #e7272d;
    }

    /* Quantity */
    .quantity-control {
        display: flex;
        align-items: center;
        border: 1px solid #e7272d;
        border-radius: 6px;
        overflow: hidden;
        width: fit-content;
    }

    .quantity-control button {
        background: #fff;
        border: none;
        padding: 8px 14px;
        font-size: 18px;
        cursor: pointer;
        color: #e7272d;
    }

    .quantity-control input {
        width: 40px;
        text-align: center;
        border: none;
        font-size: 16px;
        outline: none;
    }

    /* Footer */
    .offcanvas-footer {
        position: sticky;
        bottom: 0;
        background: #fff;
        text-align: right;
        z-index: 100;
        padding: 20px;
        display: flex;
        justify-content: space-between;
    }

    .btn-add {
        background: #e7272d;
        color: #fff;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
    }

    /* Trigger button */
    .btn-open {
        margin: 20px;
        padding: 12px 20px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    /* Close button */
    .close-btn {
        display: block;
        text-align: right;
        margin-bottom: 10px;
    }

    .close-btn label {
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
    }
</style>

@section('content')
<!-- Breadcrumb Navigation -->
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{route('manager_order')}}" class="cursor-pointer">
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
    <div class="flex items-center gap-2 md:gap-3 ml-auto md:ml-0">
        <!-- <button onclick="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Item</span>
        </button> -->
    </div>
</div>

<!-- Alerts -->
@if(Session::get('test') == 'restaurant_manager')
<div class="Manageralertmsg"></div>
<div id="alertSoundContainer_manager"></div>
<div class="alertreadytoservedmsg"></div>
<div id="ReadyToservedalertSoundContainer"></div>
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
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Updated</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Status</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Confirm</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Send To Kitchen</th>
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
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Add-ons</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Note</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Date & Time</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Sub</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Ready To Served</th>
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

<!-- New Order Model -->
@foreach ($orders as $index => $order)
<div id="NewOrderModal" class="custom-modal hidden NewOrderModal">
    <div class="custom-modal-backdrop" onclick="closeNewOrderModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">New Order</h2>

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
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Action</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">

            </tbody>
            <tfoot>

            </tfoot>
        </table>

        <div class="mt-4 text-right">
            <button type="button" onclick="closeNewOrderModal()" class="px-4 py-2 border rounded mr-1 cursor-pointer">
                Cancel
            </button>
            <button type="button" data-order-id="{{ $order->id }}" id="PrintBtn" class="px-4 py-2 bg-primary text-white border rounded cursor-pointer">
                Send To Kitchen
            </button>
        </div>
    </div>
</div>
@endforeach

<!-- Hidden checkbox (only once) -->
<input type="checkbox" id="offcanvas-toggle">

<!-- Overlay -->
<div class="overlay"></div>

<!-- Offcanvas -->
<div class="offcanvas">

    <!-- Header -->
    <div class="offcanvas-header">
        <img src="" class="offcanvas-image mr-5" alt="Food">
        <h2 class="offcanvas-title mr-auto" id="offcanvasBottomLabel">Veggie Club Sandwich</h2>

        <div class="close-btn ml-auto">
            <label for="offcanvas-toggle" onClick="window.location.href=window.location.href">&times;</label>
        </div>
    </div>

    <div class="offcanvas-body">

        <!-- Add On -->
        <!-- <div class="offcanvas-section">
        <h3>Add On</h3>
        <p>Want something extra? Pick an add-on you like!</p>
        <div class="addon">
            <label>
                <input type="checkbox"> Extra Cheese
            </label>
            <span>₹20</span>
        </div>
        <div class="addon">
            <label>
                <input type="checkbox"> Fries
            </label>
            <span>₹50</span>
        </div>
    </div> -->

        <div class="addon-content"></div>

        <!-- Cooking Preferences -->
        <div class="offcanvas-section">
            <h3>Any Cooking Preferences?</h3>
            <textarea class="w-full px-3 md:px-4 py-2.5 md:py-3 text-sm md:text-base border border-border rounded-button focus:bg-white hover:border-primary focus:border-primary transition-all duration-300 resize-none order_note" name="order_note" placeholder="Let us know if you have any preferences..." id="exampleFormControlTextarea1" rows="3"></textarea>
        </div>

    </div>

    <!-- Footer -->
    <div class="offcanvas-footer">
        <div class="quantity-control">

        </div>
        <div class="offcanvas-add-btn-div">
            <button class="btn-add">Add to Cart ₹250</button>
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

    function openNewOrderModal() {
        document.getElementById("NewOrderModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeNewOrderModal() {
        document.getElementById("NewOrderModal").classList.add("hidden");
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


    $(document).on('click', '#PrintBtn', function() {
        const orderId = $(this).data("order-id");
        const url = "{{ url('/print-order') }}/" + orderId;

        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                alert('Order send to kitchen successfully.');
                window.location.reload();
            },
            error: function(xhr) {
                if (xhr.status === 400) {
                    let res = xhr.responseJSON;
                    alert(res.message);
                }
            }
        });
    });

    $(document).on('click', '.pagination-link', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        fetchOrders(url);
    });

    let currentPageUrl = null;

    function fetchOrders(url = null) {

        var tableId = "{{ request()->table_id ?? 'null' }}"; // Ensure table ID is available
        // If URL passed → store it
        if (url) {
            currentPageUrl = url;
        }


        let fetchUrl = currentPageUrl ?
            currentPageUrl :
            "{{ route('getLatestManagerOrders', ':table_id') }}".replace(':table_id', tableId);

        if (tableId) {
            $.ajax({
                url: fetchUrl,
                type: "GET",
                success: function(response) {
                    let orders = response.orders.data; // Adjust based on API response structure
                    let tableBody = $("#orderTable tbody");
                    tableBody.empty(); // Clear previous data

                    if (orders.length > 0) {
                        $.each(orders, function(index, order) {
                            let rowClass = '';
                            if (order.status === 'processed') {
                                rowClass += order.customer_code === response.last.customer_code ? 'customer_tr' : '';
                            }
                            let statusColor = order.status === 'completed' ? 'green' : 'orange';
                            let printStatus = order.order_print_status === 0 ? '<span class="text-foreground text-sm font-semibold" style="color: red;">No</span>' : (order.order_print_status === 1 ? '<span class="text-foreground text-sm font-semibold" style="color: green;">Yes</span>' : '');



                            let row = `
                                <tr class="border-b border-gray-50 hover:bg-gray-50 ${rowClass}">
                                    <td class="py-4 px-2">
                                        <span class="text-foreground text-sm font-medium">${order.customer_code}</span>
                                    </td>
                                    <td class="py-4 px-2">
                                        <p class="text-foreground text-sm font-medium">${order.latest_created_at}</p>
                                    </td>
                                    <td class="py-4 px-2">
                                        <p class="text-foreground text-sm font-medium">${order.latest_updated_at}</p>
                                    </td>
                                    <td class="py-4 px-2">
                                        <span class="text-xs text-white font-medium px-2 py-2 rounded-full" style="background-color:${statusColor};">${order.status}</span>
                                    </td>
                                    <td class="py-4 px-2">
                                        <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">${order.order_confirm_by}</p>
                                    </td>
                                    <td class="py-4 px-2">
                                        ${printStatus}
                                    </td>
                                    <td class="py-4 px-2">
                                        <div class="flex items-center gap-2">
                                            <button data-customer-code="${order.customer_code}" data-order-id="${order.id}" class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer view-manager-order-btn">
                                                All Order
                                            </button>

                                            <button data-customer-code="${order.customer_code}" data-order-id="${order.id}" class="px-3 py-1.5 border border-border text-foreground text-xs font-medium rounded-button hover:border-primary hover:text-primary transition-all duration-200 cursor-pointer new-order-btn">
                                                New Order
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                `;
                            tableBody.append(row);

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
                        });
                    } else {
                        tableBody.append('<tr><td colspan="7" class="text-center pt-3 pb-3" style="background-color:yellow">No orders found for this table.</td></tr>');
                    }

                    $(document).on('click', '.view-manager-order-btn', function() {
                        const customerCode = $(this).data('customer-code');
                        const orderId = $(this).data('order-id'); // Get order ID

                        displayOrderDetails(customerCode, response.data);

                        // Call the updateNotified function to set is_notified to true
                        $.ajax({
                            url: '/update-notified/' + orderId, // Trigger the update for this specific order
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                is_notified: true
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#alertSoundContainer').html('');
                                    openOrderModal();
                                    console.log('Order notification status updated to true.');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log('Error updating notification status: ' + error);
                            }
                        });
                    });


                    $(document).on('click', '.new-order-btn', function() {
                        const customerCode = $(this).data('customer-code');
                        const orderId = $(this).data('order-id'); // Get order ID
                        openNewOrderModal();

                        let modalHTML = '';
                        let total = 0;
                        let matchFound = false;

                        response.order_print.forEach((value) => {
                            if (customerCode === value.customer_code) {
                                const jsonData = JSON.parse(value.item_data);
                                jsonData.forEach((detail) => {

                                    let itemPrice = parseFloat(detail.price);
                                    let itemQty = parseInt(detail.quantity);
                                    let itemTotal = itemPrice * itemQty;
                                    total += detail.price * detail.quantity;
                                    let note_data = '';

                                    note_data += detail.note == null ? '-' : detail.note;

                                    let addonData = JSON.parse(detail.addons); // assuming `details.addons` is a JSON string
                                    let addonNames = [];
                                    let addonTotal = 0;

                                    if (Array.isArray(addonData)) {
                                        addonData.forEach(function(addon) {
                                            if (addon.name !== undefined && addon.price !== undefined) {
                                                addonNames.push(addon.name);
                                                addonTotal += addon.price * detail.quantity;
                                            }
                                        });
                                    }

                                    let totalWithAddons = itemTotal + addonTotal;

                                    let addon = addonData == null ? '-' : addonNames;

                                    let disabled = detail.quantity == 1 ? 'disabled' : '';

                                    if (detail.print == 'No') {
                                        matchFound = true;
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
                                                    <div class="flex items-center gap-2">
                                                    <form action="/remove-order-item/${value.order_id}/${detail.item_id}" method="POST">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" onclick="return confirm('Do you want to repeat this item?');" class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer">
                                                           Delete
                                                        </button>
                                                    </form>

                                                        <label for="offcanvas-toggle" class="px-3 py-1.5 border border-border text-foreground text-xs font-medium rounded-button hover:border-primary hover:text-primary transition-all duration-200 cursor-pointer item_details"
                                                            data-id="${detail.item_id}" data-order_id="${value.order_id}">
                                                                Edit
                                                            </label>
                                                    </div>
                                                </td>
                                            </tr>`;
                                    }
                                });
                            }
                        });

                        if (!matchFound) {
                            modalHTML = `<tr><td class="text-center pt-3 pb-3" style="background-color:yellow" colspan="8">You have not any new order.</td></tr>`;
                        }


                        // Update the modal content
                        $('.NewOrderModal tbody').html(modalHTML);

                    });

                },
                error: function(xhr, status, error) {
                    console.error("Error fetching orders:", error);
                }
            });
        }
    }

    // Fetch orders every 10 seconds
    setInterval(fetchOrders, 1000);

    // Fetch orders immediately on page load
    fetchOrders();


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
                    </tr>`;
                });
            }
        });

        if (!matchFound) {
            modalHTML = `<tr><td class="text-center" colspan="7">A customer has recently logged into their account but has not placed an order yet.</td></tr>`;
        }

        modalHTML2 = `
            <tr>
                <td colspan="7" class="text-right"><h3><strong>Total:</strong></h3></td>
                <td align="right" class=""><h3><strong>₹${total.toFixed(2)}</strong></h3></td>
            </tr>
        `;

        // Update the modal content
        $('.OrderModal tbody').html(modalHTML);
        $('.OrderModal tfoot').html(modalHTML2);
    }

    $(document).on('click', '.item_details', function() {

        $('#offcanvasMode').val('edit');


        $('.NewOrderModal').addClass('hidden');

        const itemId = $(this).data('id');
        const orderId = $(this).data('order_id');

        $.ajax({
            url: "{{ route('view_item') }}",
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
                itemId: itemId,
                orderId: orderId,
            },
            success: function(response) {
                // Store base price globally
                basePrice = parseFloat(response.price);

                orderID = parseFloat(response.order_id);
                let quantity = parseInt(response.quantity) || 1;
                let selectedAddonTotal = 0;

                $('#offcanvasBottomLabel').text(response.name);
                $('.offcanvas-image').attr('src', '/items/' + response.picture);
                $('.quantity-control .qty-input').val(response.quantity || 1);
                $('.offcanvas-add-btn').attr('data-item-id', response.item_id);
                $('.order_note').val(response.note || '');

                if (response.add_ons.length > 0) {
                    let addonsExtra = '';
                    let addonsVariation = '';
                    let addonsVegtoppings = '';


                    $.each(response.add_ons, function(index, addon) {

                        const isChecked = addon.selected ? 'checked' : '';
                        let variation_price = addon.price + basePrice;


                        const addonHTML = `<div class="addon">
                                        <label> <input type="checkbox" class="addon-checkbox" id="checkbox-${addon.id}" data-price="${addon.price}"  data-name="${addon.name}" ${isChecked}> ${addon.name} </label>
                                        <span>₹${addon.price}</span>
                                    </div>`;


                        let addonHTML2 = `<div class="addon">
                                        <label> <input type="radio" name="variation_option" class="addon-radio addon-checkbox" id="radio-${addon.id}" data-price="${addon.price}" data-name="${addon.name}" ${isChecked}> ${addon.name} </label>
                                        <span>₹${variation_price}</span>
                                    </div>`;

                        if (addon.type == 'Extra') {
                            if (!addonsExtra) {
                                addonsExtra += `
                            <div class="offcanvas-section">
                                <h3>Add On</h3>
                                <p>Want something extra? Pick an add-on you like!</p>
                            `;
                            }
                            addonsExtra += addonHTML;
                        } else if (addon.type == 'Variation') {
                            if (!addonsVariation) {
                                addonsVariation += `
                                <div class="offcanvas-section">
                                <h3>Variation</h3>
                                <p>Choose what suits you best</p>

                                <div class="addon">
                                        <label> <input type="radio" name="variation_option" class="addon-radio regular-radio" id="radio-" data-price="${basePrice}" data-name="Regular" checked> Regular </label>
                                        <span>₹${basePrice}</span>
                                </div>

                            `;
                            }

                            addonsVariation += addonHTML2;
                        } else if (addon.type == 'Veg-toppings') {
                            if (!addonsVegtoppings) {
                                addonsVegtoppings += `
                                <div class="offcanvas-section">
                                <h3>Veg Topping</h3>
                                <p>Pick your favorite veg toppings to customize your meal.</p>
                            `;
                            }
                            addonsVegtoppings += addonHTML;
                        }
                        // addonsHtml += addonHTML;

                        if (addon.selected) {
                            selectedAddonTotal += parseFloat(addon.price);
                        }

                    });

                    if (addonsExtra) addonsExtra += '</div>';
                    if (addonsVariation) addonsVariation += '</div>';
                    if (addonsVegtoppings) addonsVegtoppings += '</div>';

                    $('.offcanvas-body .addon-content').show();
                    $('.offcanvas-body .addon-content').html(addonsVariation + addonsVegtoppings + addonsExtra);
                    $('.offcanvas-body .note').css('margin-top', '30px');

                    $('.regular-radio').on('change', function() {
                        updateRegularradioPrice();
                    });
                } else {
                    $('.offcanvas-body .addon').hide();
                    $('.offcanvas-body .note').css('margin-top', '0px');
                }

                // Calculate price after setting everything
                let finalPrice = (basePrice + selectedAddonTotal) * quantity;
                $('.offcanvas-total-price').text(finalPrice);
                $('.offcanvas-add-btn-div').html(
                    `<button type="button" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer offcanvas-add-btn" id="updateOrder" data-base-price="${finalPrice}" data-item-id="${response.item_id}" data-order-id="${response.order_id}">Add to Cart ₹${finalPrice}</button>`
                );


                $('.quantity-control').html(
                    `<button class="qty-btn minus" data-id="${response.item_id}"><i class="fa fa-minus"></i></button>
                <input type="number" class="qty-input" value="${response.quantity}" min="1">
            <button class="qty-btn plus" data-id="${response.item_id}"><i class="fa fa-plus"></i></button>`
                );

                // Add live addon/quantity change listeners (optional)
                $(document).off('change.addon').on('change.addon', '.addon-checkbox', function() {
                    recalculatePrice();
                });

                $(document).off('click.qty').on('click.qty', '.quantity-control .qty-btn', function() {
                    let input = $('.quantity-control .qty-input');
                    let value = parseInt(input.val());
                    if ($(this).hasClass('plus')) value += 1;
                    else if (value > 1) value -= 1;
                    input.val(value);
                    recalculatePrice();
                });

                $(document).off('input.qty').on('input.qty', '.quantity-control .qty-input', function() {
                    let val = parseInt($(this).val());
                    if (isNaN(val) || val < 1) $(this).val(1);
                    recalculatePrice();
                });
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Something went wrong. Check console.');
            }
        });
    });

    $(document).on('click', '#updateOrder', function() {

        let quantity = parseInt($('.quantity-control .qty-input').val()) || 1;
        let note = $('#exampleFormControlTextarea1').val() || '';
        let price = $('.offcanvas-add-btn').attr('data-base-price');

        let orderId = $('.offcanvas-add-btn').attr('data-order-id');



        // Get selected add-on IDs
        let selectedAddons = [];
        $('.addon-checkbox:checked').each(function() {
            selectedAddons.push({
                id: $(this).attr('id').replace(/^(checkbox-|radio-)/, ''),
                name: $(this).data('name'),
                price: $(this).data('price')
            });
        });

        // alert(selectedAddons);
        // exit;

        // You can store item ID in a hidden field or set a data attribute in your add-to-cart button
        // For example, store it in .offcanvas-add-btn-div like this:
        let itemId = $('.offcanvas-add-btn').attr('data-item-id');


        // Send AJAX request to add to cart
        $.ajax({
            url: "{{ route('UpdateManagerOrder') }}", // Replace with your actual route
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}", // CSRF token
                itemId: itemId,
                quantity: quantity,
                addons: selectedAddons,
                note: note,
                orderId: orderId
            },
            success: function(response) {
                alert('Item updated successfully!');
                // $('#offcanvasBottom').offcanvas('hide');
                window.location.reload();
                // You can also refresh cart summary here if needed
            },
            error: function(xhr) {
                alert('Failed to add item to cart.');
                console.error(xhr.responseText);
            }
        });
    });

    function updateRegularradioPrice() {
        let quantity = parseInt($('.quantity-control .qty-input').val()) || 1;
        let total = basePrice;

        $('.addon-checkbox:checked').each(function() {
            let addonPrice = parseFloat($(this).data('price')) || 0;
            total += addonPrice;
        });

        let finalPrice = total * quantity;
        let itemId = $('.offcanvas-add-btn').attr('data-item-id') || '';


        $('.offcanvas-add-btn-div').html(
            `<button type="button" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer offcanvas-add-btn" id="updateOrder" data-base-price="${basePrice}" data-item-id="${itemId}" data-order-id="${orderID}">Add to Cart ₹${finalPrice}</button>`
        );
    }

    function recalculatePrice() {
        let quantity = parseInt($('.quantity-control .qty-input').val()) || 1;
        let totalAddon = 0;

        $('.addon-checkbox:checked').each(function() {
            totalAddon += parseFloat($(this).data('price')) || 0;
        });

        let final = (basePrice + totalAddon) * quantity;
        let itemId = $('.offcanvas-add-btn').attr('data-item-id') || '';


        $('.offcanvas-total-price').text(final);
        $('.offcanvas-add-btn-div').html(
            `<button type="button" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer offcanvas-add-btn" id="updateOrder" data-base-price="${basePrice}" data-item-id="${itemId}" data-order-id="${orderID}">Add to Cart ₹${final}</button>`
        );
    }
</script>
@endsection