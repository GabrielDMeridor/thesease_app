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

    <!-- Content Row for Analytics -->
    <div class="row">
        <!-- Step Progress Analytics Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Analytics by Program</h6>
                </div>
                <div class="card-body">
                    <!-- Degree and Program Selection -->
                    <div class="form-group">
                        <label for="degreeSelect">Select Degree:</label>
                        <select id="degreeSelect" class="form-control">
                            <option value="">-- Select Degree --</option>
                            <option value="Masteral">Masteral</option>
                            <option value="Doctorate">Doctorate</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="programSelect">Select Program:</label>
                        <select id="programSelect" class="form-control" disabled>
                            <option value="">-- Select Program --</option>
                        </select>
                    </div>
                    <canvas id="analyticsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Nationality Analytics Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Nationality Analytics</h6>
                </div>
                <div class="card-body">
                    <canvas id="nationalityChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Charts and AJAX calls -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const programs = {
        'Masteral': ['MAEd', 'MA-Psych-CP', 'MBA', 'MS-CJ-Crim', 'MDS', 'MIT', 'MSPH', 'MPH', 'MS-MLS', 'MAN', 'MN'],
        'Doctorate': ['PhD-CI-ELT', 'PhD-Ed-EM', 'PhD-Mgmt', 'DBA', 'DIT', 'DrPH-HPE']
    };

    let barChart, pieChart;

    function initCharts() {
        const barCtx = document.getElementById('analyticsChart').getContext('2d');
        barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5', 'Step 6', 'Step 7', 'Step 8'],
                datasets: [{
                    label: 'Students in Step',
                    data: [0, 0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, stepSize: 1 }
                }
            }
        });

        const pieCtx = document.getElementById('nationalityChart').getContext('2d');
        pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Filipino', 'Foreign'],
                datasets: [{
                    label: 'Student Nationality',
                    data: [0, 0],
                    backgroundColor: ['rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)'],
                    borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 1
                }]
            }
        });
    }

    function updateBarChart(data) {
        barChart.data.datasets[0].data = [
            data.step_1, data.step_2, data.step_3, data.step_4,
            data.step_5, data.step_6, data.step_7, data.step_8
        ];
        barChart.update();
    }

    function updatePieChart(data) {
        pieChart.data.datasets[0].data = [data.filipino, data.foreign];
        pieChart.update();
    }

    $('#degreeSelect').on('change', function() {
        const selectedDegree = $(this).val();
        $('#programSelect').empty().append('<option value="">-- Select Program --</option>').prop('disabled', true);

        if (selectedDegree && programs[selectedDegree]) {
            programs[selectedDegree].forEach(program => {
                $('#programSelect').append(new Option(program, program));
            });
            $('#programSelect').prop('disabled', false);
        } else {
            $('#programSelect').prop('disabled', true);
        }
    });

    $('#programSelect').on('change', function() {
        const selectedProgram = $(this).val();

        if (selectedProgram) {
            $.ajax({
                url: "{{ route('admin.analyticsData') }}",
                type: "POST",
                data: {
                    program: selectedProgram,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    updateBarChart(data.stepsData);
                    updatePieChart(data.nationalityData);
                }
            });
        }
    });

    $(document).ready(function() {
        initCharts();
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
@endsection
