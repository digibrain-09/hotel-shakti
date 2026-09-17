<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@push('scripts')
<script>
    function checkOrders() {
        let ordersHTML = '';
        let alertordermsg = '';
        let alertSound = '';
        let PrintalertSound = '';
        let alertpaymentmsg = '';
        let alertTakeAwayordermsg = '';
        let alertTakeAwaySound = '';
        let alertreadytoservedmsg = '';
        let ReadyToservedalertSound = '';


        $.ajax({
            url: "{{ route('checkOrders') }}", // Create this route in Laravel
            type: "GET",
            success: function(response) {
                if (response.newOrders.length > 0) {

                    response.newOrders.forEach((order) => {
                        alertordermsg += `<div class="alert alert-info alert-dismissible">
                        <button type="button"
                            class="close order-close closebtn"
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


                    // Play the alert sound if there are new orders
                    $('#alertSoundContainer').html(alertSound);
                    // document.getElementById('alertSound').play();

                }

                $('.alertordermsg').html(alertordermsg);


                if (response.paymentDone.length > 0) {

                    response.paymentDone.forEach((order) => {
                        alertpaymentmsg += `<div class="alert alert-success" style="background:#04AA6D;color:#fff">
                            <button type="button"
                                class="close print-close closebtn" style="color:#fff"
                                data-order-id="${order.order_id}">
                                <span>&times;</span>
                            </button>
                            The customer at ${order.table_name} has completed payment for order #${order.customer_code}. Time to print the bill!
                        </div>`;

                        PrintalertSound += `<audio id="alertSound" autoplay>
                        <source src="{{ asset('sounds/alert2.wav') }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                        </audio>`;

                    });

                    // Play the alert sound if there are new orders
                    $('#PrintalertSoundContainer').html(PrintalertSound);

                }

                $('.alertpaymentmsg').html(alertpaymentmsg);

                if (response.newTakeAwayOrders.length > 0) {

                    response.newTakeAwayOrders.forEach((order) => {

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

                        alertTakeAwayordermsg += `<div class="alert alert-success" style="background:#04AA6D;color:#fff">
                        <button type="button"
                            class="close order-close closebtn"  style="color:#fff"
                            data-order-id="${order.id}">
                            <span>&times;</span>
                        </button>
                            Order #${order.customer_code} is ready for take away.(${itemNames})
                        </div>`;

                        alertTakeAwaySound += `<audio id="alertSound" autoplay>
                            <source src="{{ asset('sounds/alert2.wav') }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                            </audio>`;
                    });


                    // Play the alert sound if there are new orders
                    $('#alertTakeAwaySound').html(alertTakeAwaySound);
                    // document.getElementById('alertSound').play();

                }

                $('.alertTakeAwayordermsg').html(alertTakeAwayordermsg);

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

                $(document).on("click", ".alert .order-close", function() {
                    const orderId = $(this).data('order-id'); // Get order ID

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
                                console.log('Order notification status updated to true.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Error updating notification status: ' + error);
                        }
                    });
                });

                $(document).on("click", ".alert .print-close", function() {
                    const orderId = $(this).data('order-id'); // Get order ID

                    // Call the updateNotified function to set is_notified to true
                    $.ajax({
                        url: '/update-print-notified/' + orderId, // Trigger the update for this specific order
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

    // Check for new orders every 10 seconds
    setInterval(checkOrders, 3000);
</script>
@endpush