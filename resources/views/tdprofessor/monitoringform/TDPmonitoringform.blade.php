@extends('tdprofessor.TDPmain-layout')

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
                <span class="badge badge-danger badge-counter">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Notifications Center</h6>
                <div class="overflow-auto" style="max-height: 300px;">
                    @foreach (auth()->user()->notifications->take(5) as $notification)
                        <a class="dropdown-item d-flex align-items-center {{ $notification->read_at ? 'text-muted' : 'font-weight-bold' }}" href="#" onclick="markAsRead('{{ $notification->id }}')">
                            <div class="mr-3">
                                <div class="icon-circle">
                                    <i class="fas fa-bell fa-fw"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                                <span>{{ $notification->data['message'] }}</span>
                                @if (!empty($notification->data['reason']))
                                    <p class="mb-0 text-gray-700">Reason: {{ $notification->data['reason'] }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="dropdown-item text-center small text-gray-500">
                    <a href="{{ route('notifications.markAsRead') }}">Mark all as read</a>
                </div>

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

<!-- Title and Search Form -->

@endsection

@section('body')
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div>
    <br>
   <div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('tdprofessor.monitoring.search') }}" id="searchForm" class="mb-4">
            <div class="container">
                <div class="row">
                    <div class="col-sm">
                        <!-- Search input for student name -->
                        <div class="form-group">
                            <label for="searchInput">Search by <b>Student Name</b></label>
                            <div class="input-group">
                                <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search Student Name..." value="{{ old('search', $search) }}">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<br>
</div>
<div class="container-fluid">
    <table class="table table-bordered table-hover table-striped custom-table">
        <thead class="table-dark">
            <tr>
                <th class="text-center">Student Name</th>
                <th class="text-center">Proposal Date & Time</th>
                <th class="text-center">Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
                <tr>
                    <td class="text-center">{{ $appointment->student->name ?? 'Unknown' }}</td>
                    <td class="text-center">
                        {{ $appointment->proposal_defense_date ? \Carbon\Carbon::parse($appointment->proposal_defense_date)->format('m/d/Y h:i A') : 'N/A' }}
                    </td>
                    <td class="text-center">
                        {{ $appointment->status ?? 'Pending' }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('panel.showStudentMonitoringForm', $appointment->student_id) }}" class="btn btn-primary">View Monitoring Form</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
