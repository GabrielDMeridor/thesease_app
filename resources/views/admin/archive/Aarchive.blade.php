@extends('admin.Amain-layout')

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
                                <span>{{ $notification->data['message'] }}</span>
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

    <div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.archive') }}" id="filterForm" class="mb-4">
            <div class="container">
                <div class="row">
                    <div class="col-sm">
                        <!-- Keyword search input -->
                        <div class="form-group">
                            <label for="keyword">Search by <b>Name</b></label>
                            <input type="text" id="keyword" name="keyword" class="form-control" placeholder="Enter name" value="{{ request('keyword') }}">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<br>

            <div class="user-management-section">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped custom-table">
                        <thead class="table-dark">
                            <tr style="text-align:center;">
                                <th style="text-align:center;">Files</th>
                                <th style="text-align:center;">Uploaded By</th>
                                <th style="text-align:center;">Date Uploaded</th>
                            </tr>
                        </thead>
                        <tbody id="user-table-body">
                        @forelse ($users as $user)
                        <tr>
                            <!-- Button to View File Names -->
                            <td class="text-center">
                                <button type="button" class="btn btn-primary" onclick="openFileListModal({{ $user->id }})">
                                    <i class="fas fa-folder-open"></i> View Files
                                </button>
                            </td>

                            <!-- Uploaded by -->
                            <td class="text-center" style="font-weight:600;">{{ $user->name }}</td>

                            <!-- Date Uploaded -->
                            <td class="text-center">{{ \Carbon\Carbon::parse($user->created_at)->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No files found.</td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center tablefooter mt-2 flex-nowrap">
                    <!-- Pagination Info (left corner) -->
                    <div class="pagination-info">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                    </div>

                    <!-- Pagination Links (right corner) -->
                    <nav aria-label="User Pagination" class="mt-2 ">
                        {!! $users->links('pagination::bootstrap-4') !!}
                    </nav>
                </div>
            </div>
        </div>

        <br>
        <br>

<!-- Modal for Listing Files -->
<div class="modal fade" id="fileListModal" tabindex="-1" role="dialog" aria-labelledby="fileListModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileListModalLabel">Files</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="file-list-body">
                <!-- List of files will be dynamically inserted here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal for File Preview -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="file-preview-container">
                    <!-- Content will be dynamically loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="downloadLink" class="btn btn-primary" download>Download</a>
            </div>
        </div>
    </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
            $(document).ready(function () {
            $('#keyword').on('keyup', function () {
                let keyword = $(this).val();

                $.ajax({
                    url: "{{ route('superadmin.archive.search') }}",
                    type: 'GET',
                    data: { keyword: keyword },
                    success: function (response) {
                        let tableBody = '';

                        response.forEach(function (user) {
                            tableBody += `
                                <tr>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary" onclick="openFileListModal(${user.id})">
                                            <i class="fas fa-folder-open"></i> View Files
                                        </button>
                                    </td>
                                    <td class="text-center" style="font-weight:600;">${user.name}</td>
                                    <td class="text-center">${user.created_at}</td>
                                </tr>`;
                        });

                        $('#user-table-body').html(tableBody);
                    },
                    error: function () {
                        alert('Unable to fetch results.');
                    }
                });
            });
        });
    // Load file list modal
    let files = <?php echo json_encode($users->mapWithKeys(function($user) {
        return [
            $user->id => [
                'immigration' => $user->immigration_or_studentvisa, 
                'routing' => $user->routing_form_one, 
                'manuscript' => $user->manuscript,
                'adviser' => $user->adviser_appointment_form
            ]
        ];
    })->toArray()); ?>;

    // Function to open the file list modal
    function openFileListModal(userId) {
        const fileListBody = document.getElementById('file-list-body');
        fileListBody.innerHTML = '';

        const userFiles = files[userId];

        if (userFiles.immigration) {
            fileListBody.innerHTML += `<a href="#" onclick="showFileModal('/storage/immigrations/${userFiles.immigration}', 'image')">Immigration/Student Visa: ${userFiles.immigration}</a><br>`;
        }
        if (userFiles.routing) {
            const fileType = userFiles.routing.endsWith('pdf') ? 'pdf' : 'doc';
            fileListBody.innerHTML += `<a href="#" onclick="showFileModal('/storage/routing_forms/${userFiles.routing}', '${fileType}')">Routing Form One: ${userFiles.routing}</a><br>`;
        }
        if (userFiles.manuscript) {
            const fileType = userFiles.manuscript.endsWith('pdf') ? 'pdf' : 'doc';
            fileListBody.innerHTML += `<a href="#" onclick="showFileModal('/storage/manuscripts/${userFiles.manuscript}', '${fileType}')">Manuscript: ${userFiles.manuscript}</a><br>`;
        }
        if (userFiles.adviser) {
            const fileType = userFiles.adviser.endsWith('pdf') ? 'pdf' : 'doc';
            fileListBody.innerHTML += `<a href="#" onclick="showFileModal('/storage/adviser_appointments/${userFiles.adviser}', '${fileType}')">Adviser Appointment Form: ${userFiles.adviser}</a><br>`;
        }

        // Show the modal
        $('#fileListModal').modal('show');
    }

    // Function to handle file modal display
    function showFileModal(fileUrl, fileType) {
        let previewContainer = document.getElementById('file-preview-container');
        let downloadLink = document.getElementById('downloadLink');

        // Clear the existing content in the modal
        previewContainer.innerHTML = '';

        // Set the download link
        downloadLink.href = fileUrl;
        downloadLink.setAttribute('download', fileUrl.split('/').pop());

        if (fileType === 'pdf') {
            // Show PDF in an iframe
            previewContainer.innerHTML = `<iframe src="${fileUrl}" style="width: 100%; height: 500px;" frameborder="0"></iframe>`;
        } else if (fileType === 'image') {
            // Show images directly
            previewContainer.innerHTML = `<img src="${fileUrl}" style="width: 100%; height: auto;" alt="File Preview">`;
        }

        // Open the modal
        $('#filePreviewModal').modal('show');

        document.getElementById('keyword').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('filterForm').submit();
        }
        });
    }
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
