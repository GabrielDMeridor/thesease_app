<?php

namespace App\Http\Controllers\TDProfessor;

use App\Http\Controllers\Controller;
use App\Models\AdviserAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\AdviserResponseNotification; // Add this import to use your notification
use App\Notifications\AdviserResponseNotificationToPCandD;
use App\Notifications\AdviseeNotifStep2;
use App\Notifications\SAAGSNotifyStep2;
use App\Models\Setting;
use App\Notifications\ProposalManuscriptUpdateNotification;
use Illuminate\Support\Facades\Notification;


class TDPRoute1Controller extends Controller
{
    public function show()
    {
        // Ensure the user is logged in as a professor
        if (!auth()->check() || auth()->user()->account_type !== 5) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a thesis/dissertation professor to access this page');
        }
    
        // Get pending requests (where the status is pending)
        $requests = AdviserAppointment::where('adviser_id', auth()->user()->id)
                    ->where('status', 'pending')
                    ->get();
    
        // Get approved advisees (where the status is approved)
        $advisees = AdviserAppointment::where('adviser_id', auth()->user()->id)
                    ->where('status', 'approved')
                    ->get();
    
        // Pass the data to the view
        return view('tdprofessor.route1.TDProute1', [
            'requests' => $requests,
            'advisees' => $advisees,
            'title' => 'Professor Dashboard',
            'user' => auth()->user(),
            
        ]);
    }


    public function updateRequestStatus(Request $request, $id)
    {
        // Find the appointment request by its ID
        $appointment = AdviserAppointment::findOrFail($id);

        // Ensure the logged-in professor is the one assigned as the adviser
        if ($appointment->adviser_id != auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Determine action (approve or disapprove)
        if ($request->input('action') === 'approve') {
            $appointment->status = 'approved';
            $message = 'Appointment request approved.';

            // Notify the student about the approval
            $student = User::find($appointment->student_id);
            $student->notify(new AdviserResponseNotification('approved', auth()->user(), $appointment->appointment_type));

            // Notify the program chair who requested the request about the approval
            $programChair = User::find($appointment->program_chair_id);
            $programChair->notify(new AdviserResponseNotificationToPCandD('approved', auth()->user(), $appointment->appointment_type, $student));

            $superadmin = User::where('account_type', 1)->get();

            foreach ($superadmin as $admin) {
                $admin->notify(new AdviserResponseNotificationToPCandD('approved', auth()->user(), $appointment->appointment_type, $student));
            }


        } elseif ($request->input('action') === 'disapprove') {
            // Increment the disapproval count
            $appointment->disapproval_count += 1;
            $appointment->status = 'disapproved';
            $message = 'Appointment request disapproved.';

            // Notify the student about the disapproval
            $student = User::find($appointment->student_id);
            $student->notify(new AdviserResponseNotification('disapproved', auth()->user(), $appointment->appointment_type));

                        // Notify the program chair who requested the request about the disapproval
            $programChair = User::find($appointment->program_chair_id);
            $programChair->notify(new AdviserResponseNotificationToPCandD('disapproved', auth()->user(), $appointment->appointment_type,$student));
            
            $superadmin = User::where('account_type', 1)->get();
            
            foreach ($superadmin as $admin) {
                $admin->notify(new AdviserResponseNotificationToPCandD('disapproved', auth()->user(), $appointment->appointment_type,$student));
                        }
            
        }

        // Save the changes to the database
        $appointment->save();

        return redirect()->back()->with('success', $message);
    }

    public function affixSignature(Request $request, $appointmentId)
{
    // Find the appointment
    $appointment = AdviserAppointment::findOrFail($appointmentId);

    // Ensure the logged-in user is the assigned adviser
    if ($appointment->adviser_id !== auth()->user()->id) {
        return redirect()->back()->with('error', 'You are not authorized to sign this form.');
    }

    // Affix the adviser's signature
    $appointment->adviser_signature = auth()->user()->name;
    $appointment->save();

    // Redirect back with success message
    return redirect()->back()->with('success', 'You have successfully signed the form.');
}
public function showAdviseeForm($studentId)
{
    $student = User::findOrFail($studentId); // Fetch the student by ID

    $appointment = AdviserAppointment::where('student_id', $studentId)
                                     ->where('adviser_id', auth()->user()->id)
                                     ->firstOrFail();

    $isDrPH = $student->program === 'DrPH';

    // Set the title with the student's name
    $title = 'Routing Form 1 for ' . $student->name;

    $globalSubmissionLink = Setting::where('key', 'submission_files_link')->value('value');
    $ovpriLink = Setting::where('key', 'ovpri_link')->value('value');
    $statisticianLink = Setting::where('key', 'statistician_link')->value('value');


    return view('tdprofessor.route1.TDPAdviseeRoute1', [
        'appointment' => $appointment,
        'student' => $student,  // Pass the student explicitly
        'advisee' => $student,
        'title' => $title,
        'isDrPH' => $isDrPH,
        'globalSubmissionLink' => $globalSubmissionLink,
        'ovpriLink' => $ovpriLink,
        'statisticianLink' => $statisticianLink

    ]);
}

public function signRoutingForm(Request $request, $id)
{
    // Find the appointment
    $appointment = AdviserAppointment::findOrFail($id);

    // Ensure the logged-in professor is the one assigned as the adviser
    if ($appointment->adviser_id != auth()->id()) {
        return redirect()->back()->with('error', 'Unauthorized action.');
    }

    // Save the professor's signature
    $appointment->adviser_signature = auth()->user()->name;
    $appointment->save();

    return redirect()->back()->with('success', 'Your signature has been affixed to the form.');
}

public function addConsultationDatesAndSign(Request $request, $appointmentId)
{
    // Find the appointment
    $appointment = AdviserAppointment::findOrFail($appointmentId);

    // Ensure the logged-in user is the assigned adviser
    if ($appointment->adviser_id !== auth()->user()->id) {
        return redirect()->back()->with('error', 'You are not authorized to sign this form.');
    }

    // Save consultation dates (convert to JSON format)
    if ($request->has('consultation_dates')) {
        $appointment->consultation_dates = json_encode($request->input('consultation_dates')); // Saving the dates as JSON
    }

    
    // Affix the endorsement signature
    if (is_null($appointment->adviser_endorsement_signature)) {
        $appointment->adviser_endorsement_signature = auth()->user()->name;
    }

    // Save the changes
    $appointment->save();

    $advisee = User::findOrFail($appointment->student_id);
    $advisee->notify(new AdviseeNotifStep2(
        auth()->user(), // The adviser (current logged-in user)
    ));

    $roles = [1, 2, 3]; // Superadmin (1), Admin (2), and Graduate School (3)
    $usersToNotify = User::whereIn('account_type', $roles)->get();

    foreach ($usersToNotify as $user) {
        $user->notify(new SAAGSNotifyStep2(auth()->user(), $advisee)); // Notify users in the roles
    }


    // Redirect back with success message
    return redirect()->back()->with('success', 'Consultation dates saved and endorsement signature affixed.');
}

public function saveConsultationDate(Request $request)
{
    // Validate incoming data
    $request->validate([
        'consultation_date' => 'required|date',
        'appointment_id' => 'required|exists:adviser_appointments,id'
    ]);

    // Find the appointment
    $appointment = AdviserAppointment::findOrFail($request->appointment_id);

    // Ensure the logged-in user is the assigned adviser
    if ($appointment->adviser_id !== auth()->user()->id) {
        return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
    }

    // Append the new date to the existing consultation dates
    $existingDates = $appointment->consultation_dates ? json_decode($appointment->consultation_dates) : [];
    $existingDates[] = $request->consultation_date;

    // Save the updated dates back to the database
    $appointment->consultation_dates = json_encode($existingDates);
    $appointment->save();

    return response()->json(['success' => true]);
}


public function markRegistrationResponded($appointmentId)
{
    // Find the appointment
    $appointment = AdviserAppointment::findOrFail($appointmentId);

    // Ensure the logged-in user is the assigned adviser
    if ($appointment->adviser_id !== auth()->user()->id) {
        return redirect()->back()->with('error', 'Unauthorized action.');
    }

    // Update the registration_response column
    $appointment->registration_response = 'responded';
    $appointment->ovpri_approval = 'pending';
    $appointment->save();

    // Notify OVPRI users in the database
    $ovpriUsers = User::where('account_type', 8)->get();
    foreach ($ovpriUsers as $ovpriUser) {
        $ovpriUser->notify(new \App\Notifications\OVPRINotification($appointment));
    }

    // Redirect back with success message
    return redirect()->back()->with('success', 'Registration marked as responded, and OVPRI has been notified.');
}

public function approveProposalManuscriptUpdate($appointmentId)
{
    $appointment = AdviserAppointment::findOrFail($appointmentId);

    // Ensure the logged-in professor is the adviser
    if ($appointment->adviser_id !== auth()->id()) {
        return redirect()->back()->with('error', 'Unauthorized action.');
    }

    // Approve the manuscript update
    $appointment->proposal_manuscript_update_status = 'approved';
    $appointment->save();

    return redirect()->back()->with('success', 'Proposal manuscript update approved successfully.');
}
public function denyProposalManuscriptUpdate($appointmentId)
{
    $appointment = AdviserAppointment::findOrFail($appointmentId);

    // Ensure the logged-in professor is the assigned adviser
    if ($appointment->adviser_id != auth()->id()) {
        return redirect()->back()->with('error', 'Unauthorized action.');
    }

    // Reset the manuscript update status to require re-upload
    $appointment->proposal_manuscript_update_status = 'denied';
    $appointment->save();

    // Notify the student to re-upload the manuscript update
    $student = User::find($appointment->student_id);
    if ($student) {
        Notification::send($student, new ProposalManuscriptUpdateNotification($student, 'deny'));
    }

    return redirect()->back()->with('success', 'Proposal manuscript update denied. The student has been notified to re-upload.');
}










    

}
