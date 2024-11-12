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
<div class="container mt-4">
    <h2>Upload Thesis</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('gs.uploadThesis') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="title">Thesis Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="file">Thesis File (PDF only)</label>
            <input type="file" name="file" class="form-control" accept=".pdf" required>
        </div>

        <div class="form-group">
            <label for="year_published">Year Published</label>
            <input type="number" name="year_published" class="form-control" min="1900" max="{{ date('Y') }}" required>
        </div>

        <!-- Degree Type Selection -->
        <div class="form-group">
            <label for="degree_type">Degree Type</label>
            <select name="degree_type" id="degree_type" class="form-control" required>
                <option value="">Select Degree Type</option>
                <option value="Masteral">Masteral</option>
                <option value="Doctorate">Doctorate</option>
            </select>
        </div>

        <!-- Program Selection -->
        <div class="form-group">
            <label for="program">Program</label>
            <select name="program" id="program" class="form-control" required>
                <option value="">Select Program</option>
                <!-- Masteral Programs -->
                <optgroup label="Masteral Programs">
                    <option value="MAEd">MAEd</option>
                    <option value="MA-Psych-CP">MA-Psych-CP</option>
                    <option value="MBA">MBA</option>
                    <option value="MS-CJ-Crim">MS-CJ-Crim</option>
                    <option value="MDS">MDS</option>
                    <option value="MIT">MIT</option>
                    <option value="MSPH">MSPH</option>
                    <option value="MPH">MPH</option>
                    <option value="MS-MLS">MS-MLS</option>
                    <option value="MAN">MAN</option>
                    <option value="MN">MN</option>
                </optgroup>
                <!-- Doctorate Programs -->
                <optgroup label="Doctorate Programs">
                    <option value="PhD-CI-ELT">PhD-CI-ELT</option>
                    <option value="PhD-Ed-EM">PhD-Ed-EM</option>
                    <option value="PhD-Mgmt">PhD-Mgmt</option>
                    <option value="DBA">DBA</option>
                    <option value="DIT">DIT</option>
                    <option value="DRPH-HPE">DRPH-HPE</option>
                </optgroup>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Upload Thesis</button>
    </form>
</div>
@endsection
