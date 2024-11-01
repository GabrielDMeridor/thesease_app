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
                    @for ($step = 1; $step <= 10; $step++) <!-- Adjust step numbers as needed -->
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
                @for ($step = 1; $step <= 10; $step++) <!-- Adjust step numbers as needed -->
                    <div class="tab-pane fade {{ $step === 1 ? 'show active' : '' }}" id="pills-step-{{ $step }}" role="tabpanel" aria-labelledby="pills-step-{{ $step }}-tab">
                        @if ($step === 1)
                            <!-- Step 1: Routing Form -->
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.sign', $student->id) }}">
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
                            @elseif ($step === 2)
                    <!-- Step 2: View Consultation Dates (no ability to add) -->
                    @if (is_null($appointment->adviser_signature) || is_null($appointment->chair_signature) || is_null($appointment->dean_signature))
                        <!-- Step is locked: Display the lock message -->
                        <p class="text-muted">Step 2 is locked. The signatures for the Adviser, Program Chair, and Dean must be completed to proceed.</p>
                    @else
                        <!-- Step 2 is unlocked: Show the consultation dates and signatures -->
                        <div class="container-fluid">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <h4>Consultation Dates and Adviser Endorsement</h4>

                                    <!-- Display Consultation Dates -->
                                    <div class="form-group">
                                        <label for="consultation_dates">Consultation Dates:</label>
                                        <div id="consultation_dates_container">
                                            @if ($appointment->consultation_dates)
                                                @foreach (json_decode($appointment->consultation_dates) as $date)
                                                    <div class="input-group mb-2">
                                                        <input type="date" name="consultation_dates[]" class="form-control" value="{{ $date }}" readonly>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p>No consultation dates set yet.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Display Adviser Endorsement Signature -->
                                    <div class="form-group">
                                        <label for="adviser_endorsement_signature">Adviser Endorsement Signature:</label>
                                        <input type="text" name="adviser_endorsement_signature" class="form-control" value="{{ $appointment->adviser_endorsement_signature ?? 'Pending' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @elseif ($step === 3)
    <!-- Step 3: View Similarity Manuscript and Certificate -->
    @if(is_null($appointment->adviser_endorsement_signature))
        <!-- Lock Step 3 if adviser's endorsement signature is not present -->
        <p class="text-muted">Step 3 is locked. Please ensure the adviser's endorsement signature is completed in Step 2 to proceed.</p>
    @else
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <h1>Similarity Check</h1>
                <div class="card-body">
                    <h4>Uploaded Similarity Manuscript</h4>

                    @if($appointment->similarity_manuscript)
                        <!-- Link to open manuscript modal -->
                        <a href="#" data-toggle="modal" data-target="#manuscriptModal">
                            {{ basename($appointment->similarity_manuscript) }}
                        </a>
                    @else
                        <p class="text-muted">No manuscript uploaded yet.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4>Similarity Check Results</h4>
                    
                    @if($appointment->similarity_certificate)
                        <!-- Link to open certificate modal -->
                        <a href="#" data-toggle="modal" data-target="#certificateModal">
                            {{ basename($appointment->similarity_certificate) }}
                        </a>
                    @else
                        <p>Please wait for the librarian to upload the certificate.</p>
                    @endif
                </div>
            </div>
        </div>

    <!-- Manuscript Modal -->
    <div class="modal fade" id="manuscriptModal" tabindex="-1" aria-labelledby="manuscriptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manuscriptModalLabel">View Manuscript</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe src="{{ Storage::url($appointment->similarity_manuscript) }}" width="100%" height="600px" style="border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <a href="{{ Storage::url($appointment->similarity_manuscript) }}" target="_blank" class="btn btn-primary" download>Download Manuscript</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Modal -->
    <div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="certificateModalLabel">View Certificate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe src="{{ Storage::url($appointment->similarity_certificate) }}" width="100%" height="600px" style="border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <a href="{{ Storage::url($appointment->similarity_certificate) }}" target="_blank" class="btn btn-primary" download>Download Certificate</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endif
@elseif ($step === 4)
    <!-- Step 4: Research Registration -->
    @if(is_null($appointment->similarity_certificate))
        <!-- Lock Step 4 if Similarity Certificate is Null -->
        <p class="text-muted">Step 4 is locked. The Similarity Certificate must be uploaded in Step 3 to proceed.</p>
    @else
<div class="container-fluid">
    <!-- Research Registration Content Here -->
    <div class="card shadow mb-4">
        <div class="card-body d-flex align-items-center justify-content-between">
            <!-- QR Code Section -->
            <div class="qr-code-section">
                <!-- Placeholder for QR Code -->
                <img src="{{ asset('img/qr_code.png') }}" alt="QR Code" class="qr-code-image">
            </div>

            <!-- Instructions Section -->
            <div class="instructions-section ml-4">
                <h5><strong>Research Registration</strong></h5>
                <p>Adviser accomplishes the <a href="https://docs.google.com/forms/d/e/1FAIpQLSeT2G_Ap-A2PrFS-WW3E4GwP38SGaLbnvQBCtr4SniWo8YQlA/viewform" target="_blank">Research Registration Form</a>. 
                   Note that the primary author will be the student, and the adviser will be the co-author. 
                   A copy of the form responses will be sent to the adviser’s email.</p>
                <p>After completing the form, please forward the copy and the manuscript to the following emails:
                    <br><strong>cdaic@auf.edu.ph</strong> (cc: <strong>ovpri@auf.edu.ph</strong>, <strong>collegesecretary.gs@auf.edu.ph</strong>).</p>

                <!-- Display Status for OVPRI Approval -->
                <p><strong>Status:</strong> 
                    @if ($appointment->ovpri_approval === 'approved')
                        <span class="text-success">Already approved by OVPRI.</span>
                    @elseif ($appointment->ovpri_approval === 'pending')
                        <span class="text-warning">Pending OVPRI approval.</span>
                    @else
                        <span class="text-muted">Not yet responded.</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
    @endif



    @elseif ($step === 5 && $isDrPH)
                            <!-- Step 5 Content specifically for DrPH students -->
                            <div class="container-fluid">
                                <div class="card shadow mb-4">
                                    <div class="card-body">
                                        <h5><strong>Community Extension Registration (For DrPH students only)</strong></h5>
                                        @if ($appointment->community_extension_link)
                                            <p><strong>Form Link:</strong> 
                                                <a href="{{ $appointment->community_extension_link }}" target="_blank">
                                                    {{ $appointment->community_extension_link }}
                                                </a>
                                            </p>
                                        @else
                                            <form action="{{ route('superadmin.uploadCommunityExtensionLink', $student->id) }}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="community_extension_link">Community Extension Link:</label>
                                                    <input type="url" name="community_extension_link" class="form-control" required placeholder="Enter the form link">
                                                </div>
                                                <button type="submit" class="btn btn-primary">Upload Link</button>
                                            </form>
                                        @endif

                                        @if ($appointment->community_extension_response)
                                        <p><strong>Status:</strong> 
                                            Responded on {{ optional($appointment->community_extension_response_date)->format('F j, Y') }}
                                        </p>
                                        @else
                                            <p class="text-muted">Student has not responded yet.</p>
                                        @endif

                                        <p><strong>Approval Status:</strong> 
                                    @if ($appointment->community_extension_approval === 'approved')
                                        <span class="text-success">Approved</span>
                                    @elseif ($appointment->community_extension_approval === 'pending')
                                        <span class="text-warning">Pending</span>
                                    @else
                                        <span class="text-muted">Not yet responded.</span>
                                    @endif
                                </p>

                                <!-- SuperAdmin Approval Button (only if approval is pending) -->
                                @if ($appointment->community_extension_approval === 'pending')
                                    <form action="{{ route('graduateschool.approveCommunityExtension', $student->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">Approve Community Extension</button>
                                    </form>
                                @endif
                                    </div>
                                </div>
                            </div>

                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <!-- Card footer added here as requested -->
        <div class="card-footer footersaroute1"></div>
    </div>
</div>
@endsection