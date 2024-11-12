<?php

namespace App\Http\Controllers\ProgramChair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;

class PCRoute2Controller extends Controller
{
    public function show(Request $request)
    {
        // Ensure the user is logged in as a Program Chair
        if (!auth()->check() || auth()->user()->account_type !== 4) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a Program Chair to access this page.');
        }

        $programChair = auth()->user();

        // Fetch students from the same program as the Program Chair who have uploaded proof of publication
        $query = User::where('program', $programChair->program)
                     ->where('account_type', 11) // Assuming 11 is the account_type for students
                     ->whereHas('adviserAppointment', function ($query) {
                         $query->whereNotNull('proof_of_publication_path');
                     });

        // Apply search if the search query is present
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $students = $query->paginate(10);

        $title = 'Approve Proof of Publication Submissions';

        return view('programchair.route2.PCroute2', compact('students', 'programChair', 'title'));
    }

    public function approve(Request $request, $id)
    {
        // Ensure the user is a Program Chair
        if (!auth()->check() || auth()->user()->account_type !== 4) {
            return redirect()->route('getLogin')->with('error', 'Unauthorized access.');
        }

        // Find the appointment
        $appointment = AdviserAppointment::where('student_id', $id)
                                         ->whereNotNull('proof_of_publication_path')
                                         ->firstOrFail();

        // Approve the proof of publication
        $appointment->publication_status = 'approved';
        $appointment->save();

        return redirect()->route('programchair.route2.show')->with('success', 'Proof of publication approved successfully.');
    }

    public function deny(Request $request, $id)
    {
        // Ensure the user is a Program Chair
        if (!auth()->check() || auth()->user()->account_type !== 4) {
            return redirect()->route('getLogin')->with('error', 'Unauthorized access.');
        }

        // Find the appointment
        $appointment = AdviserAppointment::where('student_id', $id)
                                         ->whereNotNull('proof_of_publication_path')
                                         ->firstOrFail();

        // Deny the proof of publication
        $appointment->publication_status = 'denied';
        $appointment->save();

        return redirect()->route('programchair.route2.show')->with('error', 'Proof of publication denied.');
    }
}
