<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<form action="{{ route('scantable.PaymentSuccess') }}" method="POST">
    @csrf
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        var options = {
            "key": "rzp_test_3FofCctESjWgM5", // Razorpay key
            "amount": "{{ $amount }}", // Amount in paise
            "currency": "INR",
            "order_id": "{{ $razorpayOrderId }}",
            "name": "ScanTable",
            "description": "Payment for order #{{ $session_code }} at {{ $table_name }} is complete.",
            "image": "https://example.com/logo.png",
            "handler": function(response) {
                // Send payment details to the server
                var paymentDetails = {
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature,
                    order_id: `{{ $OrderId }}`,
                    cgst: `{{ $cgst }}`,
                    sgst: `{{ $sgst }}`,
                    cgst_rate: `{{ $cgst_rate }}`,
                    sgst_rate: `{{ $sgst_rate }}`,
                    total_with_tax: `{{ $total_with_tax }}`,
                    coupon_code: `{{ $coupon_code }}`,
                    discount: `{{ $discount }}`,
                };

                $.ajax({
                    url: "{{ route('scantable.PaymentSuccess') }}",
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: paymentDetails,
                    success: function(data) {
                        // Redirect to the URL provided by the server
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            alert("Payment processed, but no redirect URL provided.");
                        }
                    },
                    error: function(xhr) {
                        // Handle any errors
                        console.error(xhr.responseText);
                        alert("Something went wrong. Please try again.");
                    }
                });
            },
            // "prefill": {
            //     "name": "Customer Name",
            //     "email": "customer@example.com",
            // },
            "modal": {
                "ondismiss": function() {
                    // Redirect to cart on payment cancellation
                    window.location.href = "{{ route('scantable.PaymentCancel') }}";
                }
            },
            "theme": {
                "color": "#F37254"
            },
        };

        var rzp1 = new Razorpay(options);
        rzp1.open();
    </script>
</form>