@include('client.nav')

<script src="https://js.stripe.com/v3/"></script>
<script async src="https://pay.google.com/gp/p/js/pay.js"></script>


<button id="google-pay-button">Pay with Google Pay</button>

<script type="text/javascript">
    var stripe = Stripe('pk_test_51Ozw91SJVwqxTt7UCCF24yG8uzEOnHZqT4cjOZ0nBAVSekgA2e1PVyax0JwNtRRDzggVRiknVc79wiVXlYcMeBjf00ZqF5JRuk'); // Replace with your Stripe public key

    var elements = stripe.elements();
    var googlePayButton = document.getElementById('google-pay-button');

    // Set up the Google Pay client
    const googlePayClient = new google.payments.api.PaymentsClient({
        environment: 'TEST', // Change to 'PRODUCTION' for live payments
    });

    const paymentDataRequest = {
        apiVersion: 2,
        apiVersionMinor: 0,
        allowedPaymentMethods: [{
            type: 'CARD',
            parameters: {
                allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                allowedCardNetworks: ['MASTERCARD', 'VISA'],
            },
            tokenizationSpecification: {
                type: 'PAYMENT_GATEWAY',
                parameters: {
                    'gateway': 'stripe',
                    'gatewayMerchantId': 'acct_1Ozw91SJVwqxTt7U', // Replace with your Stripe merchant ID
                },
            },
        }],
    };

    googlePayClient.isReadyToPay(paymentDataRequest)
        .then(function(response) {
            if (response.result) {
                googlePayButton.addEventListener('click', function() {
                    googlePayClient.loadPaymentData(paymentDataRequest)
                        .then(function(paymentData) {
                            // Send payment data to your Laravel backend for processing
                            fetch('/process-google-pay', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    paymentData: paymentData,
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                alert(data);
                            })
                            .catch(function(err) {
                                alert('Error:', err);
                            });
                        })
                        .catch(function(err) {
                            alert('Error loading payment data:', err);
                        });
                });
            }
        })
        .catch(function(err) {
            alert('Error checking readiness:', err);
        });
</script>
