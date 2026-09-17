<style>
    .sidebar {
        height: 100%;
        width: 0;
        position: fixed;
        top: 0;
        right: 0;
        background-color: #363831;
        overflow-y: auto;
        transition: 0.3s;
        display: flex;
        flex-direction: column;
        z-index: 1000;
    }

    .quantity input {
        padding-left: 10px !important;
    }

    .sidebar p {
        margin-bottom: 0;
    }

    .place-order-btn {
        font-weight: 500;
        background-color: #e7272d !important;
        color: #fff !important;
        border: 1px solid #e7272d;
        box-shadow: none;
    }

    .place-order-btn:hover {
        font-weight: 500;
        background-color: #e7272d;
        color: #fff !important;
        border: 1px solid #e7272d;
    }

    .place-order-btn:focus {
        font-weight: 500;
        background-color: #e7272d;
        color: #fff !important;
        border: 1px solid #e7272d;
    }

    .quantity input {
        width: 100px;
        height: 42px;
        line-height: 1.65;
        float: left;
        display: block;
        padding: 0;
        margin: 0;
        padding-left: 20px;
        border: none;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.08);
        font-size: 1rem;
        border-radius: 4px;
        background: #fff;
    }
</style>
<div id="mySidebar" class="sidebar">

    <div class="px-6 pt-5 pb-2 border-b border-gray-50">
        <div class="space-y-4">
            <div class="flex gap-4">
                <div class="flex-1 min-w-0">
                    <h2 class="mt-2 text-white" style="text-transform:capitalize;">Table: {{ $table_name }}</h2>
                </div>
                <span class="close-btn ml-auto text-3xl cursor-pointer text-white" onclick="closeSidebar()">&times;</span>
            </div>
        </div>
    </div>


    <!-- Sidebar Content -->
    <div class="pt-5 px-3 pb-3">
        <div class="p-4">
            <div class="space-y-4">

                @php $total = 0 @endphp
                @if(count($cart_items) > 0)
                @foreach($cart_items as $details)
                @php
                $matchFound = true;
                $itemTotal = $details->price * $details->quantity;


                $addonTotal = 0;
                $addonNames = [];

                $addonData = json_decode($details->addons, true);

                if (is_array($addonData)) {
                foreach ($addonData as $addon) {
                if (isset($addon['name']) && isset($addon['price'])) {
                $addonNames[] = $addon['name'];
                $addonTotal += $addon['price'] * $details->quantity;
                }
                }
                }

                $totalWithAddons = $itemTotal + $addonTotal;
                $total += $totalWithAddons;



                $disabled = $details->quantity == 1 ? 'disabled' : '';
                @endphp

                <div class="flex gap-4 pb-4 mt-3 md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-white">{{ $details->name }}</h4>
                        <h5 class="text-white">₹{{ $details->price}}</h5>
                        <p style="color:gray">{{ implode(', ', $addonNames) }}</p>
                        @if($details->note)<p style="color:gray">"{{ $details->note }}"</p>@endif

                        <label for="offcanvas-toggle" data-id="{{ $details->item_id }}" style="font-size: 18px;color:#fff" class="edit-item item_details cursor-pointer">Edit Item <i class='fa fa-caret-right'></i></label>
                    </div>
                    <div class="quantity">
                        <input type="number" style="" class="cart_update quantity col-6"
                            min="1" max="9" step="1"
                            data-id="{{ $details->id }}"
                            value="{{ $details->quantity }}">

                    </div>
                    <h3 class="item-total text-white" id="item-total-{{ $details->id }}" data-price="{{ $details->price }}">
                        ₹{{ $totalWithAddons }}
                    </h3>
                    <a href="{{ route('RemovefromCart', ['item_id' => $details->id]) }}" style="color:#fff" class="float-right cart_remove md:text-2xl" onclick="return confirm(' you want to delete?');" id="" data-id="">&times;</a>
                </div>
                @endforeach
                @else
                <p class="text-center mt-5 text-white">Your Cart is Empty!</p>
                @endif

            </div>
        </div>
    </div>

    <div class="px-6 pt-5 mt-auto border-t border-gray-50" bis_skin_checked="1">
        <div class="flex items-center gap-3" bis_skin_checked="1">
            <div class="flex-1 min-w-0">
                <h4 class="mt-2 menu-title text-white">Total</h4>
            </div>
            <h3 class="float-right card-title ml-auto text-white">
                <strong id="cart-total">₹{{ number_format($total,2) }}</strong>
            </h3>
        </div>
        @php
        $tableId = session('tableId');
        $customer = DB::table('customers')
        ->join('orders', 'customers.customer_code', '=', 'orders.customer_code')
        ->where('customers.table_id', $tableId)
        ->where('orders.status', 'processed') // ✅ Only use customer with active (not completed) orders
        ->select('customers.*')
        ->first();

        $hasPhone = !empty($customer?->phone);
        @endphp

        @if($total > 0)
        <form id="orderForm" method="POST">
            @csrf
            <input type="hidden" name="total_amount" value="{{ $total }}">

            @if(!$hasPhone)
            <input type="hidden" name="phone" id="phoneInput">
            <button type="button" id="openPhonePopup" class="w-full mt-3 flex items-center justify-center gap-2 py-3 bg-primary text-white rounded-lg cursor-pointer">
                Place Order
            </button>
            @else
            <button type="button"
                id="placeOrderDirect"
                class="w-full mt-3 flex items-center justify-center gap-2 py-3 bg-primary text-white rounded-lg cursor-pointer">
                Place Order
            </button>

            @endif

        </form>
        @endif

    </div>
    <!-- Sticky Bottom Total -->
    <div class="sidebar-total">
        <div class="container pl-5 pr-5 pb-3 pt-5">

            <!-- Phone Modal -->
            <div id="phoneModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;background:rgba(0,0,0,0.6); padding-top:100px;">

                <div style="background:#fff; width:350px; margin:auto; padding:20px; border-radius:8px;">
                    <h4 style="font-size: 20px;">Phone Number</h4>
                    <p class="text-gray-500 text-xs">Enter your phone number now and enjoy a discount on your second order!</p>

                    <div class="form-group mb-3 mt-3">
                        <input type="number" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" id="phoneField" name="phoneField" required placeholder="Enter phone number"
                            class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <button id="submitPhone" class="px-4 py-2 bg-primary text-white border rounded cursor-pointer mt-3">Submit</button>
                    <button id="closeModal" class="px-4 py-2 border rounded mr-1 cursor-pointer mt-3">Cancel</button>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const offcanvasEl = document.getElementById('offcanvasBottom');

        if (offcanvasEl) {
            // On Offcanvas Show
            offcanvasEl.addEventListener('show.bs.offcanvas', function() {
                $('.main-sidebar').css('z-index', '0');
                $('.cart_icon').css('z-index', '0');
                closeSidebar(); // Hide sidebar
            });

            // On Offcanvas Hide
            offcanvasEl.addEventListener('hidden.bs.offcanvas', function() {
                $('.main-sidebar').css('z-index', '880');
                $('.cart_icon').css('z-index', '1');
                // Optionally reopen sidebar:
                // openSidebar();
            });
        }
    });


    function openSidebar() {
        const sidebar = document.getElementById("mySidebar");
        const openBtn = document.getElementById("open-btn");

        if (window.innerWidth <= 768) {
            // Mobile view
            sidebar.style.width = "100%";
        } else if (window.innerWidth <= 1024) {
            // Tablet view
            sidebar.style.width = "50%";
        } else {
            // Desktop view
            sidebar.style.width = "40%";
        }

        openBtn.style.display = "none";
    }


    function closeSidebar() {
        document.getElementById("mySidebar").style.width = "0";
        document.getElementById("open-btn").style.display = "inline-block";
    }

    $(document).ready(function() {
        $('.quantity input[type="number"]').on('input', function() {
            const input = $(this);
            const newVal = parseInt(input.val());
            const min = parseInt(input.attr('min'));
            const max = parseInt(input.attr('max'));
            const itemId = input.data('id');

            if (newVal >= min && newVal <= max) {
                $.ajax({
                    url: "{{ route('UpdateQuantity') }}",
                    method: "PATCH",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: itemId,
                        quantity: newVal
                    },
                    success: function() {
                        // 1. Update that item's total
                        // const itemTotalEl = $('#item-total-' + itemId);
                        // const unitPrice = parseFloat(itemTotalEl.data('price'));
                        // const newItemTotal = unitPrice * newVal;
                        // itemTotalEl.text('₹' + newItemTotal.toFixed(2));

                        // 2. Recalculate full cart total
                        // let newCartTotal = 0;
                        // $('.quantity input[type="number"]').each(function() {
                        //     const qty = parseInt($(this).val());
                        //     const id = $(this).data('id');
                        //     const price = parseFloat($('#item-total-' + id).data('price'));
                        //     newCartTotal += price * qty;
                        // });

                        // $('#cart-total').text('₹' + newCartTotal.toFixed(2));

                        // // 3. Update hidden input for total_amount in the form
                        // $('input[name="total_amount"]').val(newCartTotal.toFixed(2));

                        alert('Cart updated successfully.');
                        window.location.reload();
                    }
                });
            }
        });
    });

    jQuery(document).ready(function($) {

        $("#openPhonePopup").on("click", function() {
            $("#phoneModal").css("display", "block");
        });

        $("#closeModal").on("click", function() {
            $("#phoneModal").css("display", "none");
        });

    });

    function placeOrder() {

        let formData = $("#orderForm").serialize();

        $.ajax({
            url: "/ordermenu/order",
            type: "POST",
            data: formData,
            success: function(res) {

                if (res.success) {

                    $.ajax({
                        url: "/ordermenu/printorder/" + res.order_id,
                        type: "GET",
                        success: function(printRes) {

                            if (printRes.success) {
                                alert("Order placed successfully!");
                                window.location.href = "{{ route('OrderMenu') }}";
                            }
                        }
                    });

                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("Something went wrong. Please try again.");
            }
        });
    }

    $("#submitPhone").on("click", function() {

        let phone = $("#phoneField").val().trim();

        if (phone === "") {
            alert("Please enter phone number");
            return;
        }

        phone = phone.replace(/\D/g, '');

        if (!/^[0-9]{10}$/.test(phone)) {
            alert("Please enter a valid 10-digit phone number");
            return;
        }

        $("#phoneInput").val(phone);
        $("#phoneModal").hide();

        placeOrder(); // 🔥 same function
    });

    $("#placeOrderDirect").on("click", function() {
        placeOrder(); // 🔥 same function
    });
</script>