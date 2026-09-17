@extends('layouts.admin')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
@endif


<div class="grid grid-cols-3 gap-4 p-5">

    <!-- TO DO -->
    <div class="bg-gray-100 p-3 rounded">
        <h3 class="font-bold mb-3">To Do</h3>
        <div id="todo" class="dropzone min-h-[500px]"></div>
    </div>

    <!-- PREPARING -->
    <div class="bg-yellow-100 p-3 rounded">
        <h3 class="font-bold mb-3">Preparing</h3>
        <div id="preparing" class="dropzone min-h-[500px]"></div>
    </div>

    <!-- DONE -->
    <div class="bg-green-100 p-3 rounded">
        <h3 class="font-bold mb-3">Done</h3>
        <div id="ready" class="dropzone min-h-[500px]"></div>
    </div>

</div>

<script>
    function loadOrders() {
        $.get('/kitchen/orders', function(orders) {

            $('#todo, #preparing, #ready').html('');

            orders.forEach(order => {

                let itemsHTML = '';
                let totalQty = 0;

                let orderTime = new Date(order.created_at);
                let now = new Date();
                let diffMinutes = (now - orderTime) / 60000;

                let borderColor = 'border-green-400';

                if (diffMinutes > 30) {
                    borderColor = 'border-red-500'; // urgent
                } else if (diffMinutes > 15) {
                    borderColor = 'border-yellow-400';
                }


                let items = JSON.parse(order.kitchen_json); // your JSON column

                items.forEach(item => {
                    totalQty += parseInt(item.quantity);

                    let addonNames = [];
                    let addonTotal = 0;

                    let addonData = JSON.parse(item.addons); // assuming `details.addons` is a JSON string

                    if (Array.isArray(addonData)) {
                        addonData.forEach(function(addon) {
                            if (addon.name !== undefined && addon.price !== undefined) {
                                addonNames.push(addon.name);
                                addonTotal += addon.price * item.quantity;
                            }
                        });
                    }

                    let addon = addonData == null ? '' : addonNames;

                    itemsHTML += `
                    <div class="pb-1">
                        <div class="flex justify-between text-sm font-medium">
                            <span>${item.name}</span>
                            <span>x${item.quantity}</span>
                        </div>

                        <div class="text-xs">${addon}</div>

                        ${item.note ? `<div class="text-xs text-red-500">Note: ${item.note}</div>` : ''}
                    </div>
                `;


                });

                let card = `
                <div class="order-card bg-white p-4 rounded shadow mb-3 border-l-4 ${borderColor} cursor-pointer"
                     draggable="true"
                     data-id="${order.id}">

                    <h4 class="font-bold text-lg mb-2" style="text-transform: capitalize;">
                        Table: ${order.table_name}
                    </h4>

                    <div class="mb-2 text-gray-600 text-sm">
                        Total Items: ${totalQty}
                    </div>

                    <div class="space-y-1">
                        ${itemsHTML}
                    </div>

                </div>
            `;

                $('#' + order.status).append(card);
            });
        });
    }

    loadOrders();
    setInterval(loadOrders, 5000);

    let dragged = null;

    $(document).on('dragstart', '.order-card', function() {
        dragged = this;
    });

    $('.dropzone').on('dragover', function(e) {
        e.preventDefault();
    });

    $('.dropzone').on('drop', function() {

        if (!dragged) return;

        $(this).append(dragged);

        let orderId = $(dragged).data('id');
        let newStatus = $(this).attr('id');

        updateStatus(orderId, newStatus);
    });

    function updateStatus(orderId, status) {

        $.ajax({
            url: '/kitchen/update-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                order_id: orderId,
                status: status
            },
            success: function(res) {

                if (status === 'ready') {
                    alert('Order Ready! Waiter or Manager Notified.');
                }
            }
        });
    }
</script>

@endsection