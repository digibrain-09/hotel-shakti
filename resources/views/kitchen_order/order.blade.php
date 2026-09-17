@extends('layouts.admin')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .customer_tr span,
    .customer_tr p {
        color: red;
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
    <a href="{{route('kitchen_order')}}" class="cursor-pointer">
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
@if(Session::get('test') == 'kitchen_owner')
<div class="Kitchenalertmsg"></div>
<div id="KitchenalertSoundContainer"></div>
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
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

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
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Qty</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Add-ons</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Note</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Date & Time</th>
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

    $(document).on('click', '#ReadytoServed', function() {
        const orderId = $(this).data("order-id");
        const url = "{{ url('/order-ready') }}/" + orderId;

        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                alert('Status Updated Successfully.')
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


    $(document).ready(function() {
        function fetchOrders() {
            var tableId = "{{ request()->table_id ?? 'null' }}"; // Ensure table ID is available

            if (tableId) {
                $.ajax({
                    url: "{{ route('getLatestKitchenOrders', ':table_id') }}".replace(':table_id', tableId),
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
                                            <div class="flex items-center gap-2">
                                                <button data-customer-code="${order.customer_code}" data-order-id="${order.id}" class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer view-manager-order-btn">
                                                    All Order
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                `;
                                tableBody.append(row);
                            });
                        } else {
                            tableBody.append('<tr><td colspan="7" class="text-center pt-3 pb-3" style="background-color:yellow">No orders found for this table.</td></tr>');
                        }



                        $(document).on('click', '.view-manager-order-btn', function() {
                            const customerCode = $(this).data('customer-code');
                            const orderId = $(this).data('order-id'); // Get order ID

                            displayOrderDetails(customerCode, response.data, orderId);

                            // Call the updateNotified function to set is_notified to true
                            $.ajax({
                                url: '/update-Kitchen-order-notified/' + orderId, // Trigger the update for this specific order
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    kitchen_order_notified: true
                                },
                                success: function(response) {
                                    if (response.success) {
                                        $('#KitchenalertSoundContainer').html('');
                                        openOrderModal();
                                        console.log('Order notification status updated to true.');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.log('Error updating notification status: ' + error);
                                }
                            });
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
    });


    function displayOrderDetails(customerCode, data, orderId) {
        let modalHTML = '';
        let modalHTML2 = '';
        let total = 0;
        let matchFound = false;

        data.forEach((value) => {
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

                    let switchStatus = '';

                    const jsonItemData = JSON.parse(value.item_data);
                    const currentItem = jsonItemData.find(
                        item => item.item_id == detail.item_id
                    );

                    if (currentItem && currentItem.ReadytoServed === 'Yes') {
                        switchStatus = 'checked';
                    }




                    if (currentItem.print == 'Yes') {
                        modalHTML += `
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-4 px-2">
                            <div>
                                <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">${detail.name}</p>
                            </div>
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
                            <label class="switch">
                                <input type="checkbox" class="toggle-status" data-order-id="${orderId}" data-item-id="${detail.item_id}" ${switchStatus}>
                                <span class="slider"></span>
                            </label>
                        </td>
                    </tr>
                `;
                    }
                });
            }
        });

        if (!matchFound) {
            modalHTML = `<tr><td class="text-center" colspan="5">A customer has recently logged into their account but has not placed an order yet.</td></tr>`;
        }

        // modalHTML2 = `
        //     <tr>
        //         <td colspan="6" class="text-right"><h3><strong>Total:</strong></h3></td>
        //         <td align="right" class=""><h3><strong>₹${total.toFixed(2)}</strong></h3></td>
        //     </tr>
        // `;

        // Update the modal content
        $('.OrderModal tbody').html(modalHTML);
        // $('.OrderModal tfoot').html(modalHTML2);
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



    // Handle toggle click
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