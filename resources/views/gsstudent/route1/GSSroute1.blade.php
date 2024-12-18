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
@if (is_null(optional($appointment)->adviser_endorsement_signature))
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
                    <!-- Allow upload or update until similarity certificate is available -->
                    @if(is_null(optional($appointment)->similarity_certificate))
                        <form action="{{ route('gsstudent.uploadSimilarityManuscript') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="similarity_manuscript">Upload Manuscript</label>
                                <input type="file" name="similarity_manuscript" class="form-control" required accept=".pdf,.doc,.docx">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                {{ is_null(optional($appointment)->similarity_manuscript) ? 'Upload Manuscript' : 'Update Manuscript' }}
                            </button>
                        </form>
                    @endif
                    @if(optional($appointment)->similarity_manuscript)
                        <!-- Display uploaded manuscript name with link to open modal -->
                        <div class="form-group mt-3">
                            <label for="uploaded_manuscript">Current Uploaded Manuscript:</label>
                            <input type="text" 
                                   id="uploaded_manuscript" 
                                   class="form-control" 
                                   value="{{ basename(optional($appointment)->similarity_manuscript) }}" 
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
                @if(optional($appointment)->similarity_certificate)
                    <!-- Display uploaded certificate file name -->
                    <div class="form-group">
                        <label for="view_certificate"><strong>View Certificate:</strong></label>
                        <input type="text" 
                               id="view_certificate" 
                               class="form-control" 
                               value="{{ basename(optional($appointment)->similarity_certificate) }}" 
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
                        <iframe src="{{ Storage::url(optional($appointment)->similarity_manuscript) }}" width="100%" height="600px"></iframe>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ Storage::url(optional($appointment)->similarity_manuscript) }}" target="_blank" class="btn btn-primary" download>Download Manuscript</a>
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
                        <iframe src="{{ Storage::url(optional($appointment)->similarity_certificate) }}" width="100%" height="600px"></iframe>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ Storage::url(optional($appointment)->similarity_certificate) }}" target="_blank" class="btn btn-primary" download>Download Certificate</a>
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

                     <!-- Instructions Section -->
                     <div class="instructions-section ml-md-4">
                        <h4 class="routing-heading">Research Registration</h4>
                        <p>Adviser accomplishes the 
                        <a href="{{ $ovpriLink }}" target="_blank" class="text-decoration-underline text-primary">
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
         </div>
         @endif
         @elseif ($step === 5 && $isDrPH)
         @if (optional($appointment)->ovpri_approval !== 'approved')
         <div class="card shadow mb-4">
            <div class="card-body text-center">
               <div class="alert alert-warning mb-0" role="alert">
                  <i class="fas fa-lock mr-2"></i>
                  <strong>Step Locked:</strong> The OVPRI Approval must be completed in Step 4 to proceed.
               </div>
            </div>
         </div>
         @else
         <!-- Step 5 for DrPH students: Community Extension Registration -->
         <div class="container-fluid">
            <div class="card shadow mb-4">
               <div class="card-body">
                  <h5><strong>Community Extension Registration (For DrPH students only)</strong></h5>
                  @if ($ccfpLink)
                  <!-- Display the form link if it exists -->
                  <p><strong>Form Link:</strong> 
                  <a href="{{ $ccfpLink }}" target="_blank" class="text-decoration-underline text-primary">
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
<!-- Response Button for Student -->
@if (!$appointment->community_extension_response)
   <form action="{{ route('gsstudent.respondToCommunityExtension') }}" method="POST">
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
         <div class="card shadow mb-4">
            <div class="card-body text-center">
               <div class="alert alert-warning mb-0" role="alert">
                  <i class="fas fa-lock mr-2"></i>
                  <strong>Step Locked:</strong>   This step is locked. 
                  @if ($isDrPH)
                  Community Extension approval must be completed in Step 5 to proceed.
                  @else
                  OVPRI approval must be completed in Step 4 to proceed.
                  @endif
               </div>
            </div>
         </div>
         @else
         <!-- Step 5 for non-DrPH or Step 6 for DrPH - File Uploads -->
         <!-- Additional Section for Submission Files, shown if proposal_submission_completed is true -->
         <!-- Submission Files Section -->
         @if (auth()->user()->nationality === 'Filipino')
         @if (optional($appointment)->proposal_submission_completed ?? false)
         <div class="container my-4 d-flex justify-content-center">
            <div class="col-12">
               <div class="card shadow">
                  <div class="card-body text-center">
                     <h4 class="routing-heading">Submission Files</h4>
                     <!-- Display the submission files link if it exists -->
                     @if ($globalSubmissionLink)
            <p>
                <a href="{{ $globalSubmissionLink }}" target="_blank" class="text-primary" style="font-size: 1.25rem;">
                    <i class="fa-solid fa-link"></i> View Submission Files
                </a>
            </p>
            @else
            <p class="text-muted">Submission files link will be uploaded by the Superadmin/Admin/GraduateSchool.</p>
            @endif
                     <!-- Display the student's response status -->
                     <p>Your Response:
                        @if ($appointment->submission_files_response === 1)
                        <span class="badge badge-success">Responded</span>
                        @else
                        <span class="badge badge-warning">Not responded yet.</span>
                        @endif
                     </p>
                     <!-- Display approval status -->
                     <p>Approval Status:
                        @if ($appointment->submission_files_approval === 'approved')
                        <span class="badge badge-success">Approved</span>
                        @elseif ($appointment->submission_files_approval === 'pending')
                        <span class="badge badge-warning">Pending Approval</span>
                        @else
                        <span class="badge badge-secondary">Not yet responded.</span>
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

                            <!-- Similarity Manuscript Row -->
                            <tr>
                                <td class="text-center">Proposal Manuscript</td>
                                <td class="text-center">
                                    @if(!empty(optional($appointment)->similarity_manuscript))
                                        <a href="#" data-toggle="modal" data-target="#similarityManuscriptModal" class="text-primary text-decoration-underline">
                                            {{ basename($appointment->similarity_manuscript) }}
                                        </a>
                                    @else
                                        <span class="text-muted">No file uploaded</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="text-muted">Cannot be uploaded by student</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted">View Only</span>
                                </td>
                            </tr>

                            <!-- Similarity Certificate Row -->
                            <tr>
                                <td class="text-center">Similarity Certificate</td>
                                <td class="text-center">
                                    @if(!empty(optional($appointment)->similarity_certificate))
                                        <a href="#" data-toggle="modal" data-target="#similarityCertificateModal" class="text-primary text-decoration-underline">
                                            {{ basename($appointment->similarity_certificate) }}
                                        </a>
                                    @else
                                        <span class="text-muted">No file uploaded</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="text-muted">Cannot be uploaded by student</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted">View Only</span>
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
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <div class="alert alert-warning mb-0" role="alert">
                    <i class="fas fa-lock mr-2"></i>
                    <strong>Step Locked:</strong> This step is locked. A proposal defense date must be set to proceed.
                </div>
            </div>
        </div>
    @else
        <div class="container-fluid">
            <div class="container-flex">
                <!-- Main Proposal Manuscript Section -->
                <div class="proposal-section">
                    @if($appointment->similarity_manuscript)
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="routing-heading">Proposal Manuscript</h4>
                                <div class="table-responsive">
                                    <p>Main Proposal Manuscript: <i class="fa-solid fa-download"></i></p>
                                    <table class="table table-bordered table-hover custom-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th class="text-center">Original Proposal Manuscript</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">
                                                    <span 
                                                       onclick="$('#mainProposalManuscriptModal').modal('show')" 
                                                       style="cursor: pointer; color: #007bff; text-decoration: underline;">
                                                    {{ $appointment->original_similarity_manuscript_filename }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                            </div>
                        </div>
                    @else
                        <p>No main proposal manuscript uploaded.</p>
                    @endif
                </div>

                <!-- Proposal Manuscript Updates Section -->
                <div class="updates-section">
                    <div class="table-responsive">
                        <h4 class="routing-heading">Proposal Manuscript Updates</h4>
                        <table class="table table-bordered table-hover table-striped custom-table">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">File</th>
                                    <th class="text-center">Last Updated</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($appointment->proposal_manuscript_updates))
                                    @php
                                        $updates = json_decode($appointment->proposal_manuscript_updates, true);
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <a href="#" data-toggle="modal" data-target="#manuscriptUpdateModal">{{ $updates['original_name'] }}</a>
                                        </td>
                                        <td class="text-center">
                                            {{ isset($updates['uploaded_at']) ? \Carbon\Carbon::parse($updates['uploaded_at'])->format('m/d/Y h:i A') : 'Not available' }}
                                        </td>
                                        <td class="text-center">
                                            @if ($appointment->proposal_manuscript_update_status === 'pending')
                                                <span class="text-warning">Pending Adviser Approval</span>
                                            @elseif ($appointment->proposal_manuscript_update_status === 'approved')
                                                <span class="text-success">Approved</span>
                                            @else 
                                                <span class="text-danger">Disapproved</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ Storage::url($updates['file_path']) }}" download class="btn btn-primary">Download</a>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No updates available.</td>
                                    </tr>
                                @endif
                                <!-- Upload Form for New Update -->
                                <tr>
                                    <form action="{{ route('gsstudent.uploadProposalManuscriptUpdate') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <td class="text-center">
                                            <input type="file" name="proposal_manuscript_update" class="form-control" accept=".pdf" required>
                                        </td>
                                        <td class="text-center">{{ \Carbon\Carbon::now()->format('m/d/Y') }}</td>
                                        <td class="text-center">New Upload</td>
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

            <!-- Modal for Main Proposal Manuscript -->
            <div class="modal fade" id="mainProposalManuscriptModal" tabindex="-1" aria-labelledby="mainProposalManuscriptModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $appointment->original_similarity_manuscript_filename }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <iframe src="{{ Storage::url($appointment->similarity_manuscript) }}" width="100%" height="500px"></iframe>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ Storage::url($appointment->similarity_manuscript) }}" download class="btn btn-primary">Download</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for Proposal Manuscript Update -->
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

            <!-- Dean's Signature Section -->
            <div class="dean-signature-section">
               <h4 class="dean-signature-heading">Dean's Signature</h4>
               @php
               $allPanelSigned = count($appointment->panel_members ?? []) === count(array_filter(json_decode($appointment->panel_signatures, true) ?? []));
               @endphp
               @if ($allPanelSigned)
               @if ($appointment->dean_monitoring_signature)
               <p><strong>Dean's Signature:</strong> {{ $appointment->dean_monitoring_signature }}</p>
               @else
               <p><strong>Status:</strong> Waiting for Dean’s Signature</p>
               @endif
               @else
               <p><strong>Status:</strong> All panel members have not signed yet</p>
               @endif
            </div>


                <!-- Panel Signatures Section -->
    <div class="card mb-4 signatures-gallery">
        <h4 class="signatures-heading">Panelist Signatures</h4>
        <div class="signatures-grid">
            <!-- Loop through each panel member to display signature status -->
            @foreach ($appointment->panel_members ?? [] as $panelistId)
                @php
                    $panelist = \App\Models\User::find($panelistId);
                    $panelistName = $panelist ? $panelist->name : "Unknown Panelist";
                    $signature = $signatures[$panelistId] ?? null;
                @endphp
                <div class="signature-card mb-2">
                    <p><strong>{{ $panelistName }}</strong></p>
                    @if ($signature)
                        <p class="text-success">
                            <strong>Signed by:</strong> {{ is_array($signature) ? ($signature['name'] ?? $panelistName) : $signature }} 
                        </p>
                    @else
                        <p class="text-danger">Not signed yet.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>


            <!-- Panel Review Section -->
            <div class="container-fluid">
    <div class="card mb-4 review-panel">
        <h4 class="routing-heading">Comments and Responses</h4>

        <!-- Iterate through each panelist's comments -->
        @foreach ($appointment->panel_comments ?? [] as $comment)
        <div class="comment-item">
                <!-- Display Panelist's Comment -->
                <p><strong>Comment by {{ \App\Models\User::find($comment['panelist_id'])->name ?? 'Unknown Panelist' }}:</strong> {{ $comment['comment'] }}</p>
                <p><small>Posted on: {{ \Carbon\Carbon::parse($comment['created_at'])->format('m/d/Y h:i A') }}</small></p>

                <!-- Display Existing Student Reply for This Comment (if any) -->
                @php
                    // Filter replies for this specific comment
                    $repliesForComment = collect($appointment->student_replies)->where('comment_id', $comment['id']);
                @endphp
                @if($repliesForComment->isNotEmpty())
                    @foreach ($repliesForComment as $reply)
                        <div class="reply-item">
                            <p><strong>Your Reply:</strong> {{ $reply['reply'] }}</p>
                            <p><small>Replied on: {{ \Carbon\Carbon::parse($reply['created_at'])->format('m/d/Y h:i A') }}</small></p>

                            @if (!empty($reply['location']))
                                <p><strong>Location:</strong> {{ $reply['location'] }}</p>
                            @endif
                            <!-- Display Panel Remarks for this Reply -->
                            <h6>Panelist Remarks:</h6>
                            @php
                                // Filter remarks for this specific reply
                                $remarksForReply = collect($appointment->panel_remarks)->where('comment_id', $comment['id']);
                            @endphp
                            @if($remarksForReply->isNotEmpty())
                                @foreach ($remarksForReply as $remark)
                                    <div class="remark-item">
                                        <p><strong>Remark by {{ \App\Models\User::find($remark['panelist_id'])->name ?? 'Unknown Panelist' }}:</strong> {{ $remark['remark'] }}</p>
                                        <p><small>Remarked on: {{ \Carbon\Carbon::parse($remark['created_at'])->format('m/d/Y h:i A') }}</small></p>
                                    </div>
                                @endforeach
                            @else
                                <p>No remarks yet.</p>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p>No reply yet.</p>
                @endif

                <!-- Student Reply Form for this Comment -->
<!-- Student Reply Form -->
<form action="{{ route('gsstudent.addStudentReply', ['appointmentId' => $appointment->id, 'commentId' => $comment['id']]) }}" method="POST" class="form-section">
    @csrf
    <div class="form-group">
        <label for="reply">Your Reply</label>
        <textarea name="reply" class="form-control" placeholder="Enter your reply..." required></textarea>
    </div>
    <div class="form-group">
        <label for="location">Location (optional)</label>
        <input type="text" name="location" class="form-control" placeholder="E.g., Page 10, Paragraph 3">
    </div>
    <button type="submit" class="btn btn-primary mt-2">Submit Reply</button>
</form>

                <hr>
            </div>
        @endforeach
      </div>
               @php
               // Check if all panel members have signed
               $allPanelSigned = count($appointment->panel_members ?? []) === count(array_filter($signatures ?? []));
               @endphp
            </div>
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
            <div class="container my-4">
               <div class="card shadow mb-4">
                  <div class="card-body d-flex flex-column flex-md-row align-items-center">
                     <!-- QR Code Section -->
                     <div class="qr-code-section text-center mb-4 mb-md-0 align-items-center ">
                        <img src="{{ asset('img/cdaic_qr.png') }}" alt="QR Code for CDAIC Service Request" class="qr-code-image rounded" style="width: 180px; border: 2px solid #ddd;">
                        <p class="mt-2 text-muted" style="font-size: 0.9rem;">Scan for Service Request Form</p>
                     </div>
                     <!-- Instructions Section -->
                     <div class="instructions-section ml-md-4">
                        <h4 class="routing-heading">Consultation with Statistician</h4>
                        <p>Please complete the 
                        <a href="{{ $statisticianLink }}" target="_blank" class="text-primary">
    <i class="fa-solid fa-link"></i> CDAIC Service Request Form
</a>

                           Send your manuscript to:
                        </p>
                        <ul class="mb-3" style="list-style: none; padding-left: 0;">
                           <li><strong>cdaic@auf.edu.ph</strong></li>
                           <li>cc: <strong>calibio.mylene@auf.edu.ph</strong>, <strong>ovpri@auf.edu.ph</strong></li>
                        </ul>
                        <!-- Display Status -->
                        <p><strong>Status:</strong> 
                           @if ($appointment->statistician_approval === 'approved')
                           <span class="badge badge-success">Approved</span>
                           @elseif ($appointment->student_statistician_response === 'responded')
                           <span class="badge badge-warning">Pending</span>
                           @else
                           <span class="badge badge-secondary">Not responded yet</span>
                           @endif
                        </p>
                        <!-- Respond Button for Student -->
                        @if (is_null($appointment->student_statistician_response))
                        <form action="{{ route('gsstudent.respondToStatistician') }}" method="POST">
                           @csrf
                           <button type="submit" class="btn btn-primary">Mark as Responded</button>
                        </form>
                        @endif
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
         <!-- Content for Step 8 or 9 -->
         <div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h4 class="routing-heading">Ethics Review</h4>
            <p>Download and complete the following forms, then upload the required documents to submit to the Ethics Committee:</p>

            <!-- Table for Document Uploads and Status -->
            <div class="table-responsive">
                <table class="table table-bordered custom-table">
                    <thead class="table-light">
                        <tr>
                            <th>Document</th>
                            <th class="text-center">Download Form</th>
                            <th class="text-center">Uploaded File</th>
                            <th class="text-center">Upload Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Approved Proposal Manuscript -->
                        <tr>
                            <td>Approved Proposal Manuscript</td>
                            <td class="text-center">N/A</td>
                            <td class="text-center">
                                @php
                                    $updates = $appointment->proposal_manuscript_updates ? json_decode($appointment->proposal_manuscript_updates, true) : null;
                                @endphp
                                @if($updates && isset($updates['original_name']))
                                    <a href="#" data-toggle="modal" data-target="#approvedProposalModal">{{ $updates['original_name'] }}</a>
                                @else
                                    <span class="text-muted">No manuscript uploaded</span>
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
                            <td class="text-center">N/A</td>
                            <td class="text-center">
                                @if($appointment->ethics_proof_of_payment)
                                    <a href="#" data-toggle="modal" data-target="#ethicsProofOfPaymentModal">{{ $appointment->ethics_proof_of_payment_filename }}</a>
                                @else
                                    <form action="{{ route('gsstudent.uploadEthicsFile', 'ethics_proof_of_payment') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="ethics_proof_of_payment" class="form-control" accept=".pdf,.png,.jpg,.jpeg" required>
                                        <button type="submit" class="btn btn-primary mt-2">Upload</button>
                                    </form>
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
                            <td class="text-center">N/A</td>
                            <td class="text-center">
                                @if($appointment->ethics_curriculum_vitae)
                                    <a href="#" data-toggle="modal" data-target="#ethicsCurriculumVitaeModal">{{ $appointment->ethics_curriculum_vitae_filename }}</a>
                                @else
                                    <form action="{{ route('gsstudent.uploadEthicsFile', 'ethics_curriculum_vitae') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="ethics_curriculum_vitae" class="form-control" accept=".pdf,.png,.jpg,.jpeg" required>
                                        <button type="submit" class="btn btn-primary mt-2">Upload</button>
                                    </form>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $appointment->ethics_curriculum_vitae ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $appointment->ethics_curriculum_vitae ? 'Uploaded' : 'Not Uploaded' }}
                                </span>
                            </td>
                        </tr>

                        <!-- Dynamic Rows for Document Types with Download Links and Upload Options -->
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
                                </td>
                                <td class="text-center">
                                    @if($appointment->{$data['upload_field']})
                                        <a href="#" data-toggle="modal" data-target="{{ $data['modal'] }}">{{ $appointment->{$data['filename']} }}</a>
                                    @else
                                        <form action="{{ route('gsstudent.uploadEthicsFile', $data['upload_field']) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" name="{{ $data['upload_field'] }}" class="form-control" accept=".pdf" required>
                                            <button type="submit" class="btn btn-primary mt-2">Upload</button>
                                        </form>
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

            <!-- Send Button -->
            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('sendDataForm').submit();">Send</button>
            <form id="sendDataForm" action="{{ route('gsstudent.sendDataToAUFC') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</div>

         @endif
         @endif
      </div>
      @endfor
      <div class="card-footer footersaroute1"></div>
   </div>
</div>
<!-- Modals for Uploaded Files -->
<!-- Similarity Manuscript Modal -->
<div class="modal fade" id="similarityManuscriptModal" tabindex="-1" aria-labelledby="similarityManuscriptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="similarityManuscriptModalLabel">View Similarity Manuscript</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ Storage::url(optional($appointment)->similarity_manuscript) }}" width="100%" height="600px"></iframe>
            </div>
            <div class="modal-footer">
                <a href="{{ Storage::url(optional($appointment)->similarity_manuscript) }}" target="_blank" class="btn btn-primary" download>Download Similarity Manuscript</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Similarity Certificate Modal -->
<div class="modal fade" id="similarityCertificateModal" tabindex="-1" aria-labelledby="similarityCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="similarityCertificateModalLabel">View Similarity Certificate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ Storage::url(optional($appointment)->similarity_certificate) }}" width="100%" height="600px"></iframe>
            </div>
            <div class="modal-footer">
                <a href="{{ Storage::url(optional($appointment)->similarity_certificate) }}" target="_blank" class="btn btn-primary" download>Download Similarity Certificate</a>
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
         @if(isset($appointment) && $appointment->proposal_manuscript_updates)
               @php
                   // Decode the JSON data here within the modal
                   $updates = json_decode($appointment->proposal_manuscript_updates, true);
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
         @if(isset($appointment) && $appointment->ethics_proof_of_payment)
                <iframe src="{{ Storage::url($appointment->ethics_proof_of_payment) }}" width="100%" height="600px"></iframe>
            @else
                <p>No proof of payment uploaded.</p>
            @endif

         </div>
         <div class="modal-footer">
            @if(isset($appointment) && $appointment->ethics_proof_of_payment)
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
            @if(isset($appointment) && $appointment->ethics_curriculum_vitae)

            <iframe src="{{ Storage::url($appointment->ethics_curriculum_vitae) }}" width="100%" height="600px"></iframe>
            @else
            <p>No Curriculum Vitae uploaded.</p>
            @endif
         </div>
         <div class="modal-footer">
         @if(isset($appointment) && $appointment->ethics_curriculum_vitae)
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
            @if(isset($appointment) && $appointment->ethics_research_services_form)
            <iframe src="{{ Storage::url($appointment->ethics_research_services_form) }}" width="100%" height="600px"></iframe>
            @else
            <p>No Research Services Form uploaded.</p>
            @endif
         </div>
         <div class="modal-footer">
         @if(isset($appointment) && $appointment->ethics_research_services_form)
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
            @if(isset($appointment) && $appointment->ethics_application_form)
            <iframe src="{{ Storage::url($appointment->ethics_application_form) }}" width="100%" height="600px"></iframe>
            @else
            <p>No Application for Ethics Review Form uploaded.</p>
            @endif
         </div>
         <div class="modal-footer">
         @if(isset($appointment) && $appointment->ethics_application_form)
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
            @if(isset($appointment) && $appointment->ethics_study_protocol_form)
            <iframe src="{{ Storage::url($appointment->ethics_study_protocol_form) }}" width="100%" height="600px"></iframe>
            @else
            <p>No Study Protocol Assessment Form uploaded.</p>
            @endif
         </div>
         <div class="modal-footer">
         @if(isset($appointment) && $appointment->ethics_study_protocol_form)
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
            @if(isset($appointment) && $appointment->ethics_informed_consent_form)
            <iframe src="{{ Storage::url($appointment->ethics_informed_consent_form) }}" width="100%" height="600px"></iframe>
            @else
            <p>No Informed Consent Assessment Form uploaded.</p>
            @endif
         </div>
         <div class="modal-footer">
         @if(isset($appointment) && $appointment->ethics_informed_consent_form)
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
            @if(isset($appointment) && $appointment->ethics_sample_informed_consent)
            <iframe src="{{ Storage::url($appointment->ethics_sample_informed_consent) }}" width="100%" height="600px"></iframe>
            @else
            <p>No Sample Informed Consent uploaded.</p>
            @endif
         </div>
         <div class="modal-footer">
         @if(isset($appointment) && $appointment->ethics_sample_informed_consent)
         <a href="{{ Storage::url($appointment->ethics_sample_informed_consent) }}" download class="btn btn-primary">Download</a>
            @endif
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