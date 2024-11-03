<?php

namespace App\Http\Controllers\GSStudent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdviserAppointment;
use Illuminate\Http\Request;
use Carbon\Carbon;


class GSSCalendarController extends Controller
{
    public function showStudentSchedule()
    {
        // Ensure the user is logged in as a Graduate School Student
        $student = auth()->user();
        
        if (!$student || $student->account_type !== User::GraduateSchoolStudent) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a student to access this page.');
        }
    
        // Fetch the student's schedule and panel members
        $appointment = AdviserAppointment::with('student')
            ->where('student_id', $student->id)
            ->first();
    
        // Fetch panel member details
        $panelMembers = $appointment ? User::whereIn('id', $appointment->panel_members)->get() : [];
    
        // Define title for the page
        $title = 'Calendar';
    
        // Pass data to the view
        return view('gsstudent.calendar.GSSCalendar', compact('appointment', 'panelMembers', 'title'));
    }
    

public function getEvents()
{
    $student = auth()->user();

    $appointments = AdviserAppointment::where('student_id', $student->id)
        ->whereNotNull('proposal_defense_date')
        ->get();

    $events = $appointments->map(function ($appointment) {
        $startDateTime = Carbon::parse($appointment->proposal_defense_date . ' ' . $appointment->proposal_defense_time, 'UTC');
        $endDateTime = $startDateTime->copy()->addHour();

        return [
            'title' => $appointment->schedule_type,
            'start' => $startDateTime->toIso8601String(),
            'end' => $endDateTime->toIso8601String(),
            'allDay' => false,
            'description' => 'Date: ' . $startDateTime->format('m/d/y') . '<br>Time: ' . $startDateTime->format('g:i A'),
        ];
    });

    return response()->json($events);
}

}