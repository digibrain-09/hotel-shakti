@extends('layouts.admin')
@extends('order.slider',['nav_title'=>"Menu"])
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<style>
    .btn-primary {
        background: var(--background);
        border-radius: 50px;
        border-color: var(--background);
        box-shadow: none;
    }

    .btn {
        font-size: 16px;
    }

    .card .card-body {
        padding: 20px;
    }

    .navbar {
        padding: .5rem 1rem;
    }

    .item_image {
        height: 200px;
        width: 200px;
        float: right;
        border-radius: 20px;
    }


    .image-container {
        position: relative;
        min-height: 250px;
        /* adjust as needed */
    }

    .item-section small {
        color: grey;
    }


    .checkbox-lg .custom-control-label::before,
    .checkbox-lg .custom-control-label::after {
        width: 23px;
        height: 23px;
    }

    .checkbox-lg .custom-control-label {
        padding-top: 10px;
        padding-left: 30px;
    }

    .item-details-image {
        border-radius: 20px;
        width: 300px;
    }

    .custom-control-input:checked~.custom-control-label::before {
        background-color: #e7272d !important;
        border-color: #e7272d !important;
    }

    .custom-control-input:checked~.custom-control-label::after {
        color: #fff;
    }

    .quantity-wrapper2 {
        display: flex;
        align-items: center;
        border: 2px solid #e7272d;
        border-radius: 5px;
        overflow: hidden;
        margin-top: 3px;
    }

    .quantity-wrapper2 .qty-btn {
        flex: 0 0 30%;
        background-color: #fff;
        border: none;
        padding: 8px 0;
        cursor: pointer;
        color: #000;
    }

    .quantity-wrapper2 .qty-btn .fa {
        font-size: 15px !important;
        color: #e7272d;
    }

    .quantity-wrapper2 .qty-input {
        width: 50%;
        text-align: center;
        border: none;
        outline: none;
    }

    .quantity-wrapper {
        align-items: center;
        border: 2px solid #e7272d;
        border-radius: 5px;
        overflow: hidden;
        background-color: #fff;
    }

    .quantity-wrapper .qty-btn {
        flex: 0 0 30%;
        background-color: #fff;
        border: none;
        padding: 8px;
        cursor: pointer;
        color: #000;
    }

    .quantity-wrapper .qty-btn .fa {
        font-size: 15px !important;
        color: #e7272d;
    }

    .quantity-wrapper .qty-input {
        width: 50%;
        text-align: center;
        border: none;
        outline: none;
    }


    @media (max-width: 768px) {

        .slider-section h3 {
            font-size: 20px;
        }

        .item-section h3 {
            font-size: 25px;
        }

        .item_image {
            height: 100px !important;
            width: 100px !important;
        }

        .item-section h4 {
            font-size: 20px;
        }

        .item-section h5 {
            font-size: 15px;
        }

        .item-section p {
            font-size: 13px;
        }

        .item-section small {
            font-size: 12px;
        }


        .checkbox-lg .custom-control-label::before,
        .checkbox-lg .custom-control-label::after {
            width: 20px;
            height: 20px;
        }

        .checkbox-lg .custom-control-label {
            padding-top: 8px;
            padding-left: 25px;
        }

        .offcanvas-add-btn {
            width: 100%;
        }

        .quantity-wrapper {
            width: 100%;
            border: 2px solid #e7272d;
        }

        .qty-input {
            width: 100%;
        }

        .qty-btn {
            background-color: #fff;
        }

        .qty-btn .fa {
            color: #e7272d;
        }

        .item-details-image {
            width: 100%;
        }

        .quantity-wrapper2 {
            width: 40%;
            height: 50px;
        }

        .quantity-wrapper {
            width: 80%;
            border: 2px solid #e7272d;
            top: 60% !important;
            left: 50% !important;
        }

        .quantity-wrapper .qty-btn {
            background-color: #fff;
        }

        .quantity-wrapper .qty-btn .fa {
            color: #e7272d;
        }
    }

    @media only screen and (min-width: 768px) and (max-width: 1024px) {
        .quantity-wrapper {
            width: 25%;
        }
    }



    /* Hidden toggle */
    #offcanvas-toggle {
        display: none;
    }

    /* Overlay */
    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        opacity: 0;
        visibility: hidden;
        transition: 0.3s;
        z-index: 998;
    }

    /* Offcanvas */
    .offcanvas {
        position: fixed;
        left: 0;
        right: 0;
        bottom: -100%;
        background: #fff;
        border-radius: 16px 16px 0 0;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.2);
        transition: bottom 0.3s ease;
        z-index: 999;
        max-height: 90vh;
        overflow-y: auto;
    }

    /* Show when checked */
    #offcanvas-toggle:checked~.overlay {
        opacity: 1;
        visibility: visible;
    }

    #offcanvas-toggle:checked~.offcanvas {
        bottom: 0;
    }

    /* Header */
    .offcanvas-header {
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #eee;
        padding: 20px;
        margin-bottom: 15px;
    }

    .offcanvas-header img {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
    }

    .offcanvas-header h2 {
        font-size: 18px;
        margin: 0;
    }

    .offcanvas-body {
        padding: 20px;
    }

    /* Section */
    .section {
        margin-bottom: 20px;
        background: #fafafa;
        border-radius: 8px;
        padding: 15px;
        border: 1px solid #eee;
    }

    .section h3 {
        margin: 0 0 5px;
        font-size: 16px;
    }

    .section p {
        margin: 0 0 10px;
        font-size: 13px;
        color: #555;
    }

    /* Addon */
    .addon {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-top: 1px solid #eee;
    }

    .addon:first-child {
        border-top: none;
    }

    .addon label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .addon input {
        accent-color: #e7272d;
    }


    /* Quantity */
    .quantity-control {
        display: flex;
        align-items: center;
        border: 1px solid #e7272d;
        border-radius: 6px;
        overflow: hidden;
        width: fit-content;
    }

    .quantity-control button {
        background: #fff;
        border: none;
        padding: 8px 14px;
        font-size: 18px;
        cursor: pointer;
        color: #e7272d;
    }

    .quantity-control input {
        width: 40px;
        text-align: center;
        border: none;
        font-size: 16px;
        outline: none;
    }

    /* Footer */
    .offcanvas-footer {
        position: sticky;
        bottom: 0;
        background: #fff;
        padding: 20px;
        text-align: right;
        z-index: 1;
    }

    .btn-add {
        background: #e7272d;
        color: #fff;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
    }

    .cart_icon {
        position: fixed;
        right: 2%;
        top: 90%;
        float: right;
        z-index: 1;
    }

    .cart_link {
        display: inline-block;
        border-radius: 50px;
        background-color: green;
        color: white;
        box-shadow: 0 0 2px #888;
        padding: 10px;
    }

    #lblCartCount {
        font-size: 12px;
        background: black;
        color: #fff;
        padding: 0 5px;
        vertical-align: top;
        margin-left: -10px;
        border-radius: 50%;
    }
</style>

@section('content')
<!-- Breadcrumb Navigation -->
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{route('OrderMenu')}}" class="cursor-pointer">
        <span class="text-gray-500 hover:text-primary transition-all duration-200">Orders</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
    <a href="{{route('TakeAwayOrder')}}" class="cursor-pointer">
        <span class="text-gray-500 hover:text-primary transition-all duration-200">Categories</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
    <span class="text-foreground font-medium">Items</span>
</div>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Menu Management</h1>
        <p class="text-gray-500 text-sm md:text-base">Streamline takeaway orders with an easy-to-use menu system.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 ml-auto md:ml-0">
        <a href="{{ route('ViewOrder') }}">
            <button class="flex items-center gap-2 px-4 py-2.5 border border-border rounded-button text-foreground font-medium hover:border-primary transition-all duration-200 cursor-pointer">
                <i data-lucide="utensils" class="w-4 h-4"></i>
                <span>View Take Away Orders</span>
            </button>
        </a>
        <a href="{{ route('TakeAwayOrder') }}">
            <button class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>New Take Away Order</span>
            </button>
        </a>
    </div>
</div>

<!-- Alerts -->
@if(Session::get('test') == 'restaurant_admin')
<div class="alertordermsg"></div>
<div id="alertSoundContainer"></div>
<div id="PrintalertSoundContainer"></div>
<div class="alertpaymentmsg"></div>
<div class="alertTakeAwayordermsg"></div>
<div id="alertTakeAwaySound"></div>
<div class="alertreadytoservedmsg"></div>
<div id="ReadyToservedalertSoundContainer"></div>
<div class="Waiteralertordermsg"></div>
<div id="alertSoundContainer_waiter"></div>
<div class="alertreadytoservedWaitermsg"></div>
<div id="ReadyToservedWaiteralertSoundContainer"></div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-6">
    <!-- Order Items -->
    @foreach ($items as $index => $item)
    @php
    $inCart = array_key_exists($item->id, $cartItems);
    $itemQty = $inCart ? $cartItems[$item->id] : 1;
    $disabled = $itemQty == 1 ? 'disabled' : '';
    @endphp
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <div class="bg-white rounded-card p-5">
            <div class="space-y-4">
                <div class="flex gap-4 pb-4">
                    <div class="flex-1 min-w-0">
                        @if($item->food_type == 1)
                        <img src="{{ url('restaurant_logo/Non_veg_symbol.png') }}" height="15px" width="15px" alt="">
                        @else
                        <img src="{{ url('restaurant_logo/Veg_symbol.png') }}" height="15px" width="15px" alt="">
                        @endif
                        @php
                        $hasAddOns = $add_ons->contains('item_id', $item->id);
                        @endphp

                        @if ($hasAddOns)
                        <small style="color: gray;">Customisable</small>
                        @endif
                        <h4 class="mt-2 menu-title item_name" data-category="{{ $item->category_id }}" data-id="{{ $item->id }}">{{ $item->item_name}}</h4>
                        <h5 class="item_price price" data-price="{{ $item->price}}">₹{{ $item->price}}</h5>
                        <p style="color: gray;">{{ \Illuminate\Support\Str::limit($item->description, 80, '...') }}</p>
                        @if ($inCart)
                        <div class="quantity-wrapper w-35 mt-2">
                            <button class="qty-btn minus" data-id="{{ $item->id }}" {{ $disabled }}><i class="fa fa-minus"></i></button>
                            <input type="number" class="qty-input" value="{{ $itemQty }}" min="1" readonly>
                            <button class="qty-btn plus" data-id="{{ $item->id }}"><i class="fa fa-plus"></i></button>
                        </div>
                        @else
                        <label for="offcanvas-toggle"
                            class="add_to_cart add flex w-20 mt-2 items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer"
                            data-id="{{ $item->id }}">
                            Add <i class="fa fa-plus"></i>
                        </label>
                        @endif

                    </div>
                    <img src="{{ url('items/' . $item->picture) }}" class="item_image" alt="">
                </div>
            </div>
        </div>
    </div>

    @endforeach
</div>

<!-- Hidden checkbox -->
<input type="checkbox" id="offcanvas-toggle">

<!-- Overlay -->
<div class="overlay"></div>

<!-- Offcanvas -->
<div class="offcanvas">
    <!-- Header -->
    <div class="offcanvas-header">
        <input type="hidden" id="offcanvasMode" value="add">
        <img src="" class="offcanvas-image" alt="">
        <h4 class="offcanvas-title mr-auto ml-2" id="offcanvasBottomLabel">Item Name</h4>
        <div class="close-btn ml-auto">
            <label class="cursor-pointer text-3xl" for="offcanvas-toggle">&times;</label>
        </div>
    </div>

    <div class="offcanvas-body">

        <!-- Add On -->
        <!-- <div class="section"> -->
        <div class="addon-content"></div>
        <!-- </div> -->

        <!-- Cooking Preferences -->
        <div class="section">
            <h3>Any Cooking Preferences?</h3>
            <textarea class="w-full px-3 md:px-4 py-2.5 md:py-3 text-sm md:text-base border border-border rounded-button focus:bg-white hover:border-primary focus:border-primary transition-all duration-300 resize-none order_note"
                name="order_note" placeholder="Let us know if you have any preferences..." id="exampleFormControlTextarea1" rows="3"></textarea>
        </div>
    </div>


    <!-- Footer -->
    <div class="offcanvas-footer flex md:justify-between">
        <div class="quantity-wrapper2">
            <button class="qty-btn minus"><i class="fa fa-minus"></i></button>
            <input type="number" class="qty-input" value="1" min="1">
            <button class="qty-btn plus"><i class="fa fa-plus"></i></button>
        </div>
        <div class="offcanvas-add-btn-div ml-auto">
            <button class="flex mt-2 items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer btn-add">Add to Cart ₹250</button>
        </div>
    </div>
</div>

<!-- Cart Icon -->
<div class="cart_icon">
    <p class="cart_link cursor-pointer" id="open-btn" onclick="openSidebar()"><i class="fa fa-shopping-cart fa-2x" aria-hidden="true"></i></p>
    <span class='badge badge-warning' id='lblCartCount'>
        {{$cart}}
    </span>
</div>

<script>
    $(document).on('click', '.item_details', function() {
        $('#offcanvasMode').val('edit');
        closeSidebar();

        const itemId = $(this).data('id');

        $.ajax({
            url: "/take-away-order/cart_view/" + itemId,
            type: "GET",
            success: function(response) {
                // Store base price globally
                basePrice = parseFloat(response.price);
                let quantity = parseInt(response.quantity) || 1;
                let selectedAddonTotal = 0;

                $('#offcanvasBottomLabel').text(response.name);
                $('.offcanvas-image').attr('src', '/items/' + response.picture);
                $('.quantity-wrapper2 .qty-input').val(response.quantity || 1);
                $('.offcanvas-add-btn').attr('data-item-id', response.item_id);
                $('.order_note').val(response.note || '');

                $('.offcanvas-add-btn').attr('data-item-id', response.item_id);



                // Add-on handling
                if (response.add_ons.length > 0) {
                    let addonsExtra = '';
                    let addonsVariation = '';
                    let addonsVegtoppings = '';


                    $.each(response.add_ons, function(index, addon) {

                        const isChecked = addon.selected ? 'checked' : '';
                        let variation_price = addon.price + basePrice;

                        const addonHTML = `                            
                            <div class="addon">
                                <label>
                                    <input class="addon-checkbox" type="checkbox" id="checkbox-${addon.id}" data-price="${addon.price}" data-name="${addon.name}" ${isChecked}>
                                    ${addon.name}
                                </label>
                                <span for="checkbox-${addon.id}">₹${addon.price}</span>
                            </div>`;

                        let addonHTML2 = `
                                 <div class="addon">
                                    <label>
                                        <input name="variation_option" class="addon-radio addon-checkbox" type="radio" id="radio-${addon.id}" data-price="${addon.price}" data-name="${addon.name}" ${isChecked}>
                                        ${addon.name}
                                    </label>
                                    <span for="radio-${addon.id}">₹${variation_price}</span>
                                </div>
                            `;

                        if (addon.type == 'Extra') {
                            if (!addonsExtra) {
                                addonsExtra += `
                                     <div class="section mb-3">
                                        <div class="card-body addon-body">
                                        <h5>Add On</h5>
                                        <p>Want something extra? Pick an add-on you like!</p>
                                    `;
                            }
                            addonsExtra += addonHTML;
                        } else if (addon.type == 'Variation') {
                            if (!addonsVariation) {
                                addonsVariation += `
                                     <div class="section mb-3">
                            <div class="card-body addon-body">
                            <h5>Variation</h5>
                            <p>Choose what suits you best</p>


                        <div class="addon">
                            <label>
                                <input name="variation_option" class="addon-radio regular-radio" type="radio" data-price="${basePrice}" data-name="Regular" checked>
                                Regular
                            </label>
                            <span>₹${basePrice}</span>
                        </div>
                                    `;
                            }

                            addonsVariation += addonHTML2;
                        } else if (addon.type == 'Veg-toppings') {
                            if (!addonsVegtoppings) {
                                addonsVegtoppings += `
                                     <div class="section mb-3">
                                        <div class="card-body addon-body">
                                        <h5>Veg Toppings</h5>
                                        <p>Pick your favorite veg toppings to customize your meal.</p>
                                    `;
                            }
                            addonsVegtoppings += addonHTML;
                        }
                        // addonsHtml += addonHTML;

                        if (addon.selected) {
                            selectedAddonTotal += parseFloat(addon.price);
                        }
                    });

                    if (addonsExtra) addonsExtra += '</div></div>';
                    if (addonsVariation) addonsVariation += '</div></div>';
                    if (addonsVegtoppings) addonsVegtoppings += '</div></div>';

                    $('.offcanvas-body .addon-content').show();
                    $('.offcanvas-body .addon-content').html(addonsVariation + addonsVegtoppings + addonsExtra);
                    $('.offcanvas-body .note').css('margin-top', '30px');

                    $('.regular-radio').on('change', function() {
                        updateRegularradioPrice();
                    });
                } else {
                    $('.offcanvas-body .addon').hide();
                    $('.offcanvas-body .note').css('margin-top', '0px');
                }


                // Calculate price after setting everything
                let finalPrice = (basePrice + selectedAddonTotal) * quantity;
                $('.offcanvas-total-price').text(finalPrice);
                $('.offcanvas-add-btn-div').html(
                    `<button type="button" class="flex mt-2 items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer offcanvas-add-btn" id="addToCartBtn" data-base-price="${finalPrice}" data-item-id="${response.item_id}">Add to Cart ₹${finalPrice}</button>`
                );

                // Add live addon/quantity change listeners (optional)
                $(document).off('change.addon').on('change.addon', '.addon-checkbox', function() {
                    recalculatePrice();
                });

                $(document).off('click.qty').on('click.qty', '.quantity-wrapper2 .qty-btn', function() {
                    let input = $('.quantity-wrapper2 .qty-input');
                    let value = parseInt(input.val());
                    if ($(this).hasClass('plus')) value += 1;
                    else if (value > 1) value -= 1;
                    input.val(value);
                    recalculatePrice();
                });

                $(document).off('input.qty').on('input.qty', '.quantity-wrapper2 .qty-input', function() {
                    let val = parseInt($(this).val());
                    if (isNaN(val) || val < 1) $(this).val(1);
                    recalculatePrice();
                });


            },
            error: function(xhr) {
                console.error('Error loading item details:', xhr);
            }
        });
    });

    $(document).on('click', '.add', function() {

        $('#offcanvasMode').val('add');
        const itemId = $(this).data('id');


        // Fetch item details for add mode
        $.ajax({
            url: "/take-away-order/view/" + itemId,
            type: "GET",
            success: function(response) {
                basePrice = parseFloat(response.price);
                let quantity = parseInt(response.quantity) || 1;
                $('#offcanvasBottomLabel').text(response.name);
                $('.offcanvas-image').attr('src', '/items/' + response.picture);
                $('.quantity-wrapper2 .qty-input').val(1);
                $('.offcanvas-add-btn').attr('data-item-id', response.item_id);
                $('.order_note').val('');

                // $('.qty-input').val(1); // Reset quantity to 1
                // updateTotalPrice(); // Initial price set

                // Load addons
                let selectedAddonTotal = 0;
                if (response.add_ons.length > 0) {
                    let addonsExtra = '';
                    let addonsVariation = '';
                    let addonsVegtoppings = '';

                    $.each(response.add_ons, function(index, addon) {

                        let variation_price = addon.price + basePrice;

                        let addonHTML = `
                    <div class="addon">
                        <label>
                            <input class="addon-checkbox" type="checkbox" id="checkbox-${addon.id}" data-price="${addon.price}" data-name="${addon.name}">
                            ${addon.name}
                        </label>
                        <span for="checkbox-${addon.id}">₹${addon.price}</span>
                    </div>
                `;

                        let addonHTML2 = `
                    <div class="addon">
                        <label>
                            <input name="variation_option" class="addon-radio addon-checkbox" type="radio" id="radio-${addon.id}" data-price="${addon.price}" data-name="${addon.name}">
                            ${addon.name}
                        </label>
                        <span for="radio-${addon.id}">₹${variation_price}</span>
                    </div>
                `;


                        if (addon.type == 'Extra') {
                            if (!addonsExtra) {
                                addonsExtra += `
                         <div class="section mb-3">
                            <div class="card-body addon-body">
                            <h5>Add On</h5>
                            <p>Want something extra? Pick an add-on you like!</p>
                        `;
                            }
                            addonsExtra += addonHTML;
                        } else if (addon.type == 'Variation') {
                            if (!addonsVariation) {
                                addonsVariation += `
                         <div class="section mb-3">
                            <div class="card-body addon-body">
                            <h5>Variation</h5>
                            <p>Choose what suits you best</p>


                        <div class="addon">
                            <label>
                                <input name="variation_option" class="addon-radio regular-radio" type="radio" data-price="${basePrice}" data-name="Regular" checked>
                                Regular
                            </label>
                            <span>₹${basePrice}</span>
                        </div>
                        `;
                            }

                            addonsVariation += addonHTML2;
                        } else if (addon.type == 'Veg-toppings') {
                            if (!addonsVegtoppings) {
                                addonsVegtoppings += `
                         <div class="section mb-3">
                            <div class="card-body addon-body">
                            <h5>Veg Toppings</h5>
                            <p>Pick your favorite veg toppings to customize your meal.</p>
                        `;
                            }
                            addonsVegtoppings += addonHTML;
                        }
                    });

                    if (addonsExtra) addonsExtra += '</div></div>';
                    if (addonsVariation) addonsVariation += '</div></div>';
                    if (addonsVegtoppings) addonsVegtoppings += '</div></div>';


                    $('.offcanvas-body .addon-content').show();
                    $('.offcanvas-body .addon-content').html(addonsVariation + addonsVegtoppings + addonsExtra);
                    $('.offcanvas-body .note').css('margin-top', '30px');

                    $('.regular-radio').on('change', function() {
                        updateRegularradioPrice();
                    });


                } else {
                    $('.offcanvas-body .addon').hide();
                    $('.offcanvas-body .note').css('margin-top', '0px');
                }

                // Calculate price after setting everything
                let finalPrice = (basePrice + selectedAddonTotal) * quantity;
                $('.offcanvas-total-price').text(finalPrice);
                $('.offcanvas-add-btn-div').html(
                    `<button type="button" class="flex mt-2 items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer offcanvas-add-btn" id="addToCartBtn" data-base-price="${finalPrice}" data-item-id="${response.item_id}">Add to Cart ₹${finalPrice}</button>`
                );

                $(document).off('change.addon').on('change.addon', '.addon-checkbox', function() {
                    recalculatePrice();
                });

                $(document).off('click.qty').on('click.qty', '.quantity-wrapper2 .qty-btn', function() {
                    let input = $('.quantity-wrapper2 .qty-input');
                    let value = parseInt(input.val());
                    if ($(this).hasClass('plus')) value += 1;
                    else if (value > 1) value -= 1;
                    input.val(value);
                    recalculatePrice();
                });

                $(document).off('input.qty').on('input.qty', '.quantity-wrapper2 .qty-input', function() {
                    let val = parseInt($(this).val());
                    if (isNaN(val) || val < 1) $(this).val(1);
                    recalculatePrice();
                });


            }
        });
    });


    // Quantity button logic
    function recalculatePrice() {
        let quantity = parseInt($('.quantity-wrapper2 .qty-input').val()) || 1;
        let totalAddon = 0;

        $('.addon-checkbox:checked').each(function() {
            totalAddon += parseFloat($(this).data('price')) || 0;
        });

        let final = (basePrice + totalAddon) * quantity;
        let itemId = $('.offcanvas-add-btn').attr('data-item-id') || '';


        $('.offcanvas-total-price').text(final);
        $('.offcanvas-add-btn-div').html(
            `<button type="button" class="flex mt-2 items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer offcanvas-add-btn" id="addToCartBtn" data-base-price="${basePrice}" data-item-id="${itemId}">Add to Cart ₹${final}</button>`
        );
    }


    // ✅ Update total price based on quantity and add-ons
    function updateTotalPrice() {
        let quantity = parseInt($('.qty-input').val()) || 1;
        let total = basePrice;

        $('.addon-checkbox:checked').each(function() {
            let addonPrice = parseFloat($(this).data('price')) || 0;
            total += addonPrice;
        });

        let finalPrice = total * quantity;
        let itemId = $('.offcanvas-add-btn').attr('data-item-id') || '';


        $('.offcanvas-add-btn-div').html(
            `<button type="button" class="flex mt-2 items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer offcanvas-add-btn" id="addToCartBtn" data-base-price="${basePrice}" data-item-id="${itemId}">Add to Cart ₹${finalPrice}</button>`
        );
    }

    function updateRegularradioPrice() {
        let quantity = parseInt($('.qty-input').val()) || 1;
        let total = basePrice;

        $('.addon-checkbox:checked').each(function() {
            let addonPrice = parseFloat($(this).data('price')) || 0;
            total += addonPrice;
        });

        let finalPrice = total * quantity;
        let itemId = $('.offcanvas-add-btn').attr('data-item-id') || '';


        $('.offcanvas-add-btn-div').html(
            `<button type="button" class="flex mt-2 items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer offcanvas-add-btn" id="addToCartBtn" data-base-price="${basePrice}" data-item-id="${itemId}">Add to Cart ₹${finalPrice}</button>`
        );
    }

    $(document).on('click', '#addToCartBtn', function() {

        let quantity = parseInt($('.quantity-wrapper2 .qty-input').val()) || 1;
        let note = $('#exampleFormControlTextarea1').val() || '';
        let price = $('.offcanvas-add-btn').attr('data-base-price');
        let item_image = $('.offcanvas-image').attr('src') || '';



        let mode = $('#offcanvasMode').val(); // 'add' or 'edit'
        let action = (mode === 'edit') ? 'update_cart' : 'add_to_cart';

        // Get selected add-on IDs
        let selectedAddons = [];
        $('.addon-checkbox:checked').each(function() {
            selectedAddons.push({
                id: $(this).attr('id').replace(/^(checkbox-|radio-)/, ''),
                name: $(this).data('name'),
                price: $(this).data('price')
            });
        });

        // Get item ID (assumed to be stored in a hidden field or header)
        let item_name = $('#offcanvasBottomLabel').text();

        // You can store item ID in a hidden field or set a data attribute in your add-to-cart button
        // For example, store it in .offcanvas-add-btn-div like this:
        let itemId = $('.offcanvas-add-btn').attr('data-item-id');


        if (action == 'update_cart') {
            // Send AJAX request to add to cart
            $.ajax({
                url: '/take-away-order/cart', // Replace with your actual route
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}", // CSRF token
                    item_id: itemId,
                    quantity: quantity,
                    addons: selectedAddons,
                    note: note,
                    item_name: item_name,
                    price: price,
                    item_image: item_image,
                    action: action
                },
                success: function(response) {
                    // alert('Item updated successfully!');
                    // $('#offcanvasBottom').offcanvas('hide');
                    window.location.reload();
                    // You can also refresh cart summary here if needed
                },
                error: function(xhr) {
                    alert('Failed to add item to cart.');
                    console.error(xhr.responseText);
                }
            });
        } else {
            $.ajax({
                url: '/take-away-order/add-to-cart', // Replace with your actual route
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}", // CSRF token
                    item_id: itemId,
                    quantity: quantity,
                    addons: selectedAddons,
                    note: note,
                    item_name: item_name,
                    price: price,
                    item_image: item_image
                },
                success: function(response) {
                    // alert('Item added to cart successfully!');
                    // $('#offcanvasBottom').offcanvas('hide');
                    window.location.reload();
                    // You can also refresh cart summary here if needed
                },
                error: function(xhr) {
                    alert('Failed to add item to cart.');
                    console.error(xhr.responseText);
                }
            });
        }
    });

    $(document).on('click', '.quantity-wrapper .qty-btn', function() {
        const button = $(this);
        const itemId = button.data('id');
        const action = button.hasClass('plus') ? 'increment' : 'decrement';
        const input = button.siblings('.qty-input');

        $.ajax({
            url: '/take-away-order/cart/',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                item_id: itemId,
                action: action
            },
            success: function(response) {
                if (response.status === 'success') {
                    // input.val(response.quantity);
                    // alert('Cart updated successfully.');
                    window.location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Failed to update cart.');
                console.error(xhr.responseText);
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Get the query parameter ?item=xx
        const params = new URLSearchParams(window.location.search);
        const itemId = params.get('item');

        if (itemId) {
            // Find the element with data-id
            const targetItem = document.querySelector(`[data-id="${itemId}"]`);
            if (targetItem) {
                // Smooth scroll to the item
                targetItem.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Optional: highlight it for a moment
                targetItem.style.backgroundColor = '#ffff99';
                setTimeout(() => targetItem.style.backgroundColor = '', 2000);
            }
        }
    });
</script>

@endsection