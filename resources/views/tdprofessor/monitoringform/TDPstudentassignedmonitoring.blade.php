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
        <div class="overflow-auto" style="max-height: 300px;"> <!-- This is the scrolling part -->
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
<div class="container-fluid">
    <div class="sagreet">
    <h2>Monitoring Form for {{ $appointment->student->name }}</h2>
    </div>
    <br>
<div class="container-fluid">
    <!-- Proposal Manuscript Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Proposal Manuscript</h4>
            @if($appointment->proposal_manuscript)
                <div>
                    <a href="#" data-toggle="modal" data-target="#mainProposalManuscriptModal">
                        {{ $appointment->original_proposal_manuscript }}
                    </a>
                </div>

                <!-- Modal for main proposal manuscript -->
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

    <!-- Proposal Manuscript Updates Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Proposal Manuscript Updates</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Last Updated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($appointment->proposal_manuscript_updates)
                        @php
                            $updates = json_decode($appointment->proposal_manuscript_updates, true);
                        @endphp
                        <tr>
                            <td>
                                <a href="#" data-toggle="modal" data-target="#manuscriptUpdateModal">
                                    {{ $updates['original_name'] }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($updates['uploaded_at'])->format('m/d/Y') }}</td>
                            <td><a href="{{ Storage::url($updates['file_path']) }}" download class="btn btn-primary">Download</a></td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="3">No updates available.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for proposal manuscript update -->
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

    <!-- Panel Review Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Panel Review</h4>
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

                <div class="card mb-3">
                    <div class="card-header">{{ $panelistName }}</div>
                    <div class="card-body">
                        <!-- Display Comment, Student Reply, and Remarks -->
                        <p><strong>Comment:</strong> {{ $comments[$panelistId] ?? 'No comment yet' }}</p>
                        <p><strong>Student Reply:</strong> {{ $replies[$panelistId] ?? 'No reply yet' }}</p>
                        <p><strong>Remarks:</strong> {{ $remarks[$panelistId] ?? 'No remarks yet' }}</p>

                        <!-- Professor's Form for Adding Comments and Remarks -->
                        @if (Auth::id() == $panelistId)
                            <form action="{{ route('panel.addComment', $appointment->student_id) }}" method="POST" class="mt-3">
                                @csrf
                                <div class="form-group">
                                    <label for="comment">Your Comment</label>
                                    <textarea name="comment" class="form-control">{{ $comments[$panelistId] ?? '' }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Comment</button>
                            </form>

                            <form action="{{ route('panel.addRemark', $appointment->student_id) }}" method="POST" class="mt-3">
                                @csrf
                                <div class="form-group">
                                    <label for="remark">Your Remark</label>
                                    <textarea name="remark" class="form-control">{{ $remarks[$panelistId] ?? '' }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Remark</button>
                            </form>

                            <!-- Affix Signature -->
                            @if (empty($signatures[$panelistId]))
                                <form action="{{ route('panel.affixSignature', $appointment->student_id) }}" method="POST" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Affix Signature</button>
                                </form>
                            @else
                                <p><strong>Signature:</strong> {{ $signatures[$panelistId] }}</p>
                            @endif
                        @else
                            <!-- Show remarks and signatures for other panelists -->
                            <p><strong>Remarks:</strong> {{ $remarks[$panelistId] ?? 'No remarks yet' }}</p>
                            <p><strong>Signature:</strong> {{ $signatures[$panelistId] ?? 'Not signed yet' }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
