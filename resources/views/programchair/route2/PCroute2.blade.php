@extends('programchair.PCmain-layout')

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
    <!-- Search Form -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <form method="GET" action="{{ route('programchair.route2.show') }}" class="form-inline">
                <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search by student name" value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
        </div>

        <div class="card-body">
            <!-- Community Uploads for Signing Table -->
            <h5>Community Uploads for Program Chair Signing</h5>
            @if($communityAppointments->count() > 0)
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Service Form</th>
                                <th>Accomplishment Report</th>
                                <th>Final Program Signature</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($communityAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->student->name }}</td>
                                    <td>{{ $appointment->student->email }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $appointment->community_extension_service_form_path) }}" target="_blank">View Service Form</a>
                                    </td>
                                    <td>
                                        <a href="{{ asset('storage/' . $appointment->community_accomplishment_report_path) }}" target="_blank">View Accomplishment Report</a>
                                    </td>
                                    <td>
                                        @if ($appointment->final_program_signature)
                                            <span class="badge badge-success">Signed</span>
                                        @else
                                            <span class="badge badge-secondary">Not Signed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!$appointment->final_program_signature)
                                            <form action="{{ route('program-chair.sign', $appointment) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm">Sign</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $communityAppointments->links() }}
                    </div>
                </div>
            @else
                <p class="text-center">No community uploads requiring signature.</p>
            @endif

            <!-- Publication Approvals Table -->
            <h5>Approve Proof of Publication Submissions</h5>
            @if($publicationAppointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Proof of Publication</th>
                                <th>Publication Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($publicationAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->student->name }}</td>
                                    <td>{{ $appointment->student->email }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $appointment->proof_of_publication_path) }}" target="_blank">
                                            {{ $appointment->proof_of_publication_original_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($appointment->publication_status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif ($appointment->publication_status === 'denied')
                                            <span class="badge badge-danger">Denied</span>
                                        @else
                                            <span class="badge badge-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($appointment->publication_status === null)
                                            <form action="{{ route('programchair.route2.approve', $appointment->student->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form action="{{ route('programchair.route2.deny', $appointment->student->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Deny</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $publicationAppointments->links() }}
                    </div>
                </div>
            @else
                <p class="text-center">No students have uploaded proof of publication.</p>
            @endif
        </div>
    </div>
</div>
@endsection
