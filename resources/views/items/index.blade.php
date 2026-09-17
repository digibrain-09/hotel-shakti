@extends('layouts.admin')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    const routes = {
        store: "{{ route('item.store') }}",
        update: "{{ route('item.update', ':id') }}"
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
</style>

@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Restaurant Items</h1>
        <p class="text-gray-500 text-sm md:text-base">Manage and monitor your restaurant items with ease.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 md:ml-auto md:ml-0">
        <button onclick="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Item</span>
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
@if(Session::get('test') == 'kitchen_owner')
<div class="Kitchenalertmsg"></div>
<div id="KitchenalertSoundContainer"></div>
<div class="KitchenTakeAwayalertmsg"></div>
<div id="KitchenTakeAwayalertSoundContainer"></div>
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
<form method="GET" action="{{ route('item.index') }}">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
        <div class="bg-white rounded-card p-4">
            <div class="flex flex-col md:flex-row md:items-center gap-3">

                <div class="relative flex-1">
                    <i data-lucide="search"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>

                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search Item..."
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
        <h3 class="text-foreground text-lg font-bold">All Items</h3>
    </div>
    <div class="bg-white rounded-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Item</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Category Name</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Restaurant Name</th>
                        <!-- <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Air Condition Price</th> -->
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Price</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Display In Menu</th>
                        <!-- <th class="text-left py-3 px-2 text-foreground text-sm font-semibold w-[200px]">Display In AC & Non-AC</th> -->
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold w-[220px]">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($items as $index => $item)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ ('items/' . $item->picture) }}" alt="Item Image" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                <div class="min-w-0 flex-1">
                                    @if ($item->food_type == 1)
                                    <img src="{{ url('restaurant_logo/Non_veg_symbol.png') }}" aria-label="Non_veg_symbol" height="15px" width="15px" alt="">
                                    @else
                                    <img src="{{ url('restaurant_logo/Veg_symbol.png') }}" aria-label="Veg_symbol" height="15px" width="15px" alt="">
                                    @endif
                                    <p class="font-medium text-foreground truncate" style="text-transform: capitalize;">{{ $item->item_name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $item->description }}</p>
                                    <!-- <p class="text-gray-500 text-xs mt-3"><b>Display In AC & Non-AC</b></p>
                                    <label class="switch2 mr-3">
                                        <input type="checkbox" class="toggle-status2" data-id="{{ $item->id }}" data-mode="display_in_ac" {{ $item->display_in_ac ? 'checked' : '' }}>
                                        <span class="slider2 text-sm text-gray-500">AC</span>
                                    </label>
                                    <label class="switch2">
                                        <input type="checkbox" class="toggle-status2" data-id="{{ $item->id }}" data-mode="display_in_non_ac" {{ $item->display_in_non_ac ? 'checked' : '' }}>
                                        <span class="slider2 text-sm text-gray-500">Non-AC</span>
                                    </label> -->
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <div>
                                <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $item->category_name }}</p>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <div>
                                <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $item->restaurant_name }}</p>
                            </div>
                        </td>
                        <!-- <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">₹{{ number_format($item->ac_price, 2) }}</span><br>
                        </td> -->
                        <td class="py-4 px-2">
                            <span class="text-foreground text-sm font-semibold">₹{{ number_format($item->price, 2) }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <label class="switch">
                                <input type="checkbox" class="toggle-status" data-id="{{ $item->id }}" {{ $item->status ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </td>
                        <!-- <td class="py-4 px-2">
                            <label class="switch2">
                                <input type="checkbox" class="toggle-status2" data-id="{{ $item->id }}" data-mode="display_in_ac" {{ $item->display_in_ac ? 'checked' : '' }}>
                                <span class="slider2 text-sm">AC</span>
                            </label><br>
                            <label class="switch2">
                                <input type="checkbox" class="toggle-status2" data-id="{{ $item->id }}" data-mode="display_in_non_ac" {{ $item->display_in_non_ac ? 'checked' : '' }}>
                                <span class="slider2 text-sm">Non-AC</span>
                            </label>
                        </td> -->


                        <td class="py-4 px-2">
                            <div class="flex items-center gap-2">
                                <button
                                    onclick='openEditModal(@json([
                                        "item" => $item,
                                         "addons" => $selected_addons
                                            ->where("item_id", $item->id)
                                            ->pluck("addon_id")
                                            ->values()
                                    ]))'
                                    class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer">
                                    Edit
                                </button>

                                <form action="{{ route('item.destroy',$item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button onclick="return confirm(' you want to delete?');" class="px-3 py-1.5 border border-border text-foreground text-xs font-medium rounded-button hover:border-primary hover:text-primary transition-all duration-200 cursor-pointer">Delete</button>
                                </form>
                                <button onclick="openAddOnModal('{{ $item->id }}')" class="px-3 py-1.5 border border-border text-foreground text-xs font-medium rounded-button hover:border-primary hover:text-primary transition-all duration-200 cursor-pointer">Add On</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if (!$matchFound)
                    <tr>
                        <td class="text-center pt-3 pb-3" colspan="7" style="background-color:yellow">Item not found.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 px-3 pb-4">
    <div class="text-sm text-gray-600"> Showing {{ $items->firstItem() }} to {{ $items->lastItem() }}
        of {{ $items->total() }} entries</div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Previous --}}
        @if ($items->onFirstPage())
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Previous
        </span>
        @else
        <a href="{{ $items->previousPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Previous
        </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
        @if ($page == $items->currentPage())
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
        @if ($items->hasMorePages())
        <a href="{{ $items->nextPageUrl() }}"
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

<!-- Item Modal -->
<div id="ItemModal" class="custom-modal hidden">
    <div class="custom-modal-backdrop" onclick="closeModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Add New Item</h2>
        <form id="ItemForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod">
            <input type="hidden" name="item_id" id="item_id">

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Item Name (English)</label>
                <input type="text" id="item_name" name="item_name" required placeholder="Enter Item Name"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Item Name (Gujarati)</label>
                <input type="text" id="item_name_gu" name="item_name_gu" required placeholder="Enter Item Name"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
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
                    <input type="file" id="thumbnailUpload" name="picture" accept="image/*" class="hidden" required
                        onchange="previewThumbnail(event)">
                </div>
            </div>

            <div class="space-y-2">
                <label for="inputDescription" class="block text-foreground text-sm font-semibold">
                    Description
                </label>
                <textarea id="description" name="description" rows="4" required
                    class="w-full px-3 md:px-4 py-2.5 md:py-3 text-sm md:text-base border border-border rounded-button focus:bg-white hover:border-primary focus:border-primary transition-all duration-300 resize-none"
                    placeholder="Describe the Item..."></textarea>
                <p class="text-gray-500 text-xs text-right"><span id="charCount">0</span>/50 characters</p>
            </div>

            <!-- <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Air Condition Price</label>
                <input type="number" id="ac_price" name="ac_price" required placeholder="Enter Air Condition Price"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div> -->

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Price</label>
                <input type="number" id="price" name="price" required placeholder="Enter Price"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Select Food Type</label>
                <select
                    name="foodType" id="foodType"
                    class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary" required>
                    <option value="0">Veg</option>
                    <option value="1">Non-Veg</option>
                </select>
            </div>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2" for="fulfillment_type">Order Department</label>

                <select name="fulfillment_type"
                    id="fulfillment_type"
                    class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary mb-1" required>

                    <option value="kitchen"
                        {{ old('fulfillment_type', $item->fulfillment_type ?? 'kitchen') == 'kitchen' ? 'selected' : '' }}>
                        Kitchen
                    </option>

                    <option value="service"
                        {{ old('fulfillment_type', $item->fulfillment_type ?? 'kitchen') == 'service' ? 'selected' : '' }}>
                        Service / Counter
                    </option>

                </select>

                <small>
                    Choose where this item should be prepared or served.
                </small>
            </div>


            <?php if (Session::get('restaurant_id')) { ?>

            <?php } else { ?>
                <div class="form-group mb-4">
                    <label class="block text-foreground text-sm font-medium mb-2">Select Restaurant</label>
                    <select
                        name="restaurantId" id="restaurantId"
                        class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary" required>
                        @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}">{{$restaurant->restaurant_name}}</option>
                        @endforeach
                    </select>
                </div>
            <?php } ?>

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Select Category</label>
                <select
                    name="categoryId" id="categoryId"
                    class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary" required>
                    @foreach($categoies as $category)
                    <option value="{{ $category->id }}">{{$category->category_name}}</option>
                    @endforeach
                </select>
            </div>

            @if(!empty($restaurant_id))
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Select Add On</label>
                <select id="addonId" name="addonId[]" multiple style="height:100px"
                    class="w-full px-4 py-3 border border-border rounded-button">
                    @foreach($add_ons as $add_on)
                    <option value="{{ $add_on->id }}">{{ $add_on->name }}</option>
                    @endforeach
                </select>

            </div>
            @else
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Select Add On</label>
                <select
                    name="addonId[]" id="addonId" multiple style="height:100px"
                    class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary" required>
                    <option value="" selected>Select Add On</option>
                </select>
            </div>
            @endif




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

<!-- AddOn Modal -->
@foreach ($items as $index => $item)
<div id="AddOnModal-{{ $item->id }}" class="custom-modal hidden">
    <div class="custom-modal-backdrop" onclick="closeAddOnModal('{{ $item->id }}')"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Add On Items</h2>

        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Name</th>
                    <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Price</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">
                @php
                $matchFound = false; // Flag to track if a match is found
                @endphp
                @foreach ($add_ons2 as $add_on)
                @if ($add_on->item_id == $item->id)
                @php
                $matchFound = true; // Set the flag to true
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="py-4 px-2">
                        <div>
                            <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $add_on->name }}</p>
                        </div>
                    </td>
                    <td class="py-4 px-2">
                        <span class="text-foreground text-sm font-semibold">₹{{ number_format($add_on->price, 2) }}</span>
                    </td>
                </tr>

                @endif
                @endforeach
                @if (!$matchFound)
                <tr>
                    <td class="text-center" style="background-color:yellow" colspan="3">You have not add any add-on items.</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="mt-4 text-right">
            <button type="button" onclick="closeAddOnModal('{{ $item->id }}')" class="px-4 py-2 border rounded mr-1 cursor-pointer">
                Cancel
            </button>
        </div>
    </div>
</div>
@endforeach

<!-- Availability Modal -->
<div id="AvailabilityModal" class="custom-modal hidden">
    <div class="custom-modal-backdrop" onclick="closeAvailabilityModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Set Availability Duration (in hours)</h2>
        <form id="availability-form" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod">
            <input type="hidden" name="modal_item_id" id="modal_item_id">

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Hours</label>
                <input type="number" id="availability_hours" name="availability_hours" required placeholder="Enter Hours" step="0.001" min="0"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>




            <div class="mt-4 text-right">
                <button type="button" onclick="closeAvailabilityModal()" class="px-4 py-2 border rounded mr-1 cursor-pointer">
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
        $('#modalTitle').text('Add New Item');
        $('#modalSubmitBtn').text('Save').data('mode', 'add');

        $('#ItemForm')[0].reset();
        $('#item_id').val('');

        resetThumbnail();

        // 🔥 REQUIRED ONLY FOR ADD
        $('#thumbnailUpload').prop('required', true);

        openModal();
    }


    function openEditModal(data) {
        $('#modalTitle').text('Edit Item');
        $('#modalSubmitBtn').text('Update').data('mode', 'edit');

        $('#item_id').val(data.item.id);
        $('#item_name').val(data.item.item_name);
        $('#item_name_gu').val(data.item.item_name_gu);
        $('#description').val(data.item.description);
        $('#ac_price').val(data.item.price);
        $('#price').val(data.item.price);
        $('#fulfillment_type').val(data.item.fulfillment_type);


        // ✅ SET SELECT DROPDOWN VALUE
        $('#restaurantId').val(data.item.restaurant_id).trigger('change');
        $('#foodType').val(data.item.food_type).trigger('change');
        $('#categoryId').val(data.item.category_id).trigger('change');

        // ✅ MULTI SELECT ADD-ONS
        if (Array.isArray(data.addons)) {
            $('#addonId').val(data.addons);
        } else {
            $('#addonId').val([]);
        }

        // 🔥 SHOW EXISTING LOGO
        if (data.item.picture) {
            $('#thumbnailPreview').attr('src', 'items/' + data.item.picture);
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
        document.getElementById("ItemModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeModal() {
        document.getElementById("ItemModal").classList.add("hidden");
        document.body.style.overflow = "";
    }

    function openAddOnModal(item_id) {
        document
            .getElementById('AddOnModal-' + item_id)
            .classList.remove('hidden');
        document.body.style.overflow = "hidden";
    }

    function closeAddOnModal(item_id) {
        document
            .getElementById('AddOnModal-' + item_id)
            .classList.add('hidden');
        document.body.style.overflow = "";
    }



    function closeAvailabilityModal() {
        document.getElementById("AvailabilityModal").classList.add("hidden");
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


    $('#ItemForm').on('submit', function(e) {
        e.preventDefault();

        const mode = $('#modalSubmitBtn').data('mode');
        const id = $('#item_id').val();

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

    let currentItemId = null;

    // Handle toggle click
    $(document).on('change', '.toggle-status', function() {
        currentItemId = $(this).data('id');
        let isChecked = $(this).is(':checked');

        if (!isChecked) {
            // Toggle OFF – show modal to set availability
            $('#modal_item_id').val(currentItemId);
            $('#availability_hours').val('');
            $('#AvailabilityModal').removeClass('hidden');
            $('body').css('overflow', 'hidden');

            // document.getElementById("AddOnModal").classList.remove("hidden");
            // document.body.style.overflow = "hidden";

        } else {
            // Toggle ON – remove availability
            $.ajax({
                url: "{{ route('items.update-status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: currentItemId,
                    status: 1,
                    availability: null
                },
                success: function(response) {
                    alert(response.message);
                },
                error: function() {
                    alert("Something went wrong.");
                }
            });
        }
    });

    // On modal form submit
    $(document).ready(function() {
        $('#availability-form').on('submit', function(e) {
            e.preventDefault();

            let itemId = $('#modal_item_id').val();
            let hours = $('#availability_hours').val();

            $.ajax({
                url: "{{ route('items.update-status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: itemId,
                    status: 0,
                    availability: hours
                },
                success: function(response) {
                    alert(response.message);
                    $('#AvailabilityModal').addClass('hidden');
                    $('body').css('overflow', '');
                },
                error: function() {
                    alert("Something went wrong.");
                }
            });
        });
    });


    $(document).on('change', '.toggle-status2', function() {
        var status = $(this).is(':checked') ? 1 : 0;
        var itemId = $(this).data('id');

        var mode = $(this).data('mode');


        $.ajax({
            url: "{{ route('updateAreaStatus') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: itemId,
                status: status,
                mode: mode
            },
            success: function(response) {
                alert(response.message);
            },
            error: function() {
                alert("Something went wrong!");
            }
        });
    });
</script>
@endsection