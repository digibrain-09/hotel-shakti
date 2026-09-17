<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@push('scripts')
<script>
    function checkNewOrders() {
        let Manageralertmsg = '';
        let alertSound = '';
        let alertreadytoservedmsg = '';
        let ReadyToservedalertSound = '';

        $.ajax({
            url: "{{ route('checkNewOrders') }}",
            type: "GET",
            success: function(response) {
                if (response.newOrders.length > 0) {
                    response.newOrders.forEach((order) => {
                        Manageralertmsg += `<div class="alert alert-info alert-dismissible">
                           <button type="button"
                                class="close ManagerOrderClose closebtn"
                                data-order-id="${order.id}">
                                <span>&times;</span>
                            </button>
                            You have a new order #${order.customer_code} in ${order.table_name}.
                        </div>`;

                        alertSound += `<audio id="alertSound" autoplay>
                            <source src="{{ asset('sounds/alert.mp3') }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>`;
                    });

                    $('#alertSoundContainer_manager').html(alertSound);
                }

                $('.Manageralertmsg').html(Manageralertmsg);

                if (response.ReadytoServed.length > 0) {

                    response.ReadytoServed.forEach((order) => {

                        let readyItems = [];

                        // Parse item_data JSON string
                        let items = JSON.parse(order.item_data);

                        // Filter only ReadytoServed = Yes
                        items.forEach(item => {
                            if (item.ReadytoServed === "Yes" && item.alerted === "No") {
                                readyItems.push(item.name);
                            }
                        });

                        // If no ready items, skip alert
                        if (readyItems.length === 0) return;

                        // Convert items array to string
                        let itemNames = readyItems.join(', ');

                        alertreadytoservedmsg += `<div class="alert alert-success" style="background:#04AA6D;color:#fff;text-transform: capitalize;">
                            <button type="button"
                                class="close ReadyClose closebtn" style="color:#fff"
                                data-order-id="${order.id}">
                                <span>&times;</span>
                            </button>
                            ${order.table_name} | ${order.customer_code} – Order ready (${itemNames})
                        </div>`;

                        ReadyToservedalertSound += `<audio id="alertSound" autoplay>
                        <source src="{{ asset('sounds/alert2.wav') }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                        </audio>`;

                    });

                    // Play the alert sound if there are new orders
                    $('#ReadyToservedalertSoundContainer').html(ReadyToservedalertSound);

                }

                $('.alertreadytoservedmsg').html(alertreadytoservedmsg);

                $(document).on("click", ".alert .ManagerOrderClose", function() {
                    const orderId = $(this).data('order-id');
                    $.ajax({
                        url: '/update-notified/' + orderId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            order_notified: true
                        },
                        success: function(response) {
                            if (response.success) {
                                console.log('Notification marked read.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Error: ' + error);
                        }
                    });
                });

                $(document).on("click", ".alert .ReadyClose", function() {
                    const orderId = $(this).data('order-id'); // Get order ID

                    // Call the updateNotified function to set is_notified to true
                    $.ajax({
                        url: '/update-manager-kitchen-order-notified/' + orderId, // Trigger the update for this specific order
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: true
                        },
                        success: function(response) {
                            if (response.success) {
                                console.log('Order notification status updated to true.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Error updating notification status: ' + error);
                        }
                    });
                });
            }
        });
    }

    setInterval(checkNewOrders, 3000); // every 10 seconds
</script>
@endpush