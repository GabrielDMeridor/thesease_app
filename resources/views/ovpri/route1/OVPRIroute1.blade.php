@extends('ovpri.OVPRImain-layout')

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
                <span class="badge badge-danger badge-counter" id="notification-count">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Notifications Center</h6>

                <!-- Scrollable area with all notifications -->
                <div class="overflow-auto" style="max-height: 300px;"> <!-- Set max height for scrolling -->
                    @foreach (auth()->user()->notifications as $notification)
                        <div id="notification-{{ $notification->id }}" class="dropdown-item d-flex align-items-center {{ $notification->read_at ? 'text-muted' : 'font-weight-bold' }}">
                            <div class="mr-3">
                                <div class="icon-circle">
                                    <i class="fa-solid fa-bell"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                                <span>{{ $notification->data['message'] ?? 'No message available' }}</span> <!-- Default value if 'message' is missing -->
                                <!-- Conditionally display the reason if it exists -->
                                @if (!empty($notification->data['reason']))
                                    <p class="mb-0 text-gray-700">Reason: {{ $notification->data['reason'] }}</p>
                                @endif
                            </div>

                            <!-- Delete Notification Button -->
                            <button type="button" class="btn btn-link text-danger ml-auto" onclick="deleteNotification('{{ $notification->id }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
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
                <i class="fas fa-user-circle text-gray-600" style="font-size: 1.25rem;"></i>
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

<!-- Optional Toast Message for Deletion Confirmation -->
<div id="toast-message" class="alert alert-success" style="display:none; position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

@endsection


@section ('body')
<div class="container-fluid">
    <div class="sagreet">
        {{ $title }}
    </div>
    <br>

    <div class="container">
    <h2>Manage OVPRI Link</h2>

    <!-- Display Success Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Display Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form for Creating or Updating the OVPRI Link -->
    <form action="{{ route('ovpri.storeOrUpdateOVPRILink') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="ovpri_link">OVPRI Link:</label>
            <input type="url" name="ovpri_link" class="form-control" value="{{ $ovpriLink->value }}" required placeholder="Enter the OVPRI link">
        </div>
        <button type="submit" class="btn btn-primary">{{ $ovpriLink->value ? 'Update' : 'Upload' }} Link</button>
    </form>
</div>


<!-- Search Form -->
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('ovpri.route1.ajaxSearch') }}">
            <div class="container">
                <div class="row">
                    <div class="col-sm">
                        <!-- Keyword search input -->
                        <div class="input-group mb-3">
                            <input type="text" name="search" id="search" class="form-control" placeholder="Search by adviser name" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<br>

        <!-- List of Appointments -->
        <div class="table-responsive">
    <table class="table table-bordered table-hover table-striped custom-table" id="results-table">
        <thead class="table-dark">
            <tr>
                <th style="text-align:center;">Adviser Name</th>
                <th style="text-align:center;">Advisee Name</th> <!-- New column for advisee name -->
                <th style="text-align:center;">Email</th>
                <th style="text-align:center;">Program</th>
                <th style="text-align:center;">Registration Response</th>
                <th style="text-align:center;">OVPRI Approval</th>
            </tr>
        </thead>
        <tbody id="results-body">
            @forelse ($appointments as $appointment)
                <tr>
                    <td class="text-center">{{ $appointment->adviser->name }}</td>
                    <td class="text-center">{{ $appointment->student->name ?? 'N/A' }}</td> <!-- Display advisee name -->
                    <td class="text-center">{{ $appointment->adviser->email }}</td>
                    <td class="text-center">{{ $appointment->adviser->program ?? 'N/A' }}</td>
                    <td class="text-center">{{ ucfirst($appointment->registration_response) }}</td>
                    <td class="text-center">
                        @if ($appointment->ovpri_approval === 'approved')
                            Approved
                        @else
                            <form action="{{ route('ovpri.route1.approve', $appointment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No responded registrations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center" id="pagination-links">
            {{ $appointments->links() }}
        </div>
</div>

<!-- JavaScript for AJAX Search -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search').on('input', function() {
            let query = $(this).val();

            $.ajax({
                url: "{{ route('ovpri.route1.ajaxSearch') }}",
                type: 'GET',
                data: { search: query },
                success: function(data) {
                    $('#results-body').empty(); // Clear current table body

                    if (data.data.length > 0) {
                        $.each(data.data, function(index, appointment) {
                            let approveButton = appointment.ovpri_approval === 'approved'
                                ? 'Approved'
                                : `<form action="/ovpri/route1/approve/${appointment.id}" method="POST">@csrf<button type="submit" class="btn btn-success btn-sm">Approve</button></form>`;

                            let row = `
                                <tr>
                                    <td>${appointment.adviser.name}</td>
                                    <td>${appointment.adviser.email}</td>
                                    <td>${appointment.adviser.program ?? 'N/A'}</td>
                                    <td>${appointment.registration_response.charAt(0).toUpperCase() + appointment.registration_response.slice(1)}</td>
                                    <td>${approveButton}</td>
                                </tr>`;
                            $('#results-body').append(row);
                        });
                    } else {
                        $('#results-body').append('<tr><td colspan="5" class="text-center">No responded registrations found.</td></tr>');
                    }

                    // Hide pagination links for AJAX results
                    $('#pagination-links').hide();
                }
            });
        });
    });

    function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        $.ajax({
            url: `/notifications/${notificationId}`, // URL for delete route
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}' // CSRF token for security
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Remove the notification element from the dropdown
                    $(`#notification-${notificationId}`).remove();

                    // Update the notification count badge
                    let notificationCount = parseInt($('#notification-count').text());
                    notificationCount = notificationCount > 0 ? notificationCount - 1 : 0;
                    $('#notification-count').text(notificationCount);

                    // Show success toast message
                    $('#toast-message').text(response.message).fadeIn().delay(3000).fadeOut();
                }
            },
            error: function(xhr) {
                console.error('An error occurred while deleting the notification:', xhr);
                alert('Failed to delete notification');
            }
        });
    }
}
</script>
@endsection
