@extends('layouts.admin')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    const routes = {
        store: "{{ route('category.store') }}",
        update: "{{ route('category.update', ':id') }}"
    };
</script>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Restaurant Category</h1>
        <p class="text-gray-500 text-sm md:text-base">Manage and monitor restaurant categories.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 md:ml-auto md:ml-0">
        <button onclick="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Category</span>
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
<form method="GET" action="{{ route('category.index') }}">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
        <div class="bg-white rounded-card p-4">
            <div class="flex flex-col md:flex-row md:items-center gap-3">

                <div class="relative flex-1">
                    <i data-lucide="search"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>

                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search Category..."
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


<!-- Category Table -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">All Category Data</h3>
    </div>
    <div class="bg-white rounded-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Category</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold w-[200px]">Restaurant Name</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($categories as $index => $category)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ ('categories/' . $category->image) }}" alt="table qr-code" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-foreground truncate" style="text-transform: capitalize;">{{ $category->category_name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $category->description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <div>
                                <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $category->restaurant_name }}</p>
                            </div>
                        </td>

                        <td class="py-4 px-2">
                            <div class="flex items-center gap-2">
                                <button
                                    onclick='openEditModal(@json($category))'
                                    class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer">
                                    Edit
                                </button>

                                <form action="{{ route('category.destroy',$category->id) }}" method="POST">
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
                        <td class="text-center pt-3 pb-3" colspan="3" style="background-color:yellow">Category not found.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Pagination -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 px-3 pb-4">
    <div class="text-sm text-gray-600"> Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }}
        of {{ $categories->total() }} entries</div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Previous --}}
        @if ($categories->onFirstPage())
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Previous
        </span>
        @else
        <a href="{{ $categories->previousPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Previous
        </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
        @if ($page == $categories->currentPage())
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
        @if ($categories->hasMorePages())
        <a href="{{ $categories->nextPageUrl() }}"
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


<div id="CategoryModal" class="custom-modal hidden">
    <div class="custom-modal-backdrop" onclick="closeModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Add new Category</h2>
        <form id="CategoryForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod">
            <input type="hidden" name="category_id" id="category_id">

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Category Name</label>
                <input type="text" id="category_name" name="category_name" required placeholder="Enter Category Name"
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
                    <input type="file" id="thumbnailUpload" name="image" accept="image/*" class="hidden" required
                        onchange="previewThumbnail(event)">
                </div>
            </div>

            <div class="space-y-2">
                <label for="inputDescription" class="block text-foreground text-sm font-semibold">
                    Description
                </label>
                <textarea id="description" name="description" rows="4" required
                    class="w-full px-3 md:px-4 py-2.5 md:py-3 text-sm md:text-base border border-border rounded-button focus:bg-white hover:border-primary focus:border-primary transition-all duration-300 resize-none"
                    placeholder="Describe the food category..."></textarea>
                <p class="text-gray-500 text-xs text-right"><span id="charCount">0</span>/50 characters</p>
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
        $('#modalTitle').text('Add New Category');
        $('#modalSubmitBtn').text('Save').data('mode', 'add');

        $('#CategoryForm')[0].reset();
        $('#category_id').val('');

        resetThumbnail();

        // 🔥 REQUIRED ONLY FOR ADD
        $('#thumbnailUpload').prop('required', true);

        openModal();
    }


    function openEditModal(data) {
        $('#modalTitle').text('Edit Category');
        $('#modalSubmitBtn').text('Update').data('mode', 'edit');

        $('#category_id').val(data.id);
        $('#category_name').val(data.category_name);
        $('#description').val(data.description);


        // ✅ SET SELECT DROPDOWN VALUE
        $('#restaurantId').val(data.restaurant_id).trigger('change');


        // 🔥 SHOW EXISTING LOGO
        if (data.image) {
            $('#thumbnailPreview').attr('src', 'categories/' + data.image);
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
        document.getElementById("CategoryModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeModal() {
        document.getElementById("CategoryModal").classList.add("hidden");
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


    $('#CategoryForm').on('submit', function(e) {
        e.preventDefault();

        const mode = $('#modalSubmitBtn').data('mode');
        const id = $('#category_id').val();

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