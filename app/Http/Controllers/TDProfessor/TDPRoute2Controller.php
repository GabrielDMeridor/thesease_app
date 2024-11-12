<?php

namespace App\Http\Controllers\TDProfessor;

use App\Http\Controllers\Controller;
use App\Models\AdviserAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Setting;

class TDPRoute2Controller extends Controller
{
    public function show()
    {
        // Ensure the user is logged in as a professor
        if (!auth()->check() || auth()->user()->account_type !== 5) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a thesis/dissertation professor to access this page');
        }

        // Retrieve only approved advisees for the logged-in professor
        $advisees = AdviserAppointment::where('adviser_id', auth()->user()->id)
                    ->where('status', 'approved')
                    ->get();

        // Pass the retrieved advisees to the Route 2 view
        return view('tdprofessor.route2.TDProute2', [
            'advisees' => $advisees,
            'title' => 'Professor Dashboard - Route 2',
            'user' => auth()->user(),
        ]);
    }

    public function showRoutingForm($studentId)
    {
        // Retrieve student and appointment details
        $student = User::findOrFail($studentId);

        
        $appointment = AdviserAppointment::where('student_id', $studentId)
                                         ->where('adviser_id', auth()->user()->id)
                                         ->firstOrFail();

        // Determine if the student is in the DrPH program
        $isDrPH = $student->program === 'DRPH-HPE';
        $totalSteps = $isDrPH ? 9 : 8; // 9 steps for DrPH, 8 for others
        $final_statisticianLink = Setting::where('key', 'final_statistician_link')->value('value');
        $final_ovpri_link = Setting::where('key', 'final_ovpri_link')->value('value');



        // Pass the appointment and student data to the Route 2 advisee view
        return view('tdprofessor.route2.TDPAdviseeRoute2', [
            'appointment' => $appointment,
            'student' => $student,
            'advisee' => $student,
            'title' => 'Routing Form 2 for ' . $student->name,
            'isDrPH' => $isDrPH,
            'totalSteps' => $totalSteps,
            'final_statisticianLink' => $final_statisticianLink,
            'final_ovpri_link' => $final_ovpri_link
        ]);
    }
    public function addFinalConsultationDatesAndSign(Request $request, $appointmentId)
    {
        $appointment = AdviserAppointment::findOrFail($appointmentId);

        if ($appointment->adviser_id !== auth()->user()->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($request->has('final_consultation_dates')) {
            $appointment->final_consultation_dates = json_encode($request->input('final_consultation_dates'));
        }

        if (is_null($appointment->final_adviser_endorsement_signature)) {
            $appointment->final_adviser_endorsement_signature = auth()->user()->name;
        }

        $appointment->save();

        return redirect()->back()->with('success', 'Final consultation dates saved and final endorsement signature affixed.');
    }
    public function saveConsultationDate(Request $request)
{
    $request->validate([
        'consultation_date' => 'required|date',
        'appointment_id' => 'required|exists:adviser_appointments,id'
    ]);

    $appointment = AdviserAppointment::findOrFail($request->appointment_id);

    if ($appointment->adviser_id !== auth()->user()->id) {
        return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
    }

    // Append the new date to the existing consultation dates
    $existingDates = $appointment->final_consultation_dates ? json_decode($appointment->final_consultation_dates) : [];
    $existingDates[] = $request->consultation_date;

    // Save the updated dates back to the database
    $appointment->final_consultation_dates = json_encode($existingDates);
    $appointment->save();

    return response()->json(['success' => true, 'message' => 'Date saved successfully']);
}
public function markFinalRegistrationResponded($appointmentId)
{
    $appointment = AdviserAppointment::findOrFail($appointmentId);

    if ($appointment->adviser_id !== auth()->user()->id) {
        return redirect()->back()->with('error', 'Unauthorized action.');
    }

    $appointment->final_registration_response = 'responded';
    $appointment->final_ovpri_approval = 'pending';
    $appointment->save();


    return redirect()->back()->with('success', 'Final registration marked as responded, and OVPRI has been notified.');
}

}
