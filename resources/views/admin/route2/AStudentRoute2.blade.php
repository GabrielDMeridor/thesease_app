@extends('admin.Amain-layout')
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
        @elseif ($step === 6)
            <!-- Step 6 Content -->
        @elseif ($step === 7)
            <!-- Step 7 Content -->
        @elseif ($step === 8)
            <!-- Step 8 Content -->
        @elseif ($step === 9)
            <!-- Step 9 Content -->
        @endif
    </div>

    <div class="card-footer"></div>
</div>
@endsection
