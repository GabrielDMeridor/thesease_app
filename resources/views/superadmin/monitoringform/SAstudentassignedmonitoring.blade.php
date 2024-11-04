@extends('superadmin.SAmain-layout')

@section('content-header')
    <!-- Content Header and Navigation with Notifications -->
@endsection

@section('body')
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div>
    <br>

    <div class="container-fluid">
        <div class="row">
            
            <!-- Proposal Manuscript Section -->
            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="routing-heading">Proposal Manuscript</h4>
                        <hr>
                        @if($appointment->proposal_manuscript)
                            <p>Main Proposal Manuscript:</p>
                            <div>
                                <input type="text" class="form-control" value="{{ $appointment->original_proposal_manuscript }}" readonly onclick="$('#mainProposalManuscriptModal').modal('show')" style="cursor: pointer; color: #007bff; text-decoration: underline;">
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
                </div>
            </div>

            <!-- Proposal Manuscript Updates Section -->
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="routing-heading">Proposal Manuscript Updates</h4>
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
                                            <a href="#" data-toggle="modal" data-target="#manuscriptUpdateModal">
                                                {{ $updates['original_name'] }}
                                            </a>
                                        </td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($updates['uploaded_at'])->format('m/d/Y') }}</td>
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

        </div>
    </div>

    <!-- Panel Review Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="routing-heading">Panel Review</h4>
            @foreach ($appointment->panel_members as $panelistId)
                @php
                    $panelist = \App\Models\User::find($panelistId);
                    $panelistName = $panelist ? $panelist->name : "Unknown Panelist";
                    $comments = json_decode($appointment->panel_comments, true) ?? [];
                    $remarks = json_decode($appointment->panel_remarks, true) ?? [];
                    $signatures = json_decode($appointment->panel_signatures, true) ?? [];
                @endphp

                <div class="card mb-3">
                    <div class="card-header">{{ $panelistName }}</div>
                    <div class="card-body">
                        <p><strong>Comment:</strong> {{ $comments[$panelistId] ?? 'No comment yet' }}</p>
                        <p><strong>Remarks:</strong> {{ $remarks[$panelistId] ?? 'No remarks yet' }}</p>
                        <p><strong>Signature:</strong> {{ $signatures[$panelistId] ?? 'Not signed yet' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Dean's Signature Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="routing-heading">Dean's Signature</h4>
            @php
                $allPanelSigned = count($appointment->panel_members) === count(array_filter($signatures));
            @endphp

            @if($allPanelSigned)
                @if($appointment->dean_monitoring_signature)
                    <p><strong>Dean's Signature:</strong> {{ $appointment->dean_monitoring_signature }}</p>
                @else
                    <form action="{{ route('superadmin.affixDeanSignature', $appointment->student_id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Affix Dean's Signature</button>
                    </form>
                @endif
            @else
                <p><strong>Status:</strong> All panel members have not signed yet</p>
            @endif
        </div>
    </div>
</div>
@endsection
