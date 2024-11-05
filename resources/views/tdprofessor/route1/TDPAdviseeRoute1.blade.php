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
            <!-- Limit the notifications to 5 -->
            <div class="overflow-auto" style="max-height: 300px;">
               <!-- This is the scrolling part -->
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
<!-- Title Section -->
<div class="container-fluid">
<div class="sagreet">{{ $title }}</div>
<br>
<div class="card shadow mb-4">
<div class="card-header"></div>
<div class="card-body">
   @php
   $isDrPH = $student->program === 'DrPH';
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
         <!-- Step 1: Adviser Form Content -->
         <div class="container-fluid">
            <div class="card shadow mb-4">
               <div class="card-body">
                  <form method="POST" action="{{ route('professor.signRoutingForm', $appointment->id) }}">
                     @csrf
                     @method('PUT')
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
                        <input type="text" name="program" value="{{ $advisee->program }}" class="form-control" readonly>
                     </div>
                     <!-- Adviser Type -->
                     <div class="form-group">
                        <label for="appointment_type">Adviser Type:</label>
                        <input type="text" name="appointment_type" value="{{ $appointment->appointment_type }}" class="form-control" readonly>
                     </div>
                     <hr>
                     <!-- Signatures -->
                     <h4 class="routing-heading">Signatures</h4>
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
                     <button type="submit" class="btn btn-affix" style="color:white;">Affix Adviser's Signature</button>
                     @endif
                  </form>
               </div>
            </div>
         </div>
         @elseif ($step === 2)
         <!-- Step 2: Consultation with Adviser and Endorsement Signature -->
         <div class="tab-content" id="pills-tabContent">
            @if (is_null($appointment->adviser_signature) || is_null($appointment->chair_signature) || is_null($appointment->dean_signature))
            <!-- Step is locked: Display the lock message -->
            <div class="container d-flex justify-content-center my-4">
               <div style="width: 100%;">
                  <div class="card-body text-center">
                     <div class="alert alert-warning mb-0" role="alert">
                        <i class="fas fa-lock mr-2"></i>
                        <strong>Step Locked:</strong> Step 2 is locked. You must complete the signatures for the Adviser, Program Chair, and Dean to proceed.
                     </div>
                  </div>
               </div>
            </div>
            @else
            <!-- Step is unlocked: Show the step content -->
            <div class="container-fluid">
               <div class="card shadow mb-4">
                  <div class="card-body">
                     <h4 class="routing-heading">Consultation with Adviser and Endorsement Signature</h4>
                     <form method="POST" action="{{ route('professor.addConsultationDatesAndSign', $appointment->id) }}">
                        @csrf
                        <!-- Dynamic Consultation Dates Section -->
                        <div class="form-group">
                           <label for="consultation_dates">Consultation Dates:</label>
                           <div id="consultation_dates_container">
                              @if ($appointment->consultation_dates)
                              @foreach (json_decode($appointment->consultation_dates) as $date)
                              <div class="input-group mb-2">
                                 <input type="date" name="consultation_dates[]" class="form-control" value="{{ $date }}" readonly>
                                 <div class="input-group-append">
                                    <button class="btn btn-danger" type="button" onclick="removeConsultationDate(this)">Remove</button>
                                 </div>
                              </div>
                              @endforeach
                              @endif
                           </div>
                           <!-- Button to add more consultation dates -->
                           <button type="button" class="btn btn-primary" onclick="addConsultationDate()">Add Date</button>
                        </div>
                        <!-- Adviser Signature Field -->
                        <div class="form-group">
                           <label for="adviser_signature">Adviser Signature:</label>
                           @if (is_null($appointment->adviser_endorsement_signature))
                           <input type="text" name="adviser_signature" class="form-control" placeholder="Sign here">
                           @else
                           <input type="text" name="adviser_signature" class="form-control" value="{{ $appointment->adviser_endorsement_signature }}" readonly>
                           @endif
                        </div>
                        <!-- Endorsement Signature -->
                        <div class="form-group">
                           @if (is_null($appointment->adviser_endorsement_signature))
                           <button type="submit" class="btn btn-affix" style="color: white;">Affix Endorsement Signature</button>
                           @endif
                        </div>
                     </form>
                  </div>
               </div>
            </div>
            @endif
         </div>
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
               <h1 class="routing-heading" style="font-size: 60px; text-align:center;">Similarity Check</h1>
               <div class="card-body">
                  <h4 class="routing-heading">Uploaded Similarity Manuscript</h4>
                  @if($appointment->similarity_manuscript)
                  <!-- Link to open manuscript modal -->
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
                  <!-- Link to open certificate modal -->
                  <div class="form-group">
                     <label for="certificate">Uploaded Certificate:</label>
                     <i class="fa-solid fa-download"></i>
                     <input type="text" 
                        id="certificate" 
                        class="form-control" 
                        value="{{ basename($appointment->similarity_certificate) }}" 
                        readonly 
                        onclick="$('#certificateModal').modal('show')" 
                        style="cursor: pointer;" />
                  </div>
                  @else
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
                        Research Registration Form
                        </a>. 
                        Note that the primary author will be the student, and the adviser will be the co-author. A copy of the form responses will be sent to the adviser’s email.
                     </p>
                     <p>After completing the form, please forward the copy and the manuscript to the following emails:
                        <br><strong>cdaic@auf.edu.ph</strong> (cc: <strong>ovpri@auf.edu.ph</strong>, <strong>collegesecretary.gs@auf.edu.ph</strong>).
                     </p>
                     <p>After completing the form, please click on the button below to mark as responded to notify the OVPRI in the system.</p>
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
                     <!-- Button to mark as responded -->
                     @if ($appointment->ovpri_approval !== 'approved' && $appointment->registration_response !== 'responded')
                     <form method="POST" action="{{ route('tdprofessor.markRegistrationResponded', $appointment->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Respond</button>
                     </form>
                     @endif
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
         <!-- Step 5 for non-DrPH or Step 6 for DrPH - File Uploads -->
         <div class="container-fluid">
            <div class="row">
               <div class="col-md-6">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="routing-heading">File Uploads</h4>
                        <!-- Signed Routing Form 1 -->
                        <div class="form-group">
                           @if($appointment->signed_routing_form_1)
                           <div class="form-group">
                              <label for="signed_routing_form_1">Signed Routing Form 1:</label>
                              <i class="fa-solid fa-download"></i>
                              <input type="text" 
                                 id="signed_routing_form_1" 
                                 class="form-control" 
                                 value="{{ $appointment->original_signed_routing_form_1 }}" 
                                 readonly 
                                 onclick="$('#routingFormModal').modal('show')" 
                                 style="cursor: pointer;">
                           </div>
                           @else
                           <div class="form-group">
                              <label for="signed_routing_form_1">Signed Routing Form 1:</label>
                              <input type="text" 
                                 id="signed_routing_form_1" 
                                 class="form-control" 
                                 value="File not yet uploaded" 
                                 readonly 
                                 disabled 
                                 style="cursor: not-allowed;">
                           </div>
                           @endif
                        </div>
                        <!-- Proposal Manuscript -->
                        <div class="form-group">
                           @if($appointment->proposal_manuscript)
                           <div class="form-group">
                              <label for="proposal_manuscript">Proposal Manuscript:</label>
                              <i class="fa-solid fa-download"></i>
                              <input type="text" 
                                 id="proposal_manuscript" 
                                 class="form-control" 
                                 value="{{ $appointment->original_proposal_manuscript }}" 
                                 readonly 
                                 onclick="$('#proposalManuscriptModal').modal('show')" 
                                 style="cursor: pointer;">
                           </div>
                           @else
                           <div class="form-group">
                              <label for="proposal_manuscript">Proposal Manuscript:</label>
                              <input type="text" 
                                 id="proposal_manuscript" 
                                 class="form-control" 
                                 value="File not yet uploaded" 
                                 readonly 
                                 disabled 
                                 style="cursor: not-allowed;">
                           </div>
                           @endif
                        </div>
                        <!-- Video Presentation -->
                        <div class="form-group">
                           @if($appointment->proposal_video_presentation)
                           <div class="form-group">
                              <label for="proposal_video_presentation">Video Presentation:</label>
                              <i class="fa-solid fa-download"></i>
                              <input type="text" 
                                 id="proposal_video_presentation" 
                                 class="form-control" 
                                 value="{{ $appointment->original_proposal_video_presentation }}" 
                                 readonly 
                                 onclick="$('#videoPresentationModal').modal('show')" 
                                 style="cursor: pointer;">
                           </div>
                           @else
                           <div class="form-group">
                              <label for="proposal_video_presentation">Video Presentation:</label>
                              <input type="text" 
                                 id="proposal_video_presentation" 
                                 class="form-control" 
                                 value="Student did not upload the file yet" 
                                 readonly 
                                 disabled 
                                 style="cursor: not-allowed;">
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               @if ($appointment->proposal_submission_completed)
               <div class="col-md-6">
                  <div class="card mt-4 mt-md-0">
                     <div class="card-body">
                        <h5>Submission Files</h5>
                        <!-- Display the submission files link if it exists -->
                        @if ($appointment->submission_files_link)
                        <p>
                           Submission Files Link:
                           <a href="{{ $appointment->submission_files_link }}" target="_blank">
                           <i class="fa-solid fa-link"></i>
                           View Submission Files
                           </a>
                        </p>
                        @else
                        <p class="text-muted">Submission files link will be uploaded by the Superadmin/Admin/Graduate School.</p>
                        @endif
                        <!-- Display the student's response status -->
                        <p>
                           <strong>Your Response:</strong>
                           @if ($appointment->submission_files_response === 1)
                           <span class="text-success">Responded</span>
                           @else
                           <span class="text-muted">Not responded yet.</span>
                           @endif
                        </p>
                        <!-- Display the approval status -->
                        <p>
                           <strong>Approval Status:</strong>
                           @if ($appointment->submission_files__approval === 'approved')
                           <span class="text-success">Approved</span>
                           @elseif ($appointment->submission_files__approval === 'pending')
                           <span class="text-warning">Pending Approval</span>
                           @else
                           <span class="text-muted">Not yet responded.</span>
                           @endif
                        </p>
                     </div>
                  </div>
               </div>
               @endif
            </div>
         </div>
         <br>
         @endif
         @elseif (($step === 6 && !$isDrPH) || ($step === 7 && $isDrPH))
         @if (optional($appointment)->proposal_defense_date === null)
         <p class="text-muted">This step is locked. A proposal defense date must be set to proceed.</p>
         @else
         <div class="container-fluid">
            <div class="container-flex">
               <!-- Main Proposal Manuscript Section -->
               <div class="proposal-section">
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
                                       {{ $appointment->original_proposal_manuscript }}
                                       </span>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                           <bR>
                           <!-- Proposal Manuscript Updates Section -->
                           <div class="updates-section">
                              <div class="table-responsive">
                                 <h4 class="routing-heading">Proposal Manuscript Updates</h4>
                                 <p>Updated Proposal Manuscript: <i class="fa-solid fa-download"></i></p>
                                 <table class="table table-bordered table-hover table-striped custom-table">
                                    <thead class="table-dark">
                                       <tr>
                                          <th class="text-center">File</th>
                                          <th class="text-center">Last Updated</th>
                                          <th class="text-center">Action</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       @if($appointment->proposal_manuscript_updates)
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
                                             <a href="{{ Storage::url($updates['file_path']) }}" download class="btn btn-primary">Download</a>
                                          </td>
                                       </tr>
                                       @else
                                       <tr>
                                          <td colspan="3" class="text-center">No updates available.</td>
                                       </tr>
                                       @endif
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
                     </div>
                  </div>
               </div>
               <hr>
               <Br>
               <!-- Panel Review Section -->
               <div class="review-panel">
                  <h4 class="routing-heading">Panel Review</h4>
                  @foreach ($appointment->panel_members as $panelistId)
                  @php
                  // Retrieve panelist information
                  $panelist = \App\Models\User::find($panelistId);
                  $panelistName = $panelist ? $panelist->name : "Unknown Panelist";
                  $comments = json_decode($appointment->panel_comments, true) ?? [];
                  $replies = json_decode($appointment->student_replies, true) ?? [];
                  $remarks = json_decode($appointment->panel_remarks, true) ?? [];
                  $signatures = json_decode($appointment->panel_signatures, true) ?? [];
                  @endphp
                  <div class="panelist-card">
                     <div class="panelist-header">
                        <h5 class="panelist-name">{{ $panelistName }}</h5>
                        <span class="signature-status {{ !empty($signatures[$panelistId]) ? 'signed' : 'unsigned' }}">
                        {{ !empty($signatures[$panelistId]) ? 'Signed' : 'Unsigned' }}
                        </span>
                     </div>
                     <div class="panelist-content">
                        <p><strong>Comment:</strong> {{ $comments[$panelistId] ?? 'No comment yet' }}</p>
                        <p><strong>Student Reply:</strong> {{ $replies[$panelistId] ?? 'No reply yet' }}</p>
                        <p><strong>Remarks:</strong> {{ $remarks[$panelistId] ?? 'No remarks yet' }}</p>
                        <p><strong>Signature:</strong> {{ $signatures[$panelistId] ?? 'Not signed yet' }}</p>
                     </div>
                  </div>
                  @endforeach
               </div>
               <!-- Dean's Signature Section -->
               <div class="dean-signature-section">
                  <h4 class="dean-signature-heading">Dean's Signature</h4>
                  @php
                  $allPanelSigned = count($appointment->panel_members ?? []) === count(array_filter($signatures ?? []));
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
            </div>
            <!-- Modals -->
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
<div>
   
</div>
@endif
@endif
      </div>
      @endfor
   </div>
</div>
<div class="card-footer footersaroute1"></div>
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
<!-- JavaScript for adding/removing consultation dates -->
<script>
   function addConsultationDate() {
       const container = document.getElementById('consultation_dates_container');
       const inputGroup = document.createElement('div');
       inputGroup.classList.add('input-group', 'mb-2');
       inputGroup.innerHTML = `
           <input type="date" name="consultation_dates[]" class="form-control" required onchange="saveDateToDatabase(this)">
           <div class="input-group-append">
               <button class="btn btn-danger" type="button" onclick="removeConsultationDate(this)">Remove</button>
           </div>
       `;
       container.appendChild(inputGroup);
   }
   
   function removeConsultationDate(button) {
       const inputGroup = button.closest('.input-group');
       const dateValue = inputGroup.querySelector('input').value;
       const appointmentId = {{ $appointment->id }};
       
       // Send AJAX request to remove the date from the database
       if (dateValue) {
           fetch('{{ route('professor.removeConsultationDate') }}', {
               method: 'POST',
               headers: {
                   'Content-Type': 'application/json',
                   'X-CSRF-TOKEN': '{{ csrf_token() }}',
               },
               body: JSON.stringify({
                   consultation_date: dateValue,
                   appointment_id: appointmentId
               })
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   alert('Date removed successfully');
                   inputGroup.remove(); // Remove the input group from the DOM
               } else {
                   alert('Error removing date');
               }
           })
           .catch(error => console.error('Error:', error));
       }
   }
   
   // AJAX function to save date to database
   function saveDateToDatabase(input) {
       const dateValue = input.value;
       const appointmentId = {{ $appointment->id }};
   
       // Only proceed if the user has selected a date
       if (dateValue) {
           // Send AJAX request to server to save the date
           fetch('{{ route('professor.saveConsultationDate') }}', {
               method: 'POST',
               headers: {
                   'Content-Type': 'application/json',
                   'X-CSRF-TOKEN': '{{ csrf_token() }}',
               },
               body: JSON.stringify({
                   consultation_date: dateValue,
                   appointment_id: appointmentId
               })
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   alert('Date saved successfully');
               } else {
                   alert('Error saving date');
               }
           })
           .catch(error => console.error('Error:', error));
       }
   }
</script>