@extends('graduateschool.GSmain-layout')



@section('content-header')
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <ul class="navbar-nav ml-auto">
<!-- Notifications Dropdown -->
<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <!-- Counter for Notifications -->
        <span class="badge badge-danger badge-counter">{{ auth()->user()->unreadNotifications->count() }}</span>
    </a>
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">Notifications Center</h6>

        <!-- Limit the notifications to 5 -->
        <div class="overflow-auto" style="max-height: 300px;"> <!-- This is the scrolling part -->
            @foreach (auth()->user()->notifications->take(5) as $notification)  <!-- Limit to 5 -->
                <a class="dropdown-item d-flex align-items-center {{ $notification->read_at ? 'text-muted' : 'font-weight-bold' }}" href="#" onclick="markAsRead('{{ $notification->id }}')">
                    <div class="mr-3">
                        <div class="icon-circle">
                        <i class="fas fa-bell fa-fw"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                        <span>{{ $notification->data['message'] }}</span>
                        <!-- Conditionally display the reason if it exists -->
                        @if (!empty($notification->data['reason']))
                            <p class="mb-0 text-gray-700">Reason: {{ $notification->data['reason'] }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Mark all as read link -->
        <div class="dropdown-item text-center small text-gray-500">
            <a href="{{ route('notifications.markAsRead') }}">Mark all as read</a>
        </div>

        <!-- Clear all notifications button -->
        <div class="dropdown-item text-center small text-gray-500">
            <form action="{{ route('notifications.clearAll') }}" method="POST">
                @csrf
                <button class="btn btn-link" type="submit">Clear all notifications</button>
            </form>
        </div>
    </div>
</li>



        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small navbar-username">{{ auth()->user()->name }}</span>
               <i class="fas fa-user-circle text-gray-600" style="font-size: 1.25rem;"></i> <!-- Adjusted icon size -->
        </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>

<!-- /.content-header -->
@endsection

@section('body')


<div class="container-fluid">
    <div class="sagreet">
        {{ $title }}
    </div>
    <br>

 <div class="card">
        <div class="card-body pb-5 pt-4">
            <div class="row">   
                <!-- Profile Image and Basic Info -->
                <div class="col-md-3 col-sm-12 border-right">
                    <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                        <h4 class="profile-name text-gray-600">{{ $user->name }}</h4>
                        <p>
                            @if($user->account_type == 1)
                                Super Admin
                            @elseif($user->account_type == 2)
                                Admin
                            @elseif($user->account_type == 3)
                                Graduate School
                            @elseif($user->account_type == 4)
                                Program Chair
                            @elseif($user->account_type == 5)
                                Thesis Dissertation Professor
                            @elseif($user->account_type == 6)
                                Auf Ethics Review Committee
                            @elseif($user->account_type == 7)
                                Statistician
                            @elseif($user->account_type == 8)
                                OVPRI
                            @elseif($user->account_type == 9)
                                Library
                            @elseif($user->account_type == 10)
                                Language Editor
                            @elseif($user->account_type == 11)
                                Graduate School Student
                            @else
                                Unknown Account Type
                            @endif
                        </p>
                    </div>
                    
                </div>

                <!-- Profile Settings -->
                <div class="col-md-5 col-sm-12 border-right profile-section">

                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right header-style">Profile Information</h4>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="labels">Name</label>
                                <div class="col-sm-12 text-secondary profile-info-name">{{ $user->name }}</div>
                            </div>
                        </div>
                        <br>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="labels">Email</label>
                                <div class="col-sm-12 text-secondary profile-info-email">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button type="button" class="btn btn-primary update-profile-btn" data-toggle="modal" data-target="#updateProfileModal">Update Profile</button>
                        </div>
                    </div>
                </div>

                <!-- Experience and Additional Details -->
                <div class="col-md-4 col-sm-12">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center experience">
                            <span class="header-style">Password / Authentication</span>
                        </div>
                        <p class="text-gray mt-2">
                            Manage and secure access to your account by updating your password and authentication settings.
                        </p>
                        <br>
                        <button type="button" class="btn btn-primary update-profile-btn" data-toggle="modal" data-target="#changePasswordModal">Change Password</button>
                    </div>
                </div>
            </div>
        </div>
    </div>






    <!-- Update Profile Modal -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="profileSuccessMessage" class="alert alert-success" style="display: none;"></div>

                    <form id="updateProfileForm" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}">
                            <span class="invalid-feedback" id="name_error" style="display: block;"></span>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}">
                            <span class="invalid-feedback" id="email_error" style="display: block;"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitUpdateProfile">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="passwordSuccessMessage" class="alert alert-success" style="display: none;"></div>

                    <form id="changePasswordForm" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="input-group">
                                <input type="password" id="current_password" name="current_password" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" data-toggle="password" data-target="#current_password">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <span class="invalid-feedback" id="current_password_error" style="display: block;"></span>
                        </div>

                        <!-- New Password -->
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <div class="input-group">
                                <input type="password" id="new_password" name="new_password" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" data-toggle="password" data-target="#new_password">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <span class="invalid-feedback" id="new_password_error" style="display: block;"></span>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" id="confirm_password" name="new_password_confirmation" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" data-toggle="password" data-target="#confirm_password">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <span class="invalid-feedback" id="confirm_password_error" style="display: block;"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitChangePassword">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // Function to toggle password visibility
        $(document).on('click', '.toggle-password', function () {
            const input = $($(this).data('target'));
            const icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Reset modal when closed for Change Password Modal
        $('#changePasswordModal').on('hidden.bs.modal', function () {
            resetForm('#changePasswordForm');
            resetPasswordVisibility();
        });

        // Reset modal when closed for Update Profile Modal
        $('#updateProfileModal').on('hidden.bs.modal', function () {
            resetForm('#updateProfileForm');
        });

        // Function to reset form fields, error/success messages
        function resetForm(formSelector) {
            // Clear all input fields
            $(formSelector).trigger('reset');
            // Clear validation messages and remove invalid class
            $(formSelector).find('.invalid-feedback').text('');
            $(formSelector).find('.form-control').removeClass('is-invalid');
            // Hide success messages
            $(formSelector).find('.alert-success').hide();
        }

        // Function to reset password visibility to default (hide passwords)
        function resetPasswordVisibility() {
            $('.toggle-password').each(function () {
                const input = $($(this).data('target'));
                input.attr('type', 'password');
                $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
            });
        }

        // Handle form submission via AJAX for Change Password
        $('#submitChangePassword').on('click', function (e) {
            e.preventDefault();

            let form = $('#changePasswordForm');
            let actionUrl = "{{ route('graduateschool.changePassword') }}";  // Use your PUT route

            // Reset any previous error messages
            form.find('.invalid-feedback').text('');
            form.find('.form-control').removeClass('is-invalid');
            $('#passwordSuccessMessage').hide();

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        $('#passwordSuccessMessage').text(response.message).show();
                        form.trigger('reset'); // Clear form fields after success
                    } else if (response.status === 'error') {
                        handleErrors(response.errors, form);
                    }
                },
                error: function(xhr) {
                    console.error('An error occurred', xhr);
                }
            });
        });

        // Handle form submission via AJAX for Update Profile
        $('#submitUpdateProfile').on('click', function (e) {
            e.preventDefault();

            let form = $('#updateProfileForm');
            let actionUrl = "{{ route('graduateschool.updateProfile') }}";  // Use your PUT route for the profile update

            // Reset any previous error messages
            form.find('.invalid-feedback').text('');
            form.find('.form-control').removeClass('is-invalid');
            $('#profileSuccessMessage').hide();

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        $('#profileSuccessMessage').text(response.message).show();

                        // Get the updated name and email values
                        const updatedName = $('#updateProfileForm #name').val();
                        const updatedEmail = $('#updateProfileForm #email').val();

                        // Update the name and email in the Dr. B card
                        $('.profile-name').text(updatedName);
                        $('.profile-email').text(updatedEmail);

                        // Update the name and email in the navbar near the logout button
                        $('.navbar-username').text(updatedName);

                        // Update the name and email in the Profile Information card
                        $('.profile-info-name').text(updatedName);
                        $('.profile-info-email').text(updatedEmail);

                        // Update the name and email in the modal fields
                        $('#updateProfileForm #name').val(updatedName);  // Update the name in the modal
                        $('#updateProfileForm #email').val(updatedEmail);  // Update the email in the modal

                        // Clear success message after a timeout (optional)
                        setTimeout(function () {
                            $('#profileSuccessMessage').fadeOut();
                        }, 3000);
                    } else {
                        handleErrors(response.errors, form);
                    }
                },
                error: function(xhr) {
                    console.error('An error occurred', xhr);
                }
            });
        });

        // Function to handle form errors
        function handleErrors(errors, form) {
            if (errors) {
                $.each(errors, function (key, value) {
                    form.find(`#${key}_error`).text(value[0]);
                    form.find(`#${key}`).addClass('is-invalid');
                });
            }
        }
    });
</script>
@endsection
