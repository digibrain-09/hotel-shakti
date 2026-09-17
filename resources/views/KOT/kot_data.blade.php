@extends('layouts.admin')
@section('content')

<!-- Breadcrumb Navigation -->
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{route('KOT')}}" class="cursor-pointer">
        <span class="text-gray-500 hover:text-primary transition-all duration-200">KOT</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
    <span class="text-foreground font-medium">KOT Data</span>
</div>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">KOT Management</h1>
        <p class="text-gray-500 text-sm md:text-base">Quickly view and manage your KOT.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 ml-auto md:ml-0">
        <!-- <button onclick="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Item</span>
        </button> -->
    </div>
</div>

<!-- Alerts -->
@if(Session::get('test') == 'restaurant_admin')
<div class="alertordermsg"></div>
<div id="alertSoundContainer"></div>
<div id="PrintalertSoundContainer"></div>
<div class="alertpaymentmsg"></div>
@endif
@if(Session::get('test') == 'restaurant_manager')
<div class="Manageralertmsg"></div>
<div id="alertSoundContainer_manager"></div>
@endif


<div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
    <div class="bg-white rounded-card p-4">
        <div class="md:items-center gap-3">
            <!-- Search Input -->
            <form action="{{ route('KOTData',['table_id' => $table_id]) }}" method="GET" id="reportForm">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <div class="form-group mb-4 flex-1">
                        <label class="block text-foreground text-sm font-medium mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" max="{{ now()->toDateString() }}"
                            class="px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary w-full">
                    </div>
                    <div class="form-group mb-4 flex-1">
                        <label class="block text-foreground text-sm font-medium mb-2">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" max="{{ now()->toDateString() }}"
                            class="px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary w-full">
                    </div>
                    <div class="mt-4 text-right flex gap-2">
                        <button type="submit" class="items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                            <span>Apply Filters</span>
                        </button>
                        <a href="{{ route('KOTData',['table_id' => $table_id]) }}">
                            <button type="button" class="items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                                <span>Reset</span>
                            </button>
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="lg:col-span-8 none-air-conditioner">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <div class="bg-white rounded-card p-5">
            <div id="tables-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3">
                <!-- Tables will be dynamically generated here -->
                @php
                $matchFound = false; // Flag to track if a match is found
                @endphp
                @foreach ($orderKOT as $index => $data)
                @php
                $matchFound = true; // Set the flag to true
                @endphp
                <a href="{{ route('KOTOrderData',['table_id' => $data->table_id,'order_id' => $data->order_id]) }}">
                    <div id="table_td" class="relative p-4 border-2 border-gray-200 rounded-card text-center transition-all duration-200">
                        <div class="w-3 h-3 rounded-full absolute top-2 right-2"></div>
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="table_icon" data-lucide="utensils" class="w-6 h-6 text-gray-600"></i>
                        </div>
                        <h4 class="text-foreground text-sm font-bold mb-1 table_name" style="text-transform:capitalize;">{{ $data->customer_code }}</h4>
                        <p class="text-white-500 text-xs mb-1 createTime"></p>
                    </div>
                </a>
                @endforeach

            </div>
            @if (!$matchFound)
            <p class="text-center pt-3 pb-3" style="background-color:yellow">You have not any orders yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection