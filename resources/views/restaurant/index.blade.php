@extends('layouts.admin')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    const routes = {
        store: "{{ route('restaurant.store') }}",
        update: "{{ route('restaurant.update', ':id') }}"
    };
</script>
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #28a745;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    input:read-only {
        background-color: #f5f5f5;
        color: #666666;
        border: 1px solid #cccccc;
        cursor: not-allowed;
    }
</style>


@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Restaurant Management</h1>
        <p class="text-gray-500 text-sm md:text-base">Manage and monitor your restaurant details.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 md:ml-auto md:ml-0">
        <?php if (Session::get('restaurant_id')) { ?>
        <?php } else { ?>
            <button onclick="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Restaurant</span>
            </button>
        <?php } ?>
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
<?php if (Session::get('restaurant_id')) { ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">

        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total Tables</h3>
            <div class="bg-white rounded-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-foreground text-4xl font-extrabold mb-1">{{ $TableCount ?? 0 }}</p>
                    </div>
                    <div class="w-16 h-16 bg-error-light rounded-icon flex items-center justify-center">
                        <i data-lucide="table" class="w-7 h-7 text-error-dark"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total Categories</h3>
            <div class="bg-white rounded-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-foreground text-4xl font-extrabold mb-1">{{ $CategoryCount ?? 0 }}</p>
                    </div>
                    <div class="w-16 h-16 bg-accent-teal rounded-icon flex items-center justify-center">
                        <i data-lucide="list" class="w-7 h-7 text-foreground"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total Items</h3>
            <div class="bg-white rounded-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-foreground text-4xl font-extrabold mb-1">{{ $ItemCount ?? 0 }}</p>
                    </div>
                    <div class="w-16 h-16 bg-accent-peach rounded-icon flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-7 h-7 text-foreground"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-muted rounded-card pt-5 px-3 pb-3">
            <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total Coupons</h3>
            <div class="bg-white rounded-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-foreground text-4xl font-extrabold mb-1">{{ $CouponCount ?? 0 }}</p>
                    </div>
                    <div class="w-16 h-16 bg-accent-lime rounded-icon flex items-center justify-center">
                        <i data-lucide="badge-percent" class="w-7 h-7 text-foreground"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

<?php } else { ?>
    <form method="GET" action="{{ route('restaurant.index') }}">
        <div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
            <div class="bg-white rounded-card p-4">
                <div class="flex flex-col md:flex-row md:items-center gap-3">

                    <div class="relative flex-1">
                        <i data-lucide="search"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>

                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search restaurants..."
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
<?php } ?>

<!-- Restaurant Table (Desktop) -->
<div class="hidden md:block">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
            <h3 class="text-foreground text-lg font-bold">Restaurant Locations</h3>
        </div>
        <div class="bg-white rounded-card overflow-hidden">
            <table id="dataTable" class="w-full table-fixed">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Restaurant</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Location</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Mobile No.</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">GST Status</th>
                        <th class="px-4 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($restaurants as $index => $restaurant)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ ('restaurant_logo/' . $restaurant->logo) }}" alt="restaurant image" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-foreground truncate" style="text-transform: capitalize;">{{ $restaurant->restaurant_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell"><span class="truncate block">{{ $restaurant->address }}</span></td>
                        <td class="px-4 py-4 whitespace-nowrap truncate">{{ $restaurant->email }}</td>
                        <td class="px-4 py-4">{{ $restaurant->phone }}</td>
                        <td class="px-4 py-4 hidden lg:table-cell"> <label class="switch">
                                <input type="checkbox" class="toggle-status" data-id="{{ $restaurant->id }}" {{ $restaurant->GST_status ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label></td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openEditModal({{ $restaurant }})" class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button hover:bg-primary-hover transition-all duration-200 cursor-pointer">Edit</button>
                                <form action="{{ route('restaurant.destroy',$restaurant->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    @if(Session::get('restaurant_id'))
                                    @else
                                    <button onclick="return confirm(' you want to delete?');" class="px-3 py-1.5 border border-border text-foreground text-xs font-medium rounded-button hover:border-primary hover:text-primary transition-all duration-200 cursor-pointer">Delete</button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if (!$matchFound)
                    <tr>
                        <td class="text-center pt-3 pb-3" colspan="6" style="background-color:yellow">Restaurant not found.</td>
                    </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mobile Cards -->
<div id="mobileCards" class="md:hidden space-y-3">
    @php
    $matchFound = false; // Flag to track if a match is found
    @endphp
    @foreach ($restaurants as $index => $restaurant)
    @php
    $matchFound = true; // Set the flag to true
    @endphp
    <div class="bg-white rounded-2xl p-4 border border-gray-100">
        <div class="flex items-center gap-3 mb-3 min-w-0">
            <img src="{{ ('restaurant_logo/' . $restaurant->logo) }}" alt="restaurant image" class="w-12 h-12 rounded-button object-cover flex-shrink-0">
            <div class="flex-1 min-w-0">
                <h4 class="text-foreground font-semibold truncate" style="text-transform: capitalize;">{{ $restaurant->restaurant_name }}</h4>
                <p class="text-gray-500 text-xs truncate">{{ $restaurant->address }}</p>
            </div>

            <label class="switch">
                <input type="checkbox" class="toggle-status" data-id="{{ $restaurant->id }}" {{ $restaurant->GST_status ? 'checked' : '' }}>
                <span class="slider"></span>
            </label>
        </div>


        <div class="flex gap-2 pt-3 border-t border-gray-100">
            <div class="flex items-center justify-end gap-2">
                <button onclick="openEditModal({{ $restaurant }})" class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button hover:bg-primary-hover transition-all duration-200 cursor-pointer">Edit</button>
                <form action="{{ route('restaurant.destroy',$restaurant->id) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    @if(Session::get('restaurant_id'))
                    @else
                    <button onclick="return confirm(' you want to delete?');" class="px-3 py-1.5 border border-border text-foreground text-xs font-medium rounded-button hover:border-primary hover:text-primary transition-all duration-200 cursor-pointer">Delete</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
    @endforeach
    @if (!$matchFound)
    <p class="text-center pt-3 pb-3" style="background-color:yellow">Restaurant not found.</p>
    @endif
</div>

<!-- Pagination -->
<?php if (Session::get('restaurant_id')) { ?>
<?php } else { ?>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 px-3 pb-4">
        <div class="text-sm text-gray-600"> Showing {{ $restaurants->firstItem() }} to {{ $restaurants->lastItem() }}
            of {{ $restaurants->total() }} entries</div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Previous --}}
            @if ($restaurants->onFirstPage())
            <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
                Previous
            </span>
            @else
            <a href="{{ $restaurants->previousPageUrl() }}"
                class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
                Previous
            </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($restaurants->getUrlRange(1, $restaurants->lastPage()) as $page => $url)
            @if ($page == $restaurants->currentPage())
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
            @if ($restaurants->hasMorePages())
            <a href="{{ $restaurants->nextPageUrl() }}"
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
<?php } ?>

<?php
$disabled = '';
if ($AdminExists == false) {
    $disabled = 'readonly';
}

?>
<div id="restaurantModal" class="custom-modal hidden">
    <div class="custom-modal-backdrop" onclick="closeModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Add New Restaurant</h2>
        <form id="restaurantForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod">
            <input type="hidden" name="restaurant_id" id="restaurant_id">

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Restaurant Name</label>
                <input type="text" id="name" name="restaurant_name" required placeholder="Enter Restaurant Name"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Restaurant Nav</label>
                <input type="text" {{ $disabled }} id="restaurant_nav" name="restaurant_nav" required placeholder="Restaurant name (no spaces or special characters)"
                    pattern="[A-Za-z0-9]+" class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Select Image File</label>
                <!-- <input type="file" class="form-control-file" name="logo" required> -->
                <div class="relative">
                    <div id="thumbnailPlaceholder"
                        class="border-2 border-dashed border-border rounded-button p-4 md:p-6 hover:border-primary transition-all duration-300 cursor-pointer"
                        onclick="document.getElementById('thumbnailUpload').click()">
                        <div class="text-center">
                            <i data-lucide="image" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                            <p class="text-sm text-gray-600 mb-2">Click to upload thumbnail</p>
                            <p class="text-xs text-gray-400">PNG, JPG up to 5MB</p>
                        </div>
                    </div>
                    <div id="thumbnailPreviewContainer" class="hidden">
                        <img id="thumbnailPreview" class="w-full rounded-button object-cover" alt="Thumbnail preview">
                        <button type="button" onclick="removeThumbnail()"
                            class="mt-3 w-full px-3 py-2 text-sm text-error hover:text-error-dark cursor-pointer">
                            <i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i>Remove
                        </button>
                    </div>
                    <input type="file" id="thumbnailUpload" name="logo" accept="image/*" class="hidden" required
                        onchange="previewThumbnail(event)">
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Owner Name</label>
                <input type="text" id="owner_name" name="owner_name" required placeholder="Enter Owner Name"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter Email Address"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
                <small id="email-error" style="color: red;"></small>
            </div>
            @if (Session::get('restaurant_id'))
            @else
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2" id="password_label">Password</label>

                <div class="flex input-group" id="show_hide_password">
                    <input id="password" type="password" placeholder="Enter Password"
                        class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary"
                        name="password" tabindex="2" required autocomplete="off">
                    <div class="input-group-append px-4 py-3" id="toggle_password" style="margin-left: -50px;">
                        <span class="input-group-text" style="cursor:pointer;">
                            <i class="fa fa-eye-slash" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
            @endif
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Mobile No.</label>
                <input type="number" id="phone" name="phone" required placeholder="Enter Phone Number" pattern="[1-9]{1}[0-9]{9}"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Address</label>
                <textarea id="address" name="address" required placeholder="Enter Address"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Latitude</label>
                <input type="number" step="any" id="latitude" name="latitude" required placeholder="Enter Restaurant Latitude" 
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Longitude</label>
                <input type="number" step="any" id="longitude" name="longitude" required placeholder="Enter Restaurant Longitude" 
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Order Radius (meters)</label>
                <input type="number" id="geofence_radius" name="geofence_radius" required placeholder="Enter Restaurant Order Radius" 
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">CGST</label>
                <input type="number" id="CGST" name="CGST" placeholder="Enter CGST" step="0.01"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">SGST</label>
                <input type="number" id="SGST" name="SGST" placeholder="Enter SGST" step="0.01"
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    function openAddModal() {
        $('#modalTitle').text('Add Restaurant');
        $('#modalSubmitBtn').text('Save').data('mode', 'add');

        $('#restaurantForm')[0].reset();
        $('#restaurant_id').val('');



        resetThumbnail();

        // 🔥 REQUIRED ONLY FOR ADD
        $('#thumbnailUpload').prop('required', true);

        openModal();
    }


    function openEditModal(data) {
        $('#modalTitle').text('Edit Restaurant');
        $('#modalSubmitBtn').text('Update').data('mode', 'edit');

        $('#restaurant_id').val(data.id);
        $('#name').val(data.restaurant_name);
        $('#restaurant_nav').val(data.restaurant_nav);
        $('#owner_name').val(data.owner_name);
        $('#email').val(data.email);
        $('#phone').val(data.phone);
        $('#address').val(data.address);
        $('#latitude').val(data.latitude);
        $('#longitude').val(data.longitude);
        $('#geofence_radius').val(data.geofence_radius);
        $('#CGST').val(data.CGST);
        $('#SGST').val(data.SGST);
        

        // $('#password').val(data.password);
        $('#password').removeAttr('required');


        // 🔥 SHOW EXISTING LOGO
        if (data.logo) {
            $('#thumbnailPreview').attr('src', 'restaurant_logo/' + data.logo);
            $('#thumbnailPlaceholder').addClass('hidden');
            $('#thumbnailPreviewContainer').removeClass('hidden');
        } else {
            resetThumbnail();
        }


        // 🔥 REMOVE REQUIRED IN EDIT MODE
        $('#thumbnailUpload').prop('required', false);

        openModal();
    }

    function resetThumbnail() {
        $('#thumbnailUpload').val('');
        $('#thumbnailPreview').attr('src', '');
        $('#thumbnailPlaceholder').removeClass('hidden');
        $('#thumbnailPreviewContainer').addClass('hidden');
    }



    function openModal() {
        document.getElementById("restaurantModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeModal() {
        document.getElementById("restaurantModal").classList.add("hidden");
        document.body.style.overflow = "";
    }


    // Thumbnail photo functions
    function previewThumbnail(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('thumbnailPreview').src = e.target.result;
                document.getElementById('thumbnailPlaceholder').classList.add('hidden');
                document.getElementById('thumbnailPreviewContainer').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function removeThumbnail() {
        document.getElementById('thumbnailUpload').value = '';
        document.getElementById('thumbnailPreview').src = '';
        document.getElementById('thumbnailPlaceholder').classList.remove('hidden');
        document.getElementById('thumbnailPreviewContainer').classList.add('hidden');
        lucide.createIcons();
    }

    $('#restaurantForm').on('submit', function(e) {
        e.preventDefault();

        const mode = $('#modalSubmitBtn').data('mode');
        const id = $('#restaurant_id').val();

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

    $(document).ready(function() {
        // Validate email on input for both Add and Update forms
        $("#email").on("input", function() {
            let email = $(this).val();
            let errorMessage = $("#email-error");

            if (email.length > 0) {
                $.ajax({
                    url: "{{ route('check.email') }}",
                    method: "POST",
                    data: {
                        email: email,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.exists) {
                            errorMessage.text("This email is already registered.");
                            errorMessage.show(); // Ensure the message is visible
                        } else {
                            errorMessage.text("");
                            errorMessage.hide(); // Hide if no error
                        }
                    }
                });
            } else {
                errorMessage.text("");
                errorMessage.hide();
            }
        });

        // Prevent form submission if email already exists for Add and Update forms
        $("#FormAdd, #FormUpdate").submit(function(e) {
            if ($.trim($("#email-error").text()) !== "") {
                e.preventDefault(); // Stop form submission
            }
        });
    });

    $(document).on('change', '.toggle-status', function() {
        var status = $(this).is(':checked') ? 1 : 0;
        var restaurant_id = $(this).data('id');

        $.ajax({
            url: "{{ route('restaurant.update-gst-status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: restaurant_id,
                status: status
            },
            success: function(response) {
                alert(response.message);
            },
            error: function() {
                alert("Something went wrong!");
            }
        });
    });

    $(document).ready(function() {
        var $passwordInput = $('#password');
        var $toggleBtn = $('#toggle_password');
        var $icon = $('#toggle_password i');

        // Show/hide eye icon based on input
        $passwordInput.on('input', function() {
            if ($(this).val().length > 0) {
                // $toggleBtn.removeClass('d-none');
            } else {
                // $toggleBtn.addClass('d-none');
                $passwordInput.attr('type', 'password');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        // Toggle password visibility
        $toggleBtn.on('click', function(e) {
            e.preventDefault();
            if ($passwordInput.attr("type") === "password") {
                $passwordInput.attr("type", "text");
                $icon.removeClass("fa-eye-slash").addClass("fa-eye");
            } else {
                $passwordInput.attr("type", "password");
                $icon.removeClass("fa-eye").addClass("fa-eye-slash");
            }
        });
    });
</script>

@endsection