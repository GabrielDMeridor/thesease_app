<?php

namespace App\Http\Controllers\GSStudent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdviserAppointment;
use App\Models\User;
use App\Notifications\StatisticianResponseNotification;
use App\Models\Setting;


class GSSRoute2Controller extends Controller
{
    public function show(Request $request)
    {
        // Ensure the user is authenticated
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();
    
        if (!auth()->check() || auth()->user()->account_type !== 11) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a graduate school student to access this page');
        }
    
        // Determine if the user is part of the DRPH-HPE program
        $isDrPH = $user->program === 'DRPH-HPE';
    
        // Define the total steps based on the program
        $totalSteps = $isDrPH ? 9 : 8; // 9 steps for DrPH, 8 for others
    
        $final_statisticianLink = Setting::where('key', 'final_statistician_link')->value('value');
        $final_ovpri_link = Setting::where('key', 'final_ovpri_link')->value('value');

        // Render the view and pass the necessary data to it
        return view('gsstudent.route2.GSSroute2', [
            'user' => $user,
            'title' => 'Student Routing Steps',
            'totalSteps' => $totalSteps,
            'isDrPH' => $isDrPH,
            'appointment' => $appointment,
            'final_statisticianLink' => $final_statisticianLink,
            'program' => $user->program,
            'final_ovpri_link' => $final_ovpri_link // Ensure this is passed to the view
            // Make sure $appointment is not null
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
public function respondToFinalStatistician(Request $request)
{
    $user = Auth::user();
    $appointment = AdviserAppointment::where('student_id', $user->id)->first();

    // Ensure the response is only recorded if not already responded
    if (is_null($appointment->final_student_statistician_response)) {
        $appointment->final_student_statistician_response = 'responded';
        $appointment->save();

        // Notify all statisticians
        $statisticians = User::where('account_type', 7)->get();
        foreach ($statisticians as $statistician) {
            $statistician->notify(new StatisticianResponseNotification($user->name));
        }
    }

    return redirect()->route('gsstudent.route2')
                     ->with('success', 'Your response to the final consultation with the statistician has been recorded.');
}

public function uploadProofOfPublication(Request $request, AdviserAppointment $appointment)
{
    $request->validate([
        'proof_of_publication' => 'required|file|mimes:pdf,doc,docx|max:2048', // PDF or DOC files only
    ]);

    $file = $request->file('proof_of_publication');
    $path = $file->store('proof_of_publication', 'public'); // Store in the "public" disk
    $originalName = $file->getClientOriginalName();

    $appointment->update([
        'proof_of_publication_path' => $path,
        'proof_of_publication_original_name' => $originalName,
    ]);

    return redirect()->back()->with('success', 'Proof of Publication uploaded successfully.');
}

public function uploadFinalSimilarityManuscript(Request $request)
{
    // Ensure the user is authenticated
    $user = Auth::user();

    // Find or create an appointment for the student
    $appointment = AdviserAppointment::firstOrCreate(
        ['student_id' => $user->id],  // Ensure student_id is set
        []  // Default values, if any
    );

    // Validate the uploaded file
    $request->validate([
        'final_similarity_manuscript' => 'required|file|mimes:pdf|max:2048',
    ]);

    // Handle the file upload
    $file = $request->file('final_similarity_manuscript');
    $path = $file->storeAs('public/final_similarity_manuscripts', $file->getClientOriginalName());

    // Update the appointment record
    $appointment->update([
        'final_similarity_manuscript' => $path,
        'final_similarity_manuscript_original_name' => $file->getClientOriginalName(),
    ]);

    return redirect()->route('gsstudent.route2')->with('success', 'Final Similarity Manuscript uploaded successfully!');
}


public function respondToCommunity(Request $request)
{
    $user = Auth::user();
    $appointment = AdviserAppointment::where('student_id', $user->id)->first();

    // Ensure response is only recorded if not already responded
    if (!$appointment->final_community_response) {
        $appointment->final_community_response = true;
        $appointment->save();
    }

    return redirect()->route('gsstudent.route2')->with('success', 'Your community response has been recorded.');
}

public function uploadCommunityExtensionForms(Request $request)
{
    $user = Auth::user();
    
    // Retrieve or create an AdviserAppointment record for this student
    $appointment = AdviserAppointment::firstOrCreate(
        ['student_id' => $user->id]
    );

    // Validate the uploaded files (each file is optional)
    $request->validate([
        'community_extension_service_form' => 'nullable|file|mimes:pdf|max:2048',
        'community_accomplishment_report' => 'nullable|file|mimes:pdf|max:2048',
    ]);

    // Handle the file uploads, saving them directly in the public directory
    if ($request->hasFile('community_extension_service_form')) {
        $serviceForm = $request->file('community_extension_service_form');
        $serviceFormPath = 'community_extension_forms/' . $serviceForm->getClientOriginalName();
        $serviceForm->move(public_path('community_extension_forms'), $serviceForm->getClientOriginalName());
        $appointment->community_extension_service_form_path = $serviceFormPath;
    }

    if ($request->hasFile('community_accomplishment_report')) {
        $accomplishmentReport = $request->file('community_accomplishment_report');
        $accomplishmentReportPath = 'community_accomplishment_reports/' . $accomplishmentReport->getClientOriginalName();
        $accomplishmentReport->move(public_path('community_accomplishment_reports'), $accomplishmentReport->getClientOriginalName());
        $appointment->community_accomplishment_report_path = $accomplishmentReportPath;
    }

    // Save the updated file paths to the database
    $appointment->save();

    return redirect()->route('gsstudent.route2')->with('success', 'Community Extension forms uploaded successfully.');
}



}