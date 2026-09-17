@extends('layouts.admin')
@section('content')

<!-- Breadcrumb Navigation -->
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{route('KOT')}}" class="cursor-pointer">
        <span class="text-gray-500 hover:text-primary transition-all duration-200">KOT</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
    <a href="{{route('KOTData',['table_id' => $table_id])}}" class="cursor-pointer">
        <span class="text-gray-500 hover:text-primary transition-all duration-200">KOT Data</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
    <span class="text-foreground font-medium">KOT Details</span>
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


<!-- KOT Table -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">All KOT</h3>
    </div>
    <div class="bg-white rounded-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Customer ID</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Confirm By</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Created At</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($KOTorderData as $data)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50 ${rowClass}">
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-medium">{{ $data->customer_code }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $data->order_confirm_by }}</p>
                        </td>
                        <td class="py-4 px-2">
                            <p class="text-foreground text-sm font-medium">{{ $data->created_at }}</p>
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-2">
                                <a href="{{ asset('KOT/' . $data->url . '.pdf') }}" target="_blank"><button class="text-primary hover:text-primary-hover text-sm font-medium cursor-pointer">View KOT</button></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if (!$matchFound)
                    <tr>
                        <td class="text-center pt-3 pb-3" colspan="4" style="background-color:yellow">You have not any orders yet.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Pagination -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 px-3 pb-4">
    <div class="text-sm text-gray-600"> Showing {{ $KOTorderData->firstItem() }} to {{ $KOTorderData->lastItem() }}
        of {{ $KOTorderData->total() }} entries</div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Previous --}}
        @if ($KOTorderData->onFirstPage())
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Previous
        </span>
        @else
        <a href="{{ $KOTorderData->previousPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Previous
        </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($KOTorderData->getUrlRange(1, $KOTorderData->lastPage()) as $page => $url)
        @if ($page == $KOTorderData->currentPage())
        <span class="px-3 py-2 bg-primary text-white rounded-lg text-sm">
            {{ $page }}
        </span>
        @else
        <a href="{{ $url }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            {{ $page }}
        </a>
        @endif
        @endforeach

        {{-- Next --}}
        @if ($KOTorderData->hasMorePages())
        <a href="{{ $KOTorderData->nextPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Next
        </a>
        @else
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Next
        </span>
        @endif
    </div>

</div>
@endsection