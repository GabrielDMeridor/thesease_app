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
    <h1 class="mb-4">CCFP Route 2 - Sign Community Response</h1>

    <!-- Search Form -->
    <form method="GET" action="{{ route('ccfp.route2') }}" class="form-inline mb-4">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search by student name" value="{{ request('search') }}">
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <!-- Students Table -->
    <div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Email</th>
                <th>Service Form</th>
                <th>Accomplishment Report</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->student->name }}</td>
                    <td>{{ $appointment->student->email }}</td> <!-- New Email Column -->
                    <td>
                        @if($appointment->community_extension_service_form_path)
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#serviceFormModal-{{ $appointment->id }}">
                                View Service Form
                            </button>
                        @else
                            <span class="text-muted">No Service Form</span>
                        @endif
                    </td>
                    <td>
                        @if($appointment->community_accomplishment_report_path)
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#accomplishmentReportModal-{{ $appointment->id }}">
                                View Accomplishment Report
                            </button>
                        @else
                            <span class="text-muted">No Accomplishment Report</span>
                        @endif
                    </td>
                    <td>
                        @if($appointment->final_ccfp_signature)
                            <span class="badge badge-success">Signed</span>
                        @else
                            <span class="badge badge-secondary">Not Signed</span>
                        @endif
                    </td>
                    <td>
                        @if(!$appointment->final_ccfp_signature)
                            <form action="{{ route('ccfp.route2.sign', $appointment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Sign</button>
                            </form>
                        @endif
                    </td>
                </tr>

                <!-- Service Form Modal -->
                <div class="modal fade" id="serviceFormModal-{{ $appointment->id }}" tabindex="-1" aria-labelledby="serviceFormModalLabel-{{ $appointment->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="serviceFormModalLabel-{{ $appointment->id }}">Community Extension Service Form</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <iframe src="{{ Storage::url($appointment->community_extension_service_form_path) }}" width="100%" height="500px"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accomplishment Report Modal -->
                <div class="modal fade" id="accomplishmentReportModal-{{ $appointment->id }}" tabindex="-1" aria-labelledby="accomplishmentReportModalLabel-{{ $appointment->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="accomplishmentReportModalLabel-{{ $appointment->id }}">Community Accomplishment Report</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <iframe src="{{ Storage::url($appointment->community_accomplishment_report_path) }}" width="100%" height="500px"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
        @isset($students)
            {{ $students->links() }}
        @endisset
    </div>

</div>
@endsection
