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
                <span class="badge badge-danger badge-counter" id="notification-count">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Notifications Center</h6>

                <!-- Scrollable area with all notifications -->
                <div class="overflow-auto" style="max-height: 300px;"> <!-- Set max height for scrolling -->
                    @foreach (auth()->user()->notifications as $notification)
                        <div id="notification-{{ $notification->id }}" class="dropdown-item d-flex align-items-center {{ $notification->read_at ? 'text-muted' : 'font-weight-bold' }}">
                            <div class="mr-3">
                                <div class="icon-circle">
                                    <i class="fa-solid fa-bell"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                                <span>{{ $notification->data['message'] ?? 'No message available' }}</span> <!-- Default value if 'message' is missing -->
                                <!-- Conditionally display the reason if it exists -->
                                @if (!empty($notification->data['reason']))
                                    <p class="mb-0 text-gray-700">Reason: {{ $notification->data['reason'] }}</p>
                                @endif
                            </div>

                            <!-- Delete Notification Button -->
                            <button type="button" class="btn btn-link text-danger ml-auto" onclick="deleteNotification('{{ $notification->id }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
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

<!-- Optional Toast Message for Deletion Confirmation -->
<div id="toast-message" class="alert alert-success" style="display:none; position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

@endsection


@section ('body')
<div class="container-fluid">
   <div class="sagreet">{{ $title }}</div>
   <br>
   <!-- Proposal Manuscript Section -->
   <div class="card mb-4">
      <div class="card-body">
         <h4 class="routing-heading">Proposal Manuscript</h4>
         <p>Main Proposal Manuscript: <i class="fa-solid fa-download"></i></p>
         @if($appointment->proposal_manuscript)
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
                        <span onclick="$('#mainProposalManuscriptModal').modal('show')" 
                           style="cursor: pointer; color: #007bff; text-decoration: underline;">
                        {{ $appointment->original_proposal_manuscript }}
                        </span>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <br>
         <hr>
         <h4 class="routing-heading">Proposal Manuscript Updates</h4>
         <p>Updated Proposal Manuscript: <i class="fa-solid fa-download"></i></p>
         <div class="table-responsive">
    <table class="table table-bordered table-hover table-striped custom-table">
        <thead class="table-dark">
            <tr>
                <th class="text-center">File</th>
                <th class="text-center">Last Updated</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @if($appointment->proposal_manuscript_updates && $appointment->proposal_manuscript_update_status === 'approved')
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
                <td colspan="3" class="text-center">No approved updates available.</td>
            </tr>
            @endif
        </tbody>
    </table>
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
   <div class="card mb-4 signatures-gallery">
      <h4 class="signatures-heading">Signed Panelists</h4>
      <div class="signatures-grid">
         @foreach ($panelMembers as $panelistId)
         @php
         $panelist = \App\Models\User::find($panelistId);
         $panelistName = $panelist ? $panelist->name : "Unknown Panelist";
         $signature = $signatures[$panelistId] ?? null;
         @endphp
         <!-- Only show if the panelist has signed -->
         @if ($signature)
         <div class="signature-card">
            <div class="signature-info">
               <h5 class="signature-name">{{ $panelistName }}</h5>
               <p class="signature-text">{{ $signature }}</p>
               @if (!empty($signatures[$panelistId]))
               <span class="signature-status signed">Signed</span>
               @else
               <span class="signature-status unsigned">Unsigned</span>
               @endif
            </div>
         </div>
         @endif
         @endforeach
      </div>
      <!-- Dean's Signature Section -->
      <div class="dean-signature-section">
         <h4 class="dean-signature-heading">Dean's Signature</h4>
         @php
         $allPanelSigned = count($appointment->panel_members) === count(array_filter($signatures));
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
   <div class="container-fluid">
    <div class="card mb-4 review-panel">
        <h4 class="routing-heading">Panel Review</h4>

        <!-- Iterate through panel members -->
        @foreach ($panelMembers as $panelistId)
            @php
                $panelist = \App\Models\User::find($panelistId);
                $panelistName = $panelist ? $panelist->name : "Unknown Panelist";
            @endphp
            <div class="panelist-card">
                <h5 class="panelist-name">{{ $panelistName }}</h5>

                <!-- Display Comments -->
                @php
                    // Filter comments for this panelist
                    $panelistComments = collect($comments)->where('panelist_id', $panelistId);
                @endphp
                @foreach ($panelistComments as $comment)
                    <div class="comment-item">
                        <p><strong>Comment:</strong> {{ $comment['comment'] }}</p>
                        <p><small>Posted on: {{ \Carbon\Carbon::parse($comment['created_at'])->format('m/d/Y h:i A') }}</small></p>

                        <!-- Display Student Reply for this Comment (if any) -->
                        <h6>Student Reply:</h6>
                        @php
                            // Filter replies specific to this comment
                            $repliesForComment = collect($replies)->where('comment_id', $comment['id']);
                        @endphp
                        @if($repliesForComment->isNotEmpty())
                            @foreach ($repliesForComment as $reply)
                                <div class="reply-item">
                                    <p><strong>Reply:</strong> {{ $reply['reply'] }}</p>
                                    <p><small>Replied on: {{ \Carbon\Carbon::parse($reply['created_at'])->format('m/d/Y h:i A') }}</small></p>


                                    @if (!empty($reply['location']))
                                <p><strong>Location:</strong> {{ $reply['location'] }}</p>
                            @endif
                        
                                    <!-- Display Remarks on this Reply -->
                                    <h6>Panelist Remarks on Reply:</h6>
                                    @php
                                        $remarksForReply = collect($remarks)->where('comment_id', $comment['id']);
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

                        <!-- Add Remark Form for this Comment -->
                        @if (Auth::id() == $panelistId)
                            <form action="{{ route('panel.addRemark', ['studentId' => $appointment->student_id, 'commentId' => $comment['id']]) }}" method="POST">
                                @csrf
                                <textarea name="remark" class="form-control" placeholder="Enter your remark..." required></textarea>
                                <button type="submit" class="btn btn-primary mt-2">Submit Remark</button>
                            </form>
                        @endif
                    </div>
                    <hr>
                @endforeach

                                <!-- Panel Signature Section -->
<!-- Panel Signature Section -->
<div class="signature-section mt-3">
    <h6>Signature</h6>
    @if (isset($signatures[$panelistId]))
        @php
            $signatureInfo = $signatures[$panelistId];
        @endphp

        @if (is_array($signatureInfo))
            <p class="text-success"><strong>Signed by:</strong> {{ $signatureInfo['name'] ?? 'Unknown' }} on {{ \Carbon\Carbon::parse($signatureInfo['date'] ?? now())->format('m/d/Y h:i A') }}</p>
        @else
            <!-- If it is a string, display it as-is -->
            <p class="text-success"><strong>Signed by:</strong> {{ $signatureInfo }}</p>
        @endif
    @else
        <p class="text-danger">Not signed yet.</p>
        <!-- Show signature form for the authenticated panelist -->
        @if (Auth::id() == $panelistId)
            <form action="{{ route('panel.affixSignature', $appointment->student_id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-affix btn-primary mt-2">Affix Signature</button>
            </form>
        @endif
    @endif
</div>


                <!-- Add New Comment Form for this Panelist -->
                @if (Auth::id() == $panelistId)
                    <form action="{{ route('panel.addComment', $appointment->student_id) }}" method="POST">
                        @csrf
                        <textarea name="comment" class="form-control" placeholder="Add new comment..." required></textarea>
                        <button type="submit" class="btn btn-primary mt-2">Add Comment</button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
</div>


<script>
   function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        $.ajax({
            url: `/notifications/${notificationId}`, // URL for delete route
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}' // CSRF token for security
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Remove the notification element from the dropdown
                    $(`#notification-${notificationId}`).remove();

                    // Update the notification count badge
                    let notificationCount = parseInt($('#notification-count').text());
                    notificationCount = notificationCount > 0 ? notificationCount - 1 : 0;
                    $('#notification-count').text(notificationCount);

                    // Show success toast message
                    $('#toast-message').text(response.message).fadeIn().delay(3000).fadeOut();
                }
            },
            error: function(xhr) {
                console.error('An error occurred while deleting the notification:', xhr);
                alert('Failed to delete notification');
            }
        });
    }
}
</script>
@endsection