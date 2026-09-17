@extends('layouts.base',['nav_title'=>"Menu"])

@section('page-title')
Waiter | {{ config('app.name', 'Scantable') }}
@endsection

@section('body-content')
@component('components.alert')

@endcomponent


<style>
    #table-card {
        border: 2px solid green;
        height: 200px;
        background-size: cover;
        background-position: center;
    }

    .card-title {
        font-size: 20px;
        color: #000;
        font-weight: bold;
    }

    .customer_tr {
        color: red;
    }


    .table-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        /* Default: 3 columns */
        gap: 10px;
    }

    /* Responsive for tablet (max-width: 1024px) */
    @media (max-width: 1024px) {
        .table-container {
            grid-template-columns: repeat(3, 1fr);
            /* 3 columns for tablet */
        }

        #table-card {
            height: 100px;
        }
    }

    /* Responsive for mobile (max-width: 768px) */
    @media (max-width: 768px) {
        .table-container {
            grid-template-columns: repeat(3, 1fr);
            /* 3 columns for mobile */
        }

        #table-card {
            height: 80px;
        }
    }


    @media only screen and (max-width: 600px) {
        .card-title {
            font-size: 12px;
        }

        #table-card {
            height: 80px;
        }
    }
</style>

<section class="section">
    <div class="section-body">
        <div class="card mb-0">
            <div class="card-body">

                <div class="container">

                    <div class="col-md-12">

                        @php $total = 0 @endphp
                        @if($cart)
                        @foreach($cart as $details)
                        @php $total += $details->price * $details->quantity @endphp

                        <div class="row cart_list align-items-center" data-id="{{ $details->id}}">
                            <div class="col-6">
                                <div class="text">
                                    <p class="card-title table-box mb-4">{{ $details->name }}</p>

                                </div>
                            </div>
                            <div class="col">
                                <img class="product_image float-right" height="150px" width="200px" src="{{ url('items/' . $details->image) }}">
                            </div>
                            <div class="col-1">
                                <a href="{{ route('RemovefromCart', ['item_id' => $details->id]) }}" class="text-muted float-right cart_remove" onclick="return confirm(' you want to delete?');" id="" data-id=""><i class="fas fa-times"></i></a>
                            </div>
                        </div>

                        <div class="row cart_list mt-3" data-id="{{$details->id}}">
                            <div class="col-6">
                                <div class="quantity">
                                    <input type="number" class="cart_update quantity col-6" min="1" max="9" step="1" value="{{ $details->quantity }}">
                                </div>
                            </div>
                            <div class="col-5">
                                <b>
                                    <p class="float-right card-title table-box">₹{{ $details->price }}</p><br>
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
                                <h3 class="card-title">Total</h3>
                            </div>
                            <div class="col-6">
                                <h3 class="float-right card-title"><strong>₹{{ number_format($total,2) }}</strong></h3>
                            </div>
                        </div>


                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if ($total == 0) {
                                } else { ?>
                                    <form method="POST" action="{{ route('scantable.order') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="total_amount" value="{{ $total }}">
                                        <button type="submit" class="pull-right btn btn-success col-12 mb-3" name="submit">Place Order</button>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <form method="POST" action="{{ route('scantable.order') }}" enctype="multipart/form-data">
                                    @csrf
                                </form>
                            </div>
                        </div>




                        <!---cart item here------->
                    </div>
                </div>



                <div class="pagination-links" id="pagination-1" data-table-id="1"></div>
            </div>
        </div>
    </div>
</section>


@endsection


<script type="text/javascript">
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
                    url: "{{ route('scantable.update_cart') }}",
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