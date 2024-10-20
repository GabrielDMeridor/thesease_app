@extends('gsstudent.GSSmain-layout')

@section('content-header')
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <ul class="navbar-nav ml-auto">
<!-- Notification Button -->
<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <!-- Counter for Notifications -->
        <span class="badge badge-danger badge-counter">{{ auth()->user()->unreadNotifications->count() }}</span>
    </a>

    <!-- Dropdown - Notifications -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">Notifications Center</h6>
        
        @foreach (auth()->user()->unreadNotifications as $notification)
            <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="mr-3">
                    <div class="icon-circle bg-danger">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                </div>
                <div>
                    <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                    <span class="font-weight-bold">{{ $notification->data['message'] }}</span>
                </div>
            </a>
        @endforeach

        <a class="dropdown-item text-center small text-gray-500" href="#">Mark all as read</a>
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
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Greeting -->
    <div class=" sagreet">
        Welcome, {{ auth()->user()->name ?? 'GraduateSchoolStudent' }}
    </div>
    <br>

    <div class="dashboard-container">
        <!-- Main Table for List of Pending Requirements -->
        <div class="col-md-6 mb-3 mb-lg-0">
    <div class="card" style="border-radius: 0;">
            <div class="card-header">List of Pending Requirements</div>
            <div class="table-responsive">
                <table class="table table-hover table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td>Prepare Initial Files</td>
                            <td class="text-end"><span class="fas fa-check text-success"></span></td>
                        </tr>
                        <tr>
                            <td>Request for appointment of thesis/dissertation adviser</td>
                            <td class="text-end"><span class="fas fa-lock text-muted"></span></td>
                        </tr>
                        <tr>
                            <td>Consultation with Statistician</td>
                            <td class="text-end"><span class="fas fa-lock text-muted"></span></td>
                        </tr>
                        <tr>
                            <td>Submission Files to Graduate School</td>
                            <td class="text-end"><span class="fas fa-lock text-muted"></span></td>
                        </tr>
                        <tr>
                            <td>Endorsement of Manuscript for Proposal Defense</td>
                            <td class="text-end"><span class="fas fa-lock text-muted"></span></td>
                        </tr>
                        <tr>
                            <td>Similarity Checking</td>
                            <td class="text-end"><span class="fas fa-lock text-muted"></span></td>
                        </tr>
                        <tr>
                            <td>Research Registration</td>
                            <td class="text-end"><span class="fas fa-lock text-muted"></span></td>
                        </tr>
                        <tr>
                            <td>Submission of files to Graduate School</td>
                            <td class="text-end"><span class="fas fa-lock text-muted"></span></td>
                        </tr>
                        <tr>
                            <td>Application for Proposal Defense</td>
                            <td class="text-end"><span class="fas fa-lock text-muted"></span></td>
                        </tr>
                        <tr>
                            <td>Approval of the revised proposal by panel members</td>
                            <td class="text-end"><span class="fas fa-lock text-muted"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer footerdashboard" style="background-color: transparent;">
                <button class="btn btn-view-all w-100 dashbtn">Next Page</button>
            </div>
        </div>
    </div>


        <!-- Sidebar with Announcements and Upcoming Schedules -->
        <div class="sidebar" style="flex: 1;">
            <!-- Announcements Table -->
            <div class="card announcements-container" style="border-radius: 0;">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0">
                        <thead>
                            <tr class="card-header">
                                <th >Announcements</th>
                                <th >Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>There are no current announcements</td>
                                <td class="text-end text-muted">------</td>
                            </tr>
                            <tr>
                                <td>There are no current announcements</td>
                                <td class="text-end text-muted">------</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer announcements-view-all" style="background-color: transparent;">
                    <button class="btn btn-view-all w-100 dashbtn">View all announcements</button>
                </div>
            </div>

            <!-- Upcoming Schedules Table -->
            <div class="card schedules-container schedules-upcoming" style="border-radius: 0; margin-top: 20px;">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0">
                        <thead>
                            <tr class="card-header">
                                <th>Upcoming Schedules</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Consultation with Adviser</td>
                                <td class="text-end"><a href="#">May, 16</a></td>
                            </tr>
                            <tr>
                                <td>Consultation with Adviser</td>
                                <td class="text-end"><a href="#">May, 20</a></td>
                            </tr>
                            <tr>
                                <td>Meeting with Program Chair</td>
                                <td class="text-end"><a href="#">May, 29</a></td>
                            </tr>
                            <tr>
                                <td>Meeting with Adviser</td>
                                <td class="text-end"><a href="#">June, 4</a></td>
                            </tr>
                            <tr>
                                <td>Proposal Defense</td>
                                <td class="text-end"><a href="#">June, 6</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer schedules-button" style="background-color: transparent;">
                    <button class="btn btn-view-all w-100 dashbtn">Next Page</button>
                </div>
            </div>
        </div>

    </div>
<!-- /.container-fluid -->
@endsection
