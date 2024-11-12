@extends('library.Lmain-layout')

@section('content-header')
    <!-- Navigation & Header here, similar to Route 1 -->
@endsection

@section('body')
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div> 
    <br>

    <!-- Search Bar -->
    <form action="{{ route('library.route2') }}" method="GET">
        <div class="input-group mb-3">
            <input type="text" name="query" class="form-control" placeholder="Search Student by Name..." value="{{ $keyword }}">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Students Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped custom-table">
            <thead class="table-dark">
                <tr>
                    <th style="text-align:center;">Student Name</th>
                    <th style="text-align:center;">Email</th>
                    <th style="text-align:center;">Final Manuscript</th>
                    <th style="text-align:center;">Final Certificate</th>
                    <th style="text-align:center;">Action</th>
                    <th style="text-align:center;">Deny</th>
                </tr>
            </thead>
            <tbody id="appointments-table">
                @foreach($appointments as $appointment)
                    <tr>
                        <td class="text-center">{{ $appointment->student->name }}</td>
                        <td class="text-center">{{ $appointment->student->email }}</td>
                        <td class="text-center">
                            @if($appointment->final_similarity_manuscript)
                                <a href="#" data-toggle="modal" data-target="#manuscriptModal{{ $appointment->id }}">
                                    {{ basename($appointment->final_similarity_manuscript) }}
                                </a>
                            @else
                                <span>No manuscript uploaded</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('library.uploadFinalSimilarityCertificate') }}" method="POST" enctype="multipart/form-data" id="certificateUploadForm{{ $appointment->student_id }}">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $appointment->student_id }}">
                                
                                @if($appointment->final_similarity_certificate)
                                    <a href="#" data-toggle="modal" data-target="#certificateModal{{ $appointment->id }}">
                                        {{ basename($appointment->final_similarity_certificate) }}
                                    </a>
                                @else
                                    <input type="file" name="final_similarity_certificate" class="form-control" required accept=".pdf">
                                @endif
                            </form>
                        </td>
                        <td class="text-center">
                            <button type="submit" form="certificateUploadForm{{ $appointment->student_id }}" class="btn btn-primary">Save</button>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#denyModal{{ $appointment->id }}">
                                Deny
                            </button>
                        </td>
                    </tr>

                    <!-- Manuscript Modal -->
                    <div class="modal fade" id="manuscriptModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="manuscriptModalLabel{{ $appointment->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="manuscriptModalLabel{{ $appointment->id }}">View Manuscript</h5>
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
                    <div class="modal fade" id="certificateModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="certificateModalLabel{{ $appointment->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="certificateModalLabel{{ $appointment->id }}">View Certificate</h5>
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

                    <!-- Denial Modal -->
                    <div class="modal fade" id="denyModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="denyModalLabel{{ $appointment->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="denyModalLabel{{ $appointment->id }}">Deny Manuscript</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('library.denyFinalManuscript', $appointment->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="denialReason">Reason for Denial</label>
                                            <textarea name="denialReason" id="denialReason" class="form-control" rows="3" required placeholder="Enter the reason for denying this manuscript..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Deny Manuscript</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
