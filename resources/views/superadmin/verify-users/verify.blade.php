@extends('superadmin.SAmain-layout')

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
                <span class="badge badge-danger badge-counter">3+</span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Notifications Center</h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-primary">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 12, 2023</div>
                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-success">
                            <i class="fas fa-donate text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 7, 2023</div>
                        $290.29 has been deposited into your account!
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-warning">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 2, 2023</div>
                        Spending Alert: We've noticed unusually high spending for your account.
                    </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
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
<div class="row">
    <div class="container-fluid">

        <div class="sagreet">{{ $title }}</div>
        <br>
        

        <!-- Filter Form -->
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('verify-users.index') }}" id="filterForm" class="mb-4">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm">
                                <!-- Filter by Account Type -->
                                <div class="form-group">
                                    <label for="account_type">Filter by <b>Account Type</b> and <b>Verification Status</b></label>
                                    <select id="account_type" name="account_type" class="form-control" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <option value="{{ \App\Models\User::SuperAdmin }}" {{ request('account_type') == \App\Models\User::SuperAdmin ? 'selected' : '' }}>SuperAdmin</option>
                                    <option value="{{ \App\Models\User::Admin }}" {{ request('account_type') == \App\Models\User::Admin ? 'selected' : '' }}>Admin</option>
                                    <option value="{{ \App\Models\User::GraduateSchool }}" {{ request('account_type') == \App\Models\User::GraduateSchool ? 'selected' : '' }}>Graduate School</option>
                                    <option value="{{ \App\Models\User::ProgramChair }}" {{ request('account_type') == \App\Models\User::ProgramChair ? 'selected' : '' }}>Program Chair</option>
                                    <option value="{{ \App\Models\User::Thesis_DissertationProfessor }}" {{ request('account_type') == \App\Models\User::Thesis_DissertationProfessor ? 'selected' : '' }}>Thesis/Dissertation Professor</option>
                                    <option value="{{ \App\Models\User::Library }}" {{ request('account_type') == \App\Models\User::Library ? 'selected' : '' }}>Library</option>
                                    <option value="{{ \App\Models\User::AufEthicsReviewCommittee }}" {{ request('account_type') == \App\Models\User::AufEthicsReviewCommittee ? 'selected' : '' }}>AUF Ethics Review Committee</option>
                                    <option value="{{ \App\Models\User::Statistician }}" {{ request('account_type') == \App\Models\User::Statistician ? 'selected' : '' }}>Statistician</option>
                                    <option value="{{ \App\Models\User::OVPRI }}" {{ request('account_type') == \App\Models\User::OVPRI ? 'selected' : '' }}>OVPRI</option>
                                    <option value="{{ \App\Models\User::LanguageEditor }}" {{ request('account_type') == \App\Models\User::LanguageEditor ? 'selected' : '' }}>Language Editor</option>
                                    <option value="{{ \App\Models\User::GraduateSchoolStudent }}" {{ request('account_type') == \App\Models\User::GraduateSchoolStudent ? 'selected' : '' }}>Graduate School Student</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm">
                                <!-- Keyword search input -->
                                <div class="form-group">
                                    <label for="keyword">Search by <b>Name</b></label>
                                    <input type="text" id="keyword" name="keyword" class="form-control" placeholder="Enter name" value="{{ request('keyword') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container filteringstatus">
                        <div class="row">
                            <div class="col-sm">
                                <!-- Checkbox filters for verification status -->
                                <div class="form-group">
                                    <label class="lns-checkbox">
                                        <input type="checkbox" name="status[]" value="verified" {{ in_array('verified', request('status', [])) ? 'checked' : '' }} onchange="this.form.submit()">
                                        <span>Verified</span>
                                    </label>
                                    <label class="lns-checkbox">
                                        <input type="checkbox" name="status[]" value="unverified" {{ in_array('unverified', request('status', [])) ? 'checked' : '' }} onchange="this.form.submit()">
                                        <span>Unverified</span>
                                    </label>
                                    <label class="lns-checkbox">
                                        <input type="checkbox" name="status[]" value="disapproved" {{ in_array('disapproved', request('status', [])) ? 'checked' : '' }} onchange="this.form.submit()">
                                        <span>Disapproved</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <br>

        <div class="user-management-section">
            <!-- Main Table for User Information -->
            <form id="verification-form" method="POST" action="{{ route('verify-users.verify') }}">
                @csrf
                <input type="hidden" name="verification_status" id="verification-status">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped custom-table">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Account Type</th>
                                <th>Degree</th>
                                <th>Program</th>
                                <th>Nationality</th>
                                <th class="text-center">Files</th>
                                <th>Created At</th>
                                <th class="text-center">Verification Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ \App\Models\User::getAccountTypeName($user->account_type) }}</td>
                                <td>{{ $user->degree ?? 'N/A' }}</td>
                                <td>{{ $user->program ?? 'N/A' }}</td>
                                <td>{{ $user->nationality ?? 'N/A' }}</td>
                                
                                <!-- Single button for all files -->
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="openFileListModal({{ $user->id }})">
                                        <i class="fas fa-folder-open"></i> View Files
                                    </button>
                                </td>

                                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y-m-d') }}</td>

                                <!-- Verification Status -->
                                <td class="text-center" style="font-size: 1.5rem;">
                                    @if ($user->verification_status === 'verified')
                                    <i class="fas fa-check-circle text-success" style="cursor: pointer;" onclick="openModal({{ $user->id }})"></i>
                                    @elseif ($user->verification_status === 'disapproved')
                                    <i class="fas fa-times-circle text-danger" style="cursor: pointer;" onclick="openModal({{ $user->id }})"></i>
                                    @else
                                    <i class="fas fa-circle text-secondary" style="cursor: pointer;" onclick="openModal({{ $user->id }})"></i>
                                    @endif
                                </td>

                                <!-- Action Buttons -->
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="openDeletionModal({{ $user->id }})">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center tablefooter mt-2 flex-nowrap">
                    <!-- Pagination Info (left corner) -->
                    <div class="pagination-info">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                    </div>

                    <!-- Pagination Links (right corner) -->
                    <nav aria-label="User Pagination" class="mt-2 ">
                        {!! $users->links('pagination::bootstrap-4') !!}
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<br>
<br>

        <!-- Modal for Listing Files -->
        <div class="modal fade" id="fileListModal" tabindex="-1" role="dialog" aria-labelledby="fileListModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fileListModalLabel">Files</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="file-list-body">
                        <!-- List of files will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for File Preview -->
        <div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="file-preview-container"></div>
                    </div>
                    <div class="modal-footer">
                        <a href="" id="downloadLink" class="btn btn-primary" download>Download</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Verification Options -->
        <div class="modal fade" id="verificationModal" tabindex="-1" role="dialog" aria-labelledby="verificationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verificationModalLabel">Verification Options</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>What would you like to do with this user?</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-danger" onclick="openDisapproveModal({{ $user->id }})">
                            Disapprove
                        </button>

                        <button class="btn btn-success" onclick="verifyUser()">Verify</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Deletion Confirmation -->
        <div class="modal fade" id="deletionModal" tabindex="-1" role="dialog" aria-labelledby="deletionModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletionModalLabel">Confirm Deletion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this user?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Disapproval Reason -->
<div class="modal fade" id="disapproveModal" tabindex="-1" role="dialog" aria-labelledby="disapproveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disapproveModalLabel">Reason for Disapproval</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="disapproveForm" method="POST" action="{{ route('verify-users.disapprove') }}">
                    @csrf
                    <input type="hidden" name="user_id" id="user-id">
                    
                    <div class="form-group">
                        <label for="disapprove_reason">Message</label>
                        <textarea name="disapprove_reason" id="disapprove_reason" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="modal-footer">
                    <button class="btn btn-danger" onclick="disapproveUser()">Disapprove</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


        <!-- Delete Form -->
        <form id="delete-form" method="POST" action="" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <!-- JavaScript -->
        <script>
            let selectedUserId = null;

            // Function to open the modal for verification options
            function openModal(userId) {
                selectedUserId = userId;
                $('#verificationModal').modal('show');
            }

            // Function to handle verification
            function verifyUser() {
                updateVerificationStatus('verified');
            }

            // Function to handle disapproval
            function disapproveUser() {
                updateVerificationStatus('disapproved');
            }

            // Function to update the verification status
            function updateVerificationStatus(status) {
                document.getElementById('verification-status').value = status;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = selectedUserId;
                document.getElementById('verification-form').appendChild(input);
                document.getElementById('verification-form').submit();
                $('#verificationModal').modal('hide');
            }

            let userIdToDelete;

            // Function to open the deletion modal
            function openDeletionModal(userId) {
                userIdToDelete = userId;
                $('#deletionModal').modal('show');
            }

            // Function to handle delete confirmation
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                var deleteForm = document.getElementById('delete-form');
                deleteForm.action = '/superadmin/verifyusers/' + userIdToDelete;
                deleteForm.submit();
            });

            // Function to open the file list modal
            let files = <?php echo json_encode($users->mapWithKeys(function($user) {
                return [
                    $user->id => [
                        'immigration' => $user->immigration_or_studentvisa, 
                        'routing' => $user->routing_form_one, 
                        'manuscript' => $user->manuscript,
                        'adviser' => $user->adviser_appointment_form
                    ]
                ];
            })->toArray()); ?>;

            function openFileListModal(userId) {
                const fileListBody = document.getElementById('file-list-body');
                fileListBody.innerHTML = '';

                const userFiles = files[userId];

                if (userFiles.immigration) {
                    fileListBody.innerHTML += `<a href="#" onclick="showFileModal('/storage/immigrations/${userFiles.immigration}', 'image')">Immigration/Student Visa: ${userFiles.immigration}</a><br>`;
                }
                if (userFiles.routing) {
                    const fileType = userFiles.routing.endsWith('pdf') ? 'pdf' : 'doc';
                    fileListBody.innerHTML += `<a href="#" onclick="showFileModal('/storage/routing_forms/${userFiles.routing}', '${fileType}')">Routing Form One: ${userFiles.routing}</a><br>`;
                }
                if (userFiles.manuscript) {
                    const fileType = userFiles.manuscript.endsWith('pdf') ? 'pdf' : 'doc';
                    fileListBody.innerHTML += `<a href="#" onclick="showFileModal('/storage/manuscripts/${userFiles.manuscript}', '${fileType}')">Manuscript: ${userFiles.manuscript}</a><br>`;
                }
                if (userFiles.adviser) {
                    const fileType = userFiles.adviser.endsWith('pdf') ? 'pdf' : 'doc';
                    fileListBody.innerHTML += `<a href="#" onclick="showFileModal('/storage/adviser_appointments/${userFiles.adviser}', '${fileType}')">Adviser Appointment Form: ${userFiles.adviser}</a><br>`;
                }

                $('#fileListModal').modal('show');
            }

            // Function to handle file modal display
            function showFileModal(fileUrl, fileType) {
                let previewContainer = document.getElementById('file-preview-container');
                let downloadLink = document.getElementById('downloadLink');

                // Clear existing content in the modal
                previewContainer.innerHTML = '';

                // Set the download link
                downloadLink.href = fileUrl;
                downloadLink.setAttribute('download', fileUrl.split('/').pop());

                if (fileType === 'pdf') {
                    previewContainer.innerHTML = `<iframe src="${fileUrl}" style="width: 100%; height: 500px;" frameborder="0"></iframe>`;
                } else if (fileType === 'image') {
                    previewContainer.innerHTML = `<img src="${fileUrl}" style="width: 100%; height: auto;" alt="File Preview">`;
                }

                $('#filePreviewModal').modal('show');
            }
            function openDisapproveModal(userId) {
                $('#user-id').val(userId);  // Set user ID
                $('#disapproveModal').modal('show');  // Show modal
            }

        </script>
@endsection
