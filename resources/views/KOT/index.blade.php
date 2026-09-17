@extends('layouts.admin')
@section('content')


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



<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
    <!-- Left Column - Restaurant Info & Floor Selection -->
    <div class="lg:col-span-4 space-y-4 md:space-y-6">


        <!-- Types -->
        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Types</h3>
            <div class="bg-white rounded-card p-5">
                <div class="space-y-2">
                    <button id="type-none-air-conditioner" onclick="switchFloor('none-air-conditioner')"
                        class="w-full flex items-center justify-between p-4 bg-primary text-white rounded-card font-medium transition-all duration-200 cursor-pointer">

                        <div class="flex items-center gap-3">
                            <!-- <i data-lucide="building-2" class="w-5 h-5"></i> -->
                            <span>Non-Air Conditioner</span>
                        </div>

                    </button>
                    <button id="type-air-conditioner" onclick="switchFloor('air-conditioner')"
                        class="w-full flex items-center justify-between p-4 bg-gray-100 text-foreground rounded-card font-medium hover:bg-gray-200 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <!-- <i data-lucide="building" class="w-5 h-5"></i> -->
                            <span>Air Conditioner</span>
                        </div>

                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Non-Air Conditioner -->
    <div class="lg:col-span-8 none-air-conditioner">
        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
                <h3 id="floor-title" class="text-foreground text-lg font-bold">Tables - Non-Air Conditioner</h3>
            </div>
            <div class="bg-white rounded-card p-5">
                <div id="tables-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3">
                    <!-- Tables will be dynamically generated here -->
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($tables as $index => $table)
                    @if($table->seating_type == false)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <a href="{{ route('KOTData',['table_id' => $table->id]) }}">
                        <div id="table_td" class="relative p-4 border-2 border-gray-200 rounded-card text-center transition-all duration-200  @if($table->status == 'completed' || $table->count == 0)
                            {{ 'td-text-green' }} 
                            @endif">
                            <div class="w-3 h-3 ${statusColor} rounded-full absolute top-2 right-2"></div>
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="table_icon" data-lucide="utensils" class="w-6 h-6 text-gray-600"></i>
                            </div>
                            <h4 class="text-foreground text-sm font-bold mb-1 table_name" data-table-name="{{ $table->table_name }}" data-table-id="{{ $table->id }}" style="text-transform:capitalize;">{{ $table->table_name }}</h4>
                            <p class="text-white-500 text-xs mb-1 createTime"></p>
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


    <!-- Air Conditioner -->
    <div class="lg:col-span-8 air-conditioner hidden">
        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
                <h3 id="floor-title" class="text-foreground text-lg font-bold">Tables - Air Conditioner</h3>
            </div>
            <div class="bg-white rounded-card p-5">
                <div id="tables-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3">
                    <!-- Tables will be dynamically generated here -->
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($tables as $index => $table)
                    @if($table->seating_type == true)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <a href="{{ route('KOTData',['table_id' => $table->id]) }}">
                        <div id="table_td" class="relative p-4 border-2 border-gray-200 rounded-card text-center transition-all duration-200 @if($table->status == 'completed' || $table->count == 0)
                            {{ 'td-text-green' }} 
                            @endif">
                            <div class="w-3 h-3 ${statusColor} rounded-full absolute top-2 right-2"></div>
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="table_icon" data-lucide="utensils" class="w-6 h-6 text-gray-600"></i>
                            </div>
                            <h4 class="text-foreground text-sm font-bold mb-1 table_name" data-table-name="{{ $table->table_name }}" data-table-id="{{ $table->id }}" style="text-transform:capitalize;">{{ $table->table_name }}</h4>
                            <p class="text-gray-500 text-xs mb-1 createTime"></p>
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
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    function switchFloor(Type) {
        // Update floor button styles
        document.querySelectorAll('button[id^="type-"]').forEach(btn => {
            if (btn.id === `type-${Type}`) {
                $('.none-air-conditioner').addClass('hidden');
                $('.air-conditioner').removeClass('hidden');
                btn.className = 'w-full flex items-center justify-between p-4 bg-primary text-white rounded-card font-medium transition-all duration-200 cursor-pointer';
            } else {
                $('.none-air-conditioner').removeClass('hidden');
                $('.air-conditioner').addClass('hidden');
                btn.className = 'w-full flex items-center justify-between p-4 bg-gray-100 text-foreground rounded-card font-medium hover:bg-gray-200 transition-all duration-200 cursor-pointer';
            }
        });
    }
</script>
@endsection