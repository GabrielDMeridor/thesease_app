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
                        <i class="fas fa-bell fa-fw"></i>
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
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div>
    <br>


    <!-- Search form -->
    <div class="card">
        <div class="card-body">
        <form action="{{ route('superadmin.route1') }}" method="GET">
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

    <!-- Students Table -->
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped custom-table">
        <thead class="table-dark">
            <tr>
                <th style="text-align:center;">Name</th>
                <th style="text-align:center;">Program</th>
                <th style="text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr>
                    <td class="text-center">{{ $student->name }}</td>
                    <td class="text-center">{{ $student->program }}</td>
                    <td class="text-center">
                        <a href="{{ route('superadmin.showRoutingForm', $student->id) }}" class="btn btn-primary"><i class="fa-solid fa-file"></i> View Routing Form 1</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No students found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


    <!-- Pagination Links -->
    {{ $students->links() }}

</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
$(document).ready(function() {
    // Listen for keyup events on the search input
    $('input[name="search"]').on('keyup', function() {
        let search = $(this).val();

        // AJAX request to the search route
        $.ajax({
            url: "{{ route('superadmin.route1.search') }}",
            type: 'GET',
            data: { search: search },
            success: function(response) {
                let tableBody = '';

                // Loop through the results and construct table rows
                response.forEach(function(student) {
                    tableBody += `
                        <tr>
                            <td class="text-center">${student.name}</td>
                            <td class="text-center">${student.program || 'N/A'}</td>
                            <td class="text-center">
                                <a href="/superadmin/route1/student/${student.id}" class="btn btn-primary">
                                    <i class="fa-solid fa-file"></i> View Routing Form 1
                                </a>
                            </td>
                        </tr>`;
                });

                // Replace the table body with the new data
                $('table tbody').html(tableBody);
            },
            error: function() {
                console.error("Search failed.");
            }
        });
    });
});
</script>

@endsection