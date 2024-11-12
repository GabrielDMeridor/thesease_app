@extends('languageeditor.LEmain-layout')

@section('content-header')
<div class="container-fluid my-4">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h4 class="mb-4 text-center text-md-start">Review Student Submissions</h4>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">Student Name</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Manuscript</th>
                            <th class="text-center">Submission Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointments as $appointment)
                            <tr>
                                <td class="text-center">{{ $appointment->student->name }}</td>
                                <td class="text-center">{{ $appointment->student->email }}</td>
                                <td class="text-center">
                                    @if(!empty($appointment->final_similarity_manuscript))
                                        <a href="{{ Storage::url($appointment->final_similarity_manuscript) }}" target="_blank">View Manuscript</a>
                                    @else
                                        <span class="text-muted">No file uploaded</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($appointment->final_submission_approval === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif ($appointment->final_submission_approval === 'denied')
                                        <span class="badge badge-danger">Denied</span>
                                    @else
                                        <span class="badge badge-secondary">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($appointment->final_submission_approval === null)
                                        <form action="{{ route('le.approve', $appointment->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                        </form>
                                        <form action="{{ route('le.deny', $appointment->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Deny</button>
                                        </form>
                                    @else
                                        <span class="text-muted">Action Taken</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination Links -->
                <div class="d-flex justify-content-center">
                    {{ $appointments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
