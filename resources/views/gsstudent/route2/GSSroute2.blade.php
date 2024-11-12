@extends('gsstudent.GSSmain-layout')

@section('content-header')
    <!-- Header Code Here -->
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
                
                {{-- Step 1 Content: Manuscript Upload and Adviser Consultation --}}
                @if ($step === 1)
                    <div class="container-fluid">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="container">
                                    <h4>Upload Revised Manuscript</h4>
                                    @if (!$appointment->final_adviser_endorsement_signature)
                                        <form method="POST" action="{{ route('gsstudent.uploadManuscript', $appointment->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
                                                <label for="revised_manuscript">Upload Revised Manuscript:</label>
                                                <input type="file" name="revised_manuscript" class="form-control" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Upload</button>
                                        </form>
                                    @else
                                        <p class="text-muted">Final endorsement signature exists; uploading is disabled.</p>
                                    @endif

                                    @if ($appointment->revised_manuscript_path)
                                        <div class="mt-3">
                                            <label>Uploaded Manuscript:</label>
                                            <a href="#" data-toggle="modal" data-target="#manuscriptModal">
                                                {{ $appointment->revised_manuscript_original_name }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <br>

                                <h4 class="routing-heading">Consultation with Adviser and Final Endorsement Signature</h4>
                                @if ($appointment)
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
                                                <input type="text" name="final_adviser_signature" class="form-control" placeholder="Waiting for your Adviser's Signature">
                                            @else
                                                <input type="text" name="final_adviser_signature" class="form-control" value="{{ $appointment->final_adviser_endorsement_signature }}" readonly>
                                            @endif
                                        </div>

                                    </form>
                                @else
                                    <p>No appointment found for this student.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                {{-- Step 2 Content: Statistician Consultation --}}
                @elseif ($step === 2)
                    <div class="container my-4">
                        <div class="card shadow mb-4">
                            <div class="card-body d-flex flex-column flex-md-row align-items-center">
                                <!-- Instructions Section -->
                                <div class="instructions-section ml-md-4">
                                    <h4 class="routing-heading">Consultation with Statistician</h4>
                                    <p>
                                        Please complete the                         
                                        <a href="{{ $final_statisticianLink }}" target="_blank" class="text-primary">
                                            <i class="fa-solid fa-link"></i> CDAIC Service Request Form
                                        </a> and send your manuscript to:
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
                                    @if (is_null($appointment->final_student_statistician_response))
                                        <form action="{{ route('gsstudent.respondToFinalStatistician') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">Mark as Responded</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                            <!-- Proof of Publication Upload (Conditional Display for Specific Programs) -->
        @php
            $eligiblePrograms = [
                'PhD-Ed-EM', 'PhD-Mgmt', 'DIT', 'MAEd', 'MANm', 'MSPH',
                'MBA', 'MIT', 'MA-Pysch-CP', 'MS-MLS', 'MSPH', 'MSCJ-Crim'
            ];
        @endphp

        @if (in_array($program, $eligiblePrograms))
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4>Submission of Proof of Publication (If required)</h4>
                    <p>Send your proof of publication to your program chair, cc: adviser and <strong>collegesecretary.gs@auf.edu.ph</strong>. (<a href="https://docs.google.com/document/d/1Dc8m5mJYenYDTLaUtuBXcr-xwFR73HF1/edit" target="_blank">See Publication Guidelines</a>.)</p>
                    
                    <!-- Upload Proof of Publication -->
                    @if (is_null($appointment->proof_of_publication_path))
                        <form method="POST" action="{{ route('gsstudent.uploadProofOfPublication', $appointment->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="proof_of_publication">Upload Proof of Publication:</label>
                                <input type="file" name="proof_of_publication" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    @else
                        <div class="mt-3">
                            <label>Uploaded Proof of Publication:</label>
                            <a href="#" data-toggle="modal" data-target="#publicationModal">
                                {{ $appointment->proof_of_publication_original_name }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

<!-- Publication Modal for Viewing Uploaded Proof -->
<div class="modal fade" id="publicationModal" tabindex="-1" aria-labelledby="publicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="publicationModalLabel">View Proof of Publication</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ asset('storage/' . $appointment->proof_of_publication_path) }}" width="100%" height="600px" style="border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <a href="{{ asset('storage/' . $appointment->proof_of_publication_path) }}" target="_blank" class="btn btn-primary" download>Download Proof</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

                {{-- Additional Steps Content --}}
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
                <h4 class="routing-heading">Upload Final Similarity Manuscript</h4>
                @if(is_null($appointment->final_similarity_certificate))
                    <form action="{{ route('gsstudent.uploadFinalSimilarityManuscript', $appointment->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="final_similarity_manuscript">Upload Manuscript</label>
                            <input type="file" name="final_similarity_manuscript" class="form-control" required accept=".pdf,.doc,.docx">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            {{ is_null($appointment->final_similarity_manuscript) ? 'Upload Manuscript' : 'Update Manuscript' }}
                        </button>
                    </form>
                @endif

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
                @endif
            </div>
        </div>
    </div>

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
                            value="{{ $appointment->final_similarity_certificate_original_name }}" 
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
            <th>Community Uploads</th>
        </tr>
    </thead>
    <tbody>
        <!-- Program Chair Row -->
        <tr>
            <td>Program Chair</td>
            <td>
                @if ($appointment->final_program_chair_signature)
                    <span class="badge badge-success">Signed</span>
                @else
                    <span class="badge badge-secondary">Not Signed</span>
                @endif
            </td>
            <td>
                <!-- Display link if Community Service Form is uploaded -->
                @if($appointment->community_extension_service_form_path)
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#serviceFormModal">
                        View Service Form
                    </button>
                @else
                    <!-- Show file upload form if no file is uploaded -->
                    <form action="{{ route('gsstudent.uploadCommunityExtensionForms') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="community_extension_service_form" class="form-control mb-2" required>
                        <button type="submit" class="btn btn-success btn-sm">Upload Service Form</button>
                    </form>
                @endif
            </td>
        </tr>
        
        <!-- CCFP (Asst. Director for Christian Praxis) Row -->
        <tr>
            <td>CCFP (Asst. Director for Christian Praxis)</td>
            <td>
                @if ($appointment->final_ccfp_signature)
                    <span class="badge badge-success">Signed</span>
                @else
                    <span class="badge badge-secondary">Not Signed</span>
                @endif
            </td>
            <td>
                <!-- Display link if Accomplishment Report is uploaded -->
                @if($appointment->community_accomplishment_report_path)
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#accomplishmentReportModal">
                        View Accomplishment Report
                    </button>
                @else
                    <!-- Show file upload form if no file is uploaded -->
                    <form action="{{ route('gsstudent.uploadCommunityExtensionForms') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="community_accomplishment_report" class="form-control mb-2" required>
                        <button type="submit" class="btn btn-success btn-sm">Upload Accomplishment Report</button>
                    </form>
                @endif
            </td>
        </tr>
    </tbody>
</table>


        </div>
    </div>
</div>

<!-- Community Extension Service Form Modal -->
<div class="modal fade" id="serviceFormModal" tabindex="-1" aria-labelledby="serviceFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceFormModalLabel">Community Extension Service Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ Storage::url($appointment->community_extension_service_form_path) }}" width="100%" height="500px"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Community Accomplishment Report Modal -->
<div class="modal fade" id="accomplishmentReportModal" tabindex="-1" aria-labelledby="accomplishmentReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accomplishmentReportModalLabel">Community Accomplishment Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ Storage::url($appointment->community_accomplishment_report_path) }}" width="100%" height="500px"></iframe>
            </div>
        </div>
    </div>
</div>

                @elseif(($isDrPH && $step === 6) || (!$isDrPH && $step === 5))
                @if (auth()->user()->nationality === 'Filipino' && $appointment->final_submission_approval === 'approved')
    <div class="card shadow mb-4">
        <div class="card-body text-center">
            <h4 class="routing-heading">Submission Files</h4>
            @if ($globalSubmissionLink)
            <p>
                <a href="{{ $globalSubmissionLink }}" target="_blank" class="text-primary" style="font-size: 1.25rem;">
                    <i class="fa-solid fa-link"></i> View Submission Files
                </a>
            </p>
            @endif
            <!-- Display student's response status -->
            <p>Your Response:
                @if ($appointment->final_submission_files_response)
                    <span class="badge badge-success">Responded</span>
                @else
                    <span class="badge badge-warning">Not responded yet</span>
                @endif
            </p>

            <!-- Display admin approval status -->
            <p>Approval Status:
                @if ($appointment->final_submission_approval_formfee === 'approved')
                    <span class="badge badge-success">Approved</span>
                @elseif ($appointment->final_submission_approval_formfee === 'pending')
                    <span class="badge badge-warning">Pending Approval</span>
                @else
                    <span class="badge badge-secondary">Not yet responded.</span>
                @endif
            </p>

            <!-- Button to respond to submission files if not responded yet -->
            @if (!$appointment->final_submission_files_response)
                <form action="{{ route('gsstudent.respondToFinalSubmissionFiles', $appointment->id) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-primary">Respond to Submission Files</button>
                </form>
            @endif
        </div>
    </div>
@else
    <p class="text-muted">Condition not met for displaying Submission Files.</p>
@endif

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
            <!-- Show the upload form only if final_submission_approval is not approved -->
            <form action="{{ route('gsstudent.uploadManuscript', $appointment) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="revised_manuscript" class="form-control mb-2" accept=".pdf,.doc,.docx" required>
                <button type="submit" class="btn btn-primary">Upload Manuscript</button>
            </form>
        @else
            <!-- Show a message indicating upload is not allowed -->
            <span class="text-muted">Upload not allowed after approval of Language Editor</span>
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

<!-- Manuscript Modal -->
<div class="modal fade" id="manuscriptModal" tabindex="-1" aria-labelledby="manuscriptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manuscriptModalLabel">Manuscript</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ Storage::url($appointment->final_similarity_manuscript) }}" width="100%" height="500px"></iframe>
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

<!-- Proof of Publication Modal -->
<div class="modal fade" id="proofOfPublicationModal" tabindex="-1" aria-labelledby="proofOfPublicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofOfPublicationModalLabel">Proof of Publication</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ Storage::url($appointment->proof_of_publication_path) }}" width="100%" height="500px"></iframe>
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


@elseif(($isDrPH && $step === 7) || (!$isDrPH && $step === 6))
@if (optional($appointment)->proposal_defense_date === null)
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <div class="alert alert-warning mb-0" role="alert">
                    <i class="fas fa-lock mr-2"></i>
                    <strong>Step Locked:</strong> This step is locked. A Final defense date must be set to proceed.
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
                                <h4 class="routing-heading">Final Manuscript</h4>
                                <div class="table-responsive">
                                    <p>Main Final Manuscript: <i class="fa-solid fa-download"></i></p>
                                    <table class="table table-bordered table-hover custom-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th class="text-center">Original Final Manuscript</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">
                                                    <span 
                                                       onclick="$('#mainProposalManuscriptModal').modal('show')" 
                                                       style="cursor: pointer; color: #007bff; text-decoration: underline;">
                                                    {{ $appointment->final_similarity_manuscript ? basename($appointment->final_similarity_manuscript) : 'No manuscript uploaded' }}
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
                        <h4 class="routing-heading">Final Manuscript Updates</h4>
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
                                @if(empty($appointment->proposal_manuscript_updates))
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
         @elseif(($isDrPH && $step === 8) || (!$isDrPH && $step === 7))
         <!-- Step 8 Content -->
         <div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h4 class="routing-heading"> Layouting and Final Draft</h4>
            <p>Follow and use the corresponding layout for your manuscript:</p>
            <ul>
                <li>
                    <a href="{{ url('https://docs.google.com/document/d/15UNiguLBxIoRik0Yrw_uBvgNjhdldzXm/edit') }}" target="_blank">Thesis GS Template</a>
                </li>
                <li>
                    <a href="{{ url('https://docs.google.com/document/d/1t1LD-iIvaDdVCyyxydI1FX9oLF3seHW8/edit') }}" target="_blank">Dissertation GS Template</a>
                </li>
            </ul>

            <p>Appendices should include the following:</p>
            <ul>
                <li><strong>A.</strong> Ethics Clearance</li>
                <li><strong>B.</strong> Research Instruments</li>
                <li><strong>C.</strong> Copy of Published Paper / Acceptance Letter</li>
                <li><strong>D.</strong> Relevant Letters of Approval</li>
                <li><strong>E.</strong> Certification of Language Editing</li>
                <li><strong>F.</strong> Certification of Turnitin Similarity Check</li>
            </ul>

            <p>Email GS the links to the Google folder and the signed monitoring form.</p>
            <p>GS informs the student if the manuscript is ready for printing and bookbinding.</p>
        </div>
    </div>
</div>

@elseif(($isDrPH && $step === 9) || (!$isDrPH && $step === 8))
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h4 class="routing-heading">GS Exit Interview Questionnaire</h4>
            <p>Accomplish this <a href="{{ url('https://docs.google.com/forms/d/e/1FAIpQLSdGiYFAb17KcqeCGFKSiyd23aQ1xxmYjx2xltDkmrcrt5Ki3w/viewform') }}" target="_blank">exit interview</a> form. You will receive a copy of your responses to your email. Forward to <strong><a href="mailto:defense.gs@auf.edu.ph">defense.gs@auf.edu.ph</a></strong>.</p>
            
            <p>Student is advised to apply for graduation. Note that as per GS Bulletin 2022, the official graduation date is the last day of the trimester when all the program requirements have been satisfied and submitted to the registrar’s office.</p>
        </div>
    </div>
</div>
                @endif
            </div>
        @endfor
    </div>
    <div class="card-footer"></div>
</div>

<!-- Manuscript Modal -->
<div class="modal fade" id="manuscriptModal" tabindex="-1" aria-labelledby="manuscriptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manuscriptModalLabel">View Revised Manuscript</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ asset('storage/' . $appointment->revised_manuscript_path) }}" width="100%" height="600px" style="border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <a href="{{ asset('storage/' . $appointment->revised_manuscript_path) }}" target="_blank" class="btn btn-primary" download>Download Manuscript</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection
