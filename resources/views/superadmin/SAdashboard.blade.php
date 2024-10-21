@extends('superadmin.SAmain-layout')

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
        <div class="overflow-auto" style="max-height: 300px;"> <!-- This is the scrolling part -->
            @foreach (auth()->user()->notifications->take(5) as $notification)  <!-- Limit to 5 -->
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

<!-- /.content-header -->
@endsection

@section('body')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Greeting -->
    <div class=" sagreet">
            Welcome, {{ auth()->user()->name ?? 'Super Admin' }}
    </div>

    <br>
        <div class="row mb-4">
            <!-- Announcements Section -->
            <div class=" col-md-6 mb-3 mb-lg-0">
                <div class="card h-100" style="border-radius: 0;">
                    <div class="card-header">Announcements</div>
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td>There are no current announcements</td>
                                    <td class="text-end text-muted">-------</td>
                                </tr>
                                <tr>
                                    <td>There are no current announcements</td>
                                    <td class="text-end text-muted">-------</td>
                                </tr>
                                <tr>
                                    <td>There are no current announcements</td>
                                    <td class="text-end text-muted">-------</td>
                                </tr>
                                <tr>
                                    <td>There are no current announcements</td>
                                    <td class="text-end text-muted">-------</td>
                                </tr>
                                <tr>
                                    <td>There are no current announcements</td>
                                    <td class="text-end text-muted">-------</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer footerdashboard" style="background-color: transparent; ">
                        <button class="btn btn-view-all w-100 dashbtn">View all announcements</button>
                    </div>
                </div>
            </div>



            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">Post Announcement</div>
                    <div class="card-body cardannouncement">
                        <form>
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control textannouncement" id="title">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control textannouncement" id="description" rows="3"></textarea>
                            </div>

                            <!-- Post Button Container -->
                            <div class="post-button-container">
                                <button type="submit" class="btn post-button">Post</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Bar Chart Section -->
            <div class=" col-md-6 mb-3 mb-lg-0">
                <div class="card bar-chart-container h-100">
                    <div class="card-header">Students in Routing Form</div>
                    <div class="bar-chart">
                        <!-- You can use a chart.js or another library to render the bar chart -->
                        <p>Routing Form Bar Chart</p>
                    </div>
                </div>
            </div>

            <!-- Pie Chart Section -->
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <!-- Card Header -->
                    <div class="card-header py-3 piechartheader">
                        <h6 class="m-0 "  style="text-align:center;">Students</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                    <h6 class="m-0 " style="text-align:center;">Students</h6>
                        <div class="chart-pie pt-4">
                            <canvas id="myPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    
<!-- /.container-fluid -->
@endsection

<!-- Custom Styles -->
@section('styles')
<style>
    /* Custom Table Styles */
    .custom-table thead th {
        background-color: #495464; /* Dark color for the header */
        color: white; /* White text for better contrast */
        padding: 10px;
        text-align: left;
        border-bottom: 2px solid #343a40;
    }

    /* Alternating row colors */
    .custom-table tbody tr:nth-child(odd) {
        background-color: #f2f2f2; /* Light gray for odd rows */
    }

    .custom-table tbody tr:nth-child(even) {
        background-color: #d3d3d3; /* Darker gray for even rows */
    }

    /* Hover effect for rows */
    .custom-table tbody tr:hover {
        background-color: #bfbfbf; /* Slightly darker gray on hover */
    }

    /* Table cell padding */
    .custom-table tbody td {
        padding: 10px;
        text-align: left;
    }
</style>

<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example
var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ["Direct", "Referral", "Social"],
    datasets: [{
      data: [55, 30, 15],
      backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
      hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
    legend: {
      display: false
    },
    cutoutPercentage: 80,
  },
});
    </script>

@endsection
