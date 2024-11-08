@extends('aufcommittee.AUFCmain-layout')

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
                                <span>{{ $notification->data['message'] }}</span>
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
    <div class="sagreet">{{ $title }}</div>
    <br>

    <!-- Search Input -->
    <div class="card">
        <div class="card-body">
        <form action="{{ route('graduateschool.route1') }}" method="GET">
            <div class="container">
                <div class="row">
                    <div class="col-sm">
                        <!-- Keyword search input -->
                        <div class="input-group mb-3">
                            <input type="text" name="search" class="form-control" placeholder="Search students by name" value="{{ request('search') }}">
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

    <!-- Table of Students Awaiting AUFC Approval -->
<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th style="text-align:center;">Student Name</th>
                <th style="text-align:center;">Program</th>
                <th style="text-align:center;">Last Updated</th>
                <th style="text-align:center;">Status</th>
                <th style="text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody id="appointmentsTable">
            @foreach ($appointments as $appointment)
            <tr>
                <td class="text-center">{{ $appointment->student->name ?? 'N/A' }}</td>
                <td class="text-center">{{ $appointment->student->program ?? 'N/A' }}</td>
                <td class="text-center">{{ $appointment->updated_at->format('m/d/Y') }}</td>
                <td class="text-center">{{ $appointment->aufc_status == 'approved' ? 'Approved' : 'Pending' }}</td>
                <td class="text-center">
                    @if ($appointment->aufc_status !== 'approved')
                        <form action="{{ route('aufcommittee.route1.approve', $appointment->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-affix" style="color:white;">Approve</button>
                        </form>
                    @else
                        <button class="btn btn-secondary" disabled>Approved</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

    <!-- Pagination Links -->
    <div class="mt-3" id="pagination-links">
        {{ $appointments->links() }}
    </div>
<script>
    $(document).ready(function () {
        $('#searchInput').on('input', function () {
            let query = $(this).val();

            $.ajax({
                url: "{{ route('aufcommittee.route1.ajaxSearch') }}",
                type: 'GET',
                data: { search: query },
                success: function (response) {
                    let appointmentsTable = $('#appointmentsTable');
                    appointmentsTable.empty();

                    if (response.data.length === 0) {
                        appointmentsTable.append('<tr><td colspan="5" class="text-center">No students found.</td></tr>');
                    } else {
                        $.each(response.data, function (index, appointment) {
                            let statusText = appointment.aufc_status === 'approved' ? 'Approved' : 'Pending';
                            let approveButton = appointment.aufc_status === 'approved'
                                ? '<button class="btn btn-secondary" disabled>Approved</button>'
                                : `<form action="/aufcommittee/route1/approve/${appointment.id}" method="POST" style="display:inline;">
                                      @csrf
                                      <button type="submit" class="btn btn-success">Approve</button>
                                   </form>`;

                            appointmentsTable.append(`
                                <tr>
                                    <td>${appointment.student.name}</td>
                                    <td>${appointment.student.program}</td>
                                    <td>${new Date(appointment.updated_at).toLocaleDateString()}</td>
                                    <td>${statusText}</td>
                                    <td>${approveButton}</td>
                                </tr>
                            `);
                        });
                    }

                    $('#pagination-links').hide();
                },
                error: function () {
                    alert('Error retrieving data');
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
