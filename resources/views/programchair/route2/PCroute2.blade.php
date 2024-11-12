@extends('programchair.PCmain-layout')

@section('content-header')
    <div class="container-fluid">
        <h1 class="mb-4">{{ $title }}</h1>
    </div>
@endsection

@section('body')
<div class="container-fluid">
    <!-- Search Form -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <form method="GET" action="{{ route('programchair.route2.show') }}" class="form-inline">
                <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search by student name" value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
        </div>

        <div class="card-body">
            <!-- Community Uploads for Signing Table -->
            <h5>Community Uploads for Program Chair Signing</h5>
            @if($communityAppointments->count() > 0)
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Service Form</th>
                                <th>Accomplishment Report</th>
                                <th>Final Program Signature</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($communityAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->student->name }}</td>
                                    <td>{{ $appointment->student->email }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $appointment->community_extension_service_form_path) }}" target="_blank">View Service Form</a>
                                    </td>
                                    <td>
                                        <a href="{{ asset('storage/' . $appointment->community_accomplishment_report_path) }}" target="_blank">View Accomplishment Report</a>
                                    </td>
                                    <td>
                                        @if ($appointment->final_program_signature)
                                            <span class="badge badge-success">Signed</span>
                                        @else
                                            <span class="badge badge-secondary">Not Signed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!$appointment->final_program_signature)
                                            <form action="{{ route('program-chair.sign', $appointment) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm">Sign</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $communityAppointments->links() }}
                    </div>
                </div>
            @else
                <p class="text-center">No community uploads requiring signature.</p>
            @endif

            <!-- Publication Approvals Table -->
            <h5>Approve Proof of Publication Submissions</h5>
            @if($publicationAppointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Proof of Publication</th>
                                <th>Publication Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($publicationAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->student->name }}</td>
                                    <td>{{ $appointment->student->email }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $appointment->proof_of_publication_path) }}" target="_blank">
                                            {{ $appointment->proof_of_publication_original_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($appointment->publication_status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif ($appointment->publication_status === 'denied')
                                            <span class="badge badge-danger">Denied</span>
                                        @else
                                            <span class="badge badge-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($appointment->publication_status === null)
                                            <form action="{{ route('programchair.route2.approve', $appointment->student->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form action="{{ route('programchair.route2.deny', $appointment->student->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Deny</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $publicationAppointments->links() }}
                    </div>
                </div>
            @else
                <p class="text-center">No students have uploaded proof of publication.</p>
            @endif
        </div>
    </div>
</div>
@endsection
