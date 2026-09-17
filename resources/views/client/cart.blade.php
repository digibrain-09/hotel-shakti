@include('client.nav')

<div class="container">

    <div class="col-md-12 mt-2">
        <!---cart item here------->
        <h4><a href="{{ route('scantable.index', ['table' => session('table_name')]) }}" class="text-muted" data-id=""><i class="fas fa-times"></i></a></h4>
        <h4 class="page-header mt-5">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp; Shopping Cart
        </h4>
        <hr>

        @php
        $total = 0;
        $matchFound = false;
        @endphp
        @foreach($cart as $details)
        @php
        $total += $details->price * $details->quantity;
        $matchFound = true;
        @endphp

        <div class="row cart_list align-items-center" data-id="{{ $details->id}}">
            <div class="col-6">
                <div class="text">
                    <p class="item_name mb-4">{{ $details->name }}</p>
                    <!-- <input type="number" value="{{ $details['quantity'] }}" class="form-control quantity cart_update col-4" min="1" /> -->
                    <!-- <p>${{ $details['price'] }}</p> -->
                    <!-- <p>${{ $details['price'] * $details['quantity'] }}</p> -->

                </div>
            </div>
            <div class="col">
                <a href="{{ route('scantable.view', $details->id) }}"><img class="product_image float-right" src="{{ url('items/' . $details->image) }}"></a>
            </div>
            <div class="col-1">
                <a href="{{ route('scantable.remove_from_cart', ['id' => $details->id]) }}" class="text-muted float-right cart_remove" onclick="return confirm(' you want to delete?');" id="" data-id=""><i class="fas fa-times"></i></a>
            </div>
        </div>

        <div class="row cart_list mt-3" data-id="{{$details->id}}">
            <div class="col-6">
                <div class="quantity">
                    <input type="number" class="cart_update quantity col-6" min="1" max="9" step="1" value="{{ $details->quantity }}">
                </div>
                <!-- <input type="number" value="{{ $details['quantity'] }}" class="form-control quantity cart_update col-6" min="1" /> -->
            </div>
            <div class="col-5">
                <b>
                    <p class="float-right">₹{{ $details->price }}</p><br>
                    <!-- <p class="float-right">Subtotal :{{ $details['price'] * $details['quantity'] }}</p> -->
                </b>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-6">
                <h3>Total</h3>
            </div>
            <div class="col-6">
                <h3 class="float-right"><strong>₹{{ number_format($total,2) }}</strong></h3>
            </div>
        </div>


        @endforeach
        @if (!$matchFound)
        <p class="text-center">Your Cart is Empty!</p>
        @endif




        <hr>
        <div class="row">
            <div class="col-md-12">
                <?php if ($total == 0) {
                } else { ?>
                    <form method="POST" action="{{ route('scantable.order') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- @foreach($cart as $details)
                        <input type="hidden" name="product_name[]" value="{{ $details->name }}">
                        <input type="hidden" name="product_price[]" value="{{ $details->price }}">
                        <input type="hidden" name="product_quantity[]" value="{{ $details->quantity }}">
                        @endforeach-->
                        <input type="hidden" name="total_amount" value="{{ $total }}">
                        <button type="submit" class="pull-right btn btn-success col-12 mb-3" name="submit">Place Order</button>
                    </form>
                <?php } ?>

                <!-- <div id="google-pay-button"></div> -->
                <!-- <button id="google-pay-button">Pay with Google Pay</button> -->
                <!-- <button id="payButton" class="pull-right btn btn-primary col-12 mb-3">Pay with Google Pay</button> -->
                <!-- <div id="google-pay-button-container"></div> -->
                <!-- <button id="google-pay-button-container" class="pull-right btn btn-primary col-12 mb-3">Pay with Google Pay</button> -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form method="POST" action="{{ route('scantable.order') }}" enctype="multipart/form-data">
                    @csrf
                    <!-- <button type="submit" name="submit" class="pull-right chekout_btn btn btn-success col-12"><span class="glyphicon glyphicon-floppy-disk"></span>Checkout</button> -->
                </form>
            </div>
        </div>




        <!---cart item here------->
    </div>
</div>

<script type="text/javascript">
    // $(".cart_update").change(function(e) {
    //     e.preventDefault();

    //     var ele = $(this);

    //     $.ajax({
    //         url: "{{ route('scantable.update_cart') }}",
    //         method: "patch",
    //         data: {
    //             _token: '{{ csrf_token() }}',
    //             id: ele.parents(".cart_list").attr("data-id"),
    //             quantity: ele.parents(".cart_list").find(".quantity").val()
    //         },
    //         success: function(response) {
    //             window.location.reload();
    //         }
    //     });
    // });



    $(document).ready(function() {
        jQuery('<div class="quantity-nav"><button class="quantity-button quantity-up">&#xf106;</button><button class="quantity-button quantity-down">&#xf107</button></div>').insertAfter('.quantity input');
        jQuery('.quantity').each(function() {
            var spinner = jQuery(this),
                input = spinner.find('input[type="number"]'),
                btnUp = spinner.find('.quantity-up'),
                btnDown = spinner.find('.quantity-down'),
                min = input.attr('min'),
                max = input.attr('max');

            // Handle quantity-up button click
            btnUp.click(function() {
                var oldValue = parseFloat(input.val());
                var newVal = oldValue >= max ? oldValue : oldValue + 1;
                updateCart(newVal, $(this));
            });

            // Handle quantity-down button click
            btnDown.click(function() {
                var oldValue = parseFloat(input.val());
                var newVal = oldValue <= min ? oldValue : oldValue - 1;
                updateCart(newVal, $(this));
            });

            // Handle direct input changes
            input.on('input', function() {
                var newVal = parseFloat(input.val());
                if (newVal >= min && newVal <= max) {
                    updateCart(newVal, $(this));
                }
            });

            function updateCart(newVal, ele) {
                spinner.find("input").val(newVal);
                spinner.find("input").trigger("change");

                $.ajax({
                    url: "{{ route('scantable.update_cart') }}",
                    method: "patch",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: ele.parents(".cart_list").attr("data-id"),
                        quantity: newVal
                    },
                    success: function(response) {
                        window.location.reload();
                    }
                });
            }
        });
    });
</script>


<script type="text/javascript">
    // const baseRequest = {
    //     apiVersion: 2,
    //     apiVersionMinor: 0,
    // };

    // const allowedCardNetworks = ["MASTERCARD", "VISA"];
    // const allowedCardAuthMethods = ["PAN_ONLY", "CRYPTOGRAM_3DS"];

    // const tokenizationSpecification = {
    //     type: 'PAYMENT_GATEWAY',
    //     parameters: {
    //         gateway: 'stripe', // Updated to 'stripe' for Stripe payment gateway
    //         gatewayMerchantId: 'acct_1Ozw91SJVwqxTt7U' // Replace with your actual Stripe Merchant ID
    //     }
    // };

    // const cardPaymentMethod = {
    //     type: 'CARD',
    //     parameters: {
    //         allowedAuthMethods: allowedCardAuthMethods,
    //         allowedCardNetworks: allowedCardNetworks
    //     },
    //     tokenizationSpecification: tokenizationSpecification
    // };

    // const googlePayClient = new google.payments.api.PaymentsClient({
    //     environment: 'TEST' // Change to 'PRODUCTION' for live environment
    // });

    // const paymentDataRequest = Object.assign({}, baseRequest, {
    //     allowedPaymentMethods: [cardPaymentMethod],
    //     transactionInfo: {
    //         totalPriceStatus: 'FINAL',
    //         totalPrice: '10.00',
    //         currencyCode: 'USD',
    //     },
    //     merchantInfo: {
    //         merchantId: 'acct_1Ozw91SJVwqxTt7U', // Replace with your Stripe Merchant ID
    //         merchantName: 'To ScanTable'
    //     }
    // });

    // const isReadyToPayRequest = Object.assign({}, baseRequest, {
    //     allowedPaymentMethods: [cardPaymentMethod]
    // });

    // googlePayClient.isReadyToPay(isReadyToPayRequest)
    //     .then(response => {
    //         if (response.result) {
    //             displayGooglePayButton();
    //         }
    //     })
    //     .catch(err => console.error(err));

    // function displayGooglePayButton() {
    //     const button = googlePayClient.createButton({
    //         onClick: onGooglePayButtonClicked
    //     });
    //     document.getElementById('google-pay-button-container').appendChild(button);
    // }

    // function onGooglePayButtonClicked() {
    //     googlePayClient.loadPaymentData(paymentDataRequest)
    //         .then(paymentData => {
    //             processPayment(paymentData);
    //         })
    //         .catch(err => console.error(err));
    // }

    // function processPayment(paymentData) {
    //     // Handle the payment data securely
    //     console.log(paymentData); // Replace this with a server-side API call to process payment via Stripe
    // }





    // var stripe = Stripe('pk_test_51Ozw91SJVwqxTt7UCCF24yG8uzEOnHZqT4cjOZ0nBAVSekgA2e1PVyax0JwNtRRDzggVRiknVc79wiVXlYcMeBjf00ZqF5JRuk'); // Use your Stripe publishable key
    // var googlePayClient = new google.payments.api.PaymentsClient({
    //     environment: 'TEST' // Change to 'PRODUCTION' for live payments
    // });




    // var button = googlePayClient.createButton({
    //     onClick: onGooglePayButtonClick,
    //     allowedPaymentMethods: [{
    //         type: 'CARD',
    //         parameters: {
    //             allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
    //             allowedCardNetworks: ['VISA', 'MASTERCARD'],
    //         },
    //         tokenizationSpecification: {
    //             type: 'PAYMENT_GATEWAY',
    //             parameters: {
    //                 'gateway': 'stripe',
    //                 'gatewayMerchantId': 'acct_1Ozw91SJVwqxTt7U',
    //             }
    //         }
    //     }]
    // });

    // document.getElementById('google-pay-button').appendChild(button);

    // function onGooglePayButtonClick() {
    //     const paymentRequest = stripe.paymentRequest({
    //         country: 'US',
    //         currency: 'usd',
    //         total: {
    //             label: 'Total',
    //             amount: 5000, // Amount in cents
    //         },
    //         requestPayerName: true,
    //         requestPayerEmail: true,
    //     });

    //     paymentRequest.canMakePayment().then(function(result) {
    //         alert(result);
    //         if (result && result.paymentMethod) {
    //             paymentRequest.createPaymentMethod().then(function(paymentMethodResult) {
    //                 // On your frontend, after getting the client secret, confirm the payment
    //                 fetch('/process-google-pay', {
    //                         method: 'POST',
    //                         headers: {
    //                             'Content-Type': 'application/json',
    //                         },
    //                         body: JSON.stringify({
    //                             payment_method_id: paymentMethodResult.paymentMethod.id,
    //                         })
    //                     }).then(response => response.json())
    //                     .then(paymentIntent => {
    //                         stripe.confirmCardPayment(paymentIntent.client_secret).then(function(result) {
    //                             if (result.error) {
    //                                 alert('Payment failed:', result.error.message);
    //                             } else {
    //                                 alert('Payment successful:', result.paymentIntent);
    //                             }
    //                         });
    //                     });
    //             });
    //         } else {
    //             alert('Payment method not supported or no payment method available');
    //         }
    //     }).catch(function(error) {
    //         console.error('Error checking payment capability:', error);
    //     });
    // }




    // $('#google-pay-button').on('click', function() {
    //     const stripe = Stripe('pk_test_51Ozw91SJVwqxTt7UCCF24yG8uzEOnHZqT4cjOZ0nBAVSekgA2e1PVyax0JwNtRRDzggVRiknVc79wiVXlYcMeBjf00ZqF5JRuk'); // Use your Stripe publishable key

    //     alert('data'); // Ensure button click works

    //     // Use jQuery to make the POST request
    //     $.ajax({
    //         url: '/process-google-pay',
    //         type: 'POST',
    //         headers: {
    //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
    //         },
    //         success: function(response) {
    //             const clientSecret = response.clientSecret;

    //             // Confirm the payment with Google Pay
    //             stripe.confirmPayment({
    //                 clientSecret: clientSecret,
    //                 payment_method: {
    //                     type: 'card', // For Google Pay, Stripe uses "card" under the hood
    //                 },
    //                 payment_method_options: {
    //                     google_pay: {
    //                         setup_future_usage: 'off_session', // Optional: Save for future usage
    //                     },
    //                 },
    //             }).then(function(result) {
    //                 if (result.error) {
    //                     alert('Payment failed: ' + result.error.message);
    //                 } else {
    //                     if (result.paymentIntent.status === 'succeeded') {
    //                         alert('Payment successful!');
    //                     } else {
    //                         alert('Payment incomplete. Please try again.');
    //                     }
    //                 }
    //             });
    //         },
    //         error: function(xhr, status, error) {
    //             alert('Failed to create payment intent: ' + error);
    //         }
    //     });
    // });