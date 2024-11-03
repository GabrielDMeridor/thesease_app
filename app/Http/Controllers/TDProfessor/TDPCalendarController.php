<?php

namespace App\Http\Controllers\TDProfessor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdviserAppointment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TDPCalendarController extends Controller
{
    public function showTDProfessorCalendar()
    {
        $tdProfessor = Auth::user();

        // Ensure the user is logged in as a TD Professor
        if (!$tdProfessor || $tdProfessor->account_type !== User::Thesis_DissertationProfessor) {
            return redirect()->route('login')->with('error', 'You must be logged in as a TD Professor to access this page.');
        }

        // Fetch students where the TD Professor is assigned as the adviser
        $appointments = AdviserAppointment::where('adviser_id', $tdProfessor->id)->with('student')->get();

        return view('tdprofessor.calendar.TDPcalendar', [
            'title' => 'My Assigned Students and Their Schedules',
            'appointments' => $appointments,
        ]);
    }

    public function getTDEvents()
    {
        $tdProfessor = Auth::user();

        if (!$tdProfessor || $tdProfessor->account_type !== User::Thesis_DissertationProfessor) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Fetch schedules where the TD Professor is the assigned adviser
        $appointments = AdviserAppointment::with('student')
            ->where('adviser_id', $tdProfessor->id)
            ->whereNotNull('proposal_defense_date')
            ->get();

        // Format each appointment as a calendar event
        $events = $appointments->map(function ($appointment) {
            $startDateTime = Carbon::parse($appointment->proposal_defense_date . ' ' . $appointment->proposal_defense_time, 'UTC');
            $endDateTime = $startDateTime->copy()->addHour();

            return [
                'title' => $appointment->schedule_type . ' <br> ' . $appointment->student->name,
                'start' => $startDateTime->toIso8601String(),
                'end' => $endDateTime->toIso8601String(),
                'allDay' => false,
                'description' => 'Date: ' . $startDateTime->format('m/d/y') . '<br>Time: ' . $startDateTime->format('g:i A'),
            ];
        });

        return response()->json($events);
    }
}
