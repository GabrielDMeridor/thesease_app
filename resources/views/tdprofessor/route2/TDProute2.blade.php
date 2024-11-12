@extends('tdprofessor.TDPmain-layout')

@section('body')
<div class="container-fluid">
    <h2>Approved Advisees</h2>
    <div class="card">
        <div class="card-body">
            @if($advisees->isEmpty())
                <p>No advisees available.</p>
            @else
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Student Name</th>
                            <th class="text-center">Adviser Type</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($advisees as $advisee)
                        <tr>
                            <td class="text-center">{{ $advisee->student->name }}</td>
                            <td class="text-center">{{ $advisee->appointment_type }}</td>
                            <td class="text-center">{{ ucfirst($advisee->status) }}</td>
                            <td class="text-center">
                                <a href="{{ route('tdprofessor.showRoutingForm2', $advisee->student->id) }}" class="btn btn-primary">View Routing Form</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
