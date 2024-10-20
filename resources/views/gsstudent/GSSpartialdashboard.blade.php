@extends('gsstudent.GSSmain-layout')

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

        <!-- Loop through all notifications -->
        @foreach (auth()->user()->notifications as $notification)
            <a class="dropdown-item d-flex align-items-center {{ $notification->read_at ? 'text-muted' : 'font-weight-bold' }}" href="#" onclick="markAsRead('{{ $notification->id }}')">
                <div class="mr-3">
                    <div class="icon-circle bg-primary">
                        <i class="fas fa-file-alt text-white"></i>
                    </div>
                </div>
                <div>
                    <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                    <span>{{ $notification->data['message'] }}</span>
                </div>
            </a>
        @endforeach

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
        {{ $title }}
    </div>
    <br>

    <div class="card">
        <div class="card-header">Files</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>File</th>
                        <th></th>
                        <th>Action</th>
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
                                    onclick="openFileModal('{{ asset('storage/immigrations/' . $immigrationFile) }}')">{{ $immigrationFile }}</a>
                            @else
                                Not Uploaded
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal"
                                data-type="immigration_or_studentvisa"
                                data-action="{{ $immigrationFile ? 'update' : 'upload' }}">
                                {{ $immigrationFile ? 'Update' : 'Upload' }}
                            </button>
                        </td>
                    </tr>
                    @endif

                    <!-- Routing Form One -->
                    <tr>
                        <td>Routing Form One:</td>
                        <td>
                            @if ($routingFormOneFile)
                                <a href="#" data-toggle="modal" data-target="#fileModal" 
                                    onclick="openFileModal('{{ asset('storage/routing_forms/' . $routingFormOneFile) }}')">{{ $routingFormOneFile }}</a>
                            @else
                                Not Uploaded
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal"
                                data-type="routing_form_one"
                                data-action="{{ $routingFormOneFile ? 'update' : 'upload' }}">
                                {{ $routingFormOneFile ? 'Update' : 'Upload' }}
                            </button>
                        </td>
                    </tr>

                    <!-- Manuscript -->
                    <tr>
                        <td>Manuscript:</td>
                        <td>
                            @if ($manuscriptFile)
                                <a href="#" data-toggle="modal" data-target="#fileModal" 
                                    onclick="openFileModal('{{ asset('storage/manuscripts/' . $manuscriptFile) }}')">{{ $manuscriptFile }}</a>
                            @else
                                Not Uploaded
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal"
                                data-type="manuscript"
                                data-action="{{ $manuscriptFile ? 'update' : 'upload' }}">
                                {{ $manuscriptFile ? 'Update' : 'Upload' }}
                            </button>
                        </td>
                    </tr>

                    <!-- Adviser Appointment Form -->
                    <tr>
                        <td>Adviser Appointment Form:</td>
                        <td>
                            @if ($adviserAppointmentFile)
                                <a href="#" data-toggle="modal" data-target="#fileModal" 
                                    onclick="openFileModal('{{ asset('storage/adviser_appointments/' . $adviserAppointmentFile) }}')">{{ $adviserAppointmentFile }}</a>
                            @else
                                Not Uploaded
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal"
                                data-type="adviser_appointment_form"
                                data-action="{{ $adviserAppointmentFile ? 'update' : 'upload' }}">
                                {{ $adviserAppointmentFile ? 'Update' : 'Upload' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
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
        </div>
    </div>
</div>

<!-- File Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="fileForm" action="{{ route('gssstudent.file.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="file_type" id="fileType">
                    <div class="form-group">
                        <label for="file">Choose File</label>
                        <input type="file" class="form-control-file" name="file" id="file" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openFileModal(fileUrl) {
        document.getElementById('filePreview').src = fileUrl;
    }

    $('#uploadModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var fileType = button.data('type');
        $('#fileType').val(fileType);
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

</script>

@endsection
