<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdviserAppointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DeanSignedMonitoringFormNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AProposalMonitoringController extends Controller
{
    // Method to display the main monitoring form view
    public function index()
    {
        if (!auth()->check() || auth()->user()->account_type !== User::Admin) {
            return redirect()->route('getSALogin')->with('error', 'You must be logged in as an admin to access this page.');
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

        return view('admin.monitoringform.Amonitoringform', [
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

        return view('admin.monitoringform.Amonitoringform', [
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

        $signatures = json_decode($appointment->panel_signatures, true) ?? [];


        return view('admin.monitoringform.Astudentassignedmonitoring', [
            'appointment' => $appointment,
            'title' => "Monitoring Form for {$studentName}", // Dynamic title
            'signatures' => $signatures
        ]);
    }

}
