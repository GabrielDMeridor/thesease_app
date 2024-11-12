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
