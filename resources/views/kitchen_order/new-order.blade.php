@extends('layouts.admin')
<style>
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
<div class="KitchenTakeAwayalertmsg"></div>
<div id="KitchenTakeAwayalertSoundContainer"></div>
@endif

<div id="ordersContainer"></div>

<script>
    function startTimers() {
        document.querySelectorAll('.timer').forEach(function(el) {
            let startTime = new Date(el.dataset.time);
            let now = new Date();
            let diff = Math.floor((now - startTime) / 1000);

            let minutes = Math.floor(diff / 60);
            let seconds = diff % 60;

            el.innerText =
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        });
    }

    function loadOrders() {
        $.get('/get-kitchen-orders', function(response) {

            let orders = response.orders;
            let tables = response.tables;
            let html = '';

            if (orders.length > 0) {
                html += `
                    <div class="grid md:grid-cols-3 grid-cols-2 md:gap-10 gap-5">
                    `;

                orders.forEach(order => {

                    let tableName = tables[order.table_id] ?? '';

                    let borderClass = order.status === 'completed' ?
                        'border-green-500' :
                        'border-yellow-400';

                    let headerBg = order.status === 'completed' ?
                        'bg-green-500' :
                        'bg-yellow-400';

                    html += `
                    <div class="bg-white rounded-xl shadow-lg border-2 ${borderClass}">
                        <div class="p-3 ${headerBg} text-white rounded-t-xl">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="font-bold" style="text-transform: capitalize;">
                                          ${tableName}
                                    </h3>
                                    <span>${order.order_type}</span><br>
                                    
                    `;

                    if (order.kitchen_order_status !== 'ready') {
                        html += `<span class="timer" data-time="${order.created_at}">00:00</span>`;
                    }

                    html += `</div>
                    <div class="border-l md:pl-5 pl-2 text-center cursor-pointer"
                         ${order.kitchen_order_status === 'ready' && order.status === 'completed'
                            ? `onclick="updateStatus('${order.id}')"`
                            : ''}>
                        <i class="fa fa-check-circle" style="font-size:36px"></i><br>
                    `;

                    if (order.status === 'processed') {
                        html += `<span>Running</span>`;
                    } else if (order.status === 'completed') {
                        html += `<span>Dispatch</span>`;
                    }

                    html += `
                            </div>
                        </div>
                    </div>
                    <div class="p-3 space-y-2">
                        `;

                    // Parse items
                    let items = JSON.parse(order.data);

                    items.forEach(item => {

                        let addonNames = [];
                        let addonTotal = 0;

                        let addonData = item.addons ? JSON.parse(item.addons) : [];

                        if (Array.isArray(addonData)) {
                            addonData.forEach(addon => {
                                if (addon.name && addon.price) {
                                    addonNames.push(addon.name);
                                    addonTotal += addon.price * item.quantity;
                                }
                            });
                        }

                        let addon = addonNames.length ? addonNames.join(', ') : '';

                        let switchStatus = '';
                        let alertedStatus = '';


                        const jsonItemData = JSON.parse(order.item_data);
                        const currentItem = jsonItemData.find(
                            item2 => item2.item_id == item.item_id
                        );

                        if (currentItem && currentItem.ReadytoServed === 'Yes') {
                            switchStatus = 'checked';
                        }


                        if (currentItem.print == 'Yes' && currentItem.fulfillment_type == 'kitchen') {
                            html += `
                            <div class="items-center border-b border-gray-100 pb-1">
                                <div class="flex justify-between">
                                    <span>${item.quantity} x ${item.name}</span>
                            `;

                            if (currentItem.alerted === 'No') {
                                html += `
                                <label class="switch">
                                    <input type="checkbox"
                                        class="toggle-status"
                                        data-order-id="${order.id}"
                                        data-item-id="${item.item_id}"
                                        ${switchStatus}>
                                    <span class="slider"></span>
                                </label>
                                `;
                            }

                            html += `
                                </div>
                            `;

                            if (item.note) {
                                html += `<div class="text-xs text-red-500">Note: ${item.note}</div>`;
                            }

                            html += `
                                <span class="text-xs">${addon}</span>
                            </div>
                            `;
                        }
                    });

                    html += `
                        </div>
                    </div>
                    `;

                });
                html += `</div>`;
            } else {
                html += `<div class="text-center pt-3 pb-3" style="background-color:yellow">Orders not found.</div>`;
            }

            $('#ordersContainer').html(html);

            // ⭐ Start timers after refreshing HTML
            startTimers();

        });
    }

    setInterval(startTimers, 1000); // Timer loop
    setInterval(loadOrders, 3000); // Refresh orders
    loadOrders(); // First load





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

    function updateStatus(orderId) {

        $.ajax({
            url: '/kitchen/update-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                order_id: orderId,
            },
            success: function(res) {
                alert('Order Dispatch Successfully.');
                // window.location.reload();
            }
        });
    }
</script>


@endsection