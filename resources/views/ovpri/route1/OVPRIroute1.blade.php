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
@section ('body')
<div class="container-fluid">
    <div class="sagreet">
        {{ $title }}
    </div>
    <br>

    <div class="container">
        <h2>Advisers Who Responded in Registration</h2>

        <!-- Search Form -->
        <form method="GET" action="{{ route('ovpri.route1.ajaxSearch') }}" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" id="search" class="form-control" placeholder="Search by adviser name" value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </div>
        </form>

        <!-- List of Appointments -->
        <div class="table-responsive">
            <table class="table table-bordered" id="results-table">
                <thead>
                    <tr>
                        <th>Adviser Name</th>
                        <th>Program</th>
                        <th>Registration Response</th>
                        <th>OVPRI Approval</th>
                    </tr>
                </thead>
                <tbody id="results-body">
                    @forelse ($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->adviser->name }}</td>
                            <td>{{ $appointment->adviser->program ?? 'N/A' }}</td>
                            <td>{{ ucfirst($appointment->registration_response) }}</td>
                            <td>
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
                            <td colspan="4" class="text-center">No responded registrations found.</td>
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
                                    <td>${appointment.adviser.program ?? 'N/A'}</td>
                                    <td>${appointment.registration_response.charAt(0).toUpperCase() + appointment.registration_response.slice(1)}</td>
                                    <td>${approveButton}</td>
                                </tr>`;
                            $('#results-body').append(row);
                        });
                    } else {
                        $('#results-body').append('<tr><td colspan="4" class="text-center">No responded registrations found.</td></tr>');
                    }

                    // Hide pagination links for AJAX results
                    $('#pagination-links').hide();
                }
            });
        });
    });
</script>
@endsection