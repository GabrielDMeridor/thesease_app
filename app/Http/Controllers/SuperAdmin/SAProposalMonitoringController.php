<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdviserAppointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DeanSignedMonitoringFormNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SAProposalMonitoringController extends Controller
{
    // Method to display the main monitoring form view
public function index()
{
    if (!auth()->check() || auth()->user()->account_type !== User::SuperAdmin) {
        return redirect()->route('getSALogin')->with('error', 'You must be logged in as a superadmin to access this page.');
    }

    $appointments = AdviserAppointment::with('student')
        ->whereNotNull('proposal_defense_date') // Only fetch records with a non-null proposal_defense_date
        ->get()
        ->map(function ($appointment) {
            $appointment->formatted_defense_date = Carbon::parse($appointment->proposal_defense_date)->format('m/d/Y');
            $appointment->formatted_defense_time = Carbon::parse($appointment->proposal_defense_time)->format('h:i A');
            $appointment->status = $appointment->dean_monitoring_signature ? 'Done' : 'Pending';
            return $appointment;
        });

    return view('superadmin.monitoringform.SAmonitoringform', [
        'appointments' => $appointments,
        'title' => 'Monitoring Form',
        'search' => '', // No search term initially
        'user' => auth()->user(),
    ]);
}


    // Separate search method to handle form submission for search functionality
    public function search(Request $request)
    {
        $keyword = $request->input('search', '');

        $appointments = AdviserAppointment::with('student')
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('student', function ($studentQuery) use ($keyword) {
                    $studentQuery->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->get()
            ->map(function ($appointment) {
                $appointment->formatted_defense_date = Carbon::parse($appointment->proposal_defense_date)->format('m/d/Y');
                $appointment->formatted_defense_time = Carbon::parse($appointment->proposal_defense_time)->format('h:i A');
                $appointment->status = $appointment->dean_monitoring_signature ? 'Done' : 'Pending';
                return $appointment;
            });

        return view('superadmin.monitoringform.SAmonitoringform', [
            'appointments' => $appointments,
            'title' => 'Monitoring Form',
            'search' => $keyword, // Pass the search term back to the view
        ]);
    }

    // Method to show a specific student's monitoring form based on their ID
    public function showStudentMonitoringForm($studentId)
    {
        $appointment = AdviserAppointment::where('student_id', $studentId)
                        ->with('student')
                        ->firstOrFail();

        $studentName = $appointment->student->name ?? 'Student';

        return view('superadmin.monitoringform.SAstudentassignedmonitoring', [
            'appointment' => $appointment,
            'title' => "Monitoring Form for {$studentName}", // Dynamic title
        ]);
    }

    // Method to affix Dean's signature to a student's monitoring form
    public function affixDeanSignature(Request $request, $studentId)
    {
        $appointment = AdviserAppointment::where('student_id', $studentId)->firstOrFail();

        // Check if all panel members have signed
        $panelSignatures = json_decode($appointment->panel_signatures, true) ?? [];
        if (count($panelSignatures) !== count($appointment->panel_members)) {
            return back()->with('error', 'All panel members must sign before the Dean can affix a signature.');
        }

        // Affix Dean's signature if not already signed
        if (!$appointment->dean_monitoring_signature) {
            $appointment->dean_monitoring_signature = Auth::user()->name;
            $appointment->save();

            // Notify the student that the dean has signed the monitoring form
            $student = $appointment->student;
            Notification::send($student, new DeanSignedMonitoringFormNotification($appointment));

            return back()->with('success', 'Dean\'s signature added successfully, and the student has been notified.');
        }

        return back()->with('info', 'Dean has already signed this document.');
    }
}
