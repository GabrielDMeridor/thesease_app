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
<div class="container-fluid">
    <div class="sagreet">
        {{ $title }}
    </div>
    <br>

    <div style="display: flex; gap: 20px;">
        <!-- Calendar Section -->
        <div class="calendar-design">
            <h3 class="calendar-heading">Calendar</h3>
            <div id="calendar" style="min-height: 300px;" class="calendar-body">
                <!-- FullCalendar will be rendered here -->
            </div>
        </div>

        <!-- Scheduling Form Section -->
        <div style="flex: 1; border: 1px solid #000; padding: 20px;" class="setsched-body">
            <h2 class="setsched-heading">Schedule a defense</h2>

            <form action="{{ route('admin.calendar.schedule.store') }}" method="POST">
                @csrf

                <!-- Select Student -->
                <div style="margin-bottom: 15px;">
                    <label for="student_id" class="setsched-p">Select Student:</label>
                    <select name="student_id" id="student_id" required style="width: 100%; padding: 8px; margin-top: 5px;" class="form-control">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Schedule Type -->
                <div style="margin-bottom: 15px;">
                    <label for="schedule_type" class="setsched-p">Schedule Type:</label>
                    <select name="schedule_type" id="schedule_type" required style="width: 100%; padding: 8px; margin-top: 5px;" class="form-control">
                        <option value="Proposal Defense">Proposal Defense</option>
                        <option value="Final Defense">Final Defense</option>
                    </select>
                </div>

                <!-- Available Panel Members -->
                <div style="margin-bottom: 15px;">
                    <label for="available_panel_members" class="setsched-p">Select Panel Members:</label>
                    <select id="available_panel_members" style="width: 100%; padding: 8px; margin-top: 5px;" class="form-control">
                        <option value="">-- Select Panel Member --</option>
                        @foreach($advisers as $adviser)
                            <option value="{{ $adviser->id }}">{{ $adviser->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                    <!-- Selected Panel Members -->
                <div style="margin-bottom: 15px;">
                    <label class="setsched-p">Selected Panel Members:</label>
                    <ul id="selected_panel_members" style="list-style: none; padding: 0; margin-top: 5px;" class="setsched-p">
                        <!-- Selected panel members will appear here -->
                    </ul>
                </div>

                <!-- Hidden input to store selected panel members as array -->
                <input type="hidden" name="panel_members" id="panel_members">
                </div>


                <!-- Proposal Defense Schedule -->
                <div style="margin-bottom: 15px;">
                    <h3 class="setsched-heading">Proposal Defense Schedule</h3>
                    <label for="proposal_defense_date" class="setsched-p">Date:</label>
                    <input type="date" name="proposal_defense_date" id="proposal_defense_date" required style="width: 100%; padding: 8px; margin-top: 5px;" class="form-control">

                    <label for="proposal_defense_time" style="margin-top: 10px; display: block;" class="setsched-p">Time:</label>
                    <input type="time" name="proposal_defense_time" id="proposal_defense_time" required style="width: 100%; padding: 8px; margin-top: 5px;" class="form-control">
                </div>

                <button type="submit" class="btn btn-success btn-affix">Save Schedule</button>
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
            events: '/admin/calendar/events', // Fetch events from your route
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
                    removeButton.style.backgroundColor = '#CA6D38'; // Orange background color
                    removeButton.style.color = 'white'; // White text color
                    removeButton.style.border = 'none'; // Remove default border
                    removeButton.style.fontSize = '13px'; // Font size
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
