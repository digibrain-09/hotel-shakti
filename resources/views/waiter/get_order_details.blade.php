@extends('layouts.admin')
<style>
    .customer_tr span,
    .customer_tr p {
        color: red;
    }

    .promo-container {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 25px;
        padding: 5px 5px 5px 15px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);

    }

    .coupon-notice {
        background: #f9f9f9;
        padding: 15px;
        /* border: 1px solid #eee; */
        border-radius: 5px;
        font-size: 14px;
        color: #333;
        margin-top: 10px;
    }

    .coupon-notice a {
        color: #e7272d !important;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
    }

    .coupon-form {
        /* display: none; */
        margin-top: 15px;
    }

    .coupon-input {
        border: none;
        outline: none;
        font-size: 14px;
        flex: 1;
        padding: 10px;
        background: transparent;
        color: #555;
    }


    .apply-btn {
        border: none;
        outline: none;
        padding: 10px 20px;
        border-radius: 20px;
        color: #fff;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
</style>
@section('content')
<!-- Breadcrumb Navigation -->
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{route('OrderDetails')}}" class="cursor-pointer">
        <span class="text-gray-500 hover:text-primary transition-all duration-200">Orders</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
    <span class="text-foreground font-medium">Order Details</span>
</div>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Order Management</h1>
        <p class="text-gray-500 text-sm md:text-base">View active orders, review order details, and complete payment or checkout with ease.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 ml-auto md:ml-0">
        <!-- <button onclick="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Item</span>
        </button> -->
    </div>
</div>

<!-- Alerts -->
@if(Session::get('test') == 'restaurant_waiter')
<div class="Waiteralertordermsg"></div>
<div id="alertSoundContainer_waiter"></div>
<div class="alertreadytoservedWaitermsg"></div>
<div id="ReadyToservedWaiteralertSoundContainer"></div>
@endif

<!-- Orders Table -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">All Orders</h3>
    </div>
    <div class="bg-white rounded-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]" id="orderTable">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Customer ID</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Created</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Status</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Confirm By</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Payment Mode</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($orders as $index => $order)
                    @php
                    $matchFound = true; // Set the flag to true
                    $rowClass = '';
                    $statusColor = $order->status === 'processed' ? 'orange' : ($order->status === 'completed' ? 'green' : '');
                    if ($order->status === 'processed') {
                    $rowClass = $order->customer_code === $last->customer_code ? 'customer_tr' : '';
                    }
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50 {{ $rowClass }}">
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-medium">{{ $order->customer_code }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <p class="text-foreground text-sm font-medium">{{ $order->latest_created_at }}</p>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-xs text-white font-medium px-2 py-2 rounded-full" style="background-color:{{ $statusColor }};">{{ $order->status }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $order->order_confirm_by }}</p>
                        </td>
                        <td class="py-4 px-2">
                            @if ($order->status === 'completed')
                            @foreach($invoice_data as $invoice_detail)
                            @if ($invoice_detail->order_id == $order->id)
                            <select class="px-3 md:px-4 py-2.5 md:py-3 text-sm md:text-base border border-border rounded-button focus:bg-white hover:border-primary focus:border-primary transition-all duration-300 font-semibold cursor-pointer payment-method-select" name="payment-method-select"
                                aria-label="select"
                                data-order-id="{{ $order->id }}">
                                @foreach ($invoice_data as $invoice)
                                @if($invoice->customer_code == $order->customer_code)
                                <option value="" {{ $invoice->payment_mode == null ? 'selected' : '' }}>Select</option>
                                <option value="cash" {{ $invoice->payment_mode == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ $invoice->payment_mode == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="upi" {{ $invoice->payment_mode == 'upi' ? 'selected' : '' }}>UPI</option>
                                @endif
                                @endforeach
                            </select>
                            @endif
                            @endforeach
                            @else
                            <span class="text-foreground text-sm font-semibold" style="text-transform: capitalize;">Not Set</span>
                            @endif
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-2">
                                <button onclick="openOrderModal('{{ $order->customer_code }}')" class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button hover:bg-primary-hover transition-all duration-200 cursor-pointer view-order-btn">View Details</button>

                                @if($order->status == 'processed')
                                <button onclick="openCompleteOrderModal()" data-customer-code="${order.customer_code}" data-order-id="${order.id}" class="px-3 py-1.5 border border-border text-foreground text-xs font-medium rounded-button hover:border-primary hover:text-primary transition-all duration-200 cursor-pointer">Complete Order</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if (!$matchFound)
                    <tr>
                        <td class="text-center pt-3 pb-3" colspan="6" style="background-color:yellow">No orders found for this table.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 px-3 pb-4">
    <div class="text-sm text-gray-600"> Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }}
        of {{ $orders->total() }} entries</div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Previous --}}
        @if ($orders->onFirstPage())
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Previous
        </span>
        @else
        <a href="{{ $orders->previousPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Previous
        </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
        @if ($page == $orders->currentPage())
        <span class="px-3 py-2 bg-primary text-white rounded-lg text-sm">
            {{ $page }}
        </span>
        @else
        <a href="{{ $url }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            {{ $page }}
        </a>
        @endif
        @endforeach

        {{-- Next --}}
        @if ($orders->hasMorePages())
        <a href="{{ $orders->nextPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Next
        </a>
        @else
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Next
        </span>
        @endif
    </div>

</div>


<!-- Order Modal -->
@foreach ($orders as $index => $order)
<div id="OrderModal-{{ $order->customer_code }}" class="custom-modal hidden OrderModal">
    <div class="custom-modal-backdrop" onclick="closeOrderModal('{{ $order->customer_code }}')"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Order History</h2>

        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Item</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Price</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Qty</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Add-ons</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Note</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Date & Time</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Sub</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Ready To Served</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">
                @php
                $total = 0; // Initialize total variable
                $matchFound = false; // Flag to track if a match is found
                @endphp

                @foreach ($data as $value)
                @if ($order->customer_code == $value->customer_code)
                @php
                $matchFound = true; // Set the flag to true
                $json = json_decode($value->data);
                @endphp
                @foreach ($json as $index => $detail)
                @php
                $itemTotal = $detail->price * $detail->quantity;

                $addonTotal = 0;
                $addonNames = [];

                $addonData = json_decode($detail->addons, true);

                if (is_array($addonData)) {
                foreach ($addonData as $addon) {
                $addonNames[] = $addon['name']; // append names
                $addonTotal += $addon['price'] * $detail->quantity; // accumulate total price
                }
                }

                $totalWithAddons = $itemTotal + $addonTotal;
                $total += $totalWithAddons;

                $note_data = $detail->note == null ? '-' : $detail->note;

                // Convert array of addon names to comma-separated string
                $addon = empty($addonNames) ? '-' : implode(', ', $addonNames);

                $ServedStatus = '';

                $jsonItemData = json_decode($value->item_data, true);

                $currentItem = null;

                foreach ($jsonItemData as $item) {
                if ($item['item_id'] == $detail->item_id) {
                $currentItem = $item;
                break;
                }
                }



                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="py-4 px-2">
                        <div>
                            <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $detail->name }}</p>
                        </div>
                    </td>
                    <td class="py-4 px-2">
                        <span class="text-foreground text-sm font-semibold">{{ $detail->price }}</span>
                    </td>
                    <td class="py-4 px-2">
                        <p class="text-foreground text-sm font-medium">{{ $detail->quantity }}</span>
                    </td>
                    <td class="py-4 px-2">
                        <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $addon }}</p>
                    </td>
                    <td class="py-4 px-2">
                        <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $note_data }}</p>
                    </td>
                    <td class="py-4 px-2">
                        <span class="text-foreground text-sm font-semibold">{{ $value->created_at }}</span>
                    </td>
                    <td class="py-4 px-2">
                        <span class="text-foreground text-sm font-semibold text-success">₹{{ $totalWithAddons }}</span>
                    </td>
                    <td class="py-4 px-2">
                        @if ($currentItem && isset($currentItem['ReadytoServed']) && $currentItem['ReadytoServed'] === 'Yes') 
                        <span class="text-foreground text-sm font-semibold" style="color: green;">Yes</span>
                        @else
                        <span class="text-foreground text-sm font-semibold" style="color: red;">No</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                @endif
                @endforeach
                @if (!$matchFound)
                <tr>
                    <td class="text-center pt-3 pb-3" colspan="8" style="background-color:yellow">A customer has recently logged into their account but has not placed an order yet.</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" class="text-right">
                        <h3><strong>Total ₹{{ $total }}</strong></h3>
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-4 text-right">
            <button type="button" onclick="closeOrderModal('{{ $order->customer_code }}')" class="px-4 py-2 border rounded mr-1 cursor-pointer">
                Cancel
            </button>
        </div>
    </div>
</div>
@endforeach


<div id="CompleteOrderModal" class="custom-modal hidden CompleteOrderModal">
    <div class="custom-modal-backdrop" onclick="closeCompleteOrderModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Complete Order</h2>
        <form id="couponForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod">
            @foreach ($orders2 as $val)
            <input type="hidden" name="total" value="{{ $val->total_amount ?? 0 }}"> {{-- current order/cart total --}}
            <input type="hidden" name="data" value="{{ json_encode($val->data) }}">
            @endforeach

            @if($GST_status)
            <input type="hidden" name="cgst" value="{{ $cgst ?? 0 }}">
            <input type="hidden" name="sgst" value="{{ $sgst ?? 0 }}">
            <input type="hidden" name="cgst_rate" value="{{ $cgst_rate ?? 0 }}">
            <input type="hidden" name="sgst_rate" value="{{ $sgst_rate ?? 0 }}">
            @else
            <input type="hidden" name="cgst" value="0">
            <input type="hidden" name="sgst" value="0">
            <input type="hidden" name="cgst_rate" value="0">
            <input type="hidden" name="sgst_rate" value="0">
            @endif
            <div class="coupon-notice">
                <span>
                    Have a coupon? <a id="toggleCoupon">Enter your code here</a>
                </span>
                <div class="promo-container mt-3">
                    <input type="text" name="code" class="coupon-input" placeholder="Enter your coupon code" required>
                    <button type="submit" class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button hover:bg-primary-hover transition-all duration-200 cursor-pointer apply-btn">Apply</button>
                </div>
            </div>
        </form>

        <div class="mt-4 text-right">
            <h4>
                @foreach ($orders2 as $val)
                <strong>Sub-Total: ₹{{ $val->total_amount ?? 0 }}</strong>
                @endforeach
            </h4>
        </div>

        <div class="text-right" id="discountRow" style="display:none"></div>
        <div class="text-right" id="grandTotalRow"></div>
        <div class="text-right" id="cgst"></div>
        <div class="text-right" id="sgst"></div>
        <div class="text-right" id="total_with_tax"></div>
        <div class="text-right" id="final_total"></div>

        @if($GST_status)
        <div class="text-right taxRows">
            <h4><strong>CGST ({{ $cgst_rate }}%): ₹{{ number_format(session('cgst', $cgst),  2, '.', '') }}</strong></h4>
        </div>
        <div class="text-right taxRows">
            <h4><strong>SGST ({{ $sgst_rate }}%): ₹{{ number_format(session('sgst', $sgst),  2, '.', '') }}</strong></h4>
        </div>
        <div class="text-right taxRows">
            <h4><strong>To Pay: ₹{{ number_format(session('total_with_tax', $total_with_tax),  2, '.', '') }}</strong></h4>
        </div>
        @else
        <div class="text-right taxRows">
            <h4><strong>To Pay: ₹{{ number_format(session('final_total', $total),  2, '.', '') }}</strong></h4>
        </div>
        @endif

        <div class="mt-4 text-right">

            <form method="POST" action="{{ route('CompleteOrder') }}">
                @csrf

                @foreach ($orders as $index => $detail)
                @foreach ($data as $value)
                <?php $json = json_decode($value->data); ?>
                @if($detail->customer_code == $value->customer_code && $value->status == 'processed')
                @foreach ($json as $item)
                <input type="hidden" name="product_name[]" value="{{ $item->name }}">
                <input type="hidden" name="product_price[]" value="{{ $item->price }}">
                <input type="hidden" name="product_quantity[]" value="{{ $item->quantity }}">

                <input type="hidden" name="addons[]" value="{{ json_encode($item->addons) }}">

                <input type="hidden" name="customer_code" value="{{ $value->customer_code }}">
                <input type="hidden" name="order_id" value="{{ $value->id }}">
                <input type="hidden" name="table_id" value="{{ $table_id }}">
                @endforeach
                @endif
                @endforeach
                @endforeach

                @if($GST_status == true)
                <input type="hidden" name="cgst" id="form_cgst" value="{{  number_format(session('cgst', $cgst), 2, '.', '') }}">
                <input type="hidden" name="sgst" id="form_sgst" value="{{  number_format(session('sgst', $sgst), 2, '.', '') }}">
                <input type="hidden" name="cgst_rate" value="{{ $cgst_rate }}">
                <input type="hidden" name="sgst_rate" value="{{ $sgst_rate }}">
                <input type="hidden" name="total_with_tax" id="form_total_with_tax" value="{{ number_format(session('total_with_tax', $total_with_tax), 2, '.', '') }}">
                <input type="hidden" name="price" id="payNowPrice" value="{{ number_format(session('total_with_tax', $total_with_tax), 2, '.', '') }}">

                <input type="hidden" name="coupon" id="coupon" value="{{  session('coupon') ? session('coupon') : 'null' }}">
                <input type="hidden" name="discount" id="discount" value="{{  number_format(session('discount', 0), 2, '.', '') }}">

                @else
                <input type="hidden" name="cgst" value="0">
                <input type="hidden" name="sgst" value="0">
                <input type="hidden" name="cgst_rate" value="0">
                <input type="hidden" name="sgst_rate" value="0">
                <input type="hidden" name="total_with_tax" id="form_total_with_tax" value="{{ number_format(session('total_with_tax', $total), 2, '.', '') }}">
                <input type="hidden" name="price" id="payNowPrice" value="{{ number_format(session('total_with_tax', $total), 2, '.', '') }}">

                <input type="hidden" name="coupon" id="coupon" value="{{ session('coupon') ? session('coupon') : 'null' }}">
                <input type="hidden" name="discount" id="discount" value="{{  number_format(session('discount', 0), 2, '.', '') }}">
                @endif
                <button type="button" onclick="closeCompleteOrderModal()" class="px-4 py-2 border rounded mr-1 cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="submit px-4 py-2 bg-primary text-white border rounded cursor-pointer">
                    Complete Order
                </button>
            </form>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function openOrderModal(customerCode) {
        document
            .getElementById('OrderModal-' + customerCode)
            .classList.remove('hidden');
        document.body.style.overflow = "hidden";
    }

    function closeOrderModal(customerCode) {
        document
            .getElementById('OrderModal-' + customerCode)
            .classList.add('hidden');
        document.body.style.overflow = "";
    }

    function openCompleteOrderModal() {
        document.getElementById("CompleteOrderModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeCompleteOrderModal() {
        document.getElementById("CompleteOrderModal").classList.add("hidden");
        document.body.style.overflow = "";
    }

    $(document).ready(function() {
        $(document).on('change', '.payment-method-select', function() {

            var payment_mode = $(this).val();
            var orderId = $(this).data('order-id');

            // ✅ Remove alert
            $('#order-alert-' + orderId).fadeOut(200, function() {
                $(this).remove();

                // 🛑 Stop sound if no print alerts left
                if ($('#Waiteralertordermsg .alert').length === 0) {
                    stopManagerorderAlertSound();
                }
            });


            $.ajax({
                url: "{{ route('updatePaymentMethod') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: orderId,
                    payment_mode: payment_mode
                },
                success: function(response) {
                    alert('Payment method updated successfully!');
                },
                error: function() {
                    alert('Something went wrong while updating.');
                }
            });
        });
    });

    jQuery(document).ready(function($) {
        $("#couponForm").on("submit", function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('waiter.coupon_apply') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        // Show discount row
                        $(".taxRows").hide();
                        $("#discountRow").html(
                            `<h4><strong>Coupon (${response.coupon}): - ₹${response.discount}</strong></h4>`
                        ).show();

                        // Show grand total
                        $("#grandTotalRow").html(
                            `<h4><strong>Grand Total: ₹${response.final_total}</strong></h4>`
                        );

                        // Show GST/To Pay
                        let taxHtml = '';
                        if (parseFloat(response.cgst) > 0 || parseFloat(response.sgst) > 0) {
                            $("#cgst").html(`<h4><strong>CGST({{ $cgst_rate }}%): ₹${response.cgst}</strong></h4>`);
                            $("#sgst").html(`<h4><strong>SGST({{ $sgst_rate }}%): ₹${response.sgst}</strong></h4>`);
                            $("#total_with_tax").html(`<h4><strong>To Pay: ₹${response.total_with_tax}</strong></h4>`);

                            $("#payNowPrice").val(response.total_with_tax);
                            $("#form_cgst").val(response.cgst);
                            $("#form_sgst").val(response.sgst);
                            $("#form_total_with_tax").val(response.total_with_tax);

                            $("#coupon").val(response.coupon);
                            $("#discount").val(response.discount);
                            // taxHtml += `<h4 class="float-right"><strong>CGST: ₹${response.cgst}</strong></h4><br>`;
                            // taxHtml += `<h4 class="float-right"><strong>SGST: ₹${response.sgst}</strong></h4><br>`;
                            // taxHtml += `<h4 class="float-right"><strong>To Pay: ₹${response.total_with_tax}</strong></h4>`;
                        } else {
                            // taxHtml = `<h4 class="float-right"><strong>To Pay: ₹${response.final_total}</strong></h4>`;
                            $("#final_total").html(`<h4><strong>To Pay: ₹${response.final_total}</strong></h4>`);

                            $("#payNowPrice").val(response.total_with_tax);
                            $("#form_total_with_tax").val(response.total_with_tax);

                            $("#coupon").val(response.coupon);
                            $("#discount").val(response.discount);
                        }
                        $("#taxRows").html(taxHtml);
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error);
                }
            });
        });
    });

    function fetchRealTimeData() {
        // Send an AJAX request to the server-side PHP endpoint
        $.ajax({
            url: '{{ url("check_condition") }}',
            type: 'GET',
            success: function(response) {
                if (response.condition) {
                    $('#tables-container #table_td').each(function() {
                        let $this = $(this);
                        var tdText = $this.find('.table_name').attr('data-table-name');

                        // 🔥 RESET COLORS FIRST
                        $this.removeClass('td-text-orange td-text-blue');
                        $this.addClass('td-text-green');

                        response.customer.forEach((customer) => {
                            if (tdText === customer.table_name) {
                                $(this).addClass('td-text-orange');

                                // Update only the .createTime inside this matched table block
                                $(this).find('.createTime').html(customer.time_diff);
                                $(this).removeClass('td-text-green');
                                $(this).removeClass('td-text-blue');
                            }
                        });

                        response.invoice_data.forEach((invoice_data) => {
                            if (tdText === invoice_data.table_name) {
                                $(this).addClass('td-text-blue');
                                $(this).removeClass('td-text-orange');
                                $(this).removeClass('td-text-green');
                            }
                        });


                    });
                }
            },
            error: function() {
                console.log('Error occurred during AJAX request');
            }
        });


    }

    // Call the function every second
    setInterval(fetchRealTimeData, 1000);
</script>
@endsection