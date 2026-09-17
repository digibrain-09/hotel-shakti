@include('new_template.header')

<style>
    .quantity-wrapper2 {
        display: flex;
        align-items: center;
        width: 25%;
        border: 2px solid #e7272d;
        border-radius: 5px;
        overflow: hidden;
        margin-top: 3px;

        position: absolute;
        top: 80%;
        left: 82%;
        transform: translate(-50%, -50%);
        background-color: #fff;
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

    @media (max-width: 768px) {
        .quantity-wrapper2 {
            width: 80%;
            border: 2px solid #e7272d;
            top: 60% !important;
            left: 50% !important;
            transform: translate(-50%, -50%);
        }

        .quantity-wrapper2 .qty-btn {
            background-color: #fff;
        }

        .quantity-wrapper2 .qty-btn .fa {
            color: #e7272d;
        }
    }
</style>


<script>
    /* =====================================================================
       MENU / CART
       ===================================================================== */

    function toggleMobileSearch() {
        const mobileSearch = document.getElementById('mobileSearch');
        mobileSearch.classList.toggle('show');
    }

    $(document).ready(function () {
        $('#myTabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });

    let basePrice = 0; // Global base price

    $(document).ready(function () {
        $('.item_details').on('click', function (e) {
            e.preventDefault();

            const itemId = $(this).data('id');

            $.ajax({
                url: "/hotelshakti/view/" + itemId,
                type: "GET",
                success: function (response) {
                    basePrice = parseFloat(response.price);

                    $('#offcanvasBottomLabel').text(response.name);
                    $('#offcanvasBottomLabel2').text(response.name);
                    $('.offcanvas-image').attr('src', '/items/' + response.picture);

                    $('.qty-input').val(1);
                    updateTotalPrice();

                    $('.offcanvas-add-btn').attr('data-item-id', response.item_id);

                    let itemdetailsHtml = `
                    <div class="row">
                        <div class="col-12">
                            <img src="/items/${response.picture}" class="item-details-image" alt=""><br>
                            <img src="" class="mt-3 food_type_image" width="20px" height="20px" alt="">
                            <h4 class="offcanvas-title">${response.name}</h4>
                            <h5>₹${response.price}</h5>
                            <p>${response.description}</p>
                        </div>
                    </div>`;

                    $('.offcanvas-body .item-details-body').html(itemdetailsHtml);

                    if (response.food_type == 1) {
                        $('.food_type_image').attr('src', '/restaurant_logo/Non_veg_symbol.png');
                    } else {
                        $('.food_type_image').attr('src', '/restaurant_logo/Veg_symbol.png');
                    }

                    if (response.add_ons.length > 0) {
                        let addonsExtra = '';
                        let addonsVariation = '';
                        let addonsVegtoppings = '';

                        $.each(response.add_ons, function (index, addon) {

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

                        $('.addon-checkbox').on('change', function () {
                            updateTotalPrice();
                        });
                        $('.regular-radio').on('change', function () {
                            updateRegularradioPrice();
                        });

                    } else {
                        $('.offcanvas-body .addon').hide();
                        $('.offcanvas-body .note').css('margin-top', '0px');
                    }
                },
                error: function (xhr) {
                    console.error('Error loading item details:', xhr);
                }
            });
        });

        // Quantity button logic
        $(document).on('click', '.quantity-wrapper .qty-btn.minus', function () {
            let input = $('.qty-input');
            let value = parseInt(input.val());
            if (value > 1) {
                input.val(value - 1);
                updateTotalPrice();
            }
        });

        $(document).on('click', '.quantity-wrapper .qty-btn.plus', function () {
            let input = $('.qty-input');
            let value = parseInt(input.val());
            input.val(value + 1);
            updateTotalPrice();
        });

        $(document).on('input', '.quantity-wrapper .qty-input', function () {
            let val = parseInt($(this).val());
            if (isNaN(val) || val < 1) {
                $(this).val(1);
            }
            updateTotalPrice();
        });
    });

    function updateTotalPrice() {
        let quantity = parseInt($('.qty-input').val()) || 1;
        let total = basePrice;

        $('.addon-checkbox:checked').each(function () {
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

        $('.addon-checkbox:checked').each(function () {
            let addonPrice = parseFloat($(this).data('price')) || 0;
            total += addonPrice;
        });

        let finalPrice = total * quantity;
        let itemId = $('.offcanvas-add-btn').attr('data-item-id') || '';

        $('.offcanvas-add-btn-div').html(
            `<button type="button" class="btn btn-danger btn-lg offcanvas-add-btn" id="addToCartBtn" data-base-price="${basePrice}" data-item-id="${itemId}">Add to Cart ₹${finalPrice}</button>`
        );
    }

    // Handle Add to Cart button click
    $(document).on('click', '#addToCartBtn', function () {
        let quantity = parseInt($('.qty-input').val()) || 1;
        let note = $('#exampleFormControlTextarea1').val() || '';

        let price = $('.offcanvas-add-btn').attr('data-base-price');
        let item_image = $('.offcanvas-image').attr('src') || '';

        let selectedAddons = [];
        $('.addon-checkbox:checked').each(function () {
            selectedAddons.push({
                id: $(this).attr('id').replace(/^(checkbox-|radio-)/, ''),
                name: $(this).data('name'),
                price: $(this).data('price')
            });
        });

        let item_name = $('#offcanvasBottomLabel').text();
        let itemId = $('.offcanvas-add-btn').attr('data-item-id');

        $.ajax({
            url: '/hotelshakti/add-to-cart',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                item_id: itemId,
                quantity: quantity,
                addons: selectedAddons,
                note: note,
                item_name: item_name,
                price: price,
                item_image: item_image
            },
            success: function (response) {
                window.location.reload();
            },
            error: function (xhr) {
                // Middleware rejected: they moved away or the check expired.
                if (xhr.status === 403) {
                    // requestLocation(true);
                    return;
                }
                alert('Failed to add item to cart.');
                console.error(xhr.responseText);
            }
        });
    });

    $(document).on('click', '.quantity-wrapper2 .qty-btn', function () {
        const button = $(this);
        const itemId = button.data('id');
        const action = button.hasClass('plus') ? 'increment' : 'decrement';

        $.ajax({
            url: '/hotelshakti/cart/',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                item_id: itemId,
                action: action
            },
            success: function (response) {
                if (response.status === 'success') {
                    window.location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 403) {
                    // requestLocation(true);
                    return;
                }
                alert('Failed to update cart.');
                console.error(xhr.responseText);
            }
        });
    });
</script>

<header class="navbar-background py-3">
    <div class="navbar-overlay"></div>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark container navbar-content">
            <img src="{{ url('restaurant_logo/' . Session::get('logo')) }}" height="70px" width="90px" />

            <!-- Mobile search input -->
            <div id="mobileSearch" class="mobile-search mt-2">
                <form class="search-form d-flex" role="search">
                    <i class="bi bi-search search-icon"></i>
                    <input class="form-control search-input" type="search" placeholder="Search..." aria-label="Search">
                </form>
            </div>
        </nav>
    </div>
</header>

<section class="slider-section">
    <div class="container">
        <h3 class="mb-4">Craving Something Special?</h3>
        <div class="nav" id="myTabs">
            <div class="horizontal-scroll-wrapper">

                @foreach ($categories as $category)
                <div class="slide nav-item">
                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab_{{ $category->id }}" data-toggle="tab" href="#content_{{ $category->id }}">
                        <img src="{{ url('categories/' . $category->image) }}" alt="category_img" class="category_img img-responsive" />
                        <h5 class="text-center mt-2">{{ $category->category_name }}</h5>
                    </a>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</section>

<section class="item-section mt-5 mb-5">
    <div class="container">
        <h3 class="mb-4">Today's Specials</h3>
        <div class="tab-content">
            @foreach ($categories as $category)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="content_{{ $category->id }}">
                <ul class="list-unstyled category">
                    @foreach ($category->items as $item)
                    @php
                    $inCart = array_key_exists($item->id, $cartItems);
                    $itemQty = $inCart ? $cartItems[$item->id] : 1;
                    $disabled = $itemQty == 1 ? 'disabled' : '';
                    @endphp
                    @if($item->status == 1 && (
                    ($seating_type == 1 && $item->display_in_ac) ||
                    ($seating_type == 0 && $item->display_in_non_ac)
                    ))
                    <div class="row mb-2">
                        <div class="col-6">
                            @if($item->food_type == 1)
                            <img src="{{ url('restaurant_logo/Non_veg_symbol.png') }}" height="15px" width="15px" alt=""><br>
                            @else
                            <img src="{{ url('restaurant_logo/Veg_symbol.png') }}" height="15px" width="15px" alt=""><br>
                            @endif
                            @php
                            $hasAddOns = $add_ons->contains('item_id', $item->id);
                            @endphp

                            @if ($hasAddOns)
                            <small>Customisable</small>
                            @endif
                            <h4 class="mt-2 item_name">{{ $item->item_name}}</h4>
                            <h6 class="mt-2 item_name">{{ $item->item_name_gu}}</h6>
                            @if($seating_type == 1)
                            <h5 class="item_price" data-price="{{ $item->ac_price}}">₹{{ $item->ac_price}}</h5>
                            @else
                            <h5 class="item_price" data-price="{{ $item->price}}">₹{{ $item->price}}</h5>
                            @endif
                            <p>{{ \Illuminate\Support\Str::limit($item->description, 80, '...') }}</p>

                        </div>
                        <div class="col-6">
                            <div class="image-container">
                                <a href="" class="item_details" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom2" data-id="{{ $item->id }}">
                                    <img src="{{ url('items/' . $item->picture) }}" class="item_image" alt="">
                                </a>
                                @if ($inCart)
                                <div class="quantity-wrapper2 mt-2">
                                    <button class="qty-btn minus" data-id="{{ $item->id }}" {{ $disabled }}><i class="fa fa-minus"></i></button>
                                    <input type="number" class="qty-input" value="{{ $itemQty }}" min="1" readonly>
                                    <button class="qty-btn plus" data-id="{{ $item->id }}"><i class="fa fa-plus"></i></button>
                                </div>
                                @else
                                <a type="button"
                                    class="btn btn-danger add_to_cart item_details mt-2"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasBottom"
                                    data-id="{{ $item->id }}">
                                    Add <i class="fa fa-plus"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
            </div>
            @endforeach
        </div>
</section>

<section class="add-btn-section">
    <div class="offcanvas offcanvas-bottom" style="height: 70vh;" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-header">
            <img src="{{ url('items/' . $item->picture) }}" class="offcanvas-image" alt="">
            <h4 class="offcanvas-title mr-auto ml-2" id="offcanvasBottomLabel">Item Name</h4>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body small">
            <div class="addon-content">
            </div>

            <div class="card note">
                <div class="card-body note-body">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Any Cooking Preferences?</h5>
                            <textarea class="form-control" name="order_note" placeholder="Let us know if you have any preferences..." id="exampleFormControlTextarea1" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="row">
                <div class="col-5">
                    <div class="quantity-wrapper">
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

<section class="item-details-section">
    <div class="offcanvas offcanvas-bottom" style="height: 70vh;" tabindex="-1" id="offcanvasBottom2" aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title mr-auto ml-2" id="offcanvasBottomLabel2">Item Name</h4>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body small">
            <div class="card">
                <div class="card-body item-details-body">
                </div>
            </div>

        </div>

    </div>
</section>
</body>

</html>