
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
    <div class="sagreet">
        {{ $title }}
    </div>
    <br>
    <div style="display: flex; gap: 20px;">
        <!-- Calendar Section -->
        <div style="flex: 2; border: 1px solid #000; padding: 20px;">
            <h3>Calendar</h3>
            <div id="calendar" style="min-height: 300px;">
                <!-- FullCalendar will be rendered here -->
            </div>
        </div>

        <!-- Scheduling Form Section -->
        <div style="flex: 1; border: 1px solid #000; padding: 20px;">
            <h2>Set Schedule</h2>

            <form action="{{ route('superadmin.calendar.schedule.store') }}" method="POST">
                @csrf

                <!-- Select Student -->
                <div style="margin-bottom: 15px;">
                    <label for="student_id">Select Student:</label>
                    <select name="student_id" id="student_id" required style="width: 100%; padding: 8px; margin-top: 5px;">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Schedule Type -->
                <div style="margin-bottom: 15px;">
                    <label for="schedule_type">Schedule Type:</label>
                    <select name="schedule_type" id="schedule_type" required style="width: 100%; padding: 8px; margin-top: 5px;">
                        <option value="Proposal Defense">Proposal Defense</option>
                        <option value="Final Defense">Final Defense</option>
                    </select>
                </div>

                <!-- Available Panel Members -->
                <div style="margin-bottom: 15px;">
                    <label for="available_panel_members">Select Panel Members:</label>
                    <select id="available_panel_members" style="width: 100%; padding: 8px; margin-top: 5px;">
                        <option value="">-- Select Panel Member --</option>
                        @foreach($advisers as $adviser)
                            <option value="{{ $adviser->id }}">{{ $adviser->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Selected Panel Members -->
                <div style="margin-bottom: 15px;">
                    <label>Selected Panel Members:</label>
                    <ul id="selected_panel_members" style="list-style: none; padding: 0; margin-top: 5px;">
                        <!-- Selected panel members will appear here -->
                    </ul>
                </div>

                <!-- Hidden input to store selected panel members as array -->
                <input type="hidden" name="panel_members" id="panel_members">

                <!-- Proposal Defense Schedule -->
                <div style="margin-bottom: 15px;">
                    <h3>Proposal Defense Schedule</h3>
                    <label for="proposal_defense_date">Date:</label>
                    <input type="date" name="proposal_defense_date" id="proposal_defense_date" required style="width: 100%; padding: 8px; margin-top: 5px;">

                    <label for="proposal_defense_time" style="margin-top: 10px; display: block;">Time:</label>
                    <input type="time" name="proposal_defense_time" id="proposal_defense_time" required style="width: 100%; padding: 8px; margin-top: 5px;">
                </div>

                <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px;">Save Schedule</button>
            </form>
        </div>
    </div>

    <!-- FullCalendar CSS and JS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>

    <!-- Select2 CSS and JS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            timeZone: 'UTC', // Set timezone to UTC
            displayEventTime: true,
            events: '/superadmin/calendar/events', // Fetch events from your route
            slotLabelFormat: { // Ensure correct time labels
                hour: '2-digit',
                minute: '2-digit',
                hour12: true,
            },
            eventDisplay: 'block', // Force events to display as blocks in grid
            eventContent: function(arg) {
                return { html: `<b>${arg.event.title}</b><br>${arg.event.extendedProps.description}` };
            }
        });

        calendar.render();

        // Initialize Select2 for dropdowns
        $('#student_id').select2({
            placeholder: "Select a student",
            width: '100%'
        });

        $('#available_panel_members').select2({
            placeholder: "Select a panel member",
            width: '100%'
        });
    });
    </script>

    <!-- JavaScript for handling the dynamic selection of panel members -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const availablePanelMembers = document.getElementById("available_panel_members");
            const selectedPanelMembers = document.getElementById("selected_panel_members");
            const panelMembersInput = document.getElementById("panel_members");

            let selectedMembers = [];

            function updatePanelMembersInput() {
                panelMembersInput.value = JSON.stringify(selectedMembers);
            }

            function refreshAvailablePanelMembers() {
                // Remove all options from Select2, then re-add them
                $('#available_panel_members').empty();
                $('#available_panel_members').append('<option value="">-- Select Panel Member --</option>');
                @foreach($advisers as $adviser)
                    if (!selectedMembers.includes("{{ $adviser->id }}")) {
                        $('#available_panel_members').append(`<option value="{{ $adviser->id }}">{{ $adviser->name }}</option>`);
                    }
                @endforeach
                $('#available_panel_members').select2(); // Refresh Select2
            }

            availablePanelMembers.addEventListener("change", function () {
                const selectedId = availablePanelMembers.value;
                const selectedName = availablePanelMembers.options[availablePanelMembers.selectedIndex].text;

                if (selectedId && !selectedMembers.includes(selectedId)) {
                    selectedMembers.push(selectedId);

                    const listItem = document.createElement("li");
                    listItem.textContent = selectedName;
                    listItem.setAttribute("data-id", selectedId);

                    const removeButton = document.createElement("button");
                    removeButton.textContent = "Remove";
                    removeButton.style.marginLeft = "10px";
                    removeButton.type = "button";
                    removeButton.addEventListener("click", function () {
                        selectedMembers = selectedMembers.filter(id => id !== selectedId);
                        selectedPanelMembers.removeChild(listItem);
                        updatePanelMembersInput();
                        refreshAvailablePanelMembers(); // Refresh options when a member is removed
                    });

                    listItem.appendChild(removeButton);
                    selectedPanelMembers.appendChild(listItem);

                    updatePanelMembersInput();
                    refreshAvailablePanelMembers(); // Refresh options when a member is added
                }
            });

            // Initial refresh for available panel members in case some are pre-selected
            refreshAvailablePanelMembers();
        });
    </script>

    <style>
        .fc-daygrid-event {
            height: auto !important;
            max-height: 100px; /* Adjust as needed */
            overflow: hidden; /* Prevent content overflow */
        }

        .fc-daygrid-event-dot {
            height: auto !important;
        }

        .fc-daygrid-day-frame {
            overflow: hidden;
        }
    </style>
@endsection