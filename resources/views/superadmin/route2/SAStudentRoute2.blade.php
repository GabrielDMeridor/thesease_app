@extends('superadmin.SAmain-layout')
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
                @elseif ($step === 2)
                    <!-- Step 2 Content -->
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
@endsection
