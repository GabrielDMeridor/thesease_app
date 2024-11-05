<?php

namespace App\Http\Controllers\AUFCommittee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;
use App\Notifications\AUFCApprovalNotificationToRoles;  // Corrected namespace
use App\Notifications\AUFCApprovalNotificationToStudent; // Corrected namespace

class AUFCRoute1Controller extends Controller
{
    public function index(Request $request)
    {
        $appointments = AdviserAppointment::where('ethics_send_data_to_aufc', 1)
            ->where('aufc_status', '!=', 'approved')
            ->with('student')
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
}
