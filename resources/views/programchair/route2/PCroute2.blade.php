@extends('programchair.PCmain-layout')

@section('content-header')
    <!-- Header Code Here -->
@endsection

@section('body')
<div class="container-fluid">
    <div class="sagreet">{{ $title }}</div>
    <br>
</div>

<div class="card shadow mb-4">
    <div class="card-header">
        <form method="GET" action="{{ route('programchair.route2.show') }}" class="form-inline">
            <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search by student name" value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>
    <div class="card-body">
        @if($students->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Proof of Publication</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    @if ($student->adviserAppointment->proof_of_publication_path)
                                        <a href="{{ asset('storage/' . $student->adviserAppointment->proof_of_publication_path) }}" target="_blank">
                                            {{ $student->adviserAppointment->proof_of_publication_original_name }}
                                        </a>
                                    @else
                                        No proof uploaded
                                    @endif
                                </td>
                                <td>
                                    @if ($student->adviserAppointment->publication_status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif ($student->adviserAppointment->publication_status === 'denied')
                                        <span class="badge badge-danger">Denied</span>
                                    @else
                                        <form action="{{ route('programchair.route2.approve', $student->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                        </form>
                                        <form action="{{ route('programchair.route2.deny', $student->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Deny</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                {{ $students->links() }}
            </div>
        @else
            <p class="text-center">No students have uploaded proof of publication.</p>
        @endif
    </div>
</div>
@endsection
