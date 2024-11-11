<?php

namespace App\Http\Controllers\AUFCommittee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;
use App\Notifications\AUFCApprovalNotificationToRoles;  // Corrected namespace
use App\Notifications\AUFCApprovalNotificationToStudent; // Corrected namespace
use App\Notifications\AUFCDenialNotification;

class AUFCRoute1Controller extends Controller
{
    public function index(Request $request)
    {
        // Fetch all records, but prioritize showing pending ones (non-approved) at the top
        $appointments = AdviserAppointment::where('ethics_send_data_to_aufc', 1)
            ->with('student')
            ->orderByRaw("CASE WHEN aufc_status = 'approved' THEN 1 ELSE 0 END")
            ->paginate(10);
    
        $title = "Students Awaiting AUFC Approval";
        return view('aufcommittee.route1.AUFCroute1', compact('title', 'appointments'));
    }
    

    public function approve($id)
    {
        $appointment = AdviserAppointment::with('student')->findOrFail($id);

        // Update AUFC approval status
        $appointment->aufc_status = 'approved';
        $appointment->save();

        $student = User::find($appointment->student_id);

        // Notify the student and relevant roles
        if ($student) {
            $student->notify(new AUFCApprovalNotificationToStudent($student->name));
        }

        $rolesToNotify = User::whereIn('account_type', [1, 2, 3])->get();
        foreach ($rolesToNotify as $user) {
            $user->notify(new AUFCApprovalNotificationToRoles($student->name));
        }

        return redirect()->back()->with('success', 'Approval completed and notifications sent.');
    }

    public function ajaxSearch(Request $request)
    {
        $query = $request->input('search');
        
        $appointments = AdviserAppointment::where('ethics_send_data_to_aufc', 1)
            ->where('aufc_status', '!=', 'approved')
            ->whereHas('student', function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->with('student')
            ->get();

        return response()->json(['data' => $appointments]);
    }

    public function uploadEthicsClearance(Request $request, $id)
    {
        // Validate file upload
        $request->validate([
            'ethics_clearance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Find the appointment
        $appointment = AdviserAppointment::findOrFail($id);

        // Handle the file upload and save file path
        $filePath = $request->file('ethics_clearance')->store('public/ethics_clearance');
        $appointment->ethics_clearance = $filePath;
        $appointment->aufc_status = 'approved';
        $appointment->save();

        return redirect()->back()->with('success', 'Ethics clearance uploaded and approval granted.');
    }

    public function denyAppointment(Request $request, $id)
    {
        // Validate denial reason
        $request->validate([
            'denialReason' => 'required|string|max:255',
        ]);

        // Find the appointment and notify the student of the denial reason
        $appointment = AdviserAppointment::findOrFail($id);
        $student = $appointment->student;
        $student->notify(new AUFCDenialNotification($request->denialReason));

        return redirect()->back()->with('success', 'Denial notification sent to student.');
    }
}
