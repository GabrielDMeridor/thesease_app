@extends('tdprofessor.TDPmain-layout')

@section('content-header')
<h2>Assigned Students for Monitoring</h2>

<table class="table">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Proposal Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($appointments as $appointment)
            <tr>
                <td>{{ $appointment->student->name ?? 'Unknown' }}</td>
                <td>{{ $appointment->proposal_defense_date ? \Carbon\Carbon::parse($appointment->proposal_defense_date)->format('m/d/Y') : 'N/A' }}</td>
                <td>
                    <a href="{{ route('panel.showStudentMonitoringForm', $appointment->student_id) }}" class="btn btn-primary">View Monitoring Form</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
