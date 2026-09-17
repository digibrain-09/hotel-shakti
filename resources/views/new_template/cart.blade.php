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


    .cart-item2 {
        background: #fff;
        border-radius: 14px;
        padding: 12px;
        align-items: center;
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

    .quantity-controls {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: white;
        border-radius: 10px;
        padding: 4px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .quantity-wrapper {
        width: 10%;
    }


    .quantity-wrapper2 {
        display: flex;
        align-items: center;
        width: 50%;
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


    .bottom-btn-container {
        width: 100%;
        padding: 20px;
        margin-bottom: 50px;
    }


    .place-order-btn {
        font-weight: 500;
        background-color: #e7272d;
        color: #fff !important;
        border: 1px solid #e7272d;
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

    .suggestions-section .item-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 10px;
    }

    .suggestions-section .item-title {
        font-size: 15px;
        margin-top: 5px;
    }

    .scrollmenu {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 10px;
        scroll-snap-type: x mandatory;
        gap: 20px;
    }

    .scrollmenu .card {
        display: inline-block;
        flex: 0 0 auto;
        width: 300px;
        height: 330px;
        scroll-snap-align: start;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 8px;
        background: #fff;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
    }

    .suggestions-section .item-price {
        font-size: 15px;
        color: #e7272d;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .suggestions-section .place-order-btn {
        font-size: 15px;
        padding: 4px 8px;
        width: 100%;
    }

    small {
        color: gray;
    }

    @media (max-width: 768px) {
        .quantity-wrapper {
            width: 30%;
        }

        .quantity-wrapper2 {
            width: 100%;
        }

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

        .edit-item {
            font-size: 14px;
        }

        .cart_section {
            padding: 10px;
        }


        .suggestions-section .row {
            --bs-gutter-x: 10px !important;
        }


        .suggestions-section .item-title {
            font-size: 12px;
        }

        .scrollmenu .card {
            width: 140px;
            height: auto;
        }

        .scrollmenu {
            gap: 10px;
        }

        .suggestions-section .item-image {
            height: 100px;
        }

        .suggestions-section .place-order-btn,
        .suggestions-section .item-price {
            font-size: 12px;
        }

    }
</style>
<script>
    $(document).on('click', '.quantity-wrapper .qty-btn', function() {
        const button = $(this);
        const itemId = button.data('id');
        const action = button.hasClass('plus') ? 'increment' : 'decrement';
        const input = button.siblings('.qty-input');

        $.ajax({
            url: '/hotelshakti/cart/',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                item_id: itemId,
                action: action
            },
            success: function(response) {
                if (response.status === 'success') {
                    input.val(response.quantity);
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

    $(document).ready(function() {
        $('.item_details').on('click', function(e) {
            e.preventDefault();
            $('#offcanvasMode').val('edit');

            const itemId = $(this).data('id');

            $.ajax({
                url: "/hotelshakti/cart_view/" + itemId,
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
                            <div class="row mb-2">
                                <div class="col-9">
                                    <h6>${addon.name}</h6>
                                </div>
                                <div class="col-3">
                                    <div class="custom-control custom-checkbox checkbox-lg">
                                        <input type="checkbox" class="custom-control-input addon-checkbox" id="checkbox-${addon.id}" data-price="${addon.price}"  data-name="${addon.name}" ${isChecked}>
                                        <label class="custom-control-label" for="checkbox-${addon.id}">
                                            <h6>₹${addon.price}</h6>
                                        </label>
                                    </div>
                                </div>
                            </div>`;

                            let addonHTML2 = `
                                <div class="row mb-2">
                                    <div class="col-9">
                                        <h6>${addon.name}</h6>
                                    </div>
                                    <div class="col-3">
                                        <div class="custom-control custom-radio checkbox-lg">
                                            <input type="radio" name="variation_option" class="custom-control-input addon-radio addon-checkbox" id="radio-${addon.id}" data-price="${addon.price}" data-name="${addon.name}" ${isChecked}>
                                            <label class="custom-control-label" for="radio-${addon.id}">
                                                <h6>₹${variation_price}</h6>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            `;

                            if (addon.type == 'Extra') {
                                if (!addonsExtra) {
                                    addonsExtra += `
                                     <div class="card addon mb-3">
                                        <div class="card-body addon-body">
                                        <h5>Add On</h5>
                                        <p>Want something extra? Pick an add-on you like!</p>
                                        <hr>
                                    `;
                                }
                                addonsExtra += addonHTML;
                            } else if (addon.type == 'Variation') {
                                if (!addonsVariation) {
                                    addonsVariation += `
                                     <div class="card addon mb-3">
                                        <div class="card-body addon-body">
                                        <h5>Variation</h5>
                                        <p>Choose what suits you best</p>
                                        <hr>

                                         <div class="row mb-2">
                                    <div class="col-9">
                                        <h6>Regular</h6>
                                    </div>
                                    <div class="col-3">
                                        <div class="custom-control custom-radio checkbox-lg">
                                            <input type="radio" name="variation_option" class="custom-control-input addon-radio regular-radio" id="radio-" data-price="${basePrice}" data-name="Regular" checked>
                                            <label class="custom-control-label" for="radio-">
                                                <h6>₹${basePrice}</h6>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                    `;
                                }

                                addonsVariation += addonHTML2;
                            } else if (addon.type == 'Veg-toppings') {
                                if (!addonsVegtoppings) {
                                    addonsVegtoppings += `
                                     <div class="card addon mb-3">
                                        <div class="card-body addon-body">
                                        <h5>Veg Toppings</h5>
                                        <p>Pick your favorite veg toppings to customize your meal.</p>
                                        <hr>
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
                        `<button type="button" class="btn btn-danger btn-lg offcanvas-add-btn" id="addToCartBtn" data-base-price="${finalPrice}" data-item-id="${response.item_id}">Add to Cart ₹${finalPrice}</button>`
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


        $('.add').on('click', function(e) {
            e.preventDefault();

            $('#offcanvasMode').val('add');
            const itemId = $(this).data('id');


            // Fetch item details for add mode
            $.ajax({
                url: "/hotelshakti/view/" + itemId,
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
                                <div class="row mb-2">
                                    <div class="col-9">
                                        <h6>${addon.name}</h6>
                                    </div>
                                    <div class="col-3">
                                        <div class="custom-control custom-checkbox checkbox-lg">
                                            <input type="checkbox" class="custom-control-input addon-checkbox" id="checkbox-${addon.id}" data-price="${addon.price}" data-name="${addon.name}">
                                            <label class="custom-control-label" for="checkbox-${addon.id}">
                                                <h6>₹${addon.price}</h6>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            `;

                            let addonHTML2 = `
                                <div class="row mb-2">
                                    <div class="col-9">
                                        <h6>${addon.name}</h6>
                                    </div>
                                    <div class="col-3">
                                        <div class="custom-control custom-radio checkbox-lg">
                                            <input type="radio" name="variation_option" class="custom-control-input addon-radio addon-checkbox" id="radio-${addon.id}" data-price="${addon.price}" data-name="${addon.name}">
                                            <label class="custom-control-label" for="radio-${addon.id}">
                                                <h6>₹${variation_price}</h6>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            `;


                            if (addon.type == 'Extra') {
                                if (!addonsExtra) {
                                    addonsExtra += `
                                     <div class="card addon mb-3">
                                        <div class="card-body addon-body">
                                        <h5>Add On</h5>
                                        <p>Want something extra? Pick an add-on you like!</p>
                                        <hr>
                                    `;
                                }
                                addonsExtra += addonHTML;
                            } else if (addon.type == 'Variation') {
                                if (!addonsVariation) {
                                    addonsVariation += `
                                     <div class="card addon mb-3">
                                        <div class="card-body addon-body">
                                        <h5>Variation</h5>
                                        <p>Choose what suits you best</p>
                                        <hr>

                                         <div class="row mb-2">
                                    <div class="col-9">
                                        <h6>Regular</h6>
                                    </div>
                                    <div class="col-3">
                                        <div class="custom-control custom-radio checkbox-lg">
                                            <input type="radio" name="variation_option" class="custom-control-input addon-radio regular-radio" id="radio-" data-price="${basePrice}" data-name="Regular" checked>
                                            <label class="custom-control-label" for="radio-">
                                                <h6>₹${basePrice}</h6>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                    `;
                                }

                                addonsVariation += addonHTML2;
                            } else if (addon.type == 'Veg-toppings') {
                                if (!addonsVegtoppings) {
                                    addonsVegtoppings += `
                                     <div class="card addon mb-3">
                                        <div class="card-body addon-body">
                                        <h5>Veg Toppings</h5>
                                        <p>Pick your favorite veg toppings to customize your meal.</p>
                                        <hr>
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

                        // Listen for checkbox changes
                        //  $('.addon-checkbox').on('change', function() {
                        //     updateTotalPrice();
                        // });
                        $('.regular-radio').on('change', function() {
                            updateRegularradioPrice();
                        });



                    } else {
                        $('.offcanvas-body .addon').hide();
                    }

                    // Calculate price after setting everything
                    let finalPrice = (basePrice + selectedAddonTotal) * quantity;
                    $('.offcanvas-total-price').text(finalPrice);
                    $('.offcanvas-add-btn-div').html(
                        `<button type="button" class="btn btn-danger btn-lg offcanvas-add-btn" id="addToCartBtn" data-base-price="${finalPrice}" data-item-id="${response.item_id}">Add to Cart ₹${finalPrice}</button>`
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
    });

    // Recalculate function
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
            `<button type="button" class="btn btn-danger btn-lg offcanvas-add-btn" id="addToCartBtn" data-base-price="${basePrice}" data-item-id="${itemId}">Add to Cart ₹${final}</button>`
        );
    }

    // ✅ Update total price based on quantity and add-ons
    function updateTotalPrice() {
        let quantity = parseInt($('.quantity-wrapper2 .qty-input').val()) || 1;
        let total = basePrice;

        $('.addon-checkbox:checked').each(function() {
            let addonPrice = parseFloat($(this).data('price')) || 0;
            total += addonPrice;
        });

        let finalPrice = total * quantity;
        let itemId = $('.offcanvas-add-btn').attr('data-item-id') || '';

        $('.offcanvas-add-btn-div').html(
            `<button type="button" class="btn btn-danger btn-lg offcanvas-add-btn" id="addToCartBtn" data-base-price="${basePrice}" data-item-id="${itemId}">Add to Cart ₹${finalPrice}</button>`
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
            `<button type="button" class="btn btn-danger btn-lg offcanvas-add-btn" id="addToCartBtn" data-base-price="${basePrice}" data-item-id="${itemId}">Add to Cart ₹${finalPrice}</button>`
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
                url: '/hotelshakti/cart', // Replace with your actual route
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
                url: '/hotelshakti/add-to-cart', // Replace with your actual route
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

    jQuery(document).ready(function($) {

        $("#openPhonePopup").on("click", function() {
            $("#phoneModal").css("display", "block");
        });

        $("#closeModal").on("click", function() {
            $("#phoneModal").css("display", "none");
        });

        $("#submitPhone").on("click", function() {
            let phone = $("#phoneField").val().trim();

            if (phone === "") {
                alert("Please enter phone number");
                return;
            }

            // Remove non-digits (extra safety)
            phone = phone.replace(/\D/g, '');

            // Validate 10-digit phone number
            if (!/^[0-9]{10}$/.test(phone)) {
                alert("Please enter a valid 10-digit phone number");
                return;
            }

            $("#phoneInput").val(phone);
            $("#orderForm").submit();
        });

    });
</script>

<body>
    <section class="cart_section">
        <div class="cart-wrapper">
            <h3 class="mb-4 pl-2">{{$cart_count}} towards at cart</h3>
            @php
            $total = 0;
            $matchFound = false;
            @endphp
            @foreach($cart as $details)
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
            <div class="cart-item mb-4">
                <img src="{{ url($details->image) }}" alt="Oatmeal" class="item-image" />
                <div class="item-details">
                    <p class="item-title">{{ $details->name }}</p>
                    <p class="item-price">₹{{ $details['price'] }}</p>

                    <small>{{ implode(', ', $addonNames) }}</small>

                    <br>
                    @if($details['note'])<small>"{{ $details['note'] }}"</small>@endif
                    <br>

                    <a href="" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" data-id="{{ $details->item_id }}" class="edit-item item_details">Edit Item <i class='fa fa-caret-right'></i></a>
                </div>

                <div class="quantity-wrapper">
                    <button class="qty-btn minus" data-id="{{ $details->item_id }}" {{ $disabled }}><i class="fa fa-minus"></i></button>
                    <input type="number" class="qty-input" value="{{ $details->quantity }}" min="1" readonly>
                    <button class="qty-btn plus" data-id="{{ $details->item_id }}"><i class="fa fa-plus"></i></button>
                </div>


                <a href="{{ route('scantable.remove_from_cart', ['id' => $details->id]) }}" class="text-muted float-right cart_remove" onclick="return confirm('You want to delete?');" id="" data-id=""><i class="fa fa-trash-o" style="font-size:20px;color:#e7272d"></i></a>
            </div>

            @endforeach
            <a href="{{route('scantable.index', ['table' => session('table_name')])}}" class="add-more-items"><i class="fa fa-plus"></i> Add More Item </a>
            @if (!$matchFound)
            <p class="text-center mt-5 mb-5" style="color:red;">Your Cart is Empty!</p>
            @endif

        </div>
    </section>

    <section class="cart_section suggestions-section">
        <div class="cart-wrapper">
            <div class="cart-item2">
                <h4 class="mb-3">Treat yourself to</h4>
                <div class="scrollmenu">

                    @foreach ($suggestions as $addon)

                    @if($addon->status == 1 && (
                    ($seating_type == 1 && $addon->display_in_ac) ||
                    ($seating_type == 0 && $addon->display_in_non_ac)
                    ))
                    <div class="card">
                        <img src="{{ url('items/' .$addon->picture) }}" alt="item-image" class="item-image" /><br>
                        @if($addon->food_type == 1)
                        <img src="{{ url('restaurant_logo/Non_veg_symbol.png') }}" style="margin-top: 10px;" height="15px" width="15px" alt=""><br>
                        @else
                        <img src="{{ url('restaurant_logo/Veg_symbol.png') }}" style="margin-top: 10px;" height="15px" width="15px" alt=""><br>
                        @endif
                        <p class="item-title">{{ $addon->item_name }}</p>
                        @if($seating_type == 1)
                        <p class="item-price">₹{{ $addon->ac_price }}</p>
                        @else
                        <p class="item-price">₹{{ $addon->price }}</p>
                        @endif


                        <a type="button"
                            class="btn btn-danger place-order-btn add mt-2"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasBottom"
                            data-id="{{ $addon->id }}">
                            Add <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    @endif

                    @endforeach

                </div>

            </div>
        </div>
    </section>

    <div class="bottom-btn-container">
        <div class="row">
            <div class="col-6">
                <h3>Total</h3>
            </div>
            <div class="col-6">
                <h3 class="float-right"><strong>₹{{ number_format($total,2) }}</strong></h3>
            </div>


            <div class="col-12">
                <hr>
                @php
                // $hasPhone = session()->has('phone');
                if($existingCustomer){
                $phone = $existingCustomer->phone;
                // echo $phone;
                }
                @endphp

                @if($total > 0)
                <form id="orderForm" method="POST" action="{{ route('scantable.order') }}">
                    @csrf
                    <input type="hidden" name="total_amount" value="{{ $total }}">
                    @if(!$existingCustomer)
                    <input type="hidden" name="phone" id="phoneInput">
                    @endif

                    @if(!$existingCustomer)
                    <!-- first time - require popup -->
                    <button type="button" id="openPhonePopup" class="btn btn-danger col-12 mb-3">
                        Place Order
                    </button>
                    @else
                    <!-- phone already saved - submit directly -->
                    <button type="submit" class="btn btn-danger col-12 mb-3">
                        Place Order
                    </button>
                    @endif

                </form>
                @endif
            </div>


        </div>
    </div>

    <!-- Phone Modal -->
    <div id="phoneModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;background:rgba(0,0,0,0.6); padding-top:100px;">

        <div style="background:#fff; width:350px; margin:auto; padding:20px; border-radius:8px;">
            <h4>Phone Number</h4>
            <p>Enter your phone number now and enjoy a discount on your second order!</p>
            <input type="number" id="phoneField" maxlength="10" class="form-control" placeholder="Enter 10-digit phone number">

            <button id="submitPhone" class="btn btn-danger mt-3">Submit</button>
            <button id="closeModal" class="btn btn-outline-danger mt-3">Cancel</button>
        </div>
    </div>

    <section class="add-btn-section">
        <div class="offcanvas offcanvas-bottom" style="height: 70vh;" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
            <div class="offcanvas-header">
                <input type="hidden" id="offcanvasMode" value="add">

                <img src="" class="offcanvas-image" alt="">
                <h4 class="offcanvas-title mr-auto ml-2" id="offcanvasBottomLabel">Item Name</h4>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body small">
                <!-- <div class="card addon">
                    <div class="card-body addon-body">
                    </div>
                </div> -->
                <div class="addon-content">

                </div>

                <div class="card note">
                    <div class="card-body note-body">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">Any Cooking Preferences?</h5>
                                <textarea class="form-control order_note" name="order_note" placeholder="Let us know if you have any preferences..." id="exampleFormControlTextarea1" rows="3"></textarea>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="offcanvas-footer">
                <div class="row">
                    <div class="col-5">
                        <div class="quantity-wrapper2">
                            <button class="qty-btn minus"><i class="fa fa-minus"></i></button>
                            <input type="number" class="qty-input" value="1" min="1">
                            <button class="qty-btn plus"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-7 offcanvas-add-btn-div">
                    </div>
                </div>
            </div>

        </div>
    </section>



</body>

</html>