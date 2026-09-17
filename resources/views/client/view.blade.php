@include('client.nav')
<div class="view_image">
    @foreach ($items as $item)
    <img src="{{ url('items/' . $item->picture) }}" class="product_view_iamge" height="500px" alt="Snow" style="width:100%;">
    <a href="{{ route('scantable.index', session('table_name')) }}">
        <h2 class="top-left mt-3 ml-3"><b><i class="fa fa-arrow-circle-left" aria-hidden="true"></i></b></h2>
    </a>
    @endforeach


</div>
<div class="container">
    <div class="content-wrapper" id="main_section">

        @foreach ($items as $item)
        <!-- <img id="item-display" class="product_detail_image img-responsive img-thumbnail" src="{{ url('items/' . $item->picture) }}"> -->
        <div class="row">
            <div class="col-md-12 mt-3">
                <h2 class="text"><b>{{$item->item_name}}</b></h2>
            </div>
            <div class="col-md-12">
                <div class="product-price">₹ {{$item->price}}</div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="">{{ substr(strip_tags($item->description), 0, 500) }}</div>
            </div>

        </div>
        <hr>

        <h4>Frequently Bought Together</h4>
        @foreach ($categories as $row)
        @foreach ($row->items as $row)
        @if($item->category_id == $row->category_id && $item->item_name !== $row->item_name)
        @if($row->status == 1)
        <div class="row justify-content-between mt-5">
            <div class="col-4">
                <div class="text">
                    <b>
                        <spam class="item">{{ $row->item_name}}</spam>
                    </b><br>

                    <span class="">₹ {{ $row->price}}</span><br>
                    <div class="btn-group mt-2">
                        <!-- @if (empty(session()->has('session_code')))
                        <a type="button" href="javascript:void(0);" onclick="formToggle();" class="btn btn-danger add_to_cart btn-sm" name="add_to_cart" id="{{$item->id}}">
                            Add</a>
                        @else
                        <a type="button" href="{{ route('add_to_cart', $row->id) }}" class="btn btn-danger add_to_cart btn-sm" name="add_to_cart" id="{{$item->id}}">
                            Add</a>
                        @endif -->
                        <a type="button" href="{{ route('scantable.add_to_cart', $row->id) }}" class="btn btn-danger add_to_cart btn-sm" name="add_to_cart" id="{{$item->id}}">
                            Add</a>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="image">
                    <a href="{{ route('scantable.view', $row->id) }}"><img class="product_image float-right" src="{{ url('items/' . $row->picture) }}"></a>
                </div>
            </div>
        </div>
        @endif
        @endif
        @endforeach
        @endforeach

        <hr>
        <div class="row">
            <div class="col-md-12 mt-3 mb-2">

                <a type="button" href="{{ route('scantable.add_to_cart', $item->id) }}" class="btn btn-success add_to_cart col-12" name="add_to_cart" id="{{$item->id}}">
                    <i class="fas fa-plus"></i> Add to cart</a>
            </div>

        </div>


        @endforeach
    </div>

    <div class="cart_icon">
        <a href="{{ route('scantable.cart') }}" class="cart_link"><i class="fa fa-shopping-cart fa-2x" aria-hidden="true"></i></a>
        <span class='badge badge-warning' id='lblCartCount'>
            {{$cart}}
        </span>
    </div>
</div>
<script type="text/javascript">
    $(document).on('click', '.value-control', function() {
        var action = $(this).attr('data-action')
        var target = $(this).attr('data-target')
        var value = parseFloat($('[id="' + target + '"]').val());
        if (action == "plus") {
            value++;
        }
        if (action == "minus") {
            value--;
        }
        $('[id="' + target + '"]').val(value)
    });

    function formToggle() {
        <?php if (Session::get('session_code')) { ?>

        <?php } else { ?>
            var url = "{{ route('custom_login') }}";
            alert('You are not logged in. Please log in.');
            window.location.href = url;
        <?php } ?>
    }
</script>