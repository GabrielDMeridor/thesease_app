@extends('gsstudent.GSSmain-layout')



@section('content-header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <ul class="navbar-nav ml-auto">
         <!-- Notification Button -->
         <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter for Notifications -->
                <span class="badge badge-danger badge-counter">3+</span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Notifications Center</h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-primary">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 12, 2023</div>
                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-success">
                            <i class="fas fa-donate text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 7, 2023</div>
                        $290.29 has been deposited into your account!
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-warning">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 2, 2023</div>
                        Spending Alert: We've noticed unusually high spending for your account.
                    </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
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

@endsection

@section('body')


<div class="container-fluid">
    <div class="sagreet">
        {{ $title }}
    </div>
    <br>

    <div class="container">
    <h1 class="text-center mb-5">Manage your account</h1>

    <div class="row">
        <!-- Account Information Section -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Account Information</h5>
                    <p><strong>Name:</strong> {{ $user->name }}
                    @if (strtolower($user->nationality) !== 'filipino')
                        <i class="fas fa-globe"></i> <!-- Globe icon for foreign nationals -->
                    @endif
                    </p>
                    <p><strong>Degree:</strong> {{ $user->degree ?? 'N/A' }}</p>
                    <p><strong>Program:</strong> {{ $user->program ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>A.Y.:</strong> 2026 - 2027</p>
                    <button type="button" class="btn btn-primary update-profile-btn" data-toggle="modal" data-target="#changePasswordModal">Change Password</button>
                    </div>
            </div>
        </div>

        <!-- Thesis Details Section -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Your Thesis Details</h5>
                    <p><strong>Thesis Topic:</strong> The Stress-Relieving Effects of Reading: A Comparative Analysis of Physical Books Versus Screen Reading</p>
                    <h6>Thesis Progress:</h6>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 30%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">Routing Form 1</div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 10%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">Routing Form 2</div>
                    </div>
                </div>
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

@endsection
<style>
    body {
    background-color: #f0f2f5; /* Light background */
}

h1 {
    font-size: 2.5rem;
    color: #343a40;
    font-weight: 500;
    margin-bottom: 40px;
}

.card-title {
    font-weight: 600;
    color: #343a40;
}

.progress-bar {
    text-align: center;
    font-size: 1rem;
    line-height: 1.5;
}

.card-body {
    padding: 20px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

button.btn {
    margin-top: 20px;
}

.btn-danger {
    background-color: #dc3545;
    border: none;
}

.container {
    margin-top: 50px;
}

</style>

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
    let actionUrl = "{{ route('changePassword') }}";  // Use your PUT route

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