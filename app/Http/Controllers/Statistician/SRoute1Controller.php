<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Notifications\StatisticianApprovalNotificationToRoles;
use App\Notifications\StatisticianApprovalNotificationToStudent;
use App\Models\User;
use App\Models\Setting;

class SRoute1Controller extends Controller
{
    public function index(Request $request)
    {
        // Ensure the user is logged in as a Statistician
        if (!auth()->check() || auth()->user()->account_type !== 7) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a Statistician to access this page');
        }

        // Fetch appointments where student_statistician_response is "responded"
        $appointments = AdviserAppointment::where('student_statistician_response', 'responded')
            ->with('student')
            ->paginate(10);

        // Define the title variable
        $title = 'Students Who Responded for Statistician Consultation';

        $statisticianLink = Setting::where('key', 'statistician_link')->value('value');


        return view('statistician.route1.Sroute1', compact('title', 'appointments', 'statisticianLink'));
    }

    public function approve($id)
    {
        // Find the appointment by ID
        $appointment = AdviserAppointment::with('student')->findOrFail($id);
        
        // Update the statistician_approval field to "approved"
        $appointment->statistician_approval = 'approved';
        $appointment->save();
        
        // Get student info
        $student = User::find($appointment->student_id);
    
        // Notify the student in the database
        if ($student) {
            $student->notify(new StatisticianApprovalNotificationToStudent());
        }
    
        // Notify relevant roles (e.g., Admins)
        $rolesToNotify = User::whereIn('account_type', [1, 2, 3])->get();
        foreach ($rolesToNotify as $user) {
            $user->notify(new StatisticianApprovalNotificationToRoles($student->name));
        }
        
        // Redirect back with success message
        return redirect()->back()->with('success', 'Student consultation has been approved and notifications sent.');
    }
    

    public function ajaxSearch(Request $request)
    {
        // Ensure the user is logged in as a Statistician
        if (!auth()->check() || auth()->user()->account_type !== 7) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Get the search query
        $query = $request->input('search');

        // Fetch appointments where student_statistician_response is "responded" and match the student's name
        $appointments = AdviserAppointment::where('student_statistician_response', 'responded')
            ->whereHas('student', function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->with('student')
            ->get();

        // Return the data as JSON
        return response()->json(['data' => $appointments]);
    }

    public function storeOrUpdateStatisticianLink(Request $request)
{
    $request->validate([
        'statistician_link' => 'required|url',
    ]);

    // Update or create the statistician_link setting
    Setting::updateOrCreate(
        ['key' => 'statistician_link'],
        ['value' => $request->input('statistician_link')]
    );

    return redirect()->back()->with('success', 'Statistician Link updated successfully.');
}

}
