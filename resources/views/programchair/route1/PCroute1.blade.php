@extends('programchair.PCmain-layout')

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
                    <p>{{ $notification->data['reason'] }}</p> <!-- Display the reason for disapproval -->
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
@endsection

@section('body')

<!-- Main Container for the content -->
    <div class="container-fluid">
        <div class="sagreet">{{ $title }}</div>
    <br>

    <div class="container">
    <div class="row">
        <!-- Select a Student and Assign an Adviser Section -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header">
                    Choose a Student and Designate an Adviser
                </div>
                <div class="card-body">

                    <!-- Success & Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Form to assign adviser -->
                    <form method="POST" action="{{ route('programchair.assignAdviserToStudent') }}">
                        @csrf

                        <!-- Student Selection Dropdown -->
                        <div class="form-group">
                            <label for="student_id">Select Student:</label>
                            <select name="student_id" id="student_id" class="form-control" required onchange="showStudentProgram()">
                                <option value="" disabled selected>Select Student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" data-program="{{ $student->program }}">{{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Program Display -->
                        <div class="form-group">
                            <label for="program">Student's Program:</label>
                            <input type="text" id="program" class="form-control" readonly>
                        </div>

                        <!-- Adviser Selection Dropdown -->
                        <div class="form-group">
                            <label for="adviser_id">Select Adviser:</label>
                            <select name="adviser_id" id="adviser_id" class="form-control" required>
                                <option value="" disabled selected>Select Adviser</option>
                                @foreach ($advisers as $adviser)
                                    <option value="{{ $adviser->id }}">{{ $adviser->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Adviser Type -->
                        <div class="form-group">
                            <label for="appointment_type">Adviser Type:</label>
                            <input type="text" name="appointment_type" id="appointment_type" class="form-control" readonly>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success btn-affix">Request Adviser</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manage Approved Students Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Manage Approved Students
                </div>

                <div class="card-body">
                    <!-- Approved Students Section -->

                    <!-- Approved Student Selection Dropdown -->
                    <div class="form-group">
                        <label for="approved_student_id">Select Approved Student:</label>
                        <select name="approved_student_id" id="approved_student_id" class="form-control" onchange="getApprovedStudentDetails()">
                            <option value="" disabled selected>Select Approved Student</option>
                            @foreach ($approvedStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Signature Display -->
                    <div id="signatureDisplay" style="display:none;">
                        <h5>Signatures</h5>
                        <div class="form-group">
                            <label for="adviser_signature">Adviser Signature:</label>
                            <input type="text" id="adviser_signature" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="chair_signature">Program Chair Signature:</label>
                            <input type="text" id="chair_signature" class="form-control" readonly placeholder="Pending">
                        </div>
                        <div class="form-group">
                            <label for="dean_signature">Dean Signature:</label>
                            <input type="text" id="dean_signature" class="form-control" readonly>
                        </div>

                        <!-- Button to Affix Program Chair's Signature -->
                        <form method="POST" action="{{ route('programchair.affixSignature') }}">
                            @csrf
                            <input type="hidden" name="approved_student_id" id="approved_student_input">
                            <button type="submit" class="btn btn-success btn-affix" id="affixChairButton">Affix Program Chair's Signature</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // Show the program and adviser type when a student is selected
    function showStudentProgram() {
        var select = document.getElementById("student_id");
        var selectedOption = select.options[select.selectedIndex];
        var program = selectedOption.getAttribute("data-program");
        document.getElementById("program").value = program;

        var adviserType = '';
        if (['MN', 'MAN'].includes(program)) {
            adviserType = 'Clinical Case Study';
        } else if (['MIT', 'MPH'].includes(program)) {
            adviserType = 'Capstone';
        } else if (['PhD-CI-ELT', 'PHD-ED-EM', 'PHD-MGMT', 'DBA', 'DIT', 'DRPH-HPE'].includes(program)) {
            adviserType = 'Dissertation Study';
        } else {
            adviserType = 'Thesis Study';
        }
        document.getElementById("appointment_type").value = adviserType;
    }

    // Fetch signature details for an approved student
    function getApprovedStudentDetails() {
        var studentId = document.getElementById('approved_student_id').value;
        document.getElementById('approved_student_input').value = studentId;

        // Fetch signature details from the server
        fetch('/programchair/get-approved-student-details', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ approved_student_id: studentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                document.getElementById('signatureDisplay').style.display = 'block';
                document.getElementById('adviser_signature').value = data.adviser_signature;
                document.getElementById('chair_signature').value = data.chair_signature;
                document.getElementById('dean_signature').value = data.dean_signature;

                // Hide the button if Program Chair signature is already affixed
                if (data.chair_signature !== 'Pending') {
                    document.getElementById('affixChairButton').style.display = 'none';
                } else {
                    document.getElementById('affixChairButton').style.display = 'block';
                }
            }
        })
        .catch(error => console.error('Error fetching student details:', error));
    }
</script>

@endsection

