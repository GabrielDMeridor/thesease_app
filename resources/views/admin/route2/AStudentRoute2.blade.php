@extends('admin.Amain-layout')
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
                                <span>{{ $notification->data['message'] ?? 'No message available' }}</span> <!-- Default value if 'message' is missing -->
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
</div>

<div class="card shadow mb-4">
    <div class="card-header"></div>
    <br>

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
            <div class="tab-pane fade {{ $step === 1 ? 'show active' : '' }}" id="pills-step-{{ $step }}" role="tabpanel" aria-labelledby="pills-step-{{ $step }}-tab">
                @if ($step === 1)
                <div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h4 class="routing-heading">Student Uploaded Manuscript</h4>

            @if ($appointment->revised_manuscript_path)
                <!-- Display the link to the uploaded manuscript -->
                <div class="form-group">
                    <label>Uploaded Revised Manuscript:</label>
                    <a href="#" data-toggle="modal" data-target="#manuscriptModal">
                        {{ $appointment->revised_manuscript_original_name }}
                    </a>
                </div>
            @else
                <p class="text-muted">No revised manuscript uploaded by the student.</p>
            @endif

<!-- Manuscript Modal -->
<div class="modal fade" id="manuscriptModal" tabindex="-1" role="dialog" aria-labelledby="manuscriptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manuscriptModalLabel">View Uploaded Manuscript</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Display the manuscript in an iframe -->
                <iframe src="{{ Storage::url($appointment->revised_manuscript_path) }}" width="100%" height="600px" style="border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <!-- Option to download the manuscript -->
                <a href="{{ Storage::url($appointment->revised_manuscript_path) }}" target="_blank" class="btn btn-primary" download>
                    Download Manuscript
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
                                <h4 class="routing-heading">Consultation with Adviser and Final Endorsement Signature</h4>
                                <form method="POST" action="{{ route('route2.addFinalConsultationDatesAndSign', $appointment->id) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="final_consultation_dates">Final Consultation Dates:</label>
                                        <div id="final_consultation_dates_container">
                                            @if ($appointment->final_consultation_dates)
                                                @foreach (json_decode($appointment->final_consultation_dates) as $date)
                                                    <div class="input-group mb-2">
                                                        <input type="date" name="final_consultation_dates[]" class="form-control" value="{{ $date }}" readonly>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="final_adviser_signature">Final Adviser Signature:</label>
                                        @if (is_null($appointment->final_adviser_endorsement_signature))
                                            <input type="text" name="final_adviser_signature" class="form-control" placeholder="Waiting for the Adviser's Signature">
                                        @else
                                            <input type="text" name="final_adviser_signature" class="form-control" value="{{ $appointment->final_adviser_endorsement_signature }}" readonly>
                                        @endif
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
            <!-- Step 1 Content -->
        @elseif ($step === 2)

        
        <div class="container my-4">
                        <div class="card shadow mb-4">
                            <div class="card-body d-flex flex-column flex-md-row align-items-center">
                                <!-- Instructions Section -->
                                <div class="instructions-section ml-md-4">
                                    <h4 class="routing-heading">Consultation with Statistician</h4>
                                    <p>
                                        Please complete the service request form and send your manuscript to:
                                    </p>
                                    <ul class="mb-3" style="list-style: none; padding-left: 0;">
                                        <li><strong>cdaic@auf.edu.ph</strong></li>
                                        <li>cc: <strong>calibio.mylene@auf.edu.ph</strong>, <strong>ovpri@auf.edu.ph</strong></li>
                                    </ul>

                                    <!-- Display Status -->
                                    <p><strong>Status:</strong> 
                                        @if ($appointment->final_statistician_approval === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif ($appointment->final_student_statistician_response === 'responded')
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-secondary">Not responded yet</span>
                                        @endif
                                    </p>

                                    <!-- Respond Button for Student -->
                                </div>
                            </div>
                        </div>
                    </div>  
            <!-- Step 2 Content -->
        @elseif ($step === 3)

        <div class="container my-4">
                            <div class="card shadow mb-4">
                                <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between">
                                    <div class="qr-code-section text-center mb-4 mb-md-0">
                                        <img src="{{ asset('img/qr_code.png') }}" alt="QR Code" class="qr-code-image rounded" style="width: 150px; border: 2px solid #ddd;">
                                        <p class="mt-2 text-muted" style="font-size: 0.9rem;">Scan for Registration Form</p>
                                    </div>
                                    <div class="instructions-section ml-md-4">
                                        <h4 class="routing-heading">Research Registration</h4>
                                        <p>Adviser accomplishes the 
                                            <a href="{{ $final_ovpri_link }}" target="_blank" class="text-decoration-underline text-primary">
                                                <i class="fa-solid fa-link"></i>    
                                                Research Registration Form
                                            </a>.
                                            The primary author will be the student, and the adviser will be the co-author. A copy of the form responses will be sent to the adviser’s email.
                                        </p>
                                        <p>After completing the form, please forward the copy and the manuscript to:
                                            <br><strong>cdaic@auf.edu.ph</strong> (cc: <strong>ovpri@auf.edu.ph</strong>, <strong>collegesecretary.gs@auf.edu.ph</strong>).
                                        </p>
                                        <p><strong>Status:</strong> 
                                            @if ($appointment->final_ovpri_approval === 'approved')
                                                <span class="badge badge-success">Already approved by OVPRI.</span>
                                            @elseif ($appointment->final_ovpri_approval === 'pending')
                                                <span class="badge badge-warning">Pending OVPRI approval.</span>
                                            @else
                                                <span class="badge badge-secondary">Not yet responded.</span>
                                            @endif
                                        </p>

                                    </div>
                                </div>
                            </div>
                        </div>
            <!-- Step 3 Content -->
        @elseif ($step === 4)
            <!-- Step 4 Content -->

    <div class="container my-4">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="routing-heading">Final Similarity Manuscript</h4>
                
                @if($appointment->final_similarity_manuscript)
                    <div class="form-group mt-3">
                        <label for="uploaded_manuscript">Uploaded Manuscript:</label>
                        <input type="text" 
                            id="uploaded_manuscript" 
                            class="form-control" 
                            value="{{ $appointment->final_similarity_manuscript_original_name }}" 
                            readonly 
                            onclick="$('#similaritymanuscriptModal').modal('show')" 
                            style="cursor: pointer;">
                    </div>
                @else
                    <p>No manuscript uploaded yet.</p>
                @endif
            </div>
        </div>
    </div>


    <div class="card shadow mb-4">
        <div class="card-body">
            <h4 class="routing-heading">Final Similarity Certificate</h4>
            
            @if($appointment->final_similarity_certificate)
                <div class="form-group">
                    <label for="view_certificate"><strong>View Certificate:</strong></label>
                    <input type="text" 
                           id="view_certificate" 
                           class="form-control" 
                           value="{{ $appointment->final_similarity_certificate_original_name }}" 
                           readonly 
                           onclick="$('#similaritycertificateModal').modal('show')" 
                           style="cursor: pointer;">
                </div>
            @else
                <p>No certificate uploaded yet.</p>
            @endif
        </div>
    </div>

    <!-- Manuscript Modal -->
    <div class="modal fade" id="similaritymanuscriptModal" tabindex="-1" aria-labelledby="similaritymanuscriptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="similaritymanuscriptModalLabel">View Manuscript</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe src="{{ Storage::url($appointment->final_similarity_manuscript) }}" width="100%" height="600px" style="border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <a href="{{ Storage::url($appointment->final_similarity_manuscript) }}" target="_blank" class="btn btn-primary" download>Download Manuscript</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Modal -->
    <div class="modal fade" id="similaritycertificateModal" tabindex="-1" aria-labelledby="similaritycertificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="similaritycertificateModalLabel">View Certificate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe src="{{ Storage::url($appointment->final_similarity_certificate) }}" width="100%" height="600px" style="border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <a href="{{ Storage::url($appointment->final_similarity_certificate) }}" target="_blank" class="btn btn-primary" download>Download Certificate</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

                {{-- Step 5 Content (DrPH students only) --}}
                @elseif ($step === 5 && $isDrPH)
                    <div class="container my-4">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <h4 class="routing-heading">7. Community Extension Accomplishment Report (For DrPH students only)</h4>
                                <p>Complete the following forms:</p>
                                <ul style="list-style-type: none; padding: 0;">
                                    <li>
                                        <a href="https://docs.google.com/document/d/1_FXK-09OmJ306wVmi3IZNxBpgbn1pz9KOsKUnXu0zLE/edit?tab=t.0#heading=h.30j0zll" target="_blank">Community Extension Service Working Committee</a>
                                    </li>
                                    <li>
                                        <a href="https://docs.google.com/document/d/13PYjFojjvLNTqHqmM396YTeyQUIgDiVelLA2gZeapXA/edit?tab=t.0" target="_blank">Community Extension Service Accomplishment Report</a>
                                    </li>
                                </ul>

                                <p>
                                    Email the completed forms and attachments (e.g., program, photos) to the HSP Program Chair
                                    (<a href="mailto:navarro.analyn@auf.edu.ph">navarro.analyn@auf.edu.ph</a>)
                                    and the Asst. Director for Christian Praxis (<a href="mailto:adcp.ccfp@auf.edu.ph">adcp.ccfp@auf.edu.ph</a>)
                                    for signing.
                                </p>

                                <!-- Display Signature Status -->
                                <table class="table table-bordered mt-4">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Signatories</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Program Chair</td>
                                            <td>
                                                @if ($appointment->program_chair_signature)
                                                    <span class="badge badge-success">Signed</span>
                                                @else
                                                    <span class="badge badge-secondary">Not Signed</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>CCFP (Asst. Director for Christian Praxis)</td>
                                            <td>
                                                @if ($appointment->ccfp_signature)
                                                    <span class="badge badge-success">Signed</span>
                                                @else
                                                    <span class="badge badge-secondary">Not Signed</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

            <!-- Step 5 Content -->
            @elseif(($isDrPH && $step === 6) || (!$isDrPH && $step === 5))

    @if ($appointment->final_submission_approval === 'approved')
    <div class="card shadow mb-4">
    <div class="card-body text-center">
        <h4 class="routing-heading">Admin - Submission Files Review</h4>

        <!-- Display student's response status -->
        <p>Student Response Status:
            @if ($appointment->final_submission_files_response)
                <span class="badge badge-success">Responded</span>
            @else
                <span class="badge badge-warning">Not responded yet</span>
            @endif
        </p>

        <!-- Display admin approval status for form fee -->
        <p>Form Fee Approval Status:
            @if ($appointment->final_submission_approval_formfee === 'approved')
                <span class="badge badge-success">Approved</span>
            @elseif ($appointment->final_submission_approval_formfee === 'pending')
                <span class="badge badge-warning">Pending Approval</span>
            @else
                <span class="badge badge-secondary">Not yet responded.</span>
            @endif
        </p>

        <!-- Approve/Deny buttons for form fee approval -->
        @if ($appointment->final_submission_approval_formfee !== 'approved')
            <form action="{{ route('admin.approveFormFee', $appointment->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-success">Approve Form Fee</button>
            </form>
            <form action="{{ route('admin.denyFormFee', $appointment->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Deny Form Fee</button>
            </form>
        @else
            <p class="text-muted">Form fee approval already granted.</p>
        @endif
    </div>
</div>

    @else
        <p class="text-muted">No approval needed or pending submissions.</p>
    @endif

    <!-- File Uploads Section -->
    <div class="container-fluid my-4">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="mb-4 text-center text-md-start">File Uploads</h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">File Type</th>
                                <th class="text-center">Current File</th>
                                <th class="text-center">Upload New File</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Ethics Clearance Row with Modal -->
                            <tr>
                                <td class="text-center">Ethics Clearance</td>
                                <td class="text-center">
                                    @if(!empty($appointment->ethics_clearance))
                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#ethicsClearanceModal">View File</button>
                                    @else
                                        <span class="text-muted">No file uploaded</span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="text-muted">Cannot be uploaded by student</span></td>
                                <td class="text-center"><span class="text-muted">View Only</span></td>
                            </tr>

                            <!-- Manuscript Row with Upload and Modal -->
                            <tr>
                                <td class="text-center">Manuscript</td>
                                <td class="text-center">
                                    @if(!empty($appointment->final_similarity_manuscript))
                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#manuscriptModal">View Manuscript</button>
                                    @else
                                        <span class="text-muted">No file uploaded</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($appointment->final_submission_approval !== 'approved')
                                        <!-- Upload form only visible if final submission not approved -->
                                        <form action="{{ route('gsstudent.uploadManuscript', $appointment) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" name="revised_manuscript" class="form-control mb-2" accept=".pdf,.doc,.docx" required>
                                            <button type="submit" class="btn btn-primary">Upload Manuscript</button>
                                        </form>
                                    @else
                                        <span class="text-muted">Upload not allowed after approval</span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="text-muted">View and Re-upload</span></td>
                            </tr>

                            <!-- Similarity Certificate Row with Modal -->
                            <tr>
                                <td class="text-center">Similarity Certificate</td>
                                <td class="text-center">
                                    @if(!empty($appointment->final_similarity_certificate))
                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#similarityCertificateModal">View Certificate</button>
                                    @else
                                        <span class="text-muted">No file uploaded</span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="text-muted">Cannot be uploaded by student</span></td>
                                <td class="text-center"><span class="text-muted">View Only</span></td>
                            </tr>

                            <!-- Proof of Publication Row with Modal -->
                            <tr>
                                <td class="text-center">Proof of Publication</td>
                                <td class="text-center">
                                    @if(!empty($appointment->proof_of_publication_path))
                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#proofOfPublicationModal">{{ $appointment->proof_of_publication_original_name }}</button>
                                    @else
                                        <span class="text-muted">No file uploaded</span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="text-muted">Cannot be uploaded by student</span></td>
                                <td class="text-center"><span class="text-muted">View Only</span></td>
                            </tr>

                            <!-- Final Video Presentation Row with Modal and Upload Option -->
                            <tr>
                                <td class="text-center">Final Video Presentation</td>
                                <td class="text-center">
                                    @if(!empty($appointment->final_video_presentation))
                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#videoPresentationModal">View Video</button>
                                    @else
                                        <span class="text-muted">No file uploaded</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!$appointment->final_submission_files)
                                        <form action="{{ route('gsstudent.uploadFinalVideoPresentation') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" name="final_video_presentation" class="form-control" accept=".mp4,.avi,.mov" required>
                                            <button type="submit" class="btn btn-primary mt-2">Upload Video</button>
                                        </form>
                                    @else
                                        <span class="text-muted">Upload Completed</span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="text-muted">{{ $appointment->final_submission_files ? 'View Only' : 'Upload Available' }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- Modals for Viewing Files -->
<!-- Ethics Clearance Modal -->
<div class="modal fade" id="ethicsClearanceModal" tabindex="-1" aria-labelledby="ethicsClearanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ethicsClearanceModalLabel">Ethics Clearance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ Storage::url($appointment->ethics_clearance) }}" width="100%" height="500px"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Similarity Certificate Modal -->
<div class="modal fade" id="similarityCertificateModal" tabindex="-1" aria-labelledby="similarityCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="similarityCertificateModalLabel">Similarity Certificate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ Storage::url($appointment->final_similarity_certificate) }}" width="100%" height="500px"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Final Video Presentation Modal -->
<div class="modal fade" id="videoPresentationModal" tabindex="-1" aria-labelledby="videoPresentationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoPresentationModalLabel">Final Video Presentation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <video width="100%" height="500px" controls>
                    <source src="{{ Storage::url($appointment->final_video_presentation) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>

            <!-- Step 6 Content -->
        @elseif ($step === 7)
            <!-- Step 7 Content -->
        @elseif ($step === 8)
            <!-- Step 8 Content -->
        @elseif ($step === 9)
            <!-- Step 9 Content -->
        @endif
    </div>
    @endfor
    </div>

    <div class="card-footer"></div>
</div>
@endsection
