@extends('layouts.admin')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    const routes = {
        store: "{{ route('coupons.store') }}",
        update: "{{ route('coupons.update', ':id') }}"
    };
</script>
<style>
    .categoryId,
    .start_time,
    .end_time,
    .applicable_days {
        display: none;
    }
</style>

@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Restaurant Coupons</h1>
        <p class="text-gray-500 text-sm md:text-base">Manage and monitor your restaurant coupons with ease.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 md:ml-auto md:ml-0">
        <button onclick="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Coupon</span>
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
<form method="GET" action="{{ route('coupons.index') }}">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
        <div class="bg-white rounded-card p-4">
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                
                <div class="relative flex-1">
                    <i data-lucide="search"
                       class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search Coupon..."
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



<!-- Coupon Table -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">All Coupons</h3>
    </div>
    <div class="bg-white rounded-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">CouponCode</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Type</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Value</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Expires At</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($coupons as $index => $data)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-foreground truncate" style="text-transform: capitalize;">{{ $data->code  }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <div>
                                <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $data->type }}</p>
                            </div>
                        </td>

                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">{{ $data->value }}</span><br>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-gray-600 text-sm" style="text-transform: capitalize;">{{ $data->expires_at }}</span>
                        </td>


                        <td class="py-4 px-2">
                            <div class="flex items-center gap-2">
                                <button
                                    onclick='openEditModal(@json($data))'
                                    data-id="<?php echo $data->id ?>" data-type="<?php echo $data->type ?>" data-days="{{ $data->applicable_days}}"
                                    class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer editcoupons">
                                    Edit
                                </button>

                                <form action="{{ route('coupons.destroy',$data->id) }}" method="POST">
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
                        <td class="text-center pt-3 pb-3" colspan="5" style="background-color:yellow">Coupon not found.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Pagination -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 px-3 pb-4">
    <div class="text-sm text-gray-600"> Showing {{ $coupons->firstItem() }} to {{ $coupons->lastItem() }}
        of {{ $coupons->total() }} entries</div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Previous --}}
        @if ($coupons->onFirstPage())
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Previous
        </span>
        @else
        <a href="{{ $coupons->previousPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Previous
        </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($coupons->getUrlRange(1, $coupons->lastPage()) as $page => $url)
        @if ($page == $coupons->currentPage())
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
        @if ($coupons->hasMorePages())
        <a href="{{ $coupons->nextPageUrl() }}"
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


<!-- Coupon Modal -->
<div id="CouponModal" class="custom-modal hidden">
    <div class="custom-modal-backdrop" onclick="closeModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Add new Coupon</h2>
        <form id="CouponForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod">
            <input type="hidden" name="coupon_id" id="coupon_id">

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Coupon Code</label>
                <input type="text" id="code" name="code" required style="text-transform: uppercase;"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
                <small class="text-danger" style="color:red" id="code_error"></small>
            </div>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Discount Type</label>
                <select
                    name="type" id="type"
                    class="w-full px-4 py-3 border border-border rounded-button type
                   focus:outline-none focus:ring-2 focus:ring-primary" required>
                    <option value="">Select Type</option>
                    <option value="Flat Amount Discount" {{ old('type') == 'Flat Amount Discount' ? 'selected' : '' }}>Flat Amount Discount</option>
                    <option value="Percentage Discount" {{ old('type') == 'Percentage Discount' ? 'selected' : '' }}>Percentage Discount</option>
                    <option value="First Order Discount" {{ old('type') == 'First Order Discount' ? 'selected' : '' }}>First Order</option>
                    <option value="Category-Specific Discount" {{ old('type') == 'Category-Specific Discount' ? 'selected' : '' }}>Category-Specific</option>
                    <option value="Time-Based Discount" {{ old('type') == 'Time-Based Discount' ? 'selected' : '' }}>Time-Based</option>
                    <option value="Weekend Discount" {{ old('type') == 'Weekend Discount' ? 'selected' : '' }}>Weekend Discount</option>
                    <option value="Weekday Discount" {{ old('type') == 'Weekday Discount' ? 'selected' : '' }}>Weekday Discount</option>
                </select>
            </div>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Select Discount Mode</label>
                <select
                    name="discount_mode" id="discount_mode"
                    class="w-full px-4 py-3 border border-border rounded-button discount_mode
                   focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="percent" {{ old('discount_mode') == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="fixed" {{ old('discount_mode') == 'fixed' ? 'selected' : '' }}>Flat Amount (₹)</option>
                </select>
            </div>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Discount Value</label>
                <input type="number" id="value" name="value" required placeholder="Enter Value" step="0.01"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Min order Price</label>
                <input type="number" id="min_order" name="min_order" placeholder="Enter Price" step="0.01"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="form-group mb-4 categoryId">
                <label class="block text-foreground text-sm font-medium mb-2">Select Category</label>
                <select
                    name="categoryId" id="categoryId"
                    class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="0" selected>Category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{$category->category_name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-4 start_time">
                <label class="block text-foreground text-sm font-medium mb-2">Select Start time</label>
                <input type="time" id="start_time" name="start_time" step="0.01"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="form-group mb-4 end_time">
                <label class="block text-foreground text-sm font-medium mb-2">Select End time</label>
                <input type="time" id="end_time" name="end_time" step="0.01"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="space-y-3 mb-4 applicable_days">
                <p class="text-foreground text-sm font-semibold">Select Applicable Days</p>
                <div class="grid grid-cols-4 gap-3">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="applicable_days[]" value="monday"
                            class="w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                        <span class="text-foreground text-sm group-hover:text-primary transition-colors">Monday </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="applicable_days[]" value="tuesday"
                            class="w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                        <span class="text-foreground text-sm group-hover:text-primary transition-colors">Tuesday </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="applicable_days[]" value="wednesday"
                            class="w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                        <span class="text-foreground text-sm group-hover:text-primary transition-colors">Wednesday </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="applicable_days[]" value="thursday"
                            class="w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                        <span class="text-foreground text-sm group-hover:text-primary transition-colors">Thursday </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="applicable_days[]" value="friday"
                            class="w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                        <span class="text-foreground text-sm group-hover:text-primary transition-colors">Friday </span>
                    </label>


                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="applicable_days[]" value="saturday"
                            class="w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                        <span class="text-foreground text-sm group-hover:text-primary transition-colors">Saturday </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="applicable_days[]" value="sunday"
                            class="w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                        <span class="text-foreground text-sm group-hover:text-primary transition-colors">Sunday </span>
                    </label>

                </div>
            </div>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Start Date</label>
                <input type="date" id="start_at" name="start_at" required
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Expiry Date</label>
                <input type="date" id="expires_at" name="expires_at" required
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function openAddModal() {
        $('#modalTitle').text('Add new Coupon');
        $('#modalSubmitBtn').text('Save').data('mode', 'add');

        $('#CouponForm')[0].reset();
        $('#coupon_id').val('');

        openModal();
    }


    function openEditModal(data) {
        $('#modalTitle').text('Edit Coupon');
        $('#modalSubmitBtn').text('Update').data('mode', 'edit');

        $('#coupon_id').val(data.id);
        $('#code').val(data.code);
        $('#value').val(data.value);
        $('#min_order').val(data.min_order);
        $('#start_time').val(data.start_time);
        $('#end_time').val(data.end_time);
        $('#start_at').val(data.start_at);
        $('#expires_at').val(data.expires_at);


        // ✅ SET SELECT DROPDOWN VALUE
        $('#type').val(data.type).trigger('change');
        $('#discount_mode').val(data.discount_mode).trigger('change');
        $('#categoryId').val(data.categoryId).trigger('change');
        $('#applicable_days').val(data.applicable_days).trigger('change');

        openModal();
    }



    function openModal() {
        document.getElementById("CouponModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeModal() {
        document.getElementById("CouponModal").classList.add("hidden");
        document.body.style.overflow = "";

        $('.categoryId').hide();
        $('.start_time').hide();
        $('.end_time').hide();
        $('.applicable_days').hide();

    }


    $('#CouponForm').on('submit', function(e) {
        e.preventDefault();

        const mode = $('#modalSubmitBtn').data('mode');
        const id = $('#coupon_id').val();

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



    $(document).on('change', '.type', function() {
        currentType = $(this).val();

        if (currentType === 'Flat Amount Discount') {
            $('select[name="discount_mode"]').val('fixed').prop('disabled', true);
        } else if (currentType === 'Percentage Discount') {
            $('select[name="discount_mode"]').val('percent').prop('disabled', true);
        } else {
            $('select[name="discount_mode"]').prop('disabled', false);
        }

        if (currentType === 'Category-Specific Discount' || currentType === 'Weekend Discount' || currentType === 'Weekday Discount') {
            $('.categoryId').show();
        } else {
            $('.categoryId').hide();
        }

        if (currentType == 'Time-Based Discount') {
            $('.start_time').show();
            $('.end_time').show();
        } else {
            $('.start_time').hide();
            $('.end_time').hide();
        }

        const daysCheckbox = $('input[name="applicable_days[]"]');
        // hide initially
        if (currentType === 'Weekend Discount' || currentType === 'Weekday Discount') {

            $('.applicable_days').show();
        } else {
            $('.applicable_days').hide();
            daysCheckbox.prop('checked', false); // clear when hidden
        }

        // Auto select WEEKEND
        if (currentType === 'Weekend Discount') {
            daysCheckbox.prop('checked', false); // clear all
            daysCheckbox.prop('disabled', false);

            $('input[value="saturday"]').prop('checked', true);
            $('input[value="sunday"]').prop('checked', true);

            $('input[value="monday"]').prop('disabled', true);
            $('input[value="tuesday"]').prop('disabled', true);
            $('input[value="wednesday"]').prop('disabled', true);
            $('input[value="thursday"]').prop('disabled', true);
            $('input[value="friday"]').prop('disabled', true);
        }

        // Auto select WEEKDAYS
        if (currentType === 'Weekday Discount') {
            daysCheckbox.prop('checked', false); // clear all
            daysCheckbox.prop('disabled', false);

            $('input[value="monday"]').prop('checked', true);
            $('input[value="tuesday"]').prop('checked', true);
            $('input[value="wednesday"]').prop('checked', true);
            $('input[value="thursday"]').prop('checked', true);
            $('input[value="friday"]').prop('checked', true);

            $('input[value="saturday"]').prop('disabled', true);
            $('input[value="sunday"]').prop('disabled', true);
        }

    });

    $(document).on('change', 'input[name="applicable_days[]"]', function() {

        let $group = $(this).closest('.applicable_days');
        let $checked = $group.find('input[name="applicable_days[]"]:checked');

        if ($checked.length === 0) {
            $(this).prop('checked', true);
            alert('At least 1 day is required');
        }
    });


    // Function to format the date as YYYY-MM-DD
    function getFormattedDate(date) {
        const year = date.getFullYear();
        // getMonth() is 0-indexed, so add 1
        let month = (date.getMonth() + 1).toString().padStart(2, '0');
        let day = date.getDate().toString().padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    // Set the value on page load
    window.addEventListener('DOMContentLoaded', (event) => {
        const dateInput = document.getElementById("start_at");
        // Assign the formatted date string
        dateInput.value = getFormattedDate(new Date());
    })

    let codeExists = false;

    $(document).on('input', '#code', function() {
        let code = $(this).val().trim();
        $('#code_error').text('');

        if (code === '') return;

        $.ajax({
            url: "{{ route('coupons.checkCode') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                code: code
            },
            success: function(res) {
                if (res.exists) {
                    codeExists = true;
                    $('#code_error').text('This coupon code already exists');
                } else {
                    codeExists = false;
                }
            }
        });
    });


    $(document).on('click', '.editcoupons', function() {
        currentType = $(this).data('type');
        const savedDays = $(this).data('days'); // array from DB

        if (currentType === 'Flat Amount Discount') {
            $('select[name="discount_mode"]').val('fixed').prop('disabled', true);
        } else if (currentType === 'Percentage Discount') {
            $('select[name="discount_mode"]').val('percent').prop('disabled', true);
        } else {
            $('select[name="discount_mode"]').prop('disabled', false);
        }

        if (currentType === 'Category-Specific Discount') {
            $('.categoryId').show();
        } else if (currentType === 'Weekend Discount' || currentType === 'Weekday Discount') {
            $('.applicable_days').show();
            $('.categoryId').show();
        } else {
            $('.applicable_days').hide();
            $('.categoryId').hide();
        }

        if (currentType == 'Time-Based Discount') {
            $('.start_time').show();
            $('.end_time').show();
        } else {
            $('.start_time').hide();
            $('.end_time').hide();
        }


        // 1️⃣ Always clear before editing
        $('input[name="applicable_days[]"]').prop('checked', false);

        // 2️⃣ If DB has days, check based on DB
        if (Array.isArray(savedDays)) {
            savedDays.forEach(d => {
                $(`input[name="applicable_days[]"][value="${d}"]`).prop('checked', true);
            });
        }

        // Auto select WEEKEND
        if (currentType === 'Weekend Discount') {

            $('input[name="applicable_days[]"]').prop('disabled', false);

            $('input[value="monday"]').prop('disabled', true);
            $('input[value="tuesday"]').prop('disabled', true);
            $('input[value="wednesday"]').prop('disabled', true);
            $('input[value="thursday"]').prop('disabled', true);
            $('input[value="friday"]').prop('disabled', true);
        }

        // Auto select WEEKDAYS
        if (currentType === 'Weekday Discount') {
            $('input[name="applicable_days[]"]').prop('disabled', false);

            $('input[value="saturday"]').prop('disabled', true);
            $('input[value="sunday"]').prop('disabled', true);
        }

    });
</script>
@endsection