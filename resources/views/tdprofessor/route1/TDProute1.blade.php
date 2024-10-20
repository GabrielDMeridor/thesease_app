@extends('tdprofessor.TDPmain-layout')

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
                <span class="badge badge-danger badge-counter">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Notifications Center</h6>

                @foreach (auth()->user()->unreadNotifications as $notification)
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                            <span>{{ $notification->data['message'] }}</span>
                        </div>
                    </a>
                @endforeach

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
@endsection


@section('body')
<div class="container-fluid">
    <div class="sagreet">
        {{ $title }}
    </div>
    <br>
    <!-- Section for Pending Adviser Requests -->
    <h2>Pending Adviser Requests</h2>
    
    @if($requests->isEmpty())
        <p>No pending requests at the moment.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Appointment Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $request)
                <tr>
                    <td>{{ $request->student->name }}</td>
                    <td>{{ $request->appointment_type }}</td>
                    <td>{{ $request->status }}</td>
                    <td>
                        <form action="{{ route('professor.request.update', $request->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                            <button type="submit" name="action" value="disapprove" class="btn btn-danger">Disapprove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Section for Approved Students (Advisees) -->
    <h2 class="mt-5">Your Advisees</h2>
    
    @if($advisees->isEmpty())
        <p>You have no advisees at the moment.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Appointment Type</th>
                    <th>Status</th>
                    <th>Progress</th> <!-- New Column for Progress and View Button -->
                </tr>
            </thead>
            <tbody>
                @foreach ($advisees as $advisee)
                <tr>
                    <td>{{ $advisee->student->name }}</td>
                    <td>{{ $advisee->appointment_type }}</td>
                    <td>{{ ucfirst($advisee->status) }}</td>
                    <td>
                        <a href="{{ route('professor.showRoutingForm', $advisee->student->id) }}" class="btn btn-info">View Routing Form 1</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
