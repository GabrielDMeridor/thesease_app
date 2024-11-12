@extends('statistician.Smain-layout')

@section('content-header')
    <h2>{{ $title }}</h2>
@endsection

@section('body')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif


        <!-- Statistician Link Management -->
        <div class="card mb-4">
        <div class="card-body">
            <h4>Manage Final Statistician Link</h4>
            <form action="{{ route('statistician.route2.updateLink') }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <input type="url" name="final_statistician_link" class="form-control" placeholder="Enter final statistician link" value="{{ $final_statisticianLink ?? '' }}" required>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">{{ $final_statisticianLink ? 'Update' : 'Upload' }} Link</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('statistician.route2.index') }}">
                <div class="input-group mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by student name" value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-search"></i> Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table to List Students Awaiting Approval -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->student->name }}</td>
                        <td>{{ $appointment->student->email }}</td>
                        <td>{{ $appointment->student->program }}</td>
                        <td>
                            @if ($appointment->final_statistician_approval === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif ($appointment->final_statistician_approval === 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if ($appointment->final_statistician_approval !== 'approved')
                                <form action="{{ route('statistician.route2.approve', $appointment->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                            @endif
                            @if ($appointment->final_statistician_approval !== 'rejected')
                                <form action="{{ route('statistician.route2.reject', $appointment->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
        {{ $appointments->appends(['search' => $search])->links() }}
    </div>
</div>
@endsection
