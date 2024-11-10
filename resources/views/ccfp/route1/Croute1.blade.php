@extends('ccfp.Cmain-layout')

@section('content-header')
<div class="container-fluid">
    <h1>{{ $title }}</h1>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- CCFP Link Management -->
    <div>
        <h2>Manage CCFP Link</h2>
        <form action="{{ route('ccfp.storeOrUpdateCCFPLink') }}" method="POST">
            @csrf
            <input type="url" name="ccfp_link" class="form-control" value="{{ $ccfpLink->value }}" required placeholder="Enter CCFP link">
            <button type="submit" class="btn btn-primary">{{ $ccfpLink->value ? 'Update' : 'Save' }} Link</button>
        </form>
    </div>

    <!-- Search Form -->
    <div class="card my-4">
        <div class="card-body">
            <form id="search-form">
                <div class="input-group">
                    <input type="text" id="search" class="form-control" placeholder="Search by student name">
                    <button class="btn btn-primary" type="button" id="search-button"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- List of Community Extension Approvals -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="results-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Community Extension Response</th>
                    <th>Approval Status</th>
                </tr>
            </thead>
            <tbody id="results-body">
                @foreach ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->student->name }}</td>
                        <td>{{ $appointment->student->email }}</td>
                        <td>{{ $appointment->student->program ?? 'N/A' }}</td>
                        <td>{{ $appointment->community_extension_response ? 'Responded' : 'Pending' }}</td>
                        <td>
                            @if ($appointment->community_extension_approval === 'approved')
                                Approved
                            @else
                                <form action="{{ route('ccfp.route1.approve', $appointment->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    {{ $appointments->links() }}
</div>

<!-- JavaScript for AJAX Search -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search').on('input', function() {
            let query = $(this).val();

            $.ajax({
                url: "{{ route('ccfp.route1.ajaxSearch') }}",
                type: 'GET',
                data: { search: query },
                success: function(data) {
                    $('#results-body').empty(); // Clear current table body

                    if (data.data.length > 0) {
                        $.each(data.data, function(index, appointment) {
                            let approveButton = appointment.community_extension_approval === 'approved'
                                ? 'Approved'
                                : `<form action="/ccfp/extension/approve/${appointment.id}" method="POST">@csrf<button type="submit" class="btn btn-success btn-sm">Approve</button></form>`;

                            let row = `
                                <tr>
                                    <td>${appointment.student.name}</td>
                                    <td>${appointment.student.email}</td>
                                    <td>${appointment.student.program ?? 'N/A'}</td>
                                    <td>${appointment.community_extension_response ? 'Responded' : 'Pending'}</td>
                                    <td>${approveButton}</td>
                                </tr>`;
                            $('#results-body').append(row);
                        });
                    } else {
                        $('#results-body').append('<tr><td colspan="5" class="text-center">No results found.</td></tr>');
                    }
                },
                error: function(xhr) {
                    console.error('An error occurred:', xhr);
                }
            });
        });
    });
</script>
@endsection
