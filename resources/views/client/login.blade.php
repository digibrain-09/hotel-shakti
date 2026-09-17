<!-- <link rel="stylesheet" href="{{asset('assets2/css2/bootstrap.css')}}">
<div class="container">
    <div class="row justify-content-center h-100">
        <div class="card w-25 my-auto shadow">
            <div class="card-header text-center text-white">
                <h2>Login form</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('customer_check') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" class="form-control" name="email" required />
                        <small style="color: red;">@if(session()->has('message')){{ session()->get('message') }}@endif</small>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" class="form-control" name="password" required />
                    </div>
                    <input type="submit" class="btn btn-primary w-100" id="login-btn" value="Login" name="submit">
                </form>
                <a href="{{ route('custom_register') }}" class=""><small style="float: right;">Create Your Acccount</small></a>
            </div>
        </div>
    </div>
</div> -->

<style>
    #body {
        margin: 0;
        padding: 0;
        background-color: #17a2b8 !important;
        height: 100vh;
    }

    #login .container #login-row #login-column #login-box {
        margin-top: 120px;
        max-width: 500px;
        height: 340px;
        border: 1px solid #fff;
        background-color: #fff;
        box-shadow: 0px 13px 26px #d9e2f880;
        border-radius: 20px;
    }

    #login .container #login-row #login-column #login-box #login-form {
        padding: 20px;
    }

    #login .container #login-row #login-column #login-box #login-form #register-link {
        margin-top: -85px;
    }
</style>

<meta name="csrf_token" content="{{ csrf_token() }}" />
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<body id="body">
    <div id="login">
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form method="POST" action="{{ route('customer_check') }}" enctype="multipart/form-data">
                            @csrf
                            <h3 class="text-center text-info mt-3">Login</h3>
                            <div class="form-group">
                                <label for="email" class="text-info">Email:</label><br>
                                <input type="text" name="email" id="email" class="form-control">
                                <small style="color: red;">@if(session()->has('message')){{ session()->get('message') }}@endif</small>
                            </div>
                            <div class="form-group">
                                <label for="password" class="text-info">Password:</label><br>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" class="btn btn-info btn-md" value="Login">
                            </div>
                            <a href="{{ route('custom_register') }}" class=""><small style="float: right;">Create Your Acccount</small></a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>