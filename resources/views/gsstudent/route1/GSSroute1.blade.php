@extends('gsstudent.GSSmain-layout')
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
                <div class="overflow-auto" style="max-height: 300px;">
                    <!-- This is the scrolling part -->
                    @foreach (auth()->user()->notifications->take(5) as $notification)  <!-- Limit to 5 -->
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
<!-- Success and Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
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
    <div class="sagreet">{{ $title }}</div>
    <!-- Title like "Routing Form 1 for student_name" -->
    <br>
</div>
<div class="card shadow mb-4">
    <div class="card-header"></div>
    <br>
    @php
    // Determine the total number of steps
    $totalSteps = $isDrPH ? 9 : 8; // 9 steps for DrPH, 8 for others
    @endphp
    <!-- Multi-Step Navigation -->
    <div class="container-fluid">
        <div class="steps">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                @for ($step = 1; $step <= $totalSteps; $step++)
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
        @for ($step = 1; $step <= $totalSteps; $step++)
        <div class="tab-pane fade {{ $step === 1 ? 'show active' : '' }}" id="pills-step-{{ $step }}"
            role="tabpanel" aria-labelledby="pills-step-{{ $step }}-tab">
            @if ($step === 1)
            <!-- Step 1 Form: Adviser Appointment Form -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <h4 class="routing-heading">Appointment Details</h4>
                                <!-- Date Display -->
                                <div class="form-group">
                                    <label for="date">Date:</label>
                                    <input type="text" name="date" value="{{ optional($appointment)->completed_at ? $appointment->completed_at->toDateString() : now()->toDateString() }}" class="form-control" readonly>
                                </div>
                                <!-- Program -->
                                <div class="form-group">
                                    <label for="program">Program:</label>
                                    <input type="text" name="program" value="{{ $user->program ?? 'N/A' }}" class="form-control" readonly>
                                </div>
                                <!-- Adviser Display -->
                                @php
                                $adviserText = 'Adviser will be assigned by the Program Chair.';
                                if (!is_null($appointment)) {
                                if ($appointment->status === 'pending') {
                                $adviserText = 'Waiting for the adviser to accept the request from the Program Chair.';
                                } elseif ($appointment->status === 'approved') {
                                $adviserText = optional($appointment->adviser)->name;
                                } else {
                                $adviserText = 'Adviser status is unclear.';
                                }
                                }
                                @endphp
                                <div class="form-group">
                                    <label for="adviser">Adviser:</label>
                                    <input type="text" class="form-control" value="{{ $adviserText }}" readonly>
                                </div>
                                <!-- Kulang pa to ng if $appointment)->status = pending = waiting for the adviser to accept the request from program chair -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-body">
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
            @elseif ($step === 2)
            <!-- Step 2: View Consultation Dates -->
            @if (!$appointment || is_null($appointment->adviser_signature) || is_null($appointment->chair_signature) || is_null($appointment->dean_signature))
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
            <div class="container-fluid">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h4 class="routing-heading">Consultation Dates and Adviser Endorsement</h4>
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
                        <!-- Adviser Endorsement Signature -->
                        <div class="form-group">
                            <label for="adviser_endorsement_signature">Adviser Endorsement Signature:</label>
                            <input type="text" name="adviser_endorsement_signature" class="form-control" 
                                value="{{ optional($appointment)->adviser_endorsement_signature ?? 'Pending' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @elseif ($step === 3)
            <!-- Step 3: Upload and View Similarity Manuscript and Certificate -->
            @if (is_null(optional(value: $appointment)->adviser_endorsement_signature))
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
                    <h1 class="routing-heading" style="font-size: 60px; text-align:center;"> Similarity Check</h1>
                    <div class="card-body">
                        <h4 class="routing-heading">Upload Similarity Manuscript</h4>
                        @if(auth()->user()->account_type == 11)
                        @if(is_null($appointment->similarity_manuscript))
                        <form action="{{ route('gsstudent.uploadSimilarityManuscript') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="similarity_manuscript">Upload Manuscript</label>
                                <input type="file" name="similarity_manuscript" class="form-control" required accept=".pdf,.doc,.docx">
                            </div>
                            <button type="submit" class="btn btn-primary">Upload Manuscript</button>
                        </form>
                        @else
                        <div class="form-group">
                            <label for="uploaded_manuscript">Uploaded Manuscript:</label>
                            <input type="text" 
                                id="uploaded_manuscript" 
                                class="form-control" 
                                value="{{ basename($appointment->similarity_manuscript) }}" 
                                readonly 
                                onclick="$('#manuscriptModal').modal('show')" 
                                style="cursor: pointer;">
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h4 class="routing-heading">Similarity Check Results</h4>
                        @if (optional($appointment)->similarity_certificate)
                        <!-- Display uploaded certificate file name -->
                        <div class="form-group">
                            <label for="view_certificate"><strong>View Certificate:</strong></label>
                            <input type="text" 
                                id="view_certificate" 
                                class="form-control" 
                                value="{{ basename($appointment->similarity_certificate) }}" 
                                readonly 
                                onclick="$('#certificateModal').modal('show')" 
                                style="cursor: pointer;">
                        </div>
                        @else
                        <div class="form-group">
                            <label for="certificate_status">Certificate Status:</label>
                            <input type="text" 
                                id="certificate_status" 
                                class="form-control" 
                                value="Please wait for the librarian to upload the certificate." 
                                readonly>
                        </div>
                        @endif
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
                                <iframe src="{{ Storage::url($appointment->similarity_manuscript) }}" width="100%" height="600px"></iframe>
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
                                <iframe src="{{ Storage::url($appointment->similarity_certificate) }}" width="100%" height="600px"></iframe>
                            </div>
                            <div class="modal-footer">
                                <a href="{{ Storage::url($appointment->similarity_certificate) }}" target="_blank" class="btn btn-primary" download>Download Certificate</a>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @elseif ($step === 4)
            <!-- Step 4: Research Registration -->
            @if (is_null(optional(value: $appointment)->similarity_certificate))
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
            <div class="container-fluid">
                <!-- Research Registration Content Here -->
                <div class="container my-4">
                    <!-- Research Registration Content -->
                    <div class="card shadow mb-4">
                        <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <!-- QR Code Section -->
                            <div class="qr-code-section text-center mb-4 mb-md-0">
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
            </div>
            @endif
            @elseif ($step === 5 && $isDrPH)
            @if (optional($appointment)->ovpri_approval !== 'approved')
            <p class="text-muted">Step 5 is locked. The OVPRI Approval must be completed in Step 4 to proceed.</p>
            @else
            <!-- Step 5 for DrPH students: Community Extension Registration -->
            <div class="container-fluid">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5><strong>Community Extension Registration (For DrPH students only)</strong></h5>
                        @if ($appointment->community_extension_link)
                        <!-- Display the form link if it exists -->
                        <p><strong>Form Link:</strong> 
                            <a href="{{ $appointment->community_extension_link }}" target="_blank">
                            Community Extension Registration Form
                            </a>
                        </p>
                        @else
                        <p class="text-muted">Form link will be uploaded by the Superadmin/Admin/GraduateSchool .</p>
                        @endif
                        <p><strong>Your Response:</strong>
                            @if ($appointment->community_extension_response === 1)
                            Responded
                            @else
                            <span class="text-muted">Not responded yet.</span>
                            @endif
                        </p>
                        <!-- Display Approval Status -->
                        <p><strong>Approval Status:</strong> 
                            @if ($appointment->community_extension_approval === 'approved')
                            <span class="text-success">Approved</span>
                            @elseif ($appointment->community_extension_approval === 'pending')
                            <span class="text-warning">Pending Approval</span>
                            @else
                            <span class="text-muted">Not yet responded.</span>
                            @endif
                        </p>
                        <!-- Response Button for Student -->
                        @if (!$appointment->community_extension_response)
                        <form action="{{ route('gsstudent.respondToCommunityExtension', parameters: $appointment->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Mark as Responded</button>
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
            <p class="text-muted">
                This step is locked. 
                @if ($isDrPH)
                Community Extension approval must be completed in Step 5 to proceed.
                @else
                OVPRI approval must be completed in Step 4 to proceed.
                @endif
            </p>
            @else
            <!-- Step 5 for non-DrPH or Step 6 for DrPH - File Uploads -->
            <!-- Additional Section for Submission Files, shown if proposal_submission_completed is true -->
            <!-- Submission Files Section -->
            @if (optional($appointment)->proposal_submission_completed ?? false)
            <div class="container my-4 d-flex justify-content-center">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <h4 class="routing-heading">Submission Files</h4>
                            <!-- Display the submission files link if it exists -->
                            @if ($appointment->submission_files_link)
                            <p>Submission Files Link:
                                <a href="{{ $appointment->submission_files_link }}" target="_blank" class="text-primary text-decoration-underline">
                                <i class="fa-solid fa-link"></i>
                                View Submission Files
                                </a>
                            </p>
                            @else
                            <p class="text-muted">Submission files link will be uploaded by the Superadmin/Admin/GraduateSchool.</p>
                            @endif
                            <!-- Display the student's response status -->
                            <p>Your Response:
                                @if ($appointment->submission_files_response === 1)
                                <span class="text-success">Responded</span>
                                @else
                                <span class="text-muted">Not responded yet.</span>
                                @endif
                            </p>
                            <!-- Display approval status -->
                            <p>Approval Status:
                                @if ($appointment->submission_files_approval === 'approved')
                                <span class="text-success">Approved</span>
                                @elseif ($appointment->submission_files_approval === 'pending')
                                <span class="text-warning">Pending Approval</span>
                                @else
                                <span class="text-muted">Not yet responded.</span>
                                @endif
                            </p>
                            <!-- Button to respond to submission files, if not responded yet -->
                            @if ($appointment->submission_files_response !== 1)
                            <form action="{{ route('gsstudent.respondToSubmissionFiles', $appointment->id) }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-primary">Respond to Submission Files</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endif
            @if (($isDrPH && optional(value: $appointment)->community_extension_approval !== 'approved') ||
            (!$isDrPH && optional($appointment)->ovpri_approval !== 'approved'))
            {{-- Display lock message based on the type of approval needed --}}
            <p class="text-muted">
                @if ($isDrPH)
                @else
                @endif
            </p>
            @else          
            <div class="container-fluid my-4">
                <!-- Research Registration Content Here -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h4 class="mb-4 text-center text-md-start routing-heading">File Uploads</h4>
                        <!-- Responsive Table Wrapper -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped custom-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">File Type</th>
                                        <th class="text-center">Current File</th>
                                        <th class="text-center">Upload New File</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Signed Routing Form 1 Row -->
                                    <tr>
                                        <td class="text-center">Signed Routing Form 1</td>
                                        <td class="text-center">
                                            @if(!empty(optional(value: $appointment)->signed_routing_form_1))
                                            <a href="#" data-toggle="modal" data-target="#routingFormModal" class="text-primary text-decoration-underline">
                                            {{ $appointment->original_signed_routing_form_1 }}
                                            </a>
                                            @else
                                            <span class="text-muted">No file uploaded</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('gsstudent.uploadSignedRoutingForm') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="file" name="signed_routing_form_1" class="form-control" accept=".pdf" required>
                                        </td>
                                        <td class="text-center">
                                        <button type="submit" class="btn btn-primary mt-2">Save</button>
                                        </form>
                                        </td>
                                    </tr>
                                    <!-- Proposal Manuscript Row -->
                                    <tr>
                                        <td class="text-center">Proposal Manuscript</td>
                                        <td class="text-center">
                                            @if(!empty(optional(value: $appointment)->proposal_manuscript))
                                            <a href="#" data-toggle="modal" data-target="#proposalManuscriptModal" class="text-primary text-decoration-underline">
                                            {{ $appointment->original_proposal_manuscript }}
                                            </a>
                                            @else
                                            <span class="text-muted">No file uploaded</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('gsstudent.uploadProposalManuscript') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="file" name="proposal_manuscript" class="form-control" accept=".pdf" required>
                                        </td>
                                        <td class="text-center">
                                        <button type="submit" class="btn btn-primary mt-2">Save</button>
                                        </form>
                                        </td>
                                    </tr>
                                    <!-- Video Presentation Row -->
                                    <tr>
                                        <td class="text-center">Video Presentation</td>
                                        <td class="text-center">
                                            @if(!empty(optional(value: $appointment)->proposal_video_presentation))
                                            <a href="#" data-toggle="modal" data-target="#videoPresentationModal" class="text-primary text-decoration-underline">
                                            {{ $appointment->original_proposal_video_presentation }}
                                            </a>
                                            @else
                                            <span class="text-muted">No file uploaded</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('gsstudent.uploadVideoPresentation') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="file" name="proposal_video_presentation" class="form-control" accept=".mp4,.avi,.mov" required>
                                        </td>
                                        <td class="text-center">
                                        <button type="submit" class="btn btn-primary mt-2">Save</button>
                                        </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @elseif (($step === 6 && !$isDrPH) || ($step === 7 && $isDrPH))
            @if (optional($appointment)->proposal_defense_date === null)
            <p class="text-muted">This step is locked. A proposal defense date must be set to proceed.</p>
            @else
            <div class="container-fluid">
                <div>
                    <div class="row">
                        <!-- Main Proposal Manuscript Section -->
                        <div class="col-md-5">
                            @if($appointment->proposal_manuscript)
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h4 class="routing-heading">Proposal Manuscript</h4>
                                    <hr>
                                    <p>Main Proposal Manuscript:                        <i class="fa-solid fa-download"></i></p>
                                    <input type="text" 
                                        class="form-control" 
                                        value="{{ $appointment->original_proposal_manuscript }}" 
                                        readonly 
                                        onclick="$('#mainProposalManuscriptModal').modal('show')" 
                                        style="cursor: pointer; color: #007bff; text-decoration: underline;">
                                </div>
                            </div>
                            <!-- Modal for Main Proposal Manuscript -->
                            <div class="modal fade" id="mainProposalManuscriptModal" tabindex="-1" aria-labelledby="mainProposalManuscriptModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ $appointment->original_proposal_manuscript }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe src="{{ Storage::url($appointment->proposal_manuscript) }}" width="100%" height="500px"></iframe>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ Storage::url($appointment->proposal_manuscript) }}" download class="btn btn-primary">Download</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <p>No main proposal manuscript uploaded.</p>
                            @endif
                        </div>
                        <!-- Proposal Manuscript Updates Section -->
                        <div class="col-md-7">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h4 class="routing-heading">Proposal Manuscript Updates</h4>
                                    <table class="table table-bordered table-hover table-striped custom-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="text-align:center;">File</th>
                                                <th style="text-align:center;">Last Updated</th>
                                                <th style="text-align:center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($appointment->proposal_manuscript_updates)
                                            @php
                                            $updates = json_decode($appointment->proposal_manuscript_updates, true);
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    <a href="#" data-toggle="modal" data-target="#manuscriptUpdateModal">
                                                    {{ $updates['original_name'] }}
                                                    </a>
                                                </td>
                                                <td class="text-center">{{ \Carbon\Carbon::parse($updates['uploaded_at'])->format('m/d/Y') }}</td>
                                                <td class="text-center">
                                                    <a href="{{ Storage::url($updates['file_path']) }}" download class="btn btn-primary">Download</a>
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <form action="{{ route('gsstudent.uploadProposalManuscriptUpdate') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <td class="text-center">
                                                        <input type="file" name="proposal_manuscript_update" class="form-control" accept=".pdf" required>
                                                    </td>
                                                    <td class="text-center">{{ \Carbon\Carbon::now()->format('m/d/Y') }}</td>
                                                    <td class="text-center">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </td>
                                                </form>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal for Proposal Manuscript Update (optional) -->
                <div class="modal fade" id="manuscriptUpdateModal" tabindex="-1" aria-labelledby="manuscriptUpdateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $updates['original_name'] ?? 'Manuscript Update' }}</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <iframe src="{{ Storage::url($updates['file_path'] ?? '') }}" width="100%" height="500px"></iframe>
                            </div>
                            <div class="modal-footer">
                                <a href="{{ Storage::url($updates['file_path'] ?? '') }}" download class="btn btn-primary">Download</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal for proposal manuscript update -->
                <div class="modal fade" id="manuscriptUpdateModal" tabindex="-1" aria-labelledby="manuscriptUpdateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $updates['original_name'] ?? 'Update File' }}</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <iframe src="{{ Storage::url($updates['file_path'] ?? '') }}" width="100%" height="500px"></iframe>
                            </div>
                            <div class="modal-footer">
                                <a href="{{ Storage::url($updates['file_path'] ?? '') }}" download class="btn btn-primary">Download</a>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <!-- Panel Review Section -->
                <h4 class="routing-heading">Panel Review</h4>
                @foreach ($appointment->panel_members ?? [] as $panelistId)
                @php
                // Retrieve panelist information
                $panelist = \App\Models\User::find($panelistId);
                $panelistName = $panelist ? $panelist->name : "Unknown Panelist";
                $comments = json_decode($appointment->panel_comments, true) ?? [];
                $replies = json_decode($appointment->student_replies, true) ?? [];
                $remarks = json_decode($appointment->panel_remarks, true) ?? [];
                $signatures = json_decode($appointment->panel_signatures, true) ?? [];
                @endphp
                <div class="card mb-3">
                    <div class="card-header">Panel: {{ $panelistName }}</div>
                    <div class="card-body">
                        <p><strong>Comment:</strong> {{ $comments[$panelistId] ?? 'No comment yet' }}</p>
                        <p><strong>Student Reply:</strong> {{ $replies[$panelistId] ?? 'No reply yet' }}</p>
                        <form action="{{ route('gsstudent.addStudentReply', $panelistId) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="reply">Your Reply</label>
                                <textarea name="reply" class="form-control">{{ $replies[$panelistId] ?? '' }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Reply</button>
                        </form>
                        <p><strong>Remarks:</strong> {{ $remarks[$panelistId] ?? 'No remarks yet' }}</p>
                        <p><strong>Signature:</strong> {{ $signatures[$panelistId] ?? 'Not signed yet' }}</p>
                    </div>
                </div>
                @endforeach
                @php
                // Check if all panel members have signed
                $allPanelSigned = count($appointment->panel_members ?? []) === count(array_filter(json_decode($appointment->panel_signatures, true) ?? []));
                @endphp
                @if ($allPanelSigned)
                <!-- Dean Signature Status Check -->
                @if ($appointment->dean_monitoring_signature)
                <p><strong>Dean's Signature:</strong> {{ $appointment->dean_monitoring_signature }}</p>
                @else
                <p><strong>Dean's Signature Status:</strong> Awaiting Dean's Signature...</p>
                @endif
                @else
                <!-- Show message if panel signatures are incomplete -->
                <p><strong>Panel Signatures Incomplete:</strong> The dean’s approval will be requested once all panel members have signed.</p>
                @endif
            </div>
            @endif
    
            @elseif (($step === 7 && !$isDrPH) || ($step === 8 && $isDrPH))
    <div class="container-fluid">
    @if (is_null($appointment) || is_null($appointment->dean_monitoring_signature))
    <!-- Step Locked Message -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="alert alert-warning mb-0" role="alert">
                        <i class="fas fa-lock mr-2"></i>
                        <strong>Step Locked:</strong> This step is locked. The Dean's signature in Monitoring Form must be completed to proceed.
                    </div>
                </div>
            </div>
        @else
            <!-- Consultation with Statistician Content -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4 class="routing-heading">Consultation with Statistician</h4>
                    <div class="d-flex align-items-start">
                        <!-- QR Code Image -->
                        <div class="qr-code mr-3">
                            <img src="{{ asset('img/cdaic_qr.png') }}" alt="QR Code" style="width: 100px; height: 100px;">
                        </div>
                        <!-- Instructions -->
                        <div class="form-group">
                            <p>Accomplish the 
                                <a href="https://docs.google.com/forms/d/e/1FAIpQLSezh_2LK83Yh435RFQ879axmNE7B761ifHd1ML4vZz54j8GSw/viewform" target="_blank">CDAIC Service Request Form</a>.
                                Send your manuscript to 
                                <strong>cdaic@auf.edu.ph</strong>, cc: 
                                <strong>calibio.mylene@auf.edu.ph</strong>, <strong>ovpri@auf.edu.ph</strong>.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Display the student's response status -->
                    <p><strong>Status:</strong>
                        @if ($appointment->statistician_approval === 'approved')
                            <span class="text-success">Approved</span>
                        @elseif ($appointment->student_statistician_response === 'responded')
                            <span class="text-warning">Pending</span>
                        @else
                            <span class="text-muted">Not responded yet</span>
                        @endif
                    </p>

                    <!-- Button for student to mark as responded -->
                    @if (is_null($appointment->student_statistician_response))
                        <form action="{{ route('gsstudent.respondToStatistician') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Mark as Responded</button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endif

    </div>
    @endfor
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
                <iframe src="{{ Storage::url(optional($appointment)->signed_routing_form_1) }}" width="100%" height="600px"></iframe>
            </div>
            <div class="modal-footer">
                <a href="{{ Storage::url(optional($appointment)->signed_routing_form_1) }}" target="_blank" class="btn btn-primary" download>Download</a>
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
                <iframe src="{{ Storage::url(optional($appointment)->proposal_manuscript) }}" width="100%" height="600px"></iframe>
            </div>
            <div class="modal-footer">
                <a href="{{ Storage::url(optional($appointment)->proposal_manuscript) }}" target="_blank" class="btn btn-primary" download>Download</a>
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
                <video id="presentationVideo" controls width="100%">
                    <source src="{{ Storage::url(optional($appointment)->proposal_video_presentation) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="modal-footer">
                <a href="{{ Storage::url(optional($appointment)->proposal_video_presentation) }}" target="_blank" class="btn btn-primary" download>Download</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#videoPresentationModal').on('shown.bs.modal', function () {
            // Play video when modal opens
            $('#presentationVideo')[0].play();
        });
    
        $('#videoPresentationModal').on('hidden.bs.modal', function () {
            // Pause video and reset time when modal closes
            $('#presentationVideo')[0].pause();
            $('#presentationVideo')[0].currentTime = 0;
        });
    });
    
</script>
@endsection
