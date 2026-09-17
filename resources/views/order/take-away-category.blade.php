@extends('layouts.admin')
@extends('order.slider',['nav_title'=>"Menu"])
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .cart_icon {
        position: fixed;
        right: 2%;
        top: 90%;
        float: right;
        z-index: 1;
    }

    .cart_link {
        display: inline-block;
        border-radius: 50px;
        background-color: green;
        color: white;
        box-shadow: 0 0 2px #888;
        padding: 10px;
    }

    #lblCartCount {
        font-size: 12px;
        background: black;
        color: #fff;
        padding: 0 5px;
        vertical-align: top;
        margin-left: -10px;
        border-radius: 50%;
    }

    .suggestion-box {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: #fff;
        border: 1px solid #ccc;
        border-top: none;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        border-radius: 0px 0px 16px 16px;
    }

    .suggestion-box div {
        padding: 8px;
        cursor: pointer;
    }

    .suggestion-box div:hover {
        background: #f0f0f0;
    }

    @media (max-width: 768px) {
        #table_card {
            height: 150px !important;
        }
    }
</style>

@section('content')
<!-- Breadcrumb Navigation -->
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{route('OrderMenu')}}" class="cursor-pointer">
        <span class="text-gray-500 hover:text-primary transition-all duration-200">Orders</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
    <span class="text-foreground font-medium">Categories</span>
</div>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Menu Management</h1>
        <p class="text-gray-500 text-sm md:text-base">Streamline takeaway orders with an easy-to-use menu system.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 ml-auto md:ml-0">
        <a href="{{ route('ViewOrder') }}">
            <button class="flex items-center gap-2 px-4 py-2.5 border border-border rounded-button text-foreground font-medium hover:border-primary transition-all duration-200 cursor-pointer">
                <i data-lucide="utensils" class="w-4 h-4"></i>
                <span>View Take Away Orders</span>
            </button>
        </a>
        <a href="{{ route('TakeAwayOrder') }}">
            <button class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>New Take Away Order</span>
            </button>
        </a>
    </div>
</div>

<!-- Alerts -->
@if(Session::get('test') == 'restaurant_admin')
<div class="alertordermsg"></div>
<div id="alertSoundContainer"></div>
<div id="PrintalertSoundContainer"></div>
<div class="alertpaymentmsg"></div>
<div class="alertTakeAwayordermsg"></div>
<div id="alertTakeAwaySound"></div>
<div class="alertreadytoservedmsg"></div>
<div id="ReadyToservedalertSoundContainer"></div>
<div class="Waiteralertordermsg"></div>
<div id="alertSoundContainer_waiter"></div>
<div class="alertreadytoservedWaitermsg"></div>
<div id="ReadyToservedWaiteralertSoundContainer"></div>
@endif

<!-- Search & Filter Bar -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
    <div class="bg-white rounded-card p-4">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <!-- Search Input -->
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                <input type="text" id="searchBox" placeholder="What are you looking for?" class="w-full pl-10 pr-4 py-2.5 border border-border rounded-button text-sm focus:outline-none focus:border-primary transition-all duration-200">
                <div id="suggestionBox" class="suggestion-box"></div>
            </div>

        </div>
    </div>
</div>

<!-- Category -->
<div class="lg:col-span-8 none-air-conditioner">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <div class="bg-white rounded-card p-5">
            <div id="tables-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 gap-3">
                <!-- Tables will be dynamically generated here -->
                @php
                $matchFound = false; // Flag to track if a match is found
                @endphp
                @foreach ($categories as $index => $table)
                @if($table->seating_type == false)
                @php
                $matchFound = true; // Set the flag to true
                @endphp
                <a class="mb-5" href="{{ route('getOrderItems', ['category_id' => $table->id] ) }}">
                    <h2 class="text-foreground text-md font-bold mb-1 table_name" style="text-transform:capitalize;">{{ $table->category_name }}</h2>
                    <div id="table_card" class="relative p-4 border-2 border-gray-200 rounded-card text-center transition-all duration-200"
                        style="background-image: url('{{ url('categories/' . $table->image) }}');height:250px;background-size: cover;background-position: center;border: 1px solid green;">
                        <div class="w-3 h-3 rounded-full absolute top-2 right-2"></div>
                    </div>
                </a>
                @endif
                @endforeach

            </div>
            @if (!$matchFound)
            <p class="text-center pt-3 pb-3" style="background-color:yellow">Tables not found.</p>
            @endif
        </div>
    </div>
</div>

<!-- Cart Icon -->
<div class="cart_icon">
    <p class="cart_link cursor-pointer" id="open-btn" onclick="openSidebar()"><i class="fa fa-shopping-cart fa-2x" aria-hidden="true"></i></p>
    <span class='badge badge-warning' id='lblCartCount'>
        {{$cart}}
    </span>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#searchBox').on('input', function() {
            let query = $(this).val().trim();
            if (query.length > 0) {
                $.ajax({
                    url: "{{ route('search.take-away-suggestions') }}",
                    type: "GET",
                    data: {
                        q: query
                    },
                    success: function(response) {
                        let suggestions = '';
                        let data = response.results;

                        if (data.length > 0) {
                            data.forEach(item => {
                                suggestions += `<div class="suggestion-item"  
                                                data-categoryid="${item.category_id}"
                                                data-id="${item.id}">
                                                ${item.item_name}
                                             </div>`;
                            });
                        } else {
                            suggestions = `<div>No results found</div>`;
                        }
                        $('#suggestionBox').html(suggestions).show();
                    }
                });
            } else {
                $('#suggestionBox').hide();
            }
        });

        // When user clicks a suggestion
        $(document).on('click', '.suggestion-item', function() {
            const categoryId = $(this).data('categoryid');
            const itemId = $(this).data('id');

            if (itemId) {
                // redirect to that product page
                window.location.href = `/take-away-order/${categoryId}?item=${itemId}`;
            }
        });
    });
</script>
@endsection