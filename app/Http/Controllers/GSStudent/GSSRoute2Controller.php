<?php

namespace App\Http\Controllers\GSStudent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdviserAppointment;
use App\Models\User;

class GSSRoute2Controller extends Controller
{
    public function show(Request $request)
    {
        // Ensure the user is authenticated
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', operator: $user->id)->first();

        if (!auth()->check() || auth()->user()->account_type !== 11) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a graduate school student to access this page');
        }

        // Determine if the user is part of the DRPH-HPE program
        $isDrPH = $user->program === 'DRPH-HPE';


        // Define the total steps based on the program
        $totalSteps = $isDrPH ? 8 : 7;

        // Render the view and pass the necessary data to it
        return view('gsstudent.route2.GSSroute2', [
            'user' => $user,
            'title' => 'Student Routing Steps',
            'totalSteps' => $totalSteps,
            'isDrPH' => $isDrPH,
            'appointment' => $appointment, // Assume this relationship or data exists
        ]);
    }
    public function uploadManuscript(Request $request, AdviserAppointment $appointment)
{
    // Ensure the user is authorized and no final endorsement signature exists
    if ($appointment->final_adviser_endorsement_signature) {
        return redirect()->back()->with('error', 'You cannot upload files after final endorsement.');
    }

    $request->validate([
        'revised_manuscript' => 'required|file|mimes:pdf,doc,docx|max:2048', // Allow only PDF or DOC files
    ]);

    $file = $request->file('revised_manuscript');
    $path = $file->store('manuscripts', 'public'); // Store file in the "public" disk
    $originalName = $file->getClientOriginalName();

    // Update appointment with file details
    $appointment->update([
        'revised_manuscript_path' => $path,
        'revised_manuscript_original_name' => $originalName,
        'uploaded_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Revised manuscript uploaded successfully.');
}

}
