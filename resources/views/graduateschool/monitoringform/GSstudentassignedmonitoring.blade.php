@extends('graduateschool.GSmain-layout')

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
                <span class="badge badge-danger badge-counter">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Notifications Center</h6>
                <div class="overflow-auto" style="max-height: 300px;">
                    @foreach (auth()->user()->notifications->take(5) as $notification)
                        <a class="dropdown-item d-flex align-items-center {{ $notification->read_at ? 'text-muted' : 'font-weight-bold' }}" href="#" onclick="markAsRead('{{ $notification->id }}')">
                            <div class="mr-3">
                                <div class="icon-circle">
                                    <i class="fas fa-bell fa-fw"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                                <span>{{ $notification->data['message'] }}</span>
                                @if (!empty($notification->data['reason']))
                                    <p class="mb-0 text-gray-700">Reason: {{ $notification->data['reason'] }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="dropdown-item text-center small text-gray-500">
                    <a href="{{ route('notifications.markAsRead') }}">Mark all as read</a>
                </div>

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

<!-- Title and Search Form -->

@endsection

@section('body')
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div>
    <br>

    <div class="container-fluid">

<!-- Proposal Manuscript Section -->
<div class="card mb-4">
    <div class="card-body">
        <h4 class="routing-heading">Proposal Manuscript</h4>
        <hr>
        @if($appointment->proposal_manuscript)
            <p>Main Proposal Manuscript: <i class="fa-solid fa-download"></i></p>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped custom-table">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">Original Proposal Manuscript</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">
                                <span onclick="$('#mainProposalManuscriptModal').modal('show')" style="cursor: pointer; color: #007bff; text-decoration: underline;">
                                    {{ $appointment->original_proposal_manuscript }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <p>No main proposal manuscript uploaded.</p>
        @endif

        <hr>
        <br>
        
<!-- Proposal Manuscript Updates Section -->

        <h4 class="routing-heading">Proposal Manuscript Updates</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped custom-table">
            <p>Updated Proposal Manuscript: <i class="fa-solid fa-download"></i></p>
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
                            <td class="text-center">
                                {{ isset($updates['uploaded_at']) ? \Carbon\Carbon::parse($updates['uploaded_at'])->format('m/d/Y h:i A') : 'Not available' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ Storage::url($updates['file_path']) }}" download class="btn btn-primary">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
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

<!-- Panel Review Section -->
<div class="card mb-4 review-panel">
    <h4 class="routing-heading">Panel Review</h4>
    @foreach ($appointment->panel_members as $panelistId)
        @php
            $panelist = \App\Models\User::find($panelistId);
            $panelistName = $panelist ? $panelist->name : "Unknown Panelist";
            $comments = json_decode($appointment->panel_comments, true) ?? [];
            $remarks = json_decode($appointment->panel_remarks, true) ?? [];
            $signatures = json_decode($appointment->panel_signatures, true) ?? [];
        @endphp
        <div class="panelist-card mb-3">
            <!-- Panelist Header with Name and Signature Status -->
            <div class="panelist-header">
                <h5 class="panelist-name">{{ $panelistName }}</h5>
                <span class="signature-status {{ !empty($signatures[$panelistId]) ? 'signed' : 'unsigned' }}">
                    {{ !empty($signatures[$panelistId]) ? 'Signed' : 'Unsigned' }}
                </span>
            </div>
            <!-- Comments and Remarks -->
            <div class="panelist-content">
                <p><strong>Comment:</strong> {{ $comments[$panelistId] ?? 'No comment yet' }}</p>
                <p><strong>Remarks:</strong> {{ $remarks[$panelistId] ?? 'No remarks yet' }}</p>
                <p><strong>Signature:</strong> {{ $signatures[$panelistId] ?? 'Not signed yet' }}</p>
            </div>
        </div>
    @endforeach
</div>

<!-- Dean's Signature Section -->
<div class="card mb-4">
    <div class="card-body">
        <h4 class="routing-heading">Dean's Signature</h4>
        @php
            // Check if all panel members have signed
            $allPanelSigned = count($appointment->panel_members ?? []) === count(array_filter(json_decode($appointment->panel_signatures, true) ?? []));
        @endphp

        @if ($allPanelSigned)
            @if ($appointment->dean_monitoring_signature)
                <p><strong>Dean's Signature:</strong> {{ $appointment->dean_monitoring_signature }}</p>
            @else
                <p><strong>Dean's Signature Status:</strong> Awaiting Dean's Signature...</p>
            @endif
        @else
            <p><strong>Panel Signatures Incomplete:</strong> The dean’s approval will be requested once all panel members have signed.</p>
        @endif
    </div>
</div>

</div>

</div>
@endsection
