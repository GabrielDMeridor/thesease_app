<?php

namespace App\Http\Controllers\LanguageEditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use Illuminate\Support\Facades\Auth;

class LERoute2Controller extends Controller
{
    public function show(Request $request)
    {
        // Ensure the user is logged in as a Language Editor
        if (!auth()->check() || auth()->user()->account_type !== 10) { // Assuming 5 is account_type for Language Editor
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a Language Editor to access this page.');
        }

        // Fetch students with completed submissions (final_submission_files = 1)
        $appointments = AdviserAppointment::where('final_submission_files', 1)
                                          ->whereNull('final_submission_approval') // Only fetch pending submissions
                                          ->with('student') // Eager load student relationship
                                          ->paginate(10);

        return view('languageeditor.route2.LEroute2', compact('appointments'));
    }

    public function approve($id)
    {
        // Ensure the user is a Language Editor
        if (!auth()->check() || auth()->user()->account_type !== 10) {
            return redirect()->route('getLogin')->with('error', 'Unauthorized access.');
        }

        // Find the appointment and approve it
        $appointment = AdviserAppointment::findOrFail($id);
        $appointment->final_submission_approval = 'approved';
        $appointment->save();

        return redirect()->route('le.show')->with('success', 'Submission approved successfully.');
    }

    public function deny($id)
    {
        // Ensure the user is a Language Editor
        if (!auth()->check() || auth()->user()->account_type !== 10) {
            return redirect()->route('getLogin')->with('error', 'Unauthorized access.');
        }

        // Find the appointment and deny it
        $appointment = AdviserAppointment::findOrFail($id);
        $appointment->final_submission_approval = 'denied';
        $appointment->save();

        return redirect()->route('le.show')->with('error', 'Submission denied.');
    }
}
