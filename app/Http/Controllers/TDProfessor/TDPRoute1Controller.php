<?php

namespace App\Http\Controllers\TDProfessor;

use App\Http\Controllers\Controller;
use App\Models\AdviserAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\AdviserResponseNotification; // Add this import to use your notification

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

    public function approveRequest($id)
    {
        $appointment = AdviserAppointment::find($id);
    
        // Ensure the logged-in professor is the adviser
        if (auth()->user()->id !== $appointment->adviser_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Approve the request by updating the status
        $appointment->status = 'approved';
        $appointment->adviser_signature = auth()->user()->name;  // Adviser can now sign
        $appointment->save();

        // Notify the student about the approval
        $student = User::find($appointment->student_id);
        $student->notify(new AdviserResponseNotification('approved', auth()->user(), $appointment->appointment_type));

        return redirect()->back()->with('success', 'Appointment request approved and the student has been notified.');
    }

    public function disapproveRequest($id)
    {
        $appointment = AdviserAppointment::find($id);
    
        // Ensure the logged-in professor is the adviser
        if (auth()->user()->id !== $appointment->adviser_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Disapprove the request by updating the status
        $appointment->status = 'disapproved';
        $appointment->save();

        // Notify the student about the disapproval
        $student = User::find($appointment->student_id);
        $student->notify(new AdviserResponseNotification('disapproved', auth()->user(), $appointment->appointment_type));

        return redirect()->back()->with('success', 'Appointment request disapproved and the student has been notified.');
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

        } elseif ($request->input('action') === 'disapprove') {
            // Increment the disapproval count
            $appointment->disapproval_count += 1;
            $appointment->status = 'disapproved';
            $message = 'Appointment request disapproved.';

            // Notify the student about the disapproval
            $student = User::find($appointment->student_id);
            $student->notify(new AdviserResponseNotification('disapproved', auth()->user(), $appointment->appointment_type));
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
        if (is_null($appointment->adviser_signature)) {
            $appointment->adviser_signature = auth()->user()->name;
            $appointment->save();
        }
    
        // Check if all signatures are affixed (Adviser, Program Chair, and Dean)
        if ($appointment->adviser_signature && $appointment->chair_signature && $appointment->dean_signature) {
            // Set the completed_at date if all signatures are present
            $appointment->completed_at = now();
            $appointment->save();
        }
    
        return redirect()->back()->with('success', 'You have successfully signed the form.');
    }
    
public function showAdviseeForm($studentId)
{
    $appointment = AdviserAppointment::where('student_id', $studentId)
                                     ->where('adviser_id', auth()->user()->id)
                                     ->firstOrFail();

    // Set the title with the student's name
    $title = 'Routing Form 1 for ' . $appointment->student->name;

    return view('tdprofessor.route1.TDPAdviseeRoute1', [
        'appointment' => $appointment,
        'advisee' => $appointment->student,
        'title' => $title, // Pass the title to the view
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



    

}
