<?php

namespace App\Http\Controllers\TDProfessor;

use App\Http\Controllers\Controller;
use App\Models\AdviserAppointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PanelCommentNotification;
use App\Notifications\PanelRemarkNotification;
use App\Notifications\AllPanelSignaturesCompletedNotification; // Notification to be created for superadmin
use Illuminate\Http\Request;
use App\Models\User;

class TDPProposalMonitoringController extends Controller
{
    // Method to display appointments for the logged-in panelist
    public function index()
    {
        if (!auth()->check() || auth()->user()->account_type !== User::Thesis_DissertationProfessor) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a Thesis/Dissertation Professor to access this page.');
        }
        $panelistId = Auth::id();
        
        // Fetch only appointments where the authenticated panelist is in the panel_members array
        $appointments = AdviserAppointment::whereJsonContains('panel_members', (string) $panelistId)
            ->with('student')
            ->get();

        return view('tdprofessor.monitoringform.TDPmonitoringform', [
            'appointments' => $appointments,
            'title' => 'Monitoring Form', // Static title for index page
            'search' => '', // No search term initially
            'user' => auth()->user()
        ]);
    }

    // Search function to filter appointments based on student name
    public function search(Request $request)
    {
        $panelistId = Auth::id();
        $keyword = $request->input('search', '');

        // Fetch only appointments where the authenticated panelist is in the panel_members array
        // and the student's name matches the search keyword
        $appointments = AdviserAppointment::whereJsonContains('panel_members', (string) $panelistId)
            ->whereHas('student', function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->with('student')
            ->get();

        return view('tdprofessor.monitoringform.TDPmonitoringform', [
            'appointments' => $appointments,
            'title' => 'Monitoring Form',
            'search' => $keyword, // Pass the search term back to the view
        ]);
    }

    // Method to display a specific student's monitoring form for the panelist
    public function showStudentMonitoringForm($studentId)
    {
        // Ensure the logged-in user is a panel member for the student
        $appointment = AdviserAppointment::where('student_id', $studentId)
                        ->whereJsonContains('panel_members', (string) Auth::id())
                        ->with('student')
                        ->firstOrFail();

        $studentName = $appointment->student->name ?? 'Student';

        return view('tdprofessor.monitoringform.TDPstudentassignedmonitoring', [
            'appointment' => $appointment,
            'title' => "Monitoring Form for {$studentName}", // Dynamic title with student's name
        ]);
    }

    // Method to add a comment for a specific student's form
    public function addComment(Request $request, $studentId)
    {
        $panelistId = Auth::id();
        $appointment = AdviserAppointment::where('student_id', $studentId)->firstOrFail();

        $comments = $appointment->panel_comments ? json_decode($appointment->panel_comments, true) : [];
        $comments[$panelistId] = $request->comment;
        $appointment->panel_comments = json_encode($comments);
        $appointment->save();

        // Notify the student about the comment
        $student = $appointment->student;
        Notification::send($student, new PanelCommentNotification(Auth::user()->name));

        return back()->with('success', 'Comment added successfully!');
    }

    // Method to add a remark for a specific student's form
    public function addRemark(Request $request, $studentId)
    {
        $panelistId = Auth::id();
        $appointment = AdviserAppointment::where('student_id', $studentId)->firstOrFail();

        $remarks = $appointment->panel_remarks ? json_decode($appointment->panel_remarks, true) : [];
        $remarks[$panelistId] = $request->remark;
        $appointment->panel_remarks = json_encode($remarks);
        $appointment->save();

        // Notify the student about the remark
        $student = $appointment->student;
        Notification::send($student, new PanelRemarkNotification(Auth::user()->name));

        return back()->with('success', 'Remark added successfully!');
    }

    // Method to affix panelist's signature to a specific student's form
    public function affixSignature(Request $request, $studentId)
    {
        $panelistId = Auth::id();
        $appointment = AdviserAppointment::where('student_id', $studentId)->firstOrFail();
    
        // Retrieve or initialize the signatures array
        $signatures = json_decode($appointment->panel_signatures, true) ?? [];
    
        // Check if this panelist has already signed
        if (!isset($signatures[$panelistId])) {
            // Add the panelist's signature
            $signatures[$panelistId] = Auth::user()->name;
            $appointment->panel_signatures = json_encode($signatures);
            $appointment->save();
        }
    
        // Check if all panel members have signed
        $panelMembers = json_decode($appointment->panel_members, true);
    
        if (count($panelMembers) === count($signatures)) {
            // Fetch all superadmins with account_type 1
            $superAdmins = User::where('account_type', 1)->get();
    
            // Send notification to all superadmins
            Notification::send($superAdmins, new AllPanelSignaturesCompletedNotification($appointment));
    
            return back()->with('success', 'All panel members have signed. Superadmin has been notified to affix their signature.');
        }
    
        return back()->with('success', 'Signature added successfully!');
    }

    // Helper function to check if all panel signatures are completed
    protected function allPanelSignaturesCompleted($appointment)
    {
        $panelMembers = json_decode($appointment->panel_members, true);
        $signatures = json_decode($appointment->panel_signatures, true) ?? [];

        // Check if every panel member ID has a corresponding signature
        foreach ($panelMembers as $panelMemberId) {
            if (empty($signatures[$panelMemberId])) {
                return false;
            }
        }
        return true;
    }
}
