@push('scripts')
<script>
    function checkNewKitchenOrders() {
        let Kitchenalertmsg = '';
        let KitchenalertSoundContainer = '';

        let KitchenTakeAwayalertmsg = '';
        let KitchenTakeAwayalertSoundContainer = '';
        $.ajax({
            url: "{{ route('checkNewKitchenOrders') }}",
            type: "GET",
            success: function(response) {
                if (response.newOrders.length > 0) {
                    response.newOrders.forEach((order) => {
                        Kitchenalertmsg += `<div class="alert alert-info alert-dismissible">
                            <button type="button"
                                class="close kitchenOrderClose  closebtn"
                                data-order-id="${order.id}">
                                <span>&times;</span>
                            </button>
                            You have a new order #${order.customer_code} in ${order.table_name}.
                        </div>`;

                        KitchenalertSoundContainer += `<audio id="alertSound" autoplay>
                            <source src="{{ asset('sounds/alert.mp3') }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>`;
                    });

                    $('#KitchenalertSoundContainer').html(KitchenalertSoundContainer);
                }

                $('.Kitchenalertmsg').html(Kitchenalertmsg);


                if (response.newTakeAwayOrders.length > 0) {
                    response.newTakeAwayOrders.forEach((order) => {
                        KitchenTakeAwayalertmsg += `<div class="alert alert-info alert-dismissible">
                            <button type="button"
                                class="close kitchenOrderClose  closebtn"
                                data-order-id="${order.id}">
                                <span>&times;</span>
                            </button>
                            New Take Away Order from #${order.customer_code} Please check.
                        </div>`;

                        KitchenTakeAwayalertSoundContainer += `<audio id="alertSound" autoplay>
                            <source src="{{ asset('sounds/alert.mp3') }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>`;
                    });

                    $('#KitchenTakeAwayalertSoundContainer').html(KitchenTakeAwayalertSoundContainer);
                }

                $('.KitchenTakeAwayalertmsg').html(KitchenTakeAwayalertmsg);



                $(document).on("click", ".alert .kitchenOrderClose", function() {
                    const orderId = $(this).data('order-id');
                    $.ajax({
                        url: '/update-Kitchen-order-notified/' + orderId,
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

            }
        });
    }

    setInterval(checkNewKitchenOrders, 3000); // every 10 seconds
</script>
@endpush