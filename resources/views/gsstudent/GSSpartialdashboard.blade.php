@extends('gsstudent.GSSmain-layout')

<style>
 /* responsiveness of the table */
.card {
    width: 100%;
    margin: 0 auto;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

.card-header {
    background-color: #f8f9fa;
    padding: 10px 15px;
    font-size: 1.25rem;
    font-weight: bold;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

.card-body {
    padding: 15px;
    overflow-x: auto; /* Enable horizontal scrolling if content overflows */
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
    font-size: 1rem;
}

/* Button styling */
.btn-primary {
    padding: 5px 10px;
    font-size: 0.9rem;
}

/* Responsive layout adjustments */
@media (max-width: 768px) {
    .table th, .table td {
        font-size: 0.875rem;
        padding: 8px;
    }

    /* Stack file input and save button on small screens */
    .table td {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .table td button,
    .table td input[type="file"] {
        width: 100%;
        margin-top: 5px;
    }
}

/* Stack table rows vertically on extra small screens */
@media (max-width: 576px) {
    .table thead {
        display: none; /* Hide the table header */
    }

    .table tr {
        display: block;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
    }

    .table td {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
    }

    /* Labeling each cell with a pseudo-element */
    .table td:nth-child(1)::before { content: "File:"; font-weight: bold; }
    .table td:nth-child(2)::before { content: "Status:"; font-weight: bold; }
    .table td:nth-child(3)::before { content: "Choose File:"; font-weight: bold; }
    .table td:nth-child(4)::before { content: "Save:"; font-weight: bold; }
}

    </style>


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

    <div class="card">
        <div class="card-header">Files</div>
        <div class="card-body">
            <form action="{{ route('gssstudent.file.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <table class="table">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Status</th>
                            <th>Choose File</th>
                            <th>Save</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Immigration File -->
                        @if (strtolower(auth()->user()->nationality) !== 'filipino')
                        <tr>
                            <td>Immigration Card:</td>
                            <td>
                                @if ($immigrationFile)
                                    <a href="#" data-toggle="modal" data-target="#fileModal" 
                                        onclick="openFileModal('{{ asset('storage/immigrations/' . $immigrationFile) }}', '{{ $immigrationFile }}')">{{ $immigrationFile }}</a>
                                @else
                                    Not Uploaded
                                @endif
                            </td>
                            <td>
                                <input type="file" name="file[immigration_or_studentvisa]" accept=".jpg, .jpeg, .png">
                                <input type="hidden" name="file_type" value="immigration_or_studentvisa">
                            </td>
                            <td>
                                <button type="submit" name="upload_type" value="immigration_or_studentvisa" class="btn btn-primary">Save</button>
                            </td>
                        </tr>
                        @endif


                        <!-- Manuscript -->
                        <tr>
                            <td>Manuscript:</td>
                            <td>
                                @if ($manuscriptFile)
                                    <a href="#" data-toggle="modal" data-target="#fileModal" 
                                        onclick="openFileModal('{{ asset('storage/manuscripts/' . $manuscriptFile) }}', '{{ $manuscriptFile }}')">{{ $manuscriptFile }}</a>
                                @else
                                    Not Uploaded
                                @endif
                            </td>
                            <td>
                                <input type="file" name="file[manuscript]" accept=".pdf">
                                <input type="hidden" name="file_type" value="manuscript">
                            </td>
                            <td>
                                <button type="submit" name="upload_type" value="manuscript" class="btn btn-primary">Save</button>
                            </td>
                        </tr>

                </table>
            </form>
        </div>
    </div>
</div>

<!-- File View Modal -->
<div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileModalLabel">View File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="filePreview" style="width:100%; height:500px;" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">
                <a id="downloadLink" href="#" download class="btn btn-primary">Download</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script>
        function openFileModal(fileUrl, fileName) {
        document.getElementById('filePreview').src = fileUrl;
        document.getElementById('downloadLink').href = fileUrl;
        document.getElementById('downloadLink').download = fileName;
        $('#fileModal').modal('show');
    }
document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = document.querySelectorAll('input[type="file"]');

    fileInputs.forEach(fileInput => {
        fileInput.addEventListener('change', function(event) {
            const fileType = event.target.getAttribute('name').replace('file[', '').replace(']', '');
            let acceptTypes = '';

            switch (fileType) {
                case 'immigration_or_studentvisa':
                    acceptTypes = '.jpeg, .jpg, .png';
                    break;
                case 'manuscript':
                    acceptTypes = '.pdf';
                    break;
            }

            event.target.setAttribute('accept', acceptTypes);
        });
    });
});



    function markAsRead(notificationId) {
    $.ajax({
        url: '/notifications/mark-as-read/' + notificationId,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}' // Add CSRF token for security
        },
        success: function(response) {
            // If the request is successful, you can visually update the notification
            $('a[data-id="' + notificationId + '"]').removeClass('font-weight-bold').addClass('text-muted');
            // Optionally update the notification count
            var currentCount = parseInt($('.badge-counter').text());
            $('.badge-counter').text(currentCount > 1 ? currentCount - 1 : '');
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
