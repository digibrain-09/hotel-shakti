@extends('layouts.admin')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    const routes = {
        store: "{{ route('add_on.store') }}",
        update: "{{ route('add_on.update', ':id') }}"
    };
</script>

@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Items Add-ons</h1>
        <p class="text-gray-500 text-sm md:text-base">Create, update, and manage items add-ons.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 md:ml-auto md:ml-0">
        <button onclick="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add</span>
        </button>
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


<!-- Success Alert -->
<div id="success-alert" style="display:none; padding:15px; background:#d4edda; color:#155724; border-radius:4px; margin-bottom:15px;">
    <h4 id="success-message"></h4>
</div>
@if (session('success'))
<div id="success-alert" style="padding: 15px; background-color: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 15px;">
    <h4>{{ session('success') }}</h4>
</div>

<script>
    // Hide alert after 4 seconds
    setTimeout(function() {
        let alertBox = document.getElementById('success-alert');
        if (alertBox) {
            alertBox.style.transition = "opacity 0.5s ease-out";
            alertBox.style.opacity = 0;
            setTimeout(() => alertBox.style.display = 'none', 500); // Remove it after fade out
        }
    }, 2000); // 4000 ms = 4 seconds
</script>
@endif


<!-- Search & Filter Bar -->
<form method="GET" action="{{ route('add_on.index') }}">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
        <div class="bg-white rounded-card p-4">
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                
                <div class="relative flex-1">
                    <i data-lucide="search"
                       class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search..."
                           class="w-full pl-10 pr-4 py-2.5 border border-border rounded-button text-sm focus:outline-none focus:border-primary transition-all duration-200">
                </div>

                <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-button">
                    Search
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Items Table -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">All Add-ons</h3>
    </div>
    <div class="bg-white rounded-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Name</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Price</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Type</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($add_ons as $index => $data)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <p class="font-medium text-foreground truncate" style="text-transform: capitalize;">{{ $data->name }}</p>
                        </td>

                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">₹{{ number_format($data->price, 2) }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <div>
                                <!-- <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $data->type }}</p> -->
                                <span class="text-gray-600 text-sm" style="text-transform: capitalize;">{{ $data->type }}</span>
                            </div>
                        </td>


                        <td class="py-4 px-2">
                            <div class="flex items-center gap-2">
                                <button
                                    onclick='openEditModal(@json($data))'
                                    class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer">
                                    Edit
                                </button>

                                <form action="{{ route('add_on.destroy',$data->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button onclick="return confirm(' you want to delete?');" class="px-3 py-1.5 border border-border text-foreground text-xs font-medium rounded-button hover:border-primary hover:text-primary transition-all duration-200 cursor-pointer">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if (!$matchFound)
                    <tr>
                        <td class="text-center pt-3 pb-3" colspan="4" style="background-color:yellow">Add-on not found.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 px-3 pb-4">
    <div class="text-sm text-gray-600"> Showing {{ $add_ons->firstItem() }} to {{ $add_ons->lastItem() }}
        of {{ $add_ons->total() }} entries</div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Previous --}}
        @if ($add_ons->onFirstPage())
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Previous
        </span>
        @else
        <a href="{{ $add_ons->previousPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Previous
        </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($add_ons->getUrlRange(1, $add_ons->lastPage()) as $page => $url)
        @if ($page == $add_ons->currentPage())
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
        @if ($add_ons->hasMorePages())
        <a href="{{ $add_ons->nextPageUrl() }}"
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

<!-- AddOn Modal -->
<div id="AddOnModal" class="custom-modal hidden">
    <div class="custom-modal-backdrop" onclick="closeModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Add new Add On Item</h2>
        <form id="AddOnForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod">
            <input type="hidden" name="addon_id" id="addon_id">

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter Name"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>


            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Price</label>
                <input type="number" id="price" name="price" required placeholder="Enter Price"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Select Type</label>
                <select
                    name="type" id="type"
                    class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary" required>
                    <option value="Variation">Variation</option>
                    <option value="Veg-toppings">Veg-toppings</option>
                    <option value="Extra">Extra</option>
                </select>
            </div>



            <div class="mt-4 text-right">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded mr-1 cursor-pointer">
                    Cancel
                </button>
                <button id="modalSubmitBtn" type="submit" name="submit" class="px-4 py-2 bg-primary text-white border rounded cursor-pointer">
                    Submit
                </button>
            </div>

        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    function openAddModal() {
        $('#modalTitle').text('Add new Add On Item');
        $('#modalSubmitBtn').text('Save').data('mode', 'add');

        $('#AddOnForm')[0].reset();
        $('#addon_id').val('');

        openModal();
    }


    function openEditModal(data) {
        $('#modalTitle').text('Edit Add On Item');
        $('#modalSubmitBtn').text('Update').data('mode', 'edit');

        $('#addon_id').val(data.id);
        $('#name').val(data.name);
        $('#price').val(data.price);


        // ✅ SET SELECT DROPDOWN VALUE
        $('#type').val(data.type).trigger('change');

        openModal();
    }



    function openModal() {
        document.getElementById("AddOnModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeModal() {
        document.getElementById("AddOnModal").classList.add("hidden");
        document.body.style.overflow = "";
    }

    $('#AddOnForm').on('submit', function(e) {
        e.preventDefault();

        const mode = $('#modalSubmitBtn').data('mode');
        const id = $('#addon_id').val();

        let url = routes.store;
        let method = 'POST';

        if (mode === 'edit') {
            url = routes.update.replace(':id', id);
            method = 'POST'; // Laravel accepts POST + _method
        }

        let formData = new FormData(this);

        if (mode === 'edit') {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                closeModal();

                if (res.success) {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'slow');
                    $('#success-message').text(res.message);
                    $('#success-alert').fadeIn();

                    setTimeout(function() {
                        $('#success-alert').fadeOut();
                    }, 2000);
                }

                // Optional reload after showing message
                setTimeout(() => location.reload(), 2000);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    console.log(errors);
                } else {
                    alert('Something went wrong');
                }
            }
        });
    });
</script>
@endsection