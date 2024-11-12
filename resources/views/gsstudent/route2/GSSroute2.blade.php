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

                {{-- Additional Steps Content --}}
                @elseif ($step === 3)


                    <!-- Step 3 Content -->

                @elseif ($step === 4)
                    <!-- Step 4 Content -->

                @elseif ($step === 5)
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
