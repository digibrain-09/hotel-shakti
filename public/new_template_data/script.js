$(document).ready(function () {
  $(".slider").slick({
    autoplay: false,
    autoplaySpeed: 2000,
    arrows: true,
    infinite: true,
    prevArrow:
      '<button class="custom-prev"><i class="fa fa-angle-left"></i></button>',
    nextArrow:
      '<button class="custom-next"><i class="fa fa-angle-right"></i></button>',
    centerMode: false,
    slidesToShow: 6,
    slidesToScroll: 2,

    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 5,
          infinite: true,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 4,
          arrows: false,
          // dots: true
        },
      },
      {
        breakpoint: 300,
        settings: "unslick", // destroys slick
      },
    ],
  });
});

function toggleMobileSearch() {
  const mobileSearch = document.getElementById("mobileSearch");
  mobileSearch.classList.toggle("show");
}

$(document).ready(function () {
  $("#myTabs a").click(function (e) {
    e.preventDefault();
    $(this).tab("show");
  });
});

let basePrice = 0; // Global base price

$(document).ready(function () {
  $(".item_details").on("click", function (e) {
    e.preventDefault();

    const itemId = $(this).data("id");

    $.ajax({
      url: "/scantable/view/" + itemId,
      type: "GET",
      success: function (response) {
        // Store base price globally
        basePrice = parseFloat(response.price);

        $("#offcanvasBottomLabel").text(response.name);
        $("#offcanvasBottomLabel2").text(response.name);
        $(".offcanvas-image").attr("src", "/items/" + response.picture);

        $(".qty-input").val(1); // Reset quantity to 1
        updateTotalPrice(); // Initial price set

        $(".offcanvas-add-btn").attr("data-item-id", response.item_id);

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

        $(".offcanvas-body .item-details-body").html(itemdetailsHtml);

        if (response.food_type == 1) {
          $(".food_type_image").attr(
            "src",
            "/restaurant_logo/Non_veg_symbol.png"
          );
        } else {
          $(".food_type_image").attr("src", "/restaurant_logo/Veg_symbol.png");
        }

        // Add-on handling
        if (response.add_ons.length > 0) {
          let addonsHtml = `
                        <h5>Add On</h5>
                        <p>Want something extra? Pick an add-on you like!</p>
                        <hr>
                    `;

          $.each(response.add_ons, function (index, addon) {
            addonsHtml += `
                            <div class="row mb-2">
                                <div class="col-9">
                                    <h6>${addon.name}</h6>
                                </div>
                                <div class="col-3">
                                    <div class="custom-control custom-checkbox checkbox-lg">
                                        <input type="checkbox" class="custom-control-input addon-checkbox" id="checkbox-${addon.id}" data-price="${addon.price}">
                                        <label class="custom-control-label" for="checkbox-${addon.id}">
                                            <h6>₹${addon.price}</h6>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        `;
          });

          $(".offcanvas-body .addon").show();
          $(".offcanvas-body .addon-body").html(addonsHtml);
          $(".offcanvas-body .note").css("margin-top", "30px");

          // Listen for checkbox changes
          $(".addon-checkbox").on("change", function () {
            updateTotalPrice();
          });
        } else {
          $(".offcanvas-body .addon").hide();
          $(".offcanvas-body .note").css("margin-top", "0px");
        }
      },
      error: function (xhr) {
        console.error("Error loading item details:", xhr);
      },
    });
  });

  // Quantity button logic
  $(document).on("click", ".qty-btn.minus", function () {
    let input = $(".qty-input");
    let value = parseInt(input.val());
    if (value > 1) {
      input.val(value - 1);
      updateTotalPrice();
    }
  });

  $(document).on("click", ".qty-btn.plus", function () {
    let input = $(".qty-input");
    let value = parseInt(input.val());
    input.val(value + 1);
    updateTotalPrice();
  });

  // Also handle manual input change
  $(document).on("input", ".qty-input", function () {
    let val = parseInt($(this).val());
    if (isNaN(val) || val < 1) {
      $(this).val(1);
    }
    updateTotalPrice();
  });
});

// ✅ Update total price based on quantity and add-ons
function updateTotalPrice() {
  let quantity = parseInt($(".qty-input").val()) || 1;
  let total = basePrice;

  $(".addon-checkbox:checked").each(function () {
    let addonPrice = parseFloat($(this).data("price")) || 0;
    total += addonPrice;
  });

  let finalPrice = total * quantity;
  let itemId = $(".offcanvas-add-btn").attr("data-item-id") || "";

  $(".offcanvas-add-btn-div").html(
    `<button type="button" class="btn btn-danger btn-lg offcanvas-add-btn" id="addToCartBtn" data-base-price="${finalPrice}" data-item-id="${itemId}">Add to Cart ₹${finalPrice}</button>`
  );
}

// Handle Add to Cart button click
$(document).on("click", "#addToCartBtn", function () {
  let quantity = parseInt($(".qty-input").val()) || 1;
  let note = $("#exampleFormControlTextarea1").val() || "";

  let price = $(".offcanvas-add-btn").attr("data-base-price");
  let item_image = $(".offcanvas-image").attr("src") || "";

  // Get selected add-on IDs
  let selectedAddons = [];
  $(".addon-checkbox:checked").each(function () {
    selectedAddons.push($(this).attr("id").replace("checkbox-", ""));
  });

  // Get item ID (assumed to be stored in a hidden field or header)
  let item_name = $("#offcanvasBottomLabel").text();

  // You can store item ID in a hidden field or set a data attribute in your add-to-cart button
  // For example, store it in .offcanvas-add-btn-div like this:
  let itemId = $(".offcanvas-add-btn").attr("data-item-id");

  // Send AJAX request to add to cart
  $.ajax({
    url: "/scantable/add-to-cart", // Replace with your actual route
    type: "POST",
    data: {
      _token: "{{ csrf_token() }}", // CSRF token
      item_id: itemId,
      quantity: quantity,
      addons: selectedAddons,
      note: note,
      item_name: item_name,
      price: price,
      item_image: item_image,
    },
    success: function (response) {
      alert("Item added to cart successfully!");
      // $('#offcanvasBottom').offcanvas('hide');
      window.location.reload();
      // You can also refresh cart summary here if needed
    },
    error: function (xhr) {
      alert("Failed to add item to cart.");
      console.error(xhr.responseText);
    },
  });
});
