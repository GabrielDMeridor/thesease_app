<?php

namespace App\Http\Controllers\OVPRI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Notifications\OVPRIApprovalNotificationToRoles;
use App\Notifications\OVPRIApprovalNotificationToAdviser;
use App\Notifications\OVPRIApprovalNotificationToStudent;
use App\Models\User;
use App\Models\Setting;

class OVPRIRoute1Controller extends Controller
{
    public function index(Request $request)
    {
        // Ensure the user is logged in as an OVPRI user
        if (!auth()->check() || auth()->user()->account_type !== 8) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as OVPRI to access this page');
        }

        // Fetch appointments where registration_response is "responded" and order by ovpri_approval (NULL values first for pending)
        $appointments = AdviserAppointment::where('registration_response', 'responded')
            ->with('adviser')
            ->orderByRaw('ovpri_approval IS NOT NULL') // Pending (NULL) approvals at the top
            ->paginate(10);

        // Define the title variable
        $title = 'Advisers Who Responded in Registration';

        $ovpriLink = Setting::firstOrCreate(
            ['key' => 'ovpri_link'],
            ['value' => null] // Default value if not already set
        );
    

        return view('ovpri.route1.OVPRIRoute1', compact('title', 'appointments', 'ovpriLink'));
    }


    public function approve($id)
    {
        // Find the appointment by ID
        $appointment = AdviserAppointment::with(['adviser', 'student'])->findOrFail($id);
    
        // Update the ovpri_approval field to "approved"
        $appointment->ovpri_approval = 'approved';
        $appointment->save();
    
        // Get adviser and student info
        $adviser = User::find($appointment->adviser_id); // Load adviser based on adviser_id
        $student = User::find($appointment->student_id); // Load student based on student_id
    
        // Send notifications to Superadmin, Admin, and Graduate School (account types 1, 2, and 3)
        $roles = [1, 2, 3];
        $usersToNotify = User::whereIn('account_type', $roles)->get();
        foreach ($usersToNotify as $user) {
            $user->notify(new OVPRIApprovalNotificationToRoles($adviser->name, $student->name));
        }
    
        // Notify the adviser
        if ($adviser) {
            $adviser->notify(new OVPRIApprovalNotificationToAdviser($student->name));
        }
    
        // Notify the student
        if ($student) {
            $student->notify(new OVPRIApprovalNotificationToStudent());
        }
    
        // Redirect back with success message
        return redirect()->back()->with('success', 'Adviser registration has been approved and notifications sent.');
    }
    public function ajaxSearch(Request $request)
    {
        // Ensure the user is logged in as an OVPRI user
        if (!auth()->check() || auth()->user()->account_type !== 8) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Get the search query
        $query = $request->input('search');

        // Fetch appointments with pending approvals at the top
        $appointments = AdviserAppointment::where('registration_response', 'responded')
            ->whereHas('adviser', function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->with('adviser')
            ->orderByRaw('ovpri_approval IS NOT NULL') // Pending (NULL) approvals at the top
            ->get();

        // Return the data as JSON
        return response()->json(['data' => $appointments]);
    }
    public function storeOrUpdateOVPRILink(Request $request)
    {
        $request->validate([
            'ovpri_link' => 'required|url',
        ]);
    
        // Update or create the ovpri_link setting
        Setting::updateOrCreate(
            ['key' => 'ovpri_link'],
            ['value' => $request->input('ovpri_link')]
        );
    
        return redirect()->back()->with('success', 'OVPRI Link updated successfully.');
    }
    

    
}
