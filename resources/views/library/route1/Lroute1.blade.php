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
                <span class="badge badge-danger badge-counter">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Notifications Center</h6>

                <!-- Limit the notifications to 5 -->
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

<!-- /.content-header -->
@endsection

@section('body')
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div> 
    <br>
</div>

<!-- Search Bar -->
<div class="mb-3">
    <form action="{{ route('library.search') }}" method="GET">
        <input type="text" name="query" id="searchInput" class="form-control" placeholder="Search Student by Name..." value="{{ old('query', $keyword ?? '') }}">
    </form>
</div>

<div class="container-fluid">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Uploaded Manuscript</th>
                    <th>Uploaded Certificate</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->student->name }}</td>

                        <!-- Uploaded Manuscript column -->
                        <td>
                            @if($appointment->similarity_manuscript)
                                <a href="#" data-toggle="modal" data-target="#manuscriptModal{{ $appointment->id }}">
                                    {{ basename($appointment->similarity_manuscript) }}
                                </a>
                            @else
                                <span>No manuscript uploaded</span>
                            @endif
                        </td>

                        <!-- Uploaded Certificate column with Choose File input -->
                        <td>
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

                        <!-- Action column with Save button only -->
                        <td>
                            <button type="submit" form="certificateUploadForm{{ $appointment->student_id }}" class="btn btn-primary">Save</button>
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
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection



