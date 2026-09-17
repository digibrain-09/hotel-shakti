@include('restaurants.royal_platter.nav')

<div class="container">

    <div class="col-md-12 mt-2">
        <!---cart item here------->
        <h4><a href="{{ route('royalplatter.index', ['table' => session('table_name')]) }}" class="text-muted" data-id=""><i class="fas fa-times"></i></a></h4>
        <h4 class="page-header mt-5">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp; Shopping Cart
        </h4>
        <hr>

        @php $total = 0 @endphp
        @if($cart)
        @foreach($cart as $details)
        @php $total += $details->price * $details->quantity @endphp

        <div class="row cart_list align-items-center" data-id="{{ $details->id}}">
            <div class="col-6">
                <div class="text">
                    <p class="item_name mb-4">{{ $details->name }}</p>
                    <!-- <input type="number" value="{{ $details['quantity'] }}" class="form-control quantity cart_update col-4" min="1" /> -->
                    <!-- <p>${{ $details['price'] }}</p> -->
                    <!-- <p>${{ $details['price'] * $details['quantity'] }}</p> -->

                </div>
            </div>
            <div class="col">
                <a href="{{ route('royalplatter.view', $details->id) }}"><img class="product_image float-right" src="{{ url('items/' . $details->image) }}"></a>
            </div>
            <div class="col-1">
                <a href="{{ route('royalplatter.remove_from_cart', ['id' => $details->id]) }}" class="text-muted float-right cart_remove" onclick="return confirm(' you want to delete?');" id="" data-id=""><i class="fas fa-times"></i></a>
            </div>
        </div>

        <div class="row cart_list mt-3" data-id="{{$details->id}}">
            <div class="col-6">
                <div class="quantity">
                    <input type="number" class="cart_update quantity col-6" min="1" max="9" step="1" value="{{ $details->quantity }}">
                </div>
                <!-- <input type="number" value="{{ $details['quantity'] }}" class="form-control quantity cart_update col-6" min="1" /> -->
            </div>
            <div class="col-5">
                <b>
                    <p class="float-right">₹{{ $details->price }}</p><br>
                    <!-- <p class="float-right">Subtotal :{{ $details['price'] * $details['quantity'] }}</p> -->
                </b>
            </div>
        </div>
        <hr>

        @endforeach
        @else
        <p>Your Cart is Empty!</p>
        @endif

        <div class="row">
            <div class="col-6">
                <h3>Total</h3>
            </div>
            <div class="col-6">
                <h3 class="float-right"><strong>₹{{ number_format($total,2) }}</strong></h3>
            </div>
        </div>


        <hr>
        <div class="row">
            <div class="col-md-12">
                <?php if ($total == 0) {
                } else { ?>
                    <form method="POST" action="{{ route('royalplatter.order') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- @foreach($cart as $details)
                        <input type="hidden" name="product_name[]" value="{{ $details->name }}">
                        <input type="hidden" name="product_price[]" value="{{ $details->price }}">
                        <input type="hidden" name="product_quantity[]" value="{{ $details->quantity }}">
                        @endforeach-->
                        <input type="hidden" name="total_amount" value="{{ $total }}"> 
                        <button type="submit" class="pull-right btn btn-success col-12 mb-3" name="submit">Place Order</button>
                    </form>
                <?php } ?>

                <!-- <div id="google-pay-button"></div> -->
                <!-- <button id="google-pay-button">Pay with Google Pay</button> -->
                <!-- <button id="payButton" class="pull-right btn btn-primary col-12 mb-3">Pay with Google Pay</button> -->
                <!-- <div id="google-pay-button-container"></div> -->
                <!-- <button id="google-pay-button-container" class="pull-right btn btn-primary col-12 mb-3">Pay with Google Pay</button> -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form method="POST" action="{{ route('royalplatter.order') }}" enctype="multipart/form-data">
                    @csrf
                    <!-- <button type="submit" name="submit" class="pull-right chekout_btn btn btn-success col-12"><span class="glyphicon glyphicon-floppy-disk"></span>Checkout</button> -->
                </form>
            </div>
        </div>


       

        <!---cart item here------->
    </div>
</div>

<script type="text/javascript">
    // $(".cart_update").change(function(e) {
    //     e.preventDefault();

    //     var ele = $(this);

    //     $.ajax({
    //         url: "{{ route('royalplatter.update_cart') }}",
    //         method: "patch",
    //         data: {
    //             _token: '{{ csrf_token() }}',
    //             id: ele.parents(".cart_list").attr("data-id"),
    //             quantity: ele.parents(".cart_list").find(".quantity").val()
    //         },
    //         success: function(response) {
    //             window.location.reload();
    //         }
    //     });
    // });



    $(document).ready(function() {
        jQuery('<div class="quantity-nav"><button class="quantity-button quantity-up">&#xf106;</button><button class="quantity-button quantity-down">&#xf107</button></div>').insertAfter('.quantity input');
        jQuery('.quantity').each(function() {
            var spinner = jQuery(this),
                input = spinner.find('input[type="number"]'),
                btnUp = spinner.find('.quantity-up'),
                btnDown = spinner.find('.quantity-down'),
                min = input.attr('min'),
                max = input.attr('max');

            // Handle quantity-up button click
            btnUp.click(function() {
                var oldValue = parseFloat(input.val());
                var newVal = oldValue >= max ? oldValue : oldValue + 1;
                updateCart(newVal, $(this));
            });

            // Handle quantity-down button click
            btnDown.click(function() {
                var oldValue = parseFloat(input.val());
                var newVal = oldValue <= min ? oldValue : oldValue - 1;
                updateCart(newVal, $(this));
            });

            // Handle direct input changes
            input.on('input', function() {
                var newVal = parseFloat(input.val());
                if (newVal >= min && newVal <= max) {
                    updateCart(newVal, $(this));
                }
            });

            function updateCart(newVal, ele) {
                spinner.find("input").val(newVal);
                spinner.find("input").trigger("change");

                $.ajax({
                    url: "{{ route('royalplatter.update_cart') }}",
                    method: "patch",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: ele.parents(".cart_list").attr("data-id"),
                        quantity: newVal
                    },
                    success: function(response) {
                        window.location.reload();
                    }
                });
            }
        });
    });
</script>
   