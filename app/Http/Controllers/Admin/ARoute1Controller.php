<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;
use App\Notifications\CommunityExtensionApprovedNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SubmissionFilesApprovedNotification;
use App\Models\Setting;

class ARoute1Controller extends Controller
{
    public function show(Request $request)
    {
        // Ensure the user is logged in as Admin or Dean (assuming Admin type is User::Admin)
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
        $title = "Routing Form 1 Checking";

        $submissionFilesLink = Setting::firstOrCreate(
            ['key' => 'submission_files_link'],
            ['value' => null]
        );
        // Pass $title and $students to the view
        return view('admin.route1.Aroute1', compact('students', 'title', 'submissionFilesLink'));
    }

    public function showRoutingForm($studentId)
    {
        // Fetch the student's routing form
        $student = User::findOrFail($studentId);
        $appointment = AdviserAppointment::where('student_id', $student->id)->first();
        
        $isDrPH = $student->program === 'DRPH-HPE';

        // Define the title for the view
        $title = 'Routing Form 1 for ' . $student->name;

        $globalSubmissionLink = Setting::where('key', 'submission_files_link')->value('value');
        $ovpriLink = Setting::where('key', 'ovpri_link')->value('value');
        $ccfpLink = Setting::where('key', 'ccfp_link')->value('value');
    $statisticianLink = Setting::where('key', 'statistician_link')->value('value');

    
        // Pass the title along with the other data to the view
        return view('admin.route1.AStudentRoute1', compact('student', 'appointment', 'title', 'isDrPH', 'globalSubmissionLink',
        'ccfpLink', 'ovpriLink', 'statisticianLink'));
    }
    
    public function sign(Request $request, $studentId)
    {
        // Fetch the appointment form
        $appointment = AdviserAppointment::where('student_id', $studentId)->first();

        // Ensure the logged-in user is the Admin or Dean
        if ($appointment && auth()->user()->account_type === User::Admin) {
            // Affix the Admin's signature (or Dean's signature)
            $appointment->dean_signature = auth()->user()->name;
            $appointment->save();

            return redirect()->route('admin.showRoutingForm', $studentId)->with('success', 'You have successfully signed the form.');
        }

        return redirect()->route('admin.showRoutingForm', $studentId)->with('error', 'Unable to sign the form.');
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


    public function approveCommunityExtension(Request $request, $studentId)
    {
        $appointment = AdviserAppointment::where('student_id', $studentId)->first();
        $adminUser = User::find(auth()->id());  // Explicitly cast to User

        if ($appointment && $adminUser) {
            $appointment->community_extension_approval = 'approved';
            $appointment->save();

            // Notify the GraduateSchool and the student
            $graduateSchoolUsers = User::where('account_type', User::GraduateSchool)->get();
            Notification::send($graduateSchoolUsers, new CommunityExtensionApprovedNotification($adminUser));
            Notification::send($appointment->student, new CommunityExtensionApprovedNotification($adminUser));

            return redirect()->route('admin.showRoutingForm', $studentId)
                             ->with('success', 'Community Extension approved successfully.');
        }

        return redirect()->route('admin.showRoutingForm', $studentId)
                         ->with('error', 'Unable to find appointment.');
    }

    public function storeOrUpdateSubmissionLink(Request $request)
    {
        // Validate that the input is a URL
        $request->validate([
            'submission_files_link' => 'required|url',
        ]);

        // Update or create the submission link setting
        Setting::updateOrCreate(
            ['key' => 'submission_files_link'],
            ['value' => $request->input('submission_files_link')]
        );

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Application Form Fee link saved successfully.');
    }
    
    public function approveSubmissionFiles(Request $request, $studentId)
    {
        // Ensure user is authenticated and has SuperAdmin privileges
        if (!auth()->check() || auth()->user()->account_type !== User::Admin) {
            return redirect()->route('getSALogin')->with('error', 'Unauthorized access.');
        }
    
        $superAdmin = User::find(auth()->id());
    
        // Retrieve authenticated user as User instance
        $appointment = AdviserAppointment::where('student_id', $studentId)->first();
    
        if ($appointment) {
            // Set the submission_files_approval to "approved"
            $appointment->submission_files_approval = 'approved';
            $appointment->save();
    
            Notification::send($appointment->student, new SubmissionFilesApprovedNotification($superAdmin));
    
    
            // Notify the student about the approval
    
            return redirect()->route('admin.showRoutingForm', $studentId)
                             ->with('success', 'Submission files approved successfully.');
        }
    
        return redirect()->route('admin.showRoutingForm', $studentId)
                         ->with('error', 'Unable to find appointment.');
    }
    
}
