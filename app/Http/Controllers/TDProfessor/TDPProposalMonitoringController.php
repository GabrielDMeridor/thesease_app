<?php

namespace App\Http\Controllers\TDProfessor;

use App\Http\Controllers\Controller;
use App\Models\AdviserAppointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PanelCommentNotification;
use App\Notifications\PanelRemarkNotification;
use App\Notifications\AllPanelSignaturesCompletedNotification;
use Illuminate\Http\Request;
use App\Models\User;

class TDPProposalMonitoringController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->account_type !== User::Thesis_DissertationProfessor) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a Thesis/Dissertation Professor to access this page.');
        }

        $panelistId = Auth::id();
        $appointments = AdviserAppointment::whereJsonContains('panel_members', (string) $panelistId)
            ->with('student')
            ->get();

        return view('tdprofessor.monitoringform.TDPmonitoringform', [
            'appointments' => $appointments,
            'title' => 'Monitoring Form',
            'search' => '',
            'user' => auth()->user()
        ]);
    }

    public function search(Request $request)
    {
        $panelistId = Auth::id();
        $keyword = $request->input('search', '');

        $appointments = AdviserAppointment::whereJsonContains('panel_members', (string) $panelistId)
            ->whereHas('student', function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->with('student')
            ->get();

        return view('tdprofessor.monitoringform.TDPmonitoringform', [
            'appointments' => $appointments,
            'title' => 'Monitoring Form',
            'search' => $keyword,
        ]);
    }

    public function showStudentMonitoringForm($studentId)
    {
        $appointment = AdviserAppointment::where('student_id', $studentId)
                        ->whereJsonContains('panel_members', (string) Auth::id())
                        ->with('student')
                        ->firstOrFail();

        $panelMembers = is_string($appointment->panel_members) ? json_decode($appointment->panel_members, true) : $appointment->panel_members ?? [];
        $comments = is_string($appointment->panel_comments) ? json_decode($appointment->panel_comments, true) : $appointment->panel_comments ?? [];
        $replies = is_string($appointment->student_replies) ? json_decode($appointment->student_replies, true) : $appointment->student_replies ?? [];
        $remarks = is_string($appointment->panel_remarks) ? json_decode($appointment->panel_remarks, true) : $appointment->panel_remarks ?? [];
        $signatures = is_string($appointment->panel_signatures) ? json_decode($appointment->panel_signatures, true) : $appointment->panel_signatures ?? [];

        $studentName = $appointment->student->name ?? 'Student';

        return view('tdprofessor.monitoringform.TDPstudentassignedmonitoring', [
            'appointment' => $appointment,
            'panelMembers' => $panelMembers,
            'comments' => $comments,
            'replies' => $replies,
            'remarks' => $remarks,
            'signatures' => $signatures,
            'title' => "Monitoring Form for {$studentName}",
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

        $student = $appointment->student;
        Notification::send($student, new PanelCommentNotification(Auth::user()->name));

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

        $student = $appointment->student;
        Notification::send($student, new PanelRemarkNotification(Auth::user()->name));

        return back()->with('success', 'Remark added successfully!');
    }

    public function affixSignature(Request $request, $studentId)
    {
        $panelistId = Auth::id();
        $appointment = AdviserAppointment::where('student_id', $studentId)->firstOrFail();

        $signatures = is_string($appointment->panel_signatures) ? json_decode($appointment->panel_signatures, true) : $appointment->panel_signatures ?? [];
        
        if (!isset($signatures[$panelistId])) {
            $signatures[$panelistId] = Auth::user()->name;
            $appointment->panel_signatures = json_encode($signatures);
            $appointment->save();
        }

        $panelMembers = is_string($appointment->panel_members) ? json_decode($appointment->panel_members, true) : $appointment->panel_members ?? [];

        if (count($panelMembers) === count($signatures)) {
            $superAdmins = User::where('account_type', 1)->get();
            Notification::send($superAdmins, new AllPanelSignaturesCompletedNotification($appointment));

            return back()->with('success', 'All panel members have signed. Superadmin has been notified to affix their signature.');
        }

        return back()->with('success', 'Signature added successfully!');
    }

    protected function allPanelSignaturesCompleted($appointment)
    {
        $panelMembers = json_decode($appointment->panel_members, true) ?? [];
        $signatures = json_decode($appointment->panel_signatures, true) ?? [];

        foreach ($panelMembers as $panelMemberId) {
            if (empty($signatures[$panelMemberId])) {
                return false;
            }
        }
        return true;
    }
}
