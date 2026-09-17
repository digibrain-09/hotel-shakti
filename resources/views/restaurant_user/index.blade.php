@extends('layouts.admin')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    const routes = {
        store: "{{ route('restaurant_user.store') }}",
        update: "{{ route('restaurant_user.update', ':id') }}"
    };
</script>

@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">Restaurant Employee</h1>
        <p class="text-gray-500 text-sm md:text-base">Add, update, and maintain employee profiles with ease.</p>
    </div>
    <div class="flex items-center gap-2 md:gap-3 md:ml-auto md:ml-0">
        <button onclick="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Employee</span>
        </button>
    </div>
</div>

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
<form method="GET" action="{{ route('restaurant_user.index') }}">
    <div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-6">
        <div class="bg-white rounded-card p-4">
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                
                <div class="relative flex-1">
                    <i data-lucide="search"
                       class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search restaurants employee..."
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

<!-- Employee Table -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">Employee Profiles</h3>
    </div>
    <div class="bg-white rounded-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Employee</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Restaurant Name</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Role</th>
                        <th class="text-left py-3 px-2 text-foreground text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @php
                    $matchFound = false; // Flag to track if a match is found
                    @endphp
                    @foreach ($restaurants_users as $index => $user)
                    @php
                    $matchFound = true; // Set the flag to true
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-3">
                                <div>
                                    <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $user->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <div>
                                <p class="text-foreground text-sm font-medium" style="text-transform: capitalize;">{{ $user->restaurant_name }}</p>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <span class="text-gray-600 text-sm" style="text-transform: capitalize;">{{ $user->role }}</span>
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-2">
                                <button
                                    onclick='openEditModal(@json($user))'
                                    class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-button cursor-pointer">
                                    Edit
                                </button>

                                <form action="{{ route('restaurant_user.destroy',$user->id) }}" method="POST">
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
                        <td class="text-center pt-3 pb-3" colspan="4" style="background-color:yellow">Restaurant Employee not found.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 px-3 pb-4">
    <div class="text-sm text-gray-600"> Showing {{ $restaurants_users->firstItem() }} to {{ $restaurants_users->lastItem() }}
        of {{ $restaurants_users->total() }} entries</div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Previous --}}
        @if ($restaurants_users->onFirstPage())
        <span class="px-3 py-2 border border-border rounded-lg text-sm text-gray-400 cursor-not-allowed">
            Previous
        </span>
        @else
        <a href="{{ $restaurants_users->previousPageUrl() }}"
            class="px-3 py-2 border border-border rounded-lg text-sm hover:border-primary">
            Previous
        </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($restaurants_users->getUrlRange(1, $restaurants_users->lastPage()) as $page => $url)
        @if ($page == $restaurants_users->currentPage())
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
        @if ($restaurants_users->hasMorePages())
        <a href="{{ $restaurants_users->nextPageUrl() }}"
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

<div id="EmployeeModal" class="custom-modal hidden">
    <div class="custom-modal-backdrop" onclick="closeModal()"></div>

    <div class="custom-modal-box">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Add Restaurant User</h2>
        <form id="EmployeeForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod">
            <input type="hidden" name="employee_id" id="employee_id">

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter Name"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter Email Address"
                    class="w-full px-4 py-3 border border-border rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
                <small id="email-error" style="color: red;"></small>
            </div>
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

            <div class="form-group mb-4">
                <label class="block text-foreground text-sm font-medium mb-2">Select Role</label>
                <select
                    name="role" id="role"
                    class="w-full px-4 py-3 border border-border rounded-button
                   focus:outline-none focus:ring-2 focus:ring-primary" required>
                    <option value="restaurant_manager">Manager</option>
                    <option value="restaurant_waiter">Waiter</option>
                    <option value="kitchen_owner">Kitchen Owner</option>
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    function openAddModal() {
        $('#modalTitle').text('Add Restaurant User');
        $('#modalSubmitBtn').text('Save').data('mode', 'add');

        $('#EmployeeForm')[0].reset();
        $('#employee_id').val('');

        // 🔥 REQUIRED ONLY FOR ADD
        $('#thumbnailUpload').prop('required', true);

        openModal();
    }


    function openEditModal(data) {
        $('#modalTitle').text('Edit Restaurant User');
        $('#modalSubmitBtn').text('Update').data('mode', 'edit');

        $('#employee_id').val(data.id);
        $('#name').val(data.name);
        $('#email').val(data.email);

        // ✅ SET SELECT DROPDOWN VALUE
        $('#restaurantId').val(data.restaurant_id).trigger('change');
        $('#role').val(data.role).trigger('change');


        // $('#password').val(data.password);
        $('#password').removeAttr('required');


        openModal();
    }


    function openModal() {
        document.getElementById("EmployeeModal").classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeModal() {
        document.getElementById("EmployeeModal").classList.add("hidden");
        document.body.style.overflow = "";
    }

    $('#EmployeeForm').on('submit', function(e) {
        e.preventDefault();

        const mode = $('#modalSubmitBtn').data('mode');
        const id = $('#employee_id').val();

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