@include('client.nav')
<style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
</style>

<div class="mt-5 text-center">
    <img src="{{asset('assets/img/check.png')}}" class="img-responsive" style="margin-left: auto;margin-right: auto;width: auto;max-height: auto; " />
    <h1>Payment Successful!</h1>
    <h4>Thank You for Choosing Us!</h4>
    <p style="color: red;">Please Wait...</p>
</div>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            window.location.href = "https://maps.app.goo.gl/7v32HS7La4HsAXNH8";
        }, 2000); // Delay in milliseconds (5000ms = 5 seconds)
    });
</script>