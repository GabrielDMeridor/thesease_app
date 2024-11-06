@extends('statistician.Smain-layout')

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

                <!-- Scrollable area with all notifications -->
                <div class="overflow-auto" style="max-height: 300px;"> <!-- Set max height for scrolling -->
                    @foreach (auth()->user()->notifications as $notification) <!-- No limit on notifications -->
                        <a class="dropdown-item d-flex align-items-center {{ $notification->read_at ? 'text-muted' : 'font-weight-bold' }}" href="#" onclick="markAsRead('{{ $notification->id }}')">
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
@endsection


@section ('body')
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div>
    <br>

    <!-- Search Form -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('statistician.route1.ajaxSearch') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-sm">
                            <div class="input-group mb-3">
                                <input type="text" name="search" id="search" class="form-control" placeholder="Search by student name" value="{{ request('search') }}">
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
                    <th style="text-align:center;">Student Name</th>
                    <th style="text-align:center;">Program</th>
                    <th style="text-align:center;">Statistician Response</th>
                    <th style="text-align:center;">Statistician Approval</th>
                </tr>
            </thead>
            <tbody id="results-body">
                @forelse ($appointments as $appointment)
                    <tr>
                        <td class="text-center">{{ $appointment->student->name }}</td>
                        <td class="text-center">{{ $appointment->student->program ?? 'N/A' }}</td>
                        <td class="text-center">{{ ucfirst($appointment->student_statistician_response) }}</td>
                        <td class="text-center">
                            @if ($appointment->statistician_approval === 'approved')
                                Approved
                            @else
                                <form action="{{ route('statistician.route1.approve', $appointment->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No responded consultations found.</td>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search').on('input', function() {
            let query = $(this).val();

            $.ajax({
                url: "{{ route('statistician.route1.ajaxSearch') }}",
                type: 'GET',
                data: { search: query },
                success: function(data) {
                    $('#results-body').empty();

                    if (data.data.length > 0) {
                        $.each(data.data, function(index, appointment) {
                            let approveButton = appointment.statistician_approval === 'approved'
                                ? 'Approved'
                                : `<form action="/statistician/route1/approve/${appointment.id}" method="POST">@csrf<button type="submit" class="btn btn-success btn-sm">Approve</button></form>`;

                            let row = `
                                <tr>
                                    <td>${appointment.student.name}</td>
                                    <td>${appointment.student.program ?? 'N/A'}</td>
                                    <td>${appointment.student_statistician_response.charAt(0).toUpperCase() + appointment.student_statistician_response.slice(1)}</td>
                                    <td>${approveButton}</td>
                                </tr>`;
                            $('#results-body').append(row);
                        });
                    } else {
                        $('#results-body').append('<tr><td colspan="4" class="text-center">No responded consultations found.</td></tr>');
                    }

                    $('#pagination-links').hide();
                }
            });
        });
    });
</script>
@endsection
