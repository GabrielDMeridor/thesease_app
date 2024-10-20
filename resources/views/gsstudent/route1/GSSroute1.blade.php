@extends('gsstudent.GSSmain-layout')

@section('content-header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Notifications Center</h6>
                @foreach (auth()->user()->notifications as $notification)
                    <a class="dropdown-item d-flex align-items-center {{ $notification->read_at ? 'text-muted' : 'font-weight-bold' }}" href="#">
                        <div class="mr-3">
                            <div class="icon-circle">
                                <i class="fa-solid fa-bell text-white"></i>
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

<!-- Success Message -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Error Message -->
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@endsection

@section('body')

<div class="container-fluid">
    <div class="sagreet">
        {{ $title }}
    </div>
    <br>

    <div class="card shadow mb-4">
        <div class="card-header">
        </div>
        <div class="card-body">
            <!-- Multi-Step Navigation -->
            <div class="steps">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    @for ($step = 1; $step <= 12; $step++)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $step === 1 ? 'active' : '' }}" id="pills-step-{{ $step }}-tab" 
                            data-toggle="pill" href="#pills-step-{{ $step }}" role="tab" aria-controls="pills-step-{{ $step }}" 
                            aria-selected="{{ $step === 1 ? 'true' : 'false' }}">
                                Step {{ $step }}
                            </a>
                        </li>
                    @endfor
                </ul>
            </div>

            <!-- Step Content (Step 1 Form with Card) -->
            <div class="tab-content" id="pills-tabContent">
                @for ($step = 1; $step <= 12; $step++)
                    <div class="tab-pane fade {{ $step === 1 ? 'show active' : '' }}" id="pills-step-{{ $step }}" role="tabpanel" aria-labelledby="pills-step-{{ $step }}-tab">
                        @if ($step === 1)
                            <!-- Step 1 Form: Adviser Appointment Form in a Card -->
                            <div class="card shadow mb-4">
                            <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('gsstudent.route1.submit') }}">
                                            @csrf

                                            <h4>Appointment Details</h4>

                                            <!-- Date -->
                                            <div class="form-group">
                                                <label for="date">Date:</label>
                                                <input type="text" name="date" value="{{ now()->toDateString() }}" class="form-control" readonly>
                                            </div>

                                            <!-- Program -->
                                            <div class="form-group">
                                                <label for="program">Program:</label>
                                                <input type="text" name="program" value="{{ $user->program }}" class="form-control" readonly>
                                            </div>

                                            <!-- Adviser Display (Read-only for students) -->
                                            <div class="form-group">
                                                <label for="adviser">Adviser:</label>
                                                @if ($appointment && $appointment->status === 'approved')
                                                    <input type="text" class="form-control" value="{{ $appointment->adviser->name }}" readonly>
                                                @elseif ($appointment && $appointment->status === 'pending')
                                                    <input type="text" class="form-control" value="Waiting for your adviser to approve." readonly>
                                                @elseif ($appointment && $appointment->status === 'disapproved')
                                                    <input type="text" class="form-control" value="Your adviser denied the request. The Program Chair is still looking for a new adviser for you." readonly>
                                                @else
                                                    <input type="text" class="form-control" value="Adviser will be assigned by the Program Chair." readonly>
                                                @endif
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h4>Signatures</h4>

                                        <!-- Adviser Signature -->
                                        <div class="form-group">
                                            <label for="adviser_signature">Adviser Signature:</label>
                                            <input type="text" name="adviser_signature" class="form-control" value="{{ $appointment->adviser_signature ?? 'Pending' }}" readonly>
                                        </div>

                                        <!-- Program Chair Signature -->
                                        <div class="form-group">
                                            <label for="program_chair_signature">Program Chair Signature:</label>
                                            <input type="text" name="program_chair_signature" class="form-control" value="{{ $appointment->chair_signature ?? 'Pending' }}" readonly>
                                        </div>

                                        <!-- Dean Signature -->
                                        <div class="form-group">
                                            <label for="dean_signature">Dean Signature:</label>
                                            <input type="text" name="dean_signature" class="form-control" value="{{ $appointment->dean_signature ?? 'Pending' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <p>Step {{ $step }} content goes here.</p>
                @endif
            </div>
        @endfor
    </div>
    </div>
            <div class="card-footer footersaroute1">
            </div>
</div>
@endsection

<!-- Custom Styling for Multi-Step Navigation and Card -->
