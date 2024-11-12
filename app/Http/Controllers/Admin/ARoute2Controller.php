<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;
use App\Models\Setting;

class ARoute2Controller extends Controller
{
    public function show(Request $request)
    {
        // Ensure the user is logged in as Admin
        if (!auth()->check() || auth()->user()->account_type !== User::Admin) {
            return redirect()->route('getSALogin')->with('error', 'You must be logged in as an admin to access this page');
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
        $title = "Routing Form 2 Checking";

        // Retrieve the final global link or create it if it doesn't exist
        $finalGlobalLink = Setting::firstOrCreate(
            ['key' => 'final_global_link'],
            ['value' => null]
        );

        // Pass $title, $students, and $finalGlobalLink to the view
        return view('admin.route2.Aroute2', compact('students', 'title', 'finalGlobalLink'));
    }

    public function showRoutingForm($studentId)
    {
        // Fetch the student's routing form data
        $student = User::findOrFail($studentId);
        $appointment = AdviserAppointment::where('student_id', $student->id)->first();
        
        $isDrPH = $student->program === 'DRPH-HPE';
        $totalSteps = $isDrPH ? 9 : 8; // 9 steps for DrPH, 8 for others

        // Define the title for the view
        $title = 'Routing Form 2 for ' . $student->name;
        $final_statisticianLink = Setting::where('key', 'final_statistician_link')->value('value');
        $final_ovpri_link = Setting::where('key', 'final_ovpri_link')->value('value');
        $final_global_link = Setting::where('key', 'final_global_link')->value('value');


        // Pass the title and other data to the view
        return view('admin.route2.AStudentRoute2', compact('student', 'appointment', 'title', 'isDrPH', 'totalSteps', 
        'final_ovpri_link','final_statisticianLink', 'final_global_link'));
    }
    public function storeOrUpdateFinalGlobalLink(Request $request)
    {
        // Validate that the input is a URL
        $request->validate([
            'final_global_link' => 'required|url',
        ]);

        // Update or create the final global link setting
        Setting::updateOrCreate(
            ['key' => 'final_global_link'],
            ['value' => $request->input('final_global_link')]
        );

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Final Global Link saved successfully.');
    }
    // AdminController.php
public function approveFormFee($id)
{
    $appointment = AdviserAppointment::findOrFail($id);
    $appointment->final_submission_approval_formfee = 'approved';
    $appointment->save();

    return redirect()->back()->with('success', 'Form fee approval marked as approved.');
}

public function denyFormFee($id)
{
    $appointment = AdviserAppointment::findOrFail($id);
    $appointment->final_submission_approval_formfee = 'denied';
    $appointment->save();

    return redirect()->back()->with('error', 'Form fee approval marked as denied.');
}

}
