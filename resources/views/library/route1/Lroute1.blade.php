@extends('library.Lmain-layout')

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



@section('body')
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div> 
    <br>

<!-- Search Bar -->
<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('library.search') }}" method="GET">
            <div class="container">
                <div class="row">
                    <div class="col-sm">
                        <!-- Keyword search input with design imported -->
                        <div class="input-group">
                            <input type="text" name="query" id="searchInput" class="form-control" placeholder="Search Student by Name..." value="{{ old('query', $keyword ?? '') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


        <!-- Students Table -->
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped custom-table">
    <thead class="table-dark">
    <tr>
        <th style="text-align:center;">Student Name</th>
        <th style="text-align:center;">Email</th>
        <th style="text-align:center;">Uploaded Manuscript</th>
        <th style="text-align:center;">Uploaded Certificate</th>
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
                @if($appointment->similarity_manuscript)
                    <a href="#" data-toggle="modal" data-target="#manuscriptModal{{ $appointment->id }}">
                        {{ basename($appointment->similarity_manuscript) }}
                    </a>
                @else
                    <span>No manuscript uploaded</span>
                @endif
            </td>
            <td class="text-center">
                <form action="{{ route('library.uploadSimilarityCertificate') }}" method="POST" enctype="multipart/form-data" id="certificateUploadForm{{ $appointment->student_id }}">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $appointment->student_id }}">
                    
                    @if($appointment->similarity_certificate)
                        <a href="#" data-toggle="modal" data-target="#certificateModal{{ $appointment->id }}">
                            {{ basename($appointment->similarity_certificate) }}
                        </a>
                    @else
                        <input type="file" name="similarity_certificate" class="form-control" required accept=".pdf">
                    @endif
                </form>
            </td>
            <td class="text-center">
                <button type="submit" form="certificateUploadForm{{ $appointment->student_id }}" class="btn btn-primary">Save</button>
            </td>
            <td class="text-center">
                <!-- Deny button triggers the denial modal -->
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
                                <iframe src="{{ Storage::url($appointment->similarity_manuscript) }}" width="100%" height="600px"></iframe>
                            </div>
                            <div class="modal-footer">
                                <a href="{{ Storage::url($appointment->similarity_manuscript) }}" target="_blank" class="btn btn-primary" download>Download Manuscript</a>
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
                                <iframe src="{{ Storage::url($appointment->similarity_certificate) }}" width="100%" height="600px"></iframe>
                            </div>
                            <div class="modal-footer">
                                <a href="{{ Storage::url($appointment->similarity_certificate) }}" target="_blank" class="btn btn-primary" download>Download Certificate</a>
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
                    <form action="{{ route('library.denyManuscript', $appointment->id) }}" method="POST">
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

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let searchRequest = null; // Track the active AJAX request
    let debounceTimer;        // Timer for debouncing

    $('#searchInput').on('keyup', function() {
        clearTimeout(debounceTimer); // Clear previous timer

        let query = $(this).val().trim();

        debounceTimer = setTimeout(function() {
            // Abort previous request if it's still in progress
            if (searchRequest) {
                searchRequest.abort();
            }

            searchRequest = $.ajax({
                url: "{{ route('library.search') }}",
                type: "GET",
                data: { query: query },
                cache: false,
                success: function(response) {
                    $('#appointments-table').html(response.html);
                    reinitializeEventListeners(); // Re-bind events for new content
                },
                error: function(xhr) {
                    if (xhr.status !== 0) { // Ignore aborted requests
                        console.error("An error occurred: " + xhr.status + " " + xhr.statusText);
                    }
                }
            });
        }, 300); // Adjust debounce time as needed
    });

    // Function to reinitialize form submission event listeners after AJAX response
    function reinitializeEventListeners() {
        // Bind click event to dynamically created "Save" buttons
        $('.save-button').on('click', function(e) {
            e.preventDefault(); // Prevent default form submission

            // Submit the form associated with the clicked "Save" button
            let formId = $(this).attr('form');
            $('#' + formId).submit();
        });
    }

    // Initialize event listeners for the initial load
    reinitializeEventListeners();
});
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



