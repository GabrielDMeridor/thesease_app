<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdviserAppointment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\StudentScheduledNotification;
use App\Notifications\PanelistAssignedNotification;
class SACalendarController extends Controller
{
    public function index()
    {
        // Ensure user is a SuperAdmin before accessing this page
        if (!auth()->check() || auth()->user()->account_type !== User::SuperAdmin) {
            return redirect()->route('getSALogin')->with('error', 'You must be logged in as a superadmin to access this page.');
        }

        // Fetch students with submission_files_approval = 'approved'
        $students = User::whereHas('adviserAppointment', function ($query) {
            $query->where('submission_files_approval', 'approved');
        })->select('id', 'name')->get();

        // Fetch advisers who can be panel members (account_type = 5 and verification_status = 'verified')
        $advisers = User::where('account_type', 5)
                        ->where('verification_status', 'verified') // Only verified advisers
                        ->get();

        // Define title and retrieve SuperAdmin user data
        $data = [
            'title' => 'Set Schedules',
            'user' => auth()->user(),
            'students' => $students,
            'advisers' => $advisers,
        ];

        return view('superadmin.calendar.SAcalendar', $data);
    }


    public function storeSchedule(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'schedule_type' => 'required|in:Proposal Defense,Final Defense',
            'proposal_defense_date' => 'required|date',
            'proposal_defense_time' => 'required',
            'panel_members' => 'required', // Ensure panel_members is provided
        ]);
    
        // Decode panel_members JSON string to an array
        $panelMembers = json_decode($request->panel_members, true);
    
        if ($request->schedule_type === 'Proposal Defense' || $request->schedule_type === 'Final Defense') {
            // Save schedule type and other data only if schedule type is "Proposal Defense"
            $appointment = AdviserAppointment::updateOrCreate(
                ['student_id' => $request->student_id],
                [
                    'proposal_defense_date' => $request->proposal_defense_date,
                    'proposal_defense_time' => $request->proposal_defense_time,
                    'schedule_type' => $request->schedule_type, // Save the schedule type
                    'panel_members' => $panelMembers, // Save decoded array
                ]
            );
    
            // Only notify the student once
            $student = User::find($request->student_id);
            $student->notify(new StudentScheduledNotification(
                $request->schedule_type,
                $request->proposal_defense_date,
                $request->proposal_defense_time
            ));
    
            // Notify each panel member once
            foreach ($panelMembers as $panelMemberId) {
                $panelMember = User::find($panelMemberId);
                if ($panelMember) {
                    $panelMember->notify(new PanelistAssignedNotification(
                        $student->name,
                        $request->schedule_type,
                        $request->proposal_defense_date,
                        $request->proposal_defense_time
                    ));
                }
            }
        }
    
        return redirect()->route('superadmin.calendar')->with('success', 'Schedule successfully created');
    }
    
    
    
    public function getEvents()
    {
        // Fetch scheduled appointments with student names
        $appointments = AdviserAppointment::with('student')
            ->whereNotNull('proposal_defense_date')
            ->get();
    
        // Format each appointment as a calendar event
        $events = $appointments->map(function ($appointment) {
            $startDateTime = Carbon::parse($appointment->proposal_defense_date . ' ' . $appointment->proposal_defense_time, 'UTC');
            $endDateTime = $startDateTime->copy()->addHour();
    
            return [
                'title' => $appointment->schedule_type, // Only the type here
                'start' => $startDateTime->toIso8601String(),
                'end' => $endDateTime->toIso8601String(),
                'allDay' => false,
                'description' =>$appointment->student->name . '<br>Date: ' . $startDateTime->format('m/d/y') . '<br>Time: ' . $startDateTime->format('g:i A'),
            ];
        });
    
        return response()->json($events);
    }
    
    
    
    
    
    
    
    
    
    
}
