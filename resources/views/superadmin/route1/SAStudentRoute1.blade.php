@extends('superadmin.SAmain-layout')

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
                                <span>{{ $notification->data['message'] }}</span>
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
    <div class="sagreet">{{ $title }}</div>
    <br>

    
    <div class="card shadow mb-4">
        <div class="card-header">
        </div>
        <div class="card-body">
        @php
            $isDrPH = $student->program === 'DRPH-HPE';
            $totalSteps = $isDrPH ? 9 : 8; // 9 steps for DrPH, 8 for others
        @endphp

<!-- Multi-Step Navigation -->
<div class="steps">
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        @for ($step = 1; $step <= $totalSteps; $step++)
            <li class="nav-item">
                <a class="nav-link {{ $step === 1 ? 'active' : '' }}" id="pills-step-{{ $step }}-tab"
                   data-toggle="pill" href="#pills-step-{{ $step }}" role="tab"
                   aria-controls="pills-step-{{ $step }}" aria-selected="{{ $step === 1 ? 'true' : 'false' }}">
                    Step {{ $step }}
                </a>
            </li>
        @endfor
    </ul>
</div>

            <!-- Step Content -->
<!-- Step Content -->
<div class="tab-content" id="pills-tabContent">
    @for ($step = 1; $step <= $totalSteps; $step++)
        <div class="tab-pane fade {{ $step === 1 ? 'show active' : '' }}" id="pills-step-{{ $step }}"
             role="tabpanel" aria-labelledby="pills-step-{{ $step }}-tab">

                        @if ($step === 1)
                            <!-- Step 1: Routing Form -->
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('superadmin.sign', $student->id) }}">
                                        @csrf

                                        <h4 class="routing-heading">Appointment Details</h4>
                                        
                                        <!-- Date Display -->
                                        <div class="form-group">
                                            <label for="date">Date:</label>
                                            @if ($appointment && $appointment->completed_at)
                                                <input type="text" name="date" value="{{ $appointment->completed_at->toDateString() }}" class="form-control" readonly>
                                            @else
                                                <input type="text" name="date" value="{{ now()->toDateString() }}" class="form-control" readonly>
                                            @endif
                                        </div>



                                        <!-- Program -->
                                        <div class="form-group">
                                            <label for="program">Program:</label>
                                            <input type="text" name="program" value="{{ $student->program }}" class="form-control" readonly>
                                        </div>

                                        <hr>

                                        <h4 class="routing-heading">Signatures</h4>

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

                                        <!-- Signature Affix Button -->
                                        @if (is_null($appointment->dean_signature))
                                            <button type="submit" class="btn btn-success btn-affix">Affix Dean's Signature</button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                            @elseif ($step === 2)
                    <!-- Step 2: View Consultation Dates (no ability to add) -->
                    @if (is_null($appointment->adviser_signature) || is_null($appointment->chair_signature) || is_null($appointment->dean_signature))
                        <!-- Step is locked: Display the lock message -->
                        <div class="container d-flex justify-content-center my-4">
                            <div style="width: 100%;">
                                <div class="card-body text-center">
                                    <div class="alert alert-warning mb-0" role="alert">
                                        <i class="fas fa-lock mr-2"></i>
                                        <strong>Step Locked:</strong> Step 2 is locked. The signatures for the Adviser, Program Chair, and Dean must be completed to proceed.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Step 2 is unlocked: Show the consultation dates and signatures -->
                        <div class="container-fluid">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <h4 class="routing-heading">Consultation Dates and Adviser Endorsement</h4>

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
        <div class="container d-flex justify-content-center my-4">
    <div style="width: 100%;">
        <div class="card-body text-center">
            <div class="alert alert-warning mb-0" role="alert">
                <i class="fas fa-lock mr-2"></i>
                <strong>Step Locked:</strong> Step 3 is locked. Please ensure the adviser's endorsement signature is completed in Step 2 to proceed.
            </div>
        </div>
    </div>
</div>
    @else
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <h3 class="routing-heading" style="font-size: 60px; text-align:center;">Similarity Check</h3>
                <div class="card-body">
                    <h4 class="routing-heading"> Uploaded Similarity Manuscript</h4>

                    @if($appointment->similarity_manuscript)
                    <div class="form-group">
                        <label for="manuscript">Uploaded Manuscript:</label>

                        <i class="fa-solid fa-download"></i>
                        
                        <input type="text" 
                        
                            id="manuscript" 
                            class="form-control" 
                            value="{{ basename($appointment->similarity_manuscript) }}" 
                            readonly 
                            onclick="$('#manuscriptModal').modal('show')" 
                            style="cursor: pointer;">
                    </div>

                    @else
                    <div class="form-group">
                        <label>Uploaded Manuscript:</label>
                        <input type="text" 
                            class="form-control text-muted" 
                            value="No manuscript uploaded yet." 
                            readonly>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4 class="routing-heading">Similarity Check Results</h4>
                    
                    @if($appointment->similarity_certificate)
                    <!-- Read-only input to open certificate modal -->
                    <div class="form-group">
                        <label for="similarity_certificate">Uploaded Certificate:</label>
                        <i class="fa-solid fa-download"></i>
                        <input type="text" 
                            id="similarity_certificate" 
                            class="form-control" 
                            value="{{ basename($appointment->similarity_certificate) }}" 
                            readonly 
                            onclick="$('#certificateModal').modal('show')" 
                            style="cursor: pointer;">
                    </div>
                @else
                    <!-- Read-only input with message -->
                    <div class="form-group">
                        <label>Certificate Status:</label>
                        <input type="text" 
                            class="form-control text-muted" 
                            value="Please wait for the librarian to upload the certificate." 
                            readonly>
                    </div>
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
        <div class="container d-flex justify-content-center my-4">
    <div style="width: 100%;">
        <div class="card-body text-center">
            <div class="alert alert-warning mb-0" role="alert">
                <i class="fas fa-lock mr-2"></i>
                <strong>Step Locked:</strong> Step 4 is locked. The Similarity Certificate must be uploaded in Step 3 to proceed.
            </div>
        </div>
    </div>
</div>
    @else
    <div class="container my-4">
    <!-- Research Registration Content Here -->
    <div class="card shadow mb-4">
        <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between">
            <!-- QR Code Section -->
            <div class="qr-code-section text-center mb-4 mb-md-0">
                <!-- Placeholder for QR Code -->
                <img src="{{ asset('img/qr_code.png') }}" alt="QR Code" class="qr-code-image rounded" style="width: 150px; border: 2px solid #ddd;">
                <p class="mt-2 text-muted" style="font-size: 0.9rem;">Scan for Registration Form</p>
            </div>

            <!-- Instructions Section -->
            <div class="instructions-section ml-md-4">
                <h4 class="routing-heading">Research Registration</h4>
                <p>Adviser accomplishes the 
                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSeT2G_Ap-A2PrFS-WW3E4GwP38SGaLbnvQBCtr4SniWo8YQlA/viewform" target="_blank" class="text-decoration-underline text-primary">
                    <i class="fa-solid fa-link"></i>
                    Research Registration Form. 
                    </a>
                    Note that the primary author will be the student, and the adviser will be the co-author. A copy of the form responses will be sent to the adviser’s email.
                </p>
                <p>After completing the form, please forward the copy and the manuscript to the following emails:
                    <br><strong>cdaic@auf.edu.ph</strong> (cc: <strong>ovpri@auf.edu.ph</strong>, <strong>collegesecretary.gs@auf.edu.ph</strong>).
                </p>

                <!-- Display Status for OVPRI Approval -->
                <p><strong>Status:</strong> 
                    @if ($appointment->ovpri_approval === 'approved')
                        <span class="badge badge-success">Already approved by OVPRI.</span>
                    @elseif ($appointment->ovpri_approval === 'pending')
                        <span class="badge badge-warning">Pending OVPRI approval.</span>
                    @else
                        <span class="badge badge-secondary">Not yet responded.</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

    @endif


    @elseif ($step === 5 && $isDrPH)
    @if ($appointment->ovpri_approval !== 'approved')
    <div class="container d-flex justify-content-center my-4">
        <div style="width: 100%;">
            <div class="card-body text-center">
                <div class="alert alert-warning mb-0" role="alert">
                    <i class="fas fa-lock mr-2"></i>
                    <strong>Step Locked:</strong> Step 5 is locked. The OVPRI Approval must be completed in Step 4 to proceed.
                </div>
            </div>
        </div>
    </div>

                @else
                <div class="container my-4">
        <!-- Step 5 Content specifically for DrPH students -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5 class="font-weight-bold mb-3">Community Extension Registration (For DrPH students only)</h5>
                
                @if ($appointment->community_extension_link)
                    <p><strong>Form Link:</strong> 
                        <a href="{{ $appointment->community_extension_link }}" target="_blank" class="text-primary">
                            {{ $appointment->community_extension_link }}
                        </a>
                    </p>
                @else
                    <form action="{{ route('superadmin.uploadCommunityExtensionLink', $student->id) }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="community_extension_link">Community Extension Link:</label>
                            <input type="url" name="community_extension_link" class="form-control" required placeholder="Enter the form link">
                        </div>
                        <button type="submit" class="btn btn-primary">Upload Link</button>
                    </form>
                @endif

                <hr class="my-4">

                @if ($appointment->community_extension_response)
                    <p><strong>Status:</strong> 
                        Responded
                    </p>
                @else
                    <p class="badge badge-secondary">Student has not responded yet.</p>
                @endif

                <p><strong>Approval Status:</strong> 
                    @if ($appointment->community_extension_approval === 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif ($appointment->community_extension_approval === 'pending')
                        <span class="badge badge-warning">Pending</span>
                    @else
                        <span class="badge badge-secondary">Not yet responded.</span>
                    @endif
                </p>

                <!-- SuperAdmin Approval Button (only if approval is pending) -->
                @if ($appointment->community_extension_approval === 'pending')
                    <form action="{{ route('superadmin.approveCommunityExtension', $student->id) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-primary">Approve Community Extension</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

                @endif
                @elseif (($step === 5 && !$isDrPH) || ($step === 6 && $isDrPH))
                @if (($isDrPH && optional($appointment)->community_extension_approval !== 'approved') ||
         (!$isDrPH && optional($appointment)->ovpri_approval !== 'approved'))
        {{-- Display lock message based on the type of approval needed --}}
        <div class="container d-flex justify-content-center my-4">
                <div style="width: 100%;">
                    <div class="card-body text-center">
                        <div class="alert alert-warning mb-0" role="alert">
                            <p>
                            <i class="fas fa-lock mr-2"></i>
                    This step is locked. 
                    @if ($isDrPH)
                        Community Extension approval must be completed in Step 5 to proceed.
                    @else
                        OVPRI approval must be completed in Step 4 to proceed.
                    @endif
                </p>
                        </div>
                    </div>
                </div>
            </div>

    @else
                <div class="container-fluid my-4">
    <div class="row">
        <!-- File Uploads Section -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="routing-heading">File Uploads</h4>
                    <!-- Signed Routing Form 1 -->
                    <div class="form-group mb-3">
                        <label for="signed_routing_form_1">Signed Routing Form 1</label>
                        <i class="fa-solid fa-download"></i>
                        @if($appointment->signed_routing_form_1)
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $appointment->original_signed_routing_form_1 }}" 
                                   readonly 
                                   onclick="$('#routingFormModal').modal('show')" 
                                   style="cursor: pointer;">
                        @else
                            <input type="text" name="date" class="form-control" readonly placeholder="File not yet uploaded">
                        @endif
                    </div>

                    <!-- Proposal Manuscript -->
                    <div class="form-group mb-3">
                        <label for="proposal_manuscript">Proposal Manuscript</label>
                        <i class="fa-solid fa-download"></i>
                        @if($appointment->proposal_manuscript)
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $appointment->original_proposal_manuscript }}" 
                                   readonly 
                                   onclick="$('#proposalManuscriptModal').modal('show')" 
                                   style="cursor: pointer;">
                        @else
                            <input type="text" name="date" class="form-control" readonly placeholder="File not yet uploaded">
                        @endif
                    </div>

                    <!-- Video Presentation -->
                    <div class="form-group">
                        <label for="proposal_video_presentation">Video Presentation</label>
                        <i class="fa-solid fa-download"></i>
                        @if($appointment->proposal_video_presentation)
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $appointment->original_proposal_video_presentation }}" 
                                   readonly 
                                   onclick="$('#videoPresentationModal').modal('show')" 
                                   style="cursor: pointer;">
                        @else
                            <input type="text" name="date" class="form-control" readonly placeholder="File not yet uploaded">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Files Section -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="routing-heading">Submission Files</h4>
                    
                    @if ($appointment->submission_files_link)
                    <p>
                        <a href="{{ $appointment->submission_files_link }}" target="_blank" class="text-primary" style="font-size: 1.25rem;">
                        <i class="fa-solid fa-link"></i>
                            {{ $appointment->submission_files_link }}
                        </a>
                    </p>
                    @else
                        <!-- Form for SuperAdmin to upload the submission files link -->
                        <form action="{{ route('superadmin.uploadSubmissionFilesLink', $student->id) }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="submission_files_link">Submission Files Link:</label>
                                <input type="url" name="submission_files_link" class="form-control" required placeholder="Enter the form link">
                            </div>
                            <button type="submit" class="btn btn-primary">Upload Link</button>
                        </form>
                    @endif

                    <hr class="my-4">

                    <!-- Display submission files response status -->
                    @if ($appointment->submission_files_response)
                        <p><strong>Status:</strong> 
                            Responded on {{ optional($appointment->submission_files_response_date)->format('F j, Y') }}
                        </p>
                    @else
                        <p class="text-muted">Student has not responded yet.</p>
                    @endif

                    <!-- Display approval status for submission files -->
                    <p><strong>Approval Status:</strong> 
                        @if ($appointment->submission_files_approval === 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif ($appointment->submission_files_approval === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-secondary">Not yet responded.</span>
                        @endif
                    </p>

                    <!-- SuperAdmin Approval Button (only if approval is pending) -->
                    @if ($appointment->submission_files_approval === 'pending')
                        <form action="{{ route('superadmin.approveSubmissionFiles', $student->id) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-primary">Approve Submission Files</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif


@elseif (($step === 6 && !$isDrPH) || ($step === 7 && $isDrPH))
    @if (optional($appointment)->proposal_defense_date === null)
        <!-- Step Locked Message in a Centered Card -->
        <div class="container-fluid d-flex justify-content-center align-items-center" style="height: 100%;">
            <div class="card" style="width: 50%; text-align: center;">
                <div class="card-body">
                    <p class="text-muted" style="font-size: 1.25rem;">This step is locked. A proposal defense date must be set to proceed.</p>
                </div>
            </div>
        </div>
    @else
    <div class="card shadow mb-4">
            <div class="card-body text-center">
                <div class="alert alert-warning mb-0" role="alert">
                    <strong>Please look for the student in the monitoring form page</strong>
                </div>
            </div>
        </div>  
    @endif

    @elseif (($step === 7 && !$isDrPH) || ($step === 8 && $isDrPH))
    <div class="container-fluid">
    @if (is_null($appointment) || is_null(optional($appointment)->dean_monitoring_signature))
        <!-- Step Locked Message -->
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <div class="alert alert-warning mb-0" role="alert">
                    <i class="fas fa-lock mr-2"></i>
                    <strong>Step Locked:</strong> This step is locked. The Dean's signature in Monitoring Form must be completed to proceed.
                </div>
            </div>
        </div>
    @elseif (optional($appointment)->dean_monitoring_signature)
        <!-- Consultation with Statistician Content -->
        <div class="container my-4">
            <div class="card shadow mb-4">
                <div class="card-body d-flex flex-column flex-md-row align-items-center">
                    <!-- QR Code Section -->
                    <div class="qr-code-section text-center mb-4 mb-md-0 d-flex flex-column align-items-center">
                        <img src="{{ asset('img/cdaic_qr.png') }}" alt="QR Code for CDAIC Service Request" class="qr-code-image rounded" style="width: 150px; border: 2px solid #ddd;">
                        <p class="mt-2 text-muted" style="font-size: 0.9rem;">Scan for Service Request Form</p>
                    </div>

                    <!-- Instructions Section -->
                    <div class="instructions-section ml-md-4">
                        <h4 class="routing-heading mb-3">Consultation with Statistician</h4>
                        <p>Please complete the 
                            <a href="https://docs.google.com/forms/d/e/1FAIpQLSezh_2LK83Yh435RFQ879axmNE7B761ifHd1ML4vZz54j8GSw/viewform" target="_blank" class="text-decoration-underline text-primary">
                                <i class="fa-solid fa-link"></i> CDAIC Service Request Form.
                            </a>
                        </p>
                        <p>Then, send your manuscript to: 
                            <strong>cdaic@auf.edu.ph</strong>, with cc to:
                        </p>
                        <ul class="mb-3" style="list-style: none; padding-left: 0;">
                            <li><strong>calibio.mylene@auf.edu.ph</strong></li>
                            <li><strong>ovpri@auf.edu.ph</strong></li>
                        </ul>

                        <!-- Display Status -->
                        <p><strong>Status:</strong> 
                            @if (optional($appointment)->statistician_approval === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif (optional($appointment)->student_statistician_response === 'responded')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-secondary">Not responded yet</span>
                            @endif
                        </p>

                    </div>
                </div>
            </div>
        </div>

    @endif
    </div>
    @elseif (($step === 8 && !$isDrPH) || ($step === 9 && $isDrPH))

<!-- Check if the Statistician Approval is "approved" -->
@if (optional($appointment)->statistician_approval !== 'approved')
    <!-- Step Locked Message -->
    <div class="card shadow mb-4">
        <div class="card-body text-center">
            <div class="alert alert-warning mb-0" role="alert">
                <i class="fas fa-lock mr-2"></i>
                <strong>Step Locked:</strong> This step is locked. Statistician approval must be completed to proceed.
            </div>
        </div>
    </div>
@else
    <!-- Display Content for Step 8 or 9 without Upload Options -->
    <div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h4 class="routing-heading">Ethics Review</h4>
            <p>Here is the current status of the student's submissions for the Ethics Review:</p>

            <!-- Ethics Review Document Table -->
            <div class="table-responsive">
                <table class="table table-bordered custom-table">
                    <thead class="table-light">
                        <tr>
                            <th>Document</th>
                            <th class="text-center">Form Link</th>
                            <th class="text-center">Submission Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Approved Proposal Manuscript -->
                        <tr>
                            <td>Approved Proposal Manuscript</td>
                            <td class="text-center">
                                @php
                                    $updates = $appointment->proposal_manuscript_updates ? json_decode($appointment->proposal_manuscript_updates, true) : null;
                                @endphp
                                @if($updates && isset($updates['original_name']))
                                    <a href="#" data-toggle="modal" data-target="#approvedProposalModal">{{ $updates['original_name'] }}</a>
                                @else
                                    <span class="text-muted">Not uploaded yet</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $updates ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $updates ? 'Uploaded' : 'Not Uploaded' }}
                                </span>
                            </td>
                        </tr>

                        <!-- Proof of Payment for Ethics Review -->
                        <tr>
                            <td>Proof of Payment for Ethics Review</td>
                            <td class="text-center">
                                @if($appointment->ethics_proof_of_payment)
                                    <a href="#" data-toggle="modal" data-target="#ethicsProofOfPaymentModal">{{ $appointment->ethics_proof_of_payment_filename }}</a>
                                @else
                                    <span class="text-muted">Not uploaded yet</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $appointment->ethics_proof_of_payment ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $appointment->ethics_proof_of_payment ? 'Uploaded' : 'Not Uploaded' }}
                                </span>
                            </td>
                        </tr>

                        <!-- Curriculum Vitae -->
                        <tr>
                            <td>Curriculum Vitae</td>
                            <td class="text-center">
                                @if($appointment->ethics_curriculum_vitae)
                                    <a href="#" data-toggle="modal" data-target="#ethicsCurriculumVitaeModal">{{ $appointment->ethics_curriculum_vitae_filename }}</a>
                                @else
                                    <span class="text-muted">Not uploaded yet</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $appointment->ethics_curriculum_vitae ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $appointment->ethics_curriculum_vitae ? 'Uploaded' : 'Not Uploaded' }}
                                </span>
                            </td>
                        </tr>

                        <!-- Dynamic Rows for Document Types with External Links -->
                        @php
                            $documents = [
                                'Research Services Form' => [
                                    'filename' => 'ethics_research_services_form_filename',
                                    'modal' => '#researchServicesFormModal',
                                    'upload_field' => 'ethics_research_services_form',
                                    'download_url' => 'https://docs.google.com/document/d/1F_R6DuTVo9nAf511_p27pjDUiWxcNg8G/edit',
                                ],
                                'Application for Ethics Review Form' => [
                                    'filename' => 'ethics_application_form_filename',
                                    'modal' => '#ethicsApplicationFormModal',
                                    'upload_field' => 'ethics_application_form',
                                    'download_url' => 'https://docs.google.com/document/d/1GejrV-uzjxxcGzrMAB3Hd3lrsKUgm5If/edit',
                                ],
                                'Study Protocol Assessment Form' => [
                                    'filename' => 'ethics_study_protocol_form_filename',
                                    'modal' => '#studyProtocolFormModal',
                                    'upload_field' => 'ethics_study_protocol_form',
                                    'download_url' => 'https://docs.google.com/document/d/10USbYR70sEOJVqMlU--XJCRNRdJF49cV/edit',
                                ],
                                'Informed Consent Assessment Form' => [
                                    'filename' => 'ethics_informed_consent_form_filename',
                                    'modal' => '#informedConsentFormModal',
                                    'upload_field' => 'ethics_informed_consent_form',
                                    'download_url' => 'https://docs.google.com/document/d/1ZIMShedZvcomR61CRjIN5yN0AAuLf2Jr/edit?rtpof=true&sd=true',
                                ],
                                'Sample Informed Consent' => [
                                    'filename' => 'ethics_sample_informed_consent_filename',
                                    'modal' => '#sampleInformedConsentModal',
                                    'upload_field' => 'ethics_sample_informed_consent',
                                    'download_url' => 'https://docs.google.com/document/d/1b0d-RdVu0iierQVoPoyLslKISQo0z6Ds/edit',
                                ],
                            ];
                        @endphp

                        @foreach($documents as $doc => $data)
                            <tr>
                                <td>{{ $doc }}</td>
                                <td class="text-center">
                                    <a href="{{ $data['download_url'] }}" target="_blank">Download Form</a>
                                    @if($appointment->{$data['upload_field']})
                                        <a href="#" data-toggle="modal" data-target="{{ $data['modal'] }}">
                                            {{ $appointment->{$data['filename']} }}
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded yet</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $appointment->{$data['upload_field']} ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $appointment->{$data['upload_field']} ? 'Uploaded' : 'Not Uploaded' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- AUFC Status -->
            <p><strong>AUFC Status:</strong> 
                <span class="badge {{ $appointment->aufc_status === 'approved' ? 'badge-success' : ($appointment->aufc_status === 'pending' ? 'badge-warning' : 'badge-secondary') }}">
                    {{ $appointment->aufc_status === 'approved' ? 'Approved' : ($appointment->aufc_status === 'pending' ? 'Pending' : 'Not Sent') }}
                </span>
            </p>
        </div>
    </div>
</div>

@endif
@endif

   </div>
                @endfor
            </div>
        </div>
        <div class="card-footer footersaroute1"></div>
    </div>
                </div>

    <!-- Modals for Uploaded Files -->
    <div class="modal fade" id="routingFormModal" tabindex="-1" aria-labelledby="routingFormModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Signed Routing Form 1</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe src="{{ Storage::url($appointment->signed_routing_form_1) }}" width="100%" height="600px"></iframe>
                </div>
                <div class="modal-footer">
                    <a href="{{ Storage::url($appointment->signed_routing_form_1) }}" target="_blank" class="btn btn-primary" download>Download</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="proposalManuscriptModal" tabindex="-1" aria-labelledby="proposalManuscriptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Proposal Manuscript</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe src="{{ Storage::url($appointment->proposal_manuscript) }}" width="100%" height="600px"></iframe>
                </div>
                <div class="modal-footer">
                    <a href="{{ Storage::url($appointment->proposal_manuscript) }}" target="_blank" class="btn btn-primary" download>Download</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="videoPresentationModal" tabindex="-1" aria-labelledby="videoPresentationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Video Presentation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <video controls width="100%">
                        <source src="{{ Storage::url($appointment->proposal_video_presentation) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div class="modal-footer">
                    <a href="{{ Storage::url($appointment->proposal_video_presentation) }}" target="_blank" class="btn btn-primary" download>Download</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
<!-- Inline Modals for Viewing Uploaded Files -->
<!-- Approved Proposal Manuscript Modal -->
<!-- Approved Proposal Manuscript Modal -->
<div class="modal fade" id="approvedProposalModal" tabindex="-1" aria-labelledby="approvedProposalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approved Proposal Manuscript</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @php
                    // Decode the JSON data here within the modal
                    $updates = $appointment->proposal_manuscript_updates ? json_decode($appointment->proposal_manuscript_updates, true) : null;
                @endphp
                
                @if($updates && !empty($updates['file_path']))
                    <iframe src="{{ Storage::url($updates['file_path']) }}" width="100%" height="600px"></iframe>
                @else
                    <p>No manuscript uploaded.</p>
                @endif
            </div>
            <div class="modal-footer">
                @if($updates && !empty($updates['file_path']))
                    <a href="{{ Storage::url($updates['file_path']) }}" download class="btn btn-primary">Download</a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Proof of Payment Modal -->
<div class="modal fade" id="ethicsProofOfPaymentModal" tabindex="-1" aria-labelledby="ethicsProofOfPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proof of Payment for Ethics Review</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @if($appointment->ethics_proof_of_payment)
                    <iframe src="{{ Storage::url($appointment->ethics_proof_of_payment) }}" width="100%" height="600px"></iframe>
                @else
                    <p>No proof of payment uploaded.</p>
                @endif
            </div>
            <div class="modal-footer">
                @if($appointment->ethics_proof_of_payment)
                    <a href="{{ Storage::url($appointment->ethics_proof_of_payment) }}" download class="btn btn-primary">Download</a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Curriculum Vitae Modal -->
<div class="modal fade" id="ethicsCurriculumVitaeModal" tabindex="-1" aria-labelledby="ethicsCurriculumVitaeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Curriculum Vitae</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @if($appointment->ethics_curriculum_vitae)
                    <iframe src="{{ Storage::url($appointment->ethics_curriculum_vitae) }}" width="100%" height="600px"></iframe>
                @else
                    <p>No Curriculum Vitae uploaded.</p>
                @endif
            </div>
            <div class="modal-footer">
                @if($appointment->ethics_curriculum_vitae)
                    <a href="{{ Storage::url($appointment->ethics_curriculum_vitae) }}" download class="btn btn-primary">Download</a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Research Services Form Modal -->
<div class="modal fade" id="researchServicesFormModal" tabindex="-1" aria-labelledby="researchServicesFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Research Services Form</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @if($appointment->ethics_research_services_form)
                    <iframe src="{{ Storage::url($appointment->ethics_research_services_form) }}" width="100%" height="600px"></iframe>
                @else
                    <p>No Research Services Form uploaded.</p>
                @endif
            </div>
            <div class="modal-footer">
                @if($appointment->ethics_research_services_form)
                    <a href="{{ Storage::url($appointment->ethics_research_services_form) }}" download class="btn btn-primary">Download</a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Application Form Modal -->
<div class="modal fade" id="ethicsApplicationFormModal" tabindex="-1" aria-labelledby="ethicsApplicationFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Application for Ethics Review Form</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @if($appointment->ethics_application_form)
                    <iframe src="{{ Storage::url($appointment->ethics_application_form) }}" width="100%" height="600px"></iframe>
                @else
                    <p>No Application for Ethics Review Form uploaded.</p>
                @endif
            </div>
            <div class="modal-footer">
                @if($appointment->ethics_application_form)
                    <a href="{{ Storage::url($appointment->ethics_application_form) }}" download class="btn btn-primary">Download</a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Study Protocol Assessment Form Modal -->
<div class="modal fade" id="studyProtocolFormModal" tabindex="-1" aria-labelledby="studyProtocolFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Study Protocol Assessment Form</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @if($appointment->ethics_study_protocol_form)
                    <iframe src="{{ Storage::url($appointment->ethics_study_protocol_form) }}" width="100%" height="600px"></iframe>
                @else
                    <p>No Study Protocol Assessment Form uploaded.</p>
                @endif
            </div>
            <div class="modal-footer">
                @if($appointment->ethics_study_protocol_form)
                    <a href="{{ Storage::url($appointment->ethics_study_protocol_form) }}" download class="btn btn-primary">Download</a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Informed Consent Assessment Form Modal -->
<div class="modal fade" id="informedConsentFormModal" tabindex="-1" aria-labelledby="informedConsentFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Informed Consent Assessment Form</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @if($appointment->ethics_informed_consent_form)
                    <iframe src="{{ Storage::url($appointment->ethics_informed_consent_form) }}" width="100%" height="600px"></iframe>
                @else
                    <p>No Informed Consent Assessment Form uploaded.</p>
                @endif
            </div>
            <div class="modal-footer">
                @if($appointment->ethics_informed_consent_form)
                    <a href="{{ Storage::url($appointment->ethics_informed_consent_form) }}" download class="btn btn-primary">Download</a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Sample Informed Consent Modal -->
<div class="modal fade" id="sampleInformedConsentModal" tabindex="-1" aria-labelledby="sampleInformedConsentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sampleInformedConsentModalLabel">Sample Informed Consent</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($appointment->ethics_sample_informed_consent)
                    <iframe src="{{ Storage::url($appointment->ethics_sample_informed_consent) }}" width="100%" height="600px"></iframe>
                @else
                    <p>No Sample Informed Consent uploaded.</p>
                @endif
            </div>
            <div class="modal-footer">
                @if($appointment->ethics_sample_informed_consent)
                    <a href="{{ Storage::url($appointment->ethics_sample_informed_consent) }}" download class="btn btn-primary">Download</a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        $.ajax({
            url: `/notifications/${notificationId}`, // URL for delete route
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}' // CSRF token for security
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Remove the notification element from the dropdown
                    $(`#notification-${notificationId}`).remove();

                    // Update the notification count badge
                    let notificationCount = parseInt($('#notification-count').text());
                    notificationCount = notificationCount > 0 ? notificationCount - 1 : 0;
                    $('#notification-count').text(notificationCount);

                    // Show success toast message
                    $('#toast-message').text(response.message).fadeIn().delay(3000).fadeOut();
                }
            },
            error: function(xhr) {
                console.error('An error occurred while deleting the notification:', xhr);
                alert('Failed to delete notification');
            }
        });
    }
}
</script>
@endsection
