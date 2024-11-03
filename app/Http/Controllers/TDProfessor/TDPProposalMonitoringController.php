<?php

namespace App\Http\Controllers\TDProfessor;

use App\Http\Controllers\Controller;
use App\Models\AdviserAppointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TDPProposalMonitoringController extends Controller
{
    public function index()
    {
        $panelistId = Auth::id();
        
        // Fetch only appointments where the authenticated panelist is in the panel_members array
        $appointments = AdviserAppointment::whereJsonContains('panel_members', (string) $panelistId)
            ->with('student')
            ->get();

        return view('tdprofessor.monitoringform.TDPmonitoringform', [
            'appointments' => $appointments,
        ]);
    }

    public function showStudentMonitoringForm($studentId)
    {
        // Ensure the logged-in user is a panel member for the student
        $appointment = AdviserAppointment::where('student_id', $studentId)
                        ->whereJsonContains('panel_members', (string) Auth::id())
                        ->with('student')
                        ->firstOrFail();

        return view('tdprofessor.monitoringform.TDPstudentassignedmonitoring', [
            'appointment' => $appointment,
        ]);
    }

    public function addComment(Request $request, $studentId)
    {
        $panelistId = Auth::id();
        $appointment = AdviserAppointment::where('student_id', $studentId)->firstOrFail();

        $comments = $appointment->panel_comments ? json_decode($appointment->panel_comments, true) : [];
        $comments[$panelistId] = $request->comment;
        $appointment->panel_comments = json_encode($comments);
        $appointment->save();

        return back()->with('success', 'Comment added successfully!');
    }

    public function addRemark(Request $request, $studentId)
    {
        $panelistId = Auth::id();
        $appointment = AdviserAppointment::where('student_id', $studentId)->firstOrFail();

        $remarks = $appointment->panel_remarks ? json_decode($appointment->panel_remarks, true) : [];
        $remarks[$panelistId] = $request->remark;
        $appointment->panel_remarks = json_encode($remarks);
        $appointment->save();

        return back()->with('success', 'Remark added successfully!');
    }

    public function affixSignature(Request $request, $studentId)
    {
        $panelistId = Auth::id();
        $appointment = AdviserAppointment::where('student_id', $studentId)->firstOrFail();

        $signatures = $appointment->panel_signatures ? json_decode($appointment->panel_signatures, true) : [];
        $signatures[$panelistId] = Auth::user()->name;
        $appointment->panel_signatures = json_encode($signatures);
        $appointment->save();

        return back()->with('success', 'Signature added successfully!');
    }
}
