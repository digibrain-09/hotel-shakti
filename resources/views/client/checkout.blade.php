@include('client.nav')

<div class="mt-5 text-center">
    <img src="{{asset('assets/img/foodhand.png')}}" class="img-responsive" style="margin-left: auto;margin-right: auto;width: auto;max-height: 300px; " />
    <h1>Thank you for your order! <br /> <small>Your dish is being prepared.</small></h1>

    <a href="{{ route('scantable.myorder') }}" class="btn btn-success"><i class="fas fa-arrow-circle-left"></i> Back to my orders</a>
</div>