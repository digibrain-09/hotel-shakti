@include('restaurants.royal_platter.nav')

<div class="container">
    <div class="col-md-12 mt-2">
        <h4>
            <a href="{{ route('royalplatter.index', ['table' => session('table_name')]) }}" class="text-muted">
                <i class="fas fa-times"></i>
            </a>
        </h4>
        <h4 class="page-header mt-5"><i class="fas fa-utensils"></i>&nbsp; Your Orders</h4>
        <hr>

        <div class="table-responsive" id="order_table">
            <table class="table">
                <thead style="background-color: red;color:#fff">
                    <tr>
                        <th width="40%">Item</th>
                        <th width="10%">Quantity</th>
                        <th width="20%">Price</th>
                        <th width="15%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0 @endphp
                </tbody>
                <tfoot></tfoot>
            </table>
        </div>

        <div id="payment_buttons"></div> <!-- Payment buttons will be appended here -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function fetchOrders() {
            $.ajax({
                url: "{{ route('royalplatter.myLatestOrder') }}",
                type: "GET",
                success: function(response) {
                    let orders = response.data; // Adjust based on API response structure
                    let orderId = response.orderId;
                    let tableBody = $("#order_table tbody");
                    let tableFoot = $("#order_table tfoot");
                    tableBody.empty(); // Clear previous data
                    tableFoot.empty(); // Clear previous footer data
                    let total = 0;

                    let totalData = 0;

                    let cgst = response.cgst;
                    let sgst = response.sgst;
                    let cgst_rate = response.cgst_rate;
                    let sgst_rate = response.sgst_rate;
                    let total_with_tax = response.total_with_tax;

                    if (orders.length > 0) {
                        $.each(orders, function(index, order) {
                            total += order.price * order.quantity; // Accumulate total

                            let row = `
                            <tr>
                                <td>${order.name}</td>
                                <td>${order.quantity}</td>
                                <td>₹${order.price}</td>
                                <td>₹${order.price * order.quantity}</td>    
                            </tr>
                        `;
                            tableBody.append(row);
                        });
                    } else {
                        tableBody.append('<tr><td colspan="4" class="text-center">You have not placed any orders yet.</td></tr>');
                    }

                    // Append total row after clearing previous total data

                    totalData = `<tr>
                                        <td style="padding:5px" colspan="5" class="text-right">
                                            <h5><strong>Sub-Total: ₹${total.toFixed(2)}</strong></h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px" colspan="5" class="text-right">
                                            <h5><strong>CGST (${cgst_rate}%): ₹${cgst.toFixed(2)}</strong></h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px" colspan="5" class="text-right">
                                            <h5><strong>SGST (${sgst_rate}%): ₹${sgst.toFixed(2)}</strong></h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px" colspan="5" class="text-right">
                                            <h5><strong>Total: ₹${total_with_tax.toFixed(2)}</strong></h5>
                                        </td>
                                    </tr>`;


                    tableFoot.append(totalData);

                    // Update payment buttons
                    let paymentButtons = $("#payment_buttons");
                    paymentButtons.empty(); // Clear previous buttons

                    if (total > 0) {
                        let buttonHTML = `
                        <div class="container">
                            <div class="row" id="two-btn">
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('royalplatter.payment') }}">
                                        @csrf
                                        ${orders.map(order => `
                                            <input type="hidden" name="product_name[]" value="${order.name}">
                                            <input type="hidden" name="product_price[]" value="${order.price}">
                                            <input type="hidden" name="product_quantity[]" value="${order.quantity}">
                                            <input type="hidden" name="order_id" value="${orderId}">
                                            <input type="hidden" name="cgst" value="${cgst}">
                                            <input type="hidden" name="sgst" value="${sgst}">
                                            <input type="hidden" name="cgst_rate" value="${cgst_rate}">
                                            <input type="hidden" name="sgst_rate" value="${sgst_rate}">
                                            <input type="hidden" name="total_with_tax" value="${total_with_tax}">
                                        `).join('')}
                                        <input type="hidden" name="price" id="payNowPrice" value="${total_with_tax}">

                                        <button type="submit" class="btn btn-success col-12 mb-3" id="payNowBtn">Pay Now</button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('royalplatter.CaseOnDelivery') }}">
                                        @csrf
                                        ${orders.map(order => `
                                            <input type="hidden" name="product_name[]" value="${order.name}">
                                            <input type="hidden" name="product_price[]" value="${order.price}">
                                            <input type="hidden" name="product_quantity[]" value="${order.quantity}">
                                            <input type="hidden" name="order_id" value="${orderId}">
                                            <input type="hidden" name="cgst" value="${cgst}">
                                            <input type="hidden" name="sgst" value="${sgst}">
                                            <input type="hidden" name="cgst_rate" value="${cgst_rate}">
                                            <input type="hidden" name="sgst_rate" value="${sgst_rate}">
                                            <input type="hidden" name="total_with_tax" value="${total_with_tax}">
                                        `).join('')}
                                        <input type="hidden" name="price" id="cashOnDeliveryPrice" value="${total_with_tax}">
                                        <button type="submit" class="btn btn-secondary col-12 mb-3" id="cashOnDeliveryBtn">Cash On Delivery</button>
                                    </form>
                                </div>
                            </div>
                        </div>`;
                        paymentButtons.append(buttonHTML);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching orders:", error);
                }
            });
        }

        // Fetch orders every 10 seconds
        setInterval(fetchOrders, 10000);

        // Fetch orders immediately on page load
        fetchOrders();
    });
</script>