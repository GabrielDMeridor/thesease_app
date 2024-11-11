@extends('ccfp.Cmain-layout')

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
@section('body')
<div class="container-fluid">
    <h1>{{ $title }}</h1>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- CCFP Link Management -->
    <div>
        <h2>Manage CCFP Link</h2>
        <form action="{{ route('ccfp.storeOrUpdateCCFPLink') }}" method="POST">
            @csrf
            <input type="url" name="ccfp_link" class="form-control" value="{{ $ccfpLink->value }}" required placeholder="Enter CCFP link">
            <button type="submit" class="btn btn-primary">{{ $ccfpLink->value ? 'Update' : 'Save' }} Link</button>
        </form>
    </div>

    <!-- Search Form -->
    <div class="card my-4">
        <div class="card-body">
            <form id="search-form">
                <div class="input-group">
                    <input type="text" id="search" class="form-control" placeholder="Search by student name">
                    <button class="btn btn-primary" type="button" id="search-button"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- List of Community Extension Approvals -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="results-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Community Extension Response</th>
                    <th>Approval Status</th>
                </tr>
            </thead>
            <tbody id="results-body">
                @foreach ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->student->name }}</td>
                        <td>{{ $appointment->student->email }}</td>
                        <td>{{ $appointment->student->program ?? 'N/A' }}</td>
                        <td>{{ $appointment->community_extension_response ? 'Responded' : 'Pending' }}</td>
                        <td>
                            @if ($appointment->community_extension_approval === 'approved')
                                Approved
                            @else
                                <form action="{{ route('ccfp.route1.approve', $appointment->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    {{ $appointments->links() }}
</div>

<!-- JavaScript for AJAX Search -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search').on('input', function() {
            let query = $(this).val();

            $.ajax({
                url: "{{ route('ccfp.route1.ajaxSearch') }}",
                type: 'GET',
                data: { search: query },
                success: function(data) {
                    $('#results-body').empty(); // Clear current table body

                    if (data.data.length > 0) {
                        $.each(data.data, function(index, appointment) {
                            let approveButton = appointment.community_extension_approval === 'approved'
                                ? 'Approved'
                                : `<form action="/ccfp/extension/approve/${appointment.id}" method="POST">@csrf<button type="submit" class="btn btn-success btn-sm">Approve</button></form>`;

                            let row = `
                                <tr>
                                    <td>${appointment.student.name}</td>
                                    <td>${appointment.student.email}</td>
                                    <td>${appointment.student.program ?? 'N/A'}</td>
                                    <td>${appointment.community_extension_response ? 'Responded' : 'Pending'}</td>
                                    <td>${approveButton}</td>
                                </tr>`;
                            $('#results-body').append(row);
                        });
                    } else {
                        $('#results-body').append('<tr><td colspan="5" class="text-center">No results found.</td></tr>');
                    }
                },
                error: function(xhr) {
                    console.error('An error occurred:', xhr);
                }
            });
        });
    });
</script>
@endsection
