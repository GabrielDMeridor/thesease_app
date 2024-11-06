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



@section('body')
<div class="container-fluid">
    <div class="sagreet">
        {{ $title }}
    </div>
    <br>
    <!-- Section for Pending Adviser Requests -->
    <div class="card">
    <div class="card-body">
        <h2>Pending Adviser Requests</h2>

        @if($requests->isEmpty())
            <p>No pending requests at the moment.</p>
        @else
            <table class="table table-hover table-striped custom-table">
                <thead>
                    <tr>
                        <th style="text-align:center;">Student Name</th>
                        <th style="text-align:center;">Adviser Type for this student</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                    <tr>
                        <td class="text-center">{{ $request->student->name }}</td>
                        <td class="text-center">{{ $request->appointment_type }}</td>
                        <td class="text-center">{{ $request->status }}</td>
                        <td class="text-center">
                            <form action="{{ route('professor.request.update', $request->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" name="action" value="disapprove" class="btn btn-affix" style="color:white;">Disapprove</button>
                                <button type="submit" name="action" value="approve" class="btn btn-primary">Approve</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<br>

    <!-- Section for Approved Students (Advisees) -->
    <div class="card">
    <div class="card-body">
        <!-- Section for Approved Students (Advisees) -->
        <h2>Your Advisees</h2>
        
        @if($advisees->isEmpty())
            <p>You have no advisees at the moment.</p>
        @else
            <table class="table table-hover table-striped custom-table">
                <thead>
                    <tr>
                        <th style="text-align:center;">Student Name</th>
                        <th style="text-align:center;">Adviser Type for this student</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;">Progress</th> <!-- New Column for Progress and View Button -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($advisees as $advisee)
                    <tr>
                        <td class="text-center">{{ $advisee->student->name }}</td>
                        <td class="text-center">{{ $advisee->appointment_type }}</td>
                        <td class="text-center">{{ ucfirst($advisee->status) }}</td>
                        <td class="text-center">
                            <a href="{{ route('professor.showRoutingForm', $advisee->student->id) }}" class="btn btn-primary">View Routing Form 1</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

</div>
@endsection
