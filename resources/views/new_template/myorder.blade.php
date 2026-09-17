@include('new_template.header')
<style>
    body {
        background-color: #f9f9f9;
    }

    .cart_section {
        justify-content: center;
        padding: 20px;
    }


    .cart-wrapper {
        border-radius: 16px;
        width: 100%;
    }

    .cart-info {
        font-size: 14px;
        color: #333;
        margin-bottom: 15px;
    }

    .cart-item {
        background: #fff;
        border-radius: 14px;
        padding: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
    }


    .item-image {
        width: 100px;
        height: 100px;
        border-radius: 20%;
        object-fit: cover;
    }

    .item-details {
        flex: 1;
    }

    .item-title {
        font-weight: 600;
        margin: 0;
        font-size: 18px;
        color: #333;
    }

    .item-price {
        font-size: 16px;
        color: #e7272d;
        font-weight: bold;
        margin-top: 5px;
        margin-bottom: 0px
    }

    .total-controls {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-right: 10px;
    }

    .bottom-btn-container {
        margin-right: 10px;
    }

    small {
        color: gray;
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
        display: none;
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
        background: #ff3b3b;
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

    .apply-btn:hover {
        background: #e12d2d;
    }

    .promo-container {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 25px;
        padding: 5px 5px 5px 15px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);

    }



    @media (max-width: 768px) {

        .item-image {
            width: 60px;
            height: 60px;
        }

        .item-title {
            font-size: 16px;
        }

        .item-price {
            font-size: 14px;
            margin-top: 0px;
        }

        small {
            font-size: 13px;
        }

        .cart_section {
            padding: 10px;
        }

    }
</style>
<section class="cart_section">
    <div class="cart-wrapper">
        <h3 class="mb-4 pl-2">Your Orders</h3>
        @php
        $total = 0;
        $matchFound = false;
        @endphp

        <!-- @foreach ($data as $detail)

        @php
        $matchFound = true;
        $itemTotal = $detail->price * $detail->quantity;

        $addonTotal = 0;
        $addonNames = [];

        $addonData = json_decode($detail->addons, true);

        if (is_array($addonData)) {
        foreach ($addonData as $addon) {
        if (isset($addon['name']) && isset($addon['price'])) {
        $addonNames[] = $addon['name'];
        $addonTotal += $addon['price'] * $detail->quantity;
        }
        }
        }

        $totalWithAddons = $itemTotal + $addonTotal;
        $total += $totalWithAddons;
        @endphp



        <div class="cart-item mb-2">
            @foreach ($items as $item)
            @if ($item->id == $detail->item_id)
            <img src="{{ url('items/' .$item->picture) }}" alt="Oatmeal" class="item-image" />
            @endif
            @endforeach

            <div class="item-details">
                <p class="item-title">{{ $detail->quantity }} x {{ $detail->name }}</p>
                <p class="item-price">₹{{ $detail->price }}</p>

                <small>{{ implode(', ', $addonNames) }}</small>

                <br>
                @if($detail->note)<small>"{{ $detail->note }}"</small>@endif

            </div>
            <div class="total-controls">
                <h6 style="color:green;">₹{{ $totalWithAddons }}</h6>
            </div>

            <form method="POST" action="{{ route('scantable.repeatorder') }}">
                @csrf
                @foreach ($items as $item)
                @if ($item->id == $detail->item_id)
                <input type="hidden" name="item_image" value="{{ 'items/' .$item->picture }}">
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                @endif
                @endforeach
                <input type="hidden" name="created_at" value="{{ $detail->created_at }}">
                <input type="hidden" name="order_id" value="{{ $orderId }}">
                <button class="btn btn-sm" type="submit" onclick="return confirm('Do you want to repeat this item?');" style="background-color: #e7272d;margin-top:-5px"><i class="fa fa-repeat" style="font-size:15px;color:#fff"></i></button>
            </form>
        </div>
        @endforeach -->

        <div id="latestOrderContainer">
            @foreach ($data as $detail)
            {{-- your order loop code --}}
            @endforeach
        </div>

        @if (!$matchFound)
        <p class="text-center">You have not placed any orders yet.</p>
        <p class="text-center"><a href="{{route('scantable.index', ['table' => session('table_name')])}}" class="add-more-items"><i class="fa fa-plus"></i> Add Items </a></p>
        @endif


        @if ($matchFound)
        <a href="{{route('scantable.index', ['table' => session('table_name')])}}" class="add-more-items"><i class="fa fa-plus"></i> Add More Item </a><br>
        <div class="coupon-notice">
            <span>
                <i class="fa fa-tag fa-lg mr-1" style="color:#e7272d"></i>
                Have a coupon? <a id="toggleCoupon">Click here to enter your code</a>
            </span>

            <div class="coupon-form" id="couponForm" style="display:none;">
                <form method="POST" action="{{ route('coupon.apply') }}">
                    @csrf
                    <input type="hidden" name="total" value="{{ $total ?? 0 }}"> {{-- current order/cart total --}}
                    <input type="hidden" name="data" value="{{ json_encode($data) }}">

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
                    <div class="promo-container">
                        <input type="text" name="code" class="coupon-input" placeholder="Enter your coupon code" required>
                        <button type="submit" class="apply-btn">Apply</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="copuonText">

        </div>



        {{-- Show messages --}}
        @if(session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
        @endif

        @if(session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
        @endif

        <!-- <div class="promo-container mt-3">
            <span style="margin-right: 8px;"><i class="fa fa-tag" style="font-size: 20px;color:#e7272d"></i></span>
            <input type="text" name="code" placeholder="Have a coupon code? Type here">
            <button class="apply-btn">Apply</button>
        </div> -->
        <!-- <div class="bottom-btn-container mt-3 mb-5">
            <div class="row">
                <div class="col-12">
                    <h5 class="float-right">
                        <strong>Sub-Total: ₹{{ number_format(session('old_total', $total), 2) }}</strong>
                    </h5>
                </div>

                @if(session('discount'))
                <div class="col-12">
                    <h5 class="float-right">
                        <strong>Coupon ({{ session('coupon') }}): - ₹{{ number_format(session('discount', $total), 2) }}</strong>
                    </h5>
                </div>

                <div class="col-12">
                    <h5 class="float-right">
                        <strong>Grand Total: ₹{{ number_format(session('final_total', $total), 2) }}</strong>
                    </h5>
                </div>
                @endif

                @if($GST_status)
                <div class="col-12">
                    <h5 class="float-right"><strong>CGST ({{ $cgst_rate }}%): ₹{{ number_format(session('cgst', $cgst), 2) }}</strong></h5>
                </div>
                <div class="col-12">
                    <h5 class="float-right"><strong>SGST ({{ $sgst_rate }}%): ₹{{ number_format(session('sgst', $sgst), 2) }}</strong></h5>
                </div>
                <div class="col-12">
                    <h5 class="float-right"><strong>To Pay: ₹{{ number_format(session('total_with_tax', $total_with_tax), 2) }}</strong></h5>
                </div>
                @else
                <div class="col-12">
                    <h5 class="float-right"><strong>To Pay: ₹{{ number_format(session('final_total', $total), 2) }}</strong></h5>
                </div>
                @endif


                <div class="col-12">
                    <hr>
                </div>
                <?php if ($total > 0) {
                ?>

        <div class="col-6">
            <form method="POST" action="{{ route('scantable.payment') }}">
                @csrf
                @foreach ($data as $order)
                <input type="hidden" name="product_name[]" value="{{ $order->name }}">
                <input type="hidden" name="product_price[]" value="{{ $order->price }}">
                <input type="hidden" name="product_quantity[]" value="{{ $order->quantity }}">
                <input type="hidden" name="addons[]" value="{{ $order->addons }}">
                @endforeach
                <input type="hidden" name="order_id" value="{{ $orderId }}">
                @if($GST_status == true)
                <input type="hidden" name="cgst" value="{{  number_format(session('cgst', $cgst),  2, '.', '') }}">
                <input type="hidden" name="sgst" value="{{  number_format(session('sgst', $sgst),  2, '.', '') }}">
                <input type="hidden" name="cgst_rate" value="{{ $cgst_rate }}">
                <input type="hidden" name="sgst_rate" value="{{ $sgst_rate }}">
                <input type="hidden" name="total_with_tax" value="{{ number_format(session('total_with_tax', $total_with_tax),  2, '.', '') }}">
                <input type="hidden" name="price" id="payNowPrice" value="{{ number_format(session('total_with_tax', $total_with_tax),  2, '.', '') }}">

                <input type="hidden" name="coupon" value="{{  session('coupon') ? session('coupon') : 0 }}">
                <input type="hidden" name="discount" value="{{  number_format(session('discount', 0),  2, '.', '') }}">

                @else
                <input type="hidden" name="cgst" value="0">
                <input type="hidden" name="sgst" value="0">
                <input type="hidden" name="cgst_rate" value="0">
                <input type="hidden" name="sgst_rate" value="0">
                <input type="hidden" name="total_with_tax" value="{{ number_format(session('total_with_tax', $total),  2, '.', '') }}">
                <input type="hidden" name="price" id="payNowPrice" value="{{ number_format(session('total_with_tax', $total),  2, '.', '') }}">

                <input type="hidden" name="coupon" value="{{ session('coupon') ? session('coupon') : 0 }}">
                <input type="hidden" name="discount" value="{{  number_format(session('discount', 0),  2, '.', '') }}">
                @endif

                <button type="submit" class="btn btn-success col-12 mb-3" id="payNowBtn">Pay Now</button>
            </form>
        </div>
        <div class="col-6">
            <form method="POST" action="{{ route('scantable.CaseOnDelivery') }}">
                @csrf
                @foreach ($data as $order)
                <input type="hidden" name="product_name[]" value="{{ $order->name }}">
                <input type="hidden" name="product_price[]" value="{{ $order->price }}">
                <input type="hidden" name="product_quantity[]" value="{{ $order->quantity }}">
                <input type="hidden" name="addons[]" value="{{ $order->addons }}">
                @endforeach
                <input type="hidden" name="order_id" value="{{ $orderId }}">
                @if($GST_status == true)
                <input type="hidden" name="cgst" value="{{  number_format(session('cgst', $cgst),  2, '.', '') }}">
                <input type="hidden" name="sgst" value="{{  number_format(session('sgst', $sgst),  2, '.', '') }}">
                <input type="hidden" name="cgst_rate" value="{{ $cgst_rate }}">
                <input type="hidden" name="sgst_rate" value="{{ $sgst_rate }}">
                <input type="hidden" name="total_with_tax" value="{{ number_format(session('total_with_tax', $total_with_tax),  2, '.', '') }}">
                <input type="hidden" name="price" id="payNowPrice" value="{{ number_format(session('total_with_tax', $total_with_tax),  2, '.', '') }}">

                <input type="hidden" name="coupon" value="{{ session('coupon') ? session('coupon') : 0 }}">
                <input type="hidden" name="discount" value="{{  number_format(session('discount', 0),  2, '.', '') }}">
                @else
                <input type="hidden" name="cgst" value="0">
                <input type="hidden" name="sgst" value="0">
                <input type="hidden" name="cgst_rate" value="0">
                <input type="hidden" name="sgst_rate" value="0">
                <input type="hidden" name="total_with_tax" value="{{ number_format(session('total_with_tax', $total),  2, '.', '') }}">
                <input type="hidden" name="price" id="payNowPrice" value="{{ number_format(session('total_with_tax', $total),  2, '.', '') }}">

                <input type="hidden" name="coupon" value="{{ session('coupon') ? session('coupon') : 0 }}">
                <input type="hidden" name="discount" value="{{  number_format(session('discount', 0),  2, '.', '') }}">
                @endif

                <button type="submit" class="btn btn-secondary col-12 mb-3" id="cashOnDeliveryBtn">Cash On Delivery</button>
            </form>
        </div>
    <?php
                }
    ?>
    -->


    </div>
    </div>
    <div id="bottomBtnContainer">
        <div class="bottom-btn-container mt-3 mb-5">
            {{-- your full bottom buttons HTML here --}}
        </div>
    </div>

    @endif
    </div>

    </div>
    </div>
</section>

<script>
    document.getElementById("toggleCoupon").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("couponForm").style.display =
            document.getElementById("couponForm").style.display === "none" ? "block" : "none";
    });

    let lastCouponError = null;


    $(document).ready(function() {
        function myLatestOrder() {

            $.ajax({
                url: "{{ route('scantable.myLatestOrder') }}",
                type: "GET",
                success: function(response) {
                    let html = "";
                    let total = 0;

                    if (response.payment_status === 'completed') {
                        let thankYouRoute = "{{ route('scantable.ThankYou') }}";
                        window.location.href = thankYouRoute;
                        return;
                    }

                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(detail) {
                            let itemTotal = detail.price * detail.quantity;
                            let addonTotal = 0;
                            let addonNames = [];

                            try {
                                let addons = JSON.parse(detail.addons);
                                if (Array.isArray(addons)) {
                                    addons.forEach(function(addon) {
                                        addonNames.push(addon.name);
                                        addonTotal += addon.price * detail.quantity;
                                    });
                                }
                            } catch (e) {}

                            let totalWithAddons = itemTotal + addonTotal;
                            total += totalWithAddons;

                            // find item image
                            let itemImage = "";
                            response.items.forEach(function(item) {
                                if (item.id == detail.item_id) {
                                    itemImage = "{{ url('items') }}/" + item.picture;
                                }
                            });

                            html += `
                            <div class="cart-item mb-2">
                                <img src="${itemImage}" class="item-image"/>
                                <div class="item-details">
                                    <p class="item-title">${detail.quantity} x ${detail.name}</p>
                                    <p class="item-price">₹${detail.price}</p>
                                    <small>${addonNames.join(", ")}</small>
                                    ${detail.note ? `<br><small>"${detail.note}"</small>` : ""}
                                </div>
                                <div class="total-controls">
                                    <h6 style="color:green;">₹${totalWithAddons}</h6>
                                </div>
                                <form method="POST" action="{{ route('scantable.repeatorder') }}">
                                @csrf
                                <input type="hidden" name="item_image" value="items/${detail.item_picture}">
                                <input type="hidden" name="item_id" value="${detail.item_id}">
                                <input type="hidden" name="created_at" value="${detail.created_at}">
                                <input type="hidden" name="order_id" value="${response.orderId}">
                                <button class="btn btn-sm" type="submit" onclick="return confirm('Do you want to repeat this item?');" style="background-color: #e7272d;margin-top:-5px"><i class="fa fa-repeat" style="font-size:15px;color:#fff"></i></button>
                                </form>
                            </div>
                        `;
                        });

                    } else {
                        html = `<p class="text-center">You have not placed any orders yet.</p>`;
                        html += `<p class="text-center"><a href="{{route('scantable.index', ['table' => session('table_name')])}}" class="add-more-items"><i class="fa fa-plus"></i> Add Items </a></p>`;
                    }

                    $("#latestOrderContainer").html(html);

                    if (response.discount > 0) {
                        $("#copuonText").html(`
                        <div class="cart-item mt-3">
                            <div class="item-details">
                                <p><i class="fa fa-check p-1" style="background-color:#e7272d;border-radius:50%;color:#fff;font-size:15px;"></i>
                                You saved ₹${Number(response.discount).toFixed(2)} with '${response.coupon}'</p>
                            </div>
                            <form method="POST" action="{{ route('coupon.remove') }}">
                                @csrf
                                <button class="btn btn-sm" type="submit"
                                        onclick="return confirm('Do you want to delete?');"
                                        style="background-color:#e7272d;margin-top:-5px">
                                    <i class="fa fa-trash-o" style="font-size:15px;color:#fff"></i>
                                </button>
                            </form>
                        </div>`);
                        $(".coupon-notice").hide();
                    } else {
                        $("#copuonText").empty();
                        $(".coupon-notice").show();
                    }

                    if (response.coupon_error && response.coupon_error !== lastCouponError) {
                        lastCouponError = response.coupon_error;
                        alert(response.coupon_error);
                    }

                    // ✅ Now build bottom container
                    let bottomHtml = `
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="float-right"><strong>Sub-Total: ₹${Number(response.sub_total).toFixed(2)}</strong></h5>
                                </div>
                    `;

                    if (response.discount > 0) {
                        bottomHtml += `
                            <div class="col-12">
                                <h5 class="float-right"><strong>Coupon (${response.coupon}): - ₹${Number(response.discount).toFixed(2)}</strong></h5>
                            </div>
                            <div class="col-12">
                                <h5 class="float-right"><strong>Grand Total: ₹${Number(response.grand_total).toFixed(2)}</strong></h5>
                            </div>
                        `;
                    }

                    if (response.GST_status) {
                        bottomHtml += `
                            <div class="col-12"><h5 class="float-right"><strong>CGST (${response.cgst_rate}%): ₹${Number(response.cgst).toFixed(2)}</strong></h5></div>
                            <div class="col-12"><h5 class="float-right"><strong>SGST (${response.sgst_rate}%): ₹${Number(response.sgst).toFixed(2)}</strong></h5></div>
                            <div class="col-12"><h5 class="float-right"><strong>To Pay: ₹${Number(response.total_with_tax).toFixed(2)}</strong></h5></div>
                        `;
                    } else {
                        bottomHtml += `
                            <div class="col-12"><h5 class="float-right"><strong>To Pay: ₹${Number(response.grand_total).toFixed(2)}</strong></h5></div>
                        `;
                    }

                    let data = '';
                    if (response.GST_status == true) {
                        data += `  <input type="hidden" name="cgst" value="${Number(response.cgst).toFixed(2)}">
                            <input type="hidden" name="sgst" value="${Number(response.sgst).toFixed(2)}">
                            <input type="hidden" name="cgst_rate" value="${response.cgst_rate}">
                            <input type="hidden" name="sgst_rate" value="${response.sgst_rate}">
                            <input type="hidden" name="total_with_tax" value="${Number(response.total_with_tax).toFixed(2)}">
                            <input type="hidden" name="price" id="payNowPrice" value="${Number(response.total_with_tax).toFixed(2)}">

                            <input type="hidden" name="coupon" value="${response.coupon}">
                            <input type="hidden" name="discount" value="${Number(response.discount).toFixed(2)}">
                            `;
                    } else {
                        data += `<input type="hidden" name="cgst" value="0">
                            <input type="hidden" name="sgst" value="0">
                            <input type="hidden" name="cgst_rate" value="0">
                            <input type="hidden" name="sgst_rate" value="0">
                            <input type="hidden" name="total_with_tax" value="${Number(response.total_with_tax).toFixed(2)}">
                            <input type="hidden" name="price" id="payNowPrice" value="${Number(response.total_with_tax).toFixed(2)}">

                            <input type="hidden" name="coupon" value="${response.coupon}">
                            <input type="hidden" name="discount" value="${Number(response.discount).toFixed(2)}">
                        `;
                    }

                    // Buttons (Pay Now + COD)
                    // if (response.total > 0) {
                    bottomHtml += `
                            <div class="col-12"><hr></div>
                            <div class="col-6 mb-5">
                                <form method="POST" action="{{ route('scantable.payment') }}">
                                    @csrf

                                    ${data}

                                    <input type="hidden" name="order_id" value="${response.orderId}">
                                    <input type="hidden" name="price" value="${response.total_with_tax}">
                                    <button type="submit" class="btn btn-success col-12 mb-3">Pay Now</button>
                                </form>
                            </div>
                            <div class="col-6 mb-5">
                                <form method="POST" action="{{ route('scantable.CaseOnDelivery') }}">
                                    @csrf

                                    ${data}

                                    <input type="hidden" name="order_id" value="${response.orderId}">
                                    <input type="hidden" name="price" value="${response.total_with_tax}">
                                    <button type="submit" class="btn btn-secondary col-12 mb-3">Pay at counter</button>
                                </form>
                            </div>
                        `;
                    // }

                    bottomHtml += `</div></div>`;
                    $("#bottomBtnContainer").html(bottomHtml);

                }
            });
        }

        // Fetch orders every 10 seconds
        setInterval(myLatestOrder, 1000);

        // Fetch orders immediately on page load
        myLatestOrder();
    });
</script>