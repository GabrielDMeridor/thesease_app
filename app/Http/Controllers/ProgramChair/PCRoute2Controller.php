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

        // Fetch AdviserAppointments for publication approvals
        $publicationAppointments = AdviserAppointment::whereHas('student', function ($query) use ($programChair, $request) {
            $query->where('program', $programChair->program)
                  ->where('account_type', 11); // Assuming 11 is the account_type for students

            // Apply search filter if a search query is present
            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
        })
        ->whereNotNull('proof_of_publication_path')
        ->paginate(10, ['*'], 'publications');

        // Fetch AdviserAppointments for community uploads requiring signing
        $communityAppointments = AdviserAppointment::whereHas('student', function ($query) use ($programChair, $request) {
            $query->where('program', $programChair->program)
                  ->where('account_type', 11);

            // Apply search filter if a search query is present
            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
        })
        ->whereNotNull('community_extension_service_form_path')
        ->whereNotNull('community_accomplishment_report_path')
        ->paginate(10, ['*'], 'community_uploads');

        $title = 'Program Chair Review - Publications and Community Uploads';

        return view('programchair.route2.PCroute2', compact('publicationAppointments', 'communityAppointments', 'title'));
    }

    public function signProgram(Request $request, AdviserAppointment $appointment)
    {
        // Ensure the user is a Program Chair
        if (auth()->user()->account_type !== 4) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Set final_program_signature to true
        $appointment->final_program_signature = true;
        $appointment->save();

        return redirect()->back()->with('success', 'Program Chair signed successfully.');
    }

    public function approve(Request $request, $id)
    {
        // Approve publication status for student
        if (!auth()->check() || auth()->user()->account_type !== 4) {
            return redirect()->route('getLogin')->with('error', 'Unauthorized access.');
        }

        $appointment = AdviserAppointment::where('student_id', $id)
                                         ->whereNotNull('proof_of_publication_path')
                                         ->firstOrFail();

        $appointment->publication_status = 'approved';
        $appointment->save();

        return redirect()->route('programchair.route2.show')->with('success', 'Proof of publication approved successfully.');
    }

    public function deny(Request $request, $id)
    {
        // Deny publication status for student
        if (!auth()->check() || auth()->user()->account_type !== 4) {
            return redirect()->route('getLogin')->with('error', 'Unauthorized access.');
        }

        $appointment = AdviserAppointment::where('student_id', $id)
                                         ->whereNotNull('proof_of_publication_path')
                                         ->firstOrFail();

        $appointment->publication_status = 'denied';
        $appointment->save();

        return redirect()->route('programchair.route2.show')->with('error', 'Proof of publication denied.');
    }
}
