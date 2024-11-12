@extends('ovpri.OVPRImain-layout')

@section('body')
<div class="container-fluid">
    <h2>{{ $title }}</h2>

        <!-- Manage OVPRI Link -->
        <div class="container">
        <h4>Manage OVPRI Link</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('ovpri.route2.storeOrUpdateOVPRILink') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="final_ovpri_link">OVPRI Link:</label>
                <input type="url" name="final_ovpri_link" class="form-control" value="{{ $final_ovpri_link }}" required placeholder="Enter the OVPRI link">
            </div>
            <button type="submit" class="btn btn-primary">{{ $final_ovpri_link ? 'Update' : 'Create' }} Link</button>
        </form>
    </div>

    <!-- Search Form -->
    <div class="container mt-4">
        <form method="GET" action="{{ route('ovpri.route2') }}">
            <div class="input-group mb-3">
                <input type="text" name="search" class="form-control" placeholder="Search by adviser name" value="{{ $searchQuery }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Adviser Name</th>
                    <th>Advisee Name</th>
                    <th>Program</th>
                    <th>Registration Response</th>
                    <th>OVPRI Approval</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->adviser->name }}</td>
                        <td>{{ $appointment->student->name ?? 'N/A' }}</td>
                        <td>{{ $appointment->adviser->program ?? 'N/A' }}</td>
                        <td>{{ ucfirst($appointment->final_registration_response) }}</td>
                        <td>
                            @if ($appointment->final_ovpri_approval === 'approved')
                                Approved
                            @else
                                <form action="{{ route('ovpri.route2.approve', $appointment->id) }}" method="POST">
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

    <div class="d-flex justify-content-center">
        {{ $appointments->links() }}
    </div>
</div>
@endsection
