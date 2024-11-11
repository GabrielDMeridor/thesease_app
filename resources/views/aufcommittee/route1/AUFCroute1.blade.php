@extends('aufcommittee.AUFCmain-layout')

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

    <!-- Search Input -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('graduateschool.route1') }}" method="GET">
                <div class="container">
                    <div class="row">
                        <div class="col-sm">
                            <!-- Keyword search input -->
                            <div class="input-group mb-3">
                                <input type="text" name="search" class="form-control" placeholder="Search students by name" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <br>

    <!-- Table of Students Awaiting AUFC Approval -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Last Updated</th>
                    <th>Status</th>
                    <th>Files</th>
                    <th>Actions</th>
                    <th>Deny</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->student->name ?? 'N/A' }}</td>
                        <td>{{ $appointment->student->program ?? 'N/A' }}</td>
                        <td>{{ $appointment->updated_at->format('m/d/Y') }}</td>
                        <td>{{ $appointment->aufc_status == 'approved' ? 'Approved' : 'Pending' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#fileListModal-{{ $appointment->id }}">
                                <i class="fas fa-folder-open"></i> View Files
                            </button>
                        </td>
                        <td>
                            @if ($appointment->aufc_status !== 'approved')
                                <form action="{{ route('aufcommittee.route1.uploadEthicsClearance', $appointment->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="ethics_clearance" class="form-control mb-2" required>
                                    <button type="submit" class="btn btn-success">Upload & Approve</button>
                                </form>
                            @else
                                <button class="btn btn-secondary" disabled>Approved</button>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#denyModal{{ $appointment->id }}">Deny</button>
                        </td>
                    </tr>

                    <!-- File List Modal -->
                    <div class="modal fade" id="fileListModal-{{ $appointment->id }}" tabindex="-1" role="dialog" aria-labelledby="fileListModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Files for {{ $appointment->student->name ?? 'Student' }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @php
                                        $files = [
                                            'proof_of_payment' => $appointment->ethics_proof_of_payment,
                                            'curriculum_vitae' => $appointment->ethics_curriculum_vitae,
                                            'research_services_form' => $appointment->ethics_research_services_form,
                                            'application_form' => $appointment->ethics_application_form,
                                            'study_protocol_form' => $appointment->ethics_study_protocol_form,
                                            'informed_consent_form' => $appointment->ethics_informed_consent_form,
                                            'sample_informed_consent' => $appointment->ethics_sample_informed_consent,
                                            'proposal_manuscript_updates' => $appointment->proposal_manuscript_updates,
                                        ];
                                    @endphp
                                    @foreach ($files as $key => $file)
                                        @if ($file)
                                            <button type="button" class="btn btn-link" onclick="openFileModal('{{ asset(str_replace('public/', 'storage/', $file)) }}', '{{ ucfirst(str_replace('_', ' ', $key)) }}')">
                                                View {{ ucfirst(str_replace('_', ' ', $key)) }}
                                            </button><br>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                                        <!-- Denial Modal -->
                                        <div class="modal fade" id="denyModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="denyModalLabel{{ $appointment->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('aufcommittee.route1.denyAppointment', $appointment->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="denyModalLabel{{ $appointment->id }}">Deny Appointment</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="denialReason">Reason for Denial</label>
                                            <textarea name="denialReason" id="denialReason" class="form-control" rows="3" required placeholder="Enter reason for denial"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Deny</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-3" id="pagination-links">
        {{ $appointments->links() }}
    </div>


    <div class="modal fade" id="fileContentModal" tabindex="-1" role="dialog" aria-labelledby="fileContentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileContentModalLabel">File Content</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="fileContentBody">
                <!-- File content will load here dynamically -->
            </div>
            <div class="modal-footer">
                <a href="" id="downloadLink" class="btn btn-primary" download>Download</a>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function () {
        $('#searchInput').on('input', function () {
            let query = $(this).val();

            $.ajax({
                url: "{{ route('aufcommittee.route1.ajaxSearch') }}",
                type: 'GET',
                data: { search: query },
                success: function (response) {
                    let appointmentsTable = $('#appointmentsTable');
                    appointmentsTable.empty();

                    if (response.data.length === 0) {
                        appointmentsTable.append('<tr><td colspan="5" class="text-center">No students found.</td></tr>');
                    } else {
                        $.each(response.data, function (index, appointment) {
                            let statusText = appointment.aufc_status === 'approved' ? 'Approved' : 'Pending';
                            let approveButton = appointment.aufc_status === 'approved'
                                ? '<button class="btn btn-secondary" disabled>Approved</button>'
                                : `<form action="/aufcommittee/route1/approve/${appointment.id}" method="POST" style="display:inline;">
                                      @csrf
                                      <button type="submit" class="btn btn-success">Approve</button>
                                   </form>`;

                            appointmentsTable.append(`
                                <tr>
                                    <td>${appointment.student.name}</td>
                                    <td>${appointment.student.program}</td>
                                    <td>${new Date(appointment.updated_at).toLocaleDateString()}</td>
                                    <td>${statusText}</td>
                                    <td>${approveButton}</td>
                                </tr>
                            `);
                        });
                    }

                    $('#pagination-links').hide();
                },
                error: function () {
                    alert('Error retrieving data');
                }
            });
        });
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
function openFileModal(filePath, fileTitle) {
    // Set modal title
    document.getElementById('fileContentModalLabel').textContent = fileTitle;

    // Set download link
    document.getElementById('downloadLink').href = filePath;

    // Clear previous content
    const fileContentBody = document.getElementById('fileContentBody');
    fileContentBody.innerHTML = '';

    // Check if file is a PDF or an image
    if (filePath.endsWith('.pdf')) {
        // Display PDF in an iframe
        fileContentBody.innerHTML = `<iframe src="${filePath}" width="100%" height="600px" frameborder="0"></iframe>`;
    } else if (/\.(jpg|jpeg|png|gif)$/i.test(filePath)) {
        // Display image in img tag
        fileContentBody.innerHTML = `<img src="${filePath}" alt="${fileTitle}" style="width: 100%; height: auto;">`;
    } else {
        // Unsupported file type message
        fileContentBody.innerHTML = `<p>This file format is not supported for preview. <a href="${filePath}" target="_blank">Download</a></p>`;
    }

    // Show the modal
    $('#fileContentModal').modal('show');
}


</script>
@endsection
