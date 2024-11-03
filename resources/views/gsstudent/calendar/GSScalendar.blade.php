@extends('gsstudent.GSSmain-layout') <!-- Assuming you have a layout for students -->

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
    <div class="sagreet">
        {{ $title }}
    </div>
    <br>

<div class="container-fluid">
    <div class="sagreet">
        {{ $title }}
    </div>
    <br><div style="display: flex; gap: 20px;">
    <!-- Calendar Section -->
    <div style="flex: 2; border: 1px solid #000; padding: 20px;">
        <h3>Calendar</h3>
        <div id="calendar" style="min-height: 300px;">
            <!-- FullCalendar will be rendered here -->
        </div>
    </div>

    <!-- Student's Schedule Display -->
    <div style="flex: 1; border: 1px solid #000; padding: 20px;">
        <h2>Your Schedule</h2>
        
        @if($appointment)
            <p><strong>Schedule Type:</strong> {{ $appointment->schedule_type }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->proposal_defense_date)->format('m/d/y') }}</p>
            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->proposal_defense_time)->format('g:i A') }}</p>
            <p><strong>Panel Members:</strong></p>
            <ul>
                @forelse ($panelMembers as $panelMember)
                    <li>{{ $panelMember->name }}</li>
                @empty
                    <li>No panel members assigned.</li>
                @endforelse
            </ul>
        @else
            <p>No schedule assigned yet.</p>
        @endif
    </div>
</div>

<!-- FullCalendar Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        timeZone: 'UTC',
        events: '/gsstudent/calendar/events', // Define the route to fetch student-specific events
        eventDisplay: 'block',
        eventContent: function(arg) {
            return { html: `<b>${arg.event.title}</b><br>${arg.event.extendedProps.description}` };
        }
    });

    calendar.render();
});
</script>

<!-- FullCalendar CSS and JS via CDN -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>

@endsection
