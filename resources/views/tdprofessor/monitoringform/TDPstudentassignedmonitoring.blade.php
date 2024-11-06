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

                <!-- Scrollable area with all notifications -->
                <div class="overflow-auto" style="max-height: 300px;"> <!-- Set max height for scrolling -->
                    @foreach (auth()->user()->notifications as $notification) <!-- No limit on notifications -->
                        <a class="dropdown-item d-flex align-items-center {{ $notification->read_at ? 'text-muted' : 'font-weight-bold' }}" href="#" onclick="markAsRead('{{ $notification->id }}')">
                            <div class="mr-3">
                                <div class="icon-circle">
                                    <i class="fa-solid fa-bell"></i>
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
   <!-- Panel Review Section -->
   <div class="card mb-4 review-panel">
      <h4 class="routing-heading">Panel Review</h4>
      @foreach ($panelMembers as $panelistId)
      @php
      $panelist = \App\Models\User::find($panelistId);
      $panelistName = $panelist ? $panelist->name : "Unknown Panelist";
      @endphp
      <div class="panelist-card">
         <!-- Panelist Header with Name and Signature Status -->
         <div class="panelist-header">
            <h5 class="panelist-name">{{ $panelistName }}</h5>
            @if (!empty($signatures[$panelistId]))
            <span class="signature-status signed">Signed</span>
            @else
            <span class="signature-status unsigned">Unsigned</span>
            @endif
         </div>
         <!-- Comments, Replies, and Remarks -->
         <div class="panelist-content">
            <p><strong>Comment:</strong> {{ $comments[$panelistId] ?? 'No comment yet' }}</p>
            <p><strong>Student Reply:</strong> {{ $replies[$panelistId] ?? 'No reply yet' }}</p>
            <p><strong>Remarks:</strong> {{ $remarks[$panelistId] ?? 'No remarks yet' }}</p>
         </div>
         <!-- Forms for Editable Panelists Only -->
         @if (Auth::id() == $panelistId)
         <div class="panelist-forms">
            <!-- Comment Form -->
            <form action="{{ route('panel.addComment', $appointment->student_id) }}" method="POST" class="form-section">
               @csrf
               <div class="form-group">
                  <label for="comment">Your Comment</label>
                  <textarea name="comment" class="form-control" placeholder="Enter your comment...">{{ $comments[$panelistId] ?? '' }}</textarea>
               </div>
               <button type="submit" class="btn btn-primary">Save Comment</button>
            </form>
            <!-- Remark Form -->
            <form action="{{ route('panel.addRemark', $appointment->student_id) }}" method="POST" class="form-section">
               @csrf
               <div class="form-group">
                  <label for="remark">Your Remark</label>
                  <textarea name="remark" class="form-control" placeholder="Enter your remark...">{{ $remarks[$panelistId] ?? '' }}</textarea>
               </div>
               <button type="submit" class="btn btn-primary">Save Remark</button>
            </form>
            <!-- Signature Form -->
            @if (empty($signatures[$panelistId]))
            <form action="{{ route('panel.affixSignature', $appointment->student_id) }}" method="POST" class="form-section">
               @csrf
               <button type="submit" class="btn btn-affix" style="color:white;">Affix Signature</button>
            </form>
            @endif
         </div>
         @endif
      </div>
      @endforeach
   </div>
   <!-- Dean's Signature Section
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
                      <p><strong>Status:</strong> Waiting for Dean’s Signature</p>
                  @endif
              @else
                  <p><strong>Status:</strong> All panel members have not signed yet</p>
              @endif
          </div>
      </div> -->
</div>
@endsection