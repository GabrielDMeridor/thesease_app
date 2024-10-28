@extends('graduateschool.GSmain-layout')

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

@section('body')
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div>
    <br>

    <div class="card shadow mb-4">
        <div class="card-header">
        </div>
        <div class="card-body">
            <!-- Multi-Step Navigation -->
            <div class="steps">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    @for ($step = 1; $step <= 12; $step++) <!-- Adjust step numbers as needed -->
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

            <!-- Step Content -->
            <div class="tab-content contentsaroute" id="pills-tabContent">
                @for ($step = 1; $step <= 5; $step++) <!-- Adjust step numbers as needed -->
                    <div class="tab-pane fade {{ $step === 1 ? 'show active' : '' }}" id="pills-step-{{ $step }}" role="tabpanel" aria-labelledby="pills-step-{{ $step }}-tab">
                        @if ($step === 1)
                            <!-- Step 1: Routing Form -->
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('graduateschool.sign', $student->id) }}">
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
                                            <input type="text" name="program" value="{{ $student->program }}" class="form-control" readonly>
                                        </div>

                                        <hr>

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

                                        <!-- Dean Signature (SuperAdmin) -->
                                        <div class="form-group">
                                            <label for="dean_signature">Dean Signature:</label>
                                            <input type="text" name="dean_signature" class="form-control" value="{{ $appointment->dean_signature ?? 'Pending' }}" readonly>
                                        </div>
                                    </form>
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
</div>
@endsection
