<?php

namespace App\Http\Controllers\GraduateSchool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;

class GSRoute1Controller extends Controller
{
    public function show(Request $request)
    {
        // Ensure the user is logged in as GraduateSchool or Dean (assuming GraduateSchool type is User::GraduateSchool)
        if (!auth()->check() || auth()->user()->account_type !== User::GraduateSchool) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as an graduate school to access this page');
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
        return view('graduateschool.route1.GSroute1', compact('students', 'title'));
    }

    public function showRoutingForm($studentId)
    {
        // Fetch the student's routing form
        $student = User::findOrFail($studentId);
        $appointment = AdviserAppointment::where('student_id', $student->id)->first();
        
        // Define the title for the view
        $title = 'Routing Form 1 for ' . $student->name;
    
        // Pass the title along with the other data to the view
        return view('graduateschool.route1.GSStudentRoute1', compact('student', 'appointment', 'title'));
    }
    
    public function sign(Request $request, $studentId)
    {
        // Fetch the appointment form
        $appointment = AdviserAppointment::where('student_id', $studentId)->first();

        // Ensure the logged-in user is the Admin or Dean
        if ($appointment && auth()->user()->account_type === User::GraduateSchool) {
            // Affix the Admin's signature (or Dean's signature)
            $appointment->dean_signature = auth()->user()->name;
            $appointment->save();

            return redirect()->route('graduateschool.showRoutingForm', $studentId)->with('success', 'You have successfully signed the form.');
        }

        return redirect()->route('graduateschool.showRoutingForm', $studentId)->with('error', 'Unable to sign the form.');
    }
    public function ajaxSearch(Request $request)
    {
        // Fetch the search term
        $searchTerm = $request->input('search');

        // Query to fetch students with an approved adviser appointment and filter by name
        $students = User::whereHas('adviserAppointment', function ($query) {
            $query->where('status', 'approved');  // Only show approved adviser appointments
        })
        ->where('account_type', 11)  // Assuming account_type 11 is for students
        ->where('name', 'LIKE', "%{$searchTerm}%")
        ->get();

        // Return the students as JSON for AJAX
        return response()->json($students);
    }
}
