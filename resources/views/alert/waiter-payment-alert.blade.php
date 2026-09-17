<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@push('scripts')
<script>
    function checkInvoiceData() {
        let ordersHTML = '';
        let Waiteralertordermsg = '';
        let alertSound = '';
        let alertpaymentmsg = '';
        let alertreadytoservedWaitermsg = '';
        let ReadyToservedWaiteralertSound = '';

        $.ajax({
            url: "{{ route('checkWaiterPaymentmode') }}", // Create this route in Laravel
            type: "GET",
            success: function(response) {
                if (response.invoice_data.length > 0) {

                    response.invoice_data.forEach((order) => {
                        Waiteralertordermsg += `<div class="alert alert-info alert-dismissible">
                        You need to set the payment mode for customer code #${order.customer_code} in ${order.table_name}.
                    </div>`;

                        alertSound += `<audio id="alertSound" autoplay>
                        <source src="{{ asset('sounds/alert.mp3') }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                        </audio>`;
                    });


                    // Play the alert sound if there are new orders
                    $('#alertSoundContainer_waiter').html(alertSound);
                    // document.getElementById('alertSound').play();

                }

                $('.Waiteralertordermsg').html(Waiteralertordermsg);

                if (response.ReadytoServedWaiter.length > 0) {

                    response.ReadytoServedWaiter.forEach((order) => {

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

                        alertreadytoservedWaitermsg += `<div class="alert alert-success" style="background:#04AA6D;color:#fff;text-transform: capitalize;">
                            <button type="button"
                                class="close ReadyCloseWaiter closebtn" style="color:#fff"
                                data-order-id="${order.id}">
                                <span>&times;</span>
                            </button>
                            ${order.table_name} | ${order.customer_code} – Order ready (${itemNames})
                        </div>`;

                        ReadyToservedWaiteralertSound += `<audio id="alertSound" autoplay>
                        <source src="{{ asset('sounds/alert2.wav') }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                        </audio>`;

                    });

                    // Play the alert sound if there are new orders
                    $('#ReadyToservedWaiteralertSoundContainer').html(ReadyToservedWaiteralertSound);

                }

                $('.alertreadytoservedWaitermsg').html(alertreadytoservedWaitermsg);

                $(document).on("click", ".alert .ReadyCloseWaiter", function() {
                    const orderId = $(this).data('order-id'); // Get order ID

                    // Call the updateNotified function to set is_notified to true
                    $.ajax({
                        url: '/update-waiter-kitchen-order-notified/' + orderId, // Trigger the update for this specific order
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
    setInterval(checkInvoiceData, 3000);
</script>
@endpush