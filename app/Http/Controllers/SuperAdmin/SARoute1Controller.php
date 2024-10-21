<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;

class SARoute1Controller extends Controller
{
public function show(Request $request)
{
    // Ensure the user is logged in as SuperAdmin or Dean
    if (!auth()->check() || auth()->user()->account_type !== User::SuperAdmin) {
        return redirect()->route('getLogin')->with('error', 'You must be logged in as a SuperAdmin to access this page');
    }

    // Query to fetch students with an approved adviser appointment
    $query = User::whereHas('adviserAppointment', function ($query) {
        $query->where('status', 'approved');  // Only show approved adviser appointments
    })->where('account_type', 11);  // Assuming account_type 11 is for students

    // Handle search input for filtering students by name
    if ($request->has('search')) {
        $searchTerm = $request->input('search');
        $query->where('name', 'LIKE', "%{$searchTerm}%");
    }

    // Get the students list with pagination
    $students = $query->paginate(10);

    // Define the title
    $title = "Routing Form 1 Checking";

    // Pass $title and $students to the view
    return view('superadmin.route1.SAroute1', compact('students', 'title'));
}


    public function showRoutingForm($studentId)
    {
        // Fetch the student's routing form
        $student = User::findOrFail($studentId);
        $appointment = AdviserAppointment::where('student_id', $student->id)->first();
        
        // Define the title for the view
        $title = 'Routing Form 1 for ' . $student->name;
    
        // Pass the title along with the other data to the view
        return view('superadmin.route1.SAStudentRoute1', compact('student', 'appointment', 'title'));
    }
    

    public function sign(Request $request, $studentId)
    {
        // Fetch the appointment form
        $appointment = AdviserAppointment::where('student_id', $studentId)->first();
    
        // Ensure the logged-in user is the Dean (SuperAdmin)
        if ($appointment && auth()->user()->account_type === User::SuperAdmin) {
            // Affix the Dean's signature if not already signed
            if (is_null($appointment->dean_signature)) {
                $appointment->dean_signature = auth()->user()->name;
                $appointment->save();
            }
    
            // Check if all signatures are affixed (Adviser, Program Chair, and Dean)
            if ($appointment->adviser_signature && $appointment->chair_signature && $appointment->dean_signature) {
                // Set the completed_at date if all signatures are present
                $appointment->completed_at = now();
                $appointment->save();
            }
    
            return redirect()->route('superadmin.showRoutingForm', $studentId)->with('success', 'You have successfully signed the form.');
        }
    
        return redirect()->route('superadmin.showRoutingForm', $studentId)->with('error', 'Unable to sign the form.');
    }
    
}
