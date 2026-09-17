@extends('layouts.admin')

@section('content')
<!-- Breadcrumb Navigation -->
<div class="flex items-center gap-2 text-sm mb-6">
    <a href="{{route('Invoice')}}" class="cursor-pointer">
        <span class="text-gray-500 hover:text-primary transition-all duration-200">Invoice</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
    <span class="text-foreground font-medium">Invoice Details</span>
</div>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Invoice Management</h1>
        <p class="text-gray-500 text-sm md:text-base">Quickly view and manage your billing and invoices.</p>
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
<div class="alertTakeAwayordermsg"></div>
<div id="alertTakeAwaySound"></div>
<div class="alertreadytoservedmsg"></div>
<div id="ReadyToservedalertSoundContainer"></div>
<div class="Waiteralertordermsg"></div>
<div id="alertSoundContainer_waiter"></div>
<div class="alertreadytoservedWaitermsg"></div>
<div id="ReadyToservedWaiteralertSoundContainer"></div>
@endif
@if(Session::get('test') == 'restaurant_manager')
<div class="Manageralertmsg"></div>
<div id="alertSoundContainer_manager"></div>
<div class="alertreadytoservedmsg"></div>
<div id="ReadyToservedalertSoundContainer"></div>
@endif


<div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
    <div class="bg-white rounded-card p-4">
        <div class="md:items-center gap-3">
            <!-- Search Input -->
            <form action="{{ route('InvoiceData',['table_id' => $table_id]) }}" method="GET" id="reportForm">
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
                        <a href="{{ route('InvoiceData',['table_id' => $table_id]) }}">
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


<!-- Invoice Table -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">All Invoices</h3>
    </div>
    <div class="bg-white rounded-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Customer ID</th>
                        <!-- <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Order Confirm By</th> -->
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Created At</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($invoiceData as $data)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50 ${rowClass}">
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-medium">{{ $data->customer_code }}</span>
                        </td>
                        <!-- <td class="py-4 px-2">
                            <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $data->order_confirm_by }}</p>
                        </td> -->
                        <td class="py-4 px-2">
                            <p class="text-foreground text-sm font-medium">{{ $data->created_at }}</p>
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-2">
                                <a href="{{ asset('invoices/invoice_' . $data->invoice_url . '.pdf') }}" target="_blank"><button class="text-primary hover:text-primary-hover text-sm font-medium cursor-pointer">View Invoice</button></a>
                                <button data-order-id="{{  $data->order_id }}" id="PrintInvoice" class="print-invoice-btn px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer">
                                    Print Invoice
                                </button>
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
    <div class="text-sm text-gray-600"> Showing {{ $invoiceData->firstItem() }} to {{ $invoiceData->lastItem() }}
        of {{ $invoiceData->total() }} entries</div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Previous --}}
        @if ($invoiceData->onFirstPage())
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Previous
        </span>
        @else
        <a href="{{ $invoiceData->previousPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Previous
        </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($invoiceData->getUrlRange(1, $invoiceData->lastPage()) as $page => $url)
        @if ($page == $invoiceData->currentPage())
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
        @if ($invoiceData->hasMorePages())
        <a href="{{ $invoiceData->nextPageUrl() }}"
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

<script>
    $(document).on('click', '.print-invoice-btn', function() {

        const orderId = $(this).data('order-id');

        console.log('Removing print alert for order:', orderId);

        // ✅ Open invoice
        const url = "{{ url('/print-invoice-order') }}/" + orderId;
        window.open(url, "_blank");
    });
</script>
@endsection