@extends('tdprofessor.TDPmain-layout')

@section('content-header')
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

            <!-- Dropdown - Notifications -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Notifications Center</h6>

                @foreach (auth()->user()->unreadNotifications as $notification)
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="mr-3">
                            <div class="icon-circle bg-danger">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                            <span class="font-weight-bold">{{ $notification->data['message'] }}</span>
                            <p>{{ $notification->data['reason'] }}</p> <!-- Display the reason for disapproval -->
                        </div>
                    </a>
                @endforeach

                <a class="dropdown-item text-center small text-gray-500" href="#">Mark all as read</a>
            </div>
        </li>
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Info -->
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

<!-- Title Section -->
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div> <!-- Title like "Routing Form 1 for student_name" -->
    <br>
</div>

<!-- Multi-Step Navigation -->
<div class="container-fluid">
    <div class="steps">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            @for ($step = 1; $step <= 12; $step++)  <!-- Adjust the number of steps as needed -->
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
</div>

<!-- Step Content -->
<div class="tab-content" id="pills-tabContent">
    @for ($step = 1; $step <= 3; $step++)  <!-- Adjust the number of steps as needed -->
        <div class="tab-pane fade {{ $step === 1 ? 'show active' : '' }}" id="pills-step-{{ $step }}" role="tabpanel" aria-labelledby="pills-step-{{ $step }}-tab">
            @if ($step === 1)
                <!-- Step 1: Adviser Form Content -->
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <form method="POST" action="{{ route('professor.signRoutingForm', $appointment->id) }}">
                                @csrf
                                @method('PUT')

                                <!-- Date -->
                                <div class="form-group">
                                    <label for="date">Date:</label>
                                    <input type="text" name="date" value="{{ $appointment->created_at->toDateString() }}" class="form-control" readonly>
                                </div>

                                <!-- Program -->
                                <div class="form-group">
                                    <label for="program">Program:</label>
                                    <input type="text" name="program" value="{{ $advisee->program }}" class="form-control" readonly>
                                </div>

                                <!-- Adviser Type -->
                                <div class="form-group">
                                    <label for="appointment_type">Adviser Type:</label>
                                    <input type="text" name="appointment_type" value="{{ $appointment->appointment_type }}" class="form-control" readonly>
                                </div>

                                <hr>

                                <!-- Signatures -->
                                <h5>Signatures</h5>

                                <!-- Adviser Signature -->
                                <div class="form-group">
                                    <label for="adviser_signature">Adviser Signature:</label>
                                    @if (is_null($appointment->adviser_signature))
                                        <input type="text" name="adviser_signature" class="form-control" placeholder="Sign here">
                                    @else
                                        <input type="text" name="adviser_signature" class="form-control" value="{{ $appointment->adviser_signature }}" readonly>
                                    @endif
                                </div>

                                <!-- Program Chair Signature -->
                                <div class="form-group">
                                    <label for="chair_signature">Program Chair Signature:</label>
                                    <input type="text" name="chair_signature" class="form-control" value="{{ $appointment->chair_signature ?? 'Pending' }}" readonly>
                                </div>

                                <!-- Dean Signature -->
                                <div class="form-group">
                                    <label for="dean_signature">Dean Signature:</label>
                                    <input type="text" name="dean_signature" class="form-control" value="{{ $appointment->dean_signature ?? 'Pending' }}" readonly>
                                </div>

                                <!-- Submit Button for Signature -->
                                @if (is_null($appointment->adviser_signature))
                                    <button type="submit" class="btn btn-success">Affix Adviser's Signature</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <p>Step {{ $step }} content goes here.</p>
            @endif
        </div>
    @endfor
</div>

@endsection

<!-- Custom Styling for Multi-Step Navigation -->
<style>
    .steps ul {
        display: flex;
        justify-content: space-between;
    }
    .steps ul .nav-item {
        flex: 1;
        text-align: center;
    }
    .steps ul .nav-link {
        padding: 10px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0;
        color: #495057;
    }
    .steps ul .nav-link.active {
        background-color: #007bff;
        color: #fff;
    }

    .card-body {
        padding: 20px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
