@extends('ccfp.Cmain-layout')

@section('content-header')
<div class="container-fluid">
    <h1 class="mb-4">CCFP Route 2 - Sign Community Response</h1>

    <!-- Search Form -->
    <form method="GET" action="{{ route('ccfp.route2') }}" class="form-inline mb-4">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search by student name" value="{{ request('search') }}">
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <!-- Students Table -->
    <div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Email</th>
                <th>Service Form</th>
                <th>Accomplishment Report</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->student->name }}</td>
                    <td>{{ $appointment->student->email }}</td> <!-- New Email Column -->
                    <td>
                        @if($appointment->community_extension_service_form_path)
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#serviceFormModal-{{ $appointment->id }}">
                                View Service Form
                            </button>
                        @else
                            <span class="text-muted">No Service Form</span>
                        @endif
                    </td>
                    <td>
                        @if($appointment->community_accomplishment_report_path)
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#accomplishmentReportModal-{{ $appointment->id }}">
                                View Accomplishment Report
                            </button>
                        @else
                            <span class="text-muted">No Accomplishment Report</span>
                        @endif
                    </td>
                    <td>
                        @if($appointment->final_ccfp_signature)
                            <span class="badge badge-success">Signed</span>
                        @else
                            <span class="badge badge-secondary">Not Signed</span>
                        @endif
                    </td>
                    <td>
                        @if(!$appointment->final_ccfp_signature)
                            <form action="{{ route('ccfp.route2.sign', $appointment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Sign</button>
                            </form>
                        @endif
                    </td>
                </tr>

                <!-- Service Form Modal -->
                <div class="modal fade" id="serviceFormModal-{{ $appointment->id }}" tabindex="-1" aria-labelledby="serviceFormModalLabel-{{ $appointment->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="serviceFormModalLabel-{{ $appointment->id }}">Community Extension Service Form</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <iframe src="{{ Storage::url($appointment->community_extension_service_form_path) }}" width="100%" height="500px"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accomplishment Report Modal -->
                <div class="modal fade" id="accomplishmentReportModal-{{ $appointment->id }}" tabindex="-1" aria-labelledby="accomplishmentReportModalLabel-{{ $appointment->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="accomplishmentReportModalLabel-{{ $appointment->id }}">Community Accomplishment Report</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <iframe src="{{ Storage::url($appointment->community_accomplishment_report_path) }}" width="100%" height="500px"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
        @isset($students)
            {{ $students->links() }}
        @endisset
    </div>

</div>
@endsection
