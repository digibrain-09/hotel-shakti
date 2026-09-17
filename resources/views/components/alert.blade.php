@if(session()->has('status'))
<div class="alert alert-success alert-dismissible show fade">
    <div class="alert-body">
        <button class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
        {{session('status')}}
    </div>
</div>
@endif
@if(session()->has('alert-success'))
<div class="alert alert-success alert-dismissible show fade">
    <div class="alert-body">
        <button class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
        {{session('alert-success')}}
    </div>
</div>
@endif
@if(session()->has('alert-danger'))
<div class="alert alert-danger alert-dismissible show fade">
    <div class="alert-body">
        <button class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
        {{session('alert-danger')}}
    </div>
</div>
@endif
@if(session()->has('alert-warning'))
<div class="alert alert-warning alert-dismissible show fade">
    <div class="alert-body">
        <button class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
        {{session('alert-warning')}}
    </div>
</div>
@endif