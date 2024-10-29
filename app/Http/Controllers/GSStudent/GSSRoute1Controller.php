<?php

namespace App\Http\Controllers\GSStudent;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdviserAppointment;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Notifications\LibraryNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;





class GSSRoute1Controller extends Controller
{
    public function show()
    {
        // Check if the user is authenticated and their account type is Graduate School Student (account_type = 11)
        if (!auth()->check() || auth()->user()->account_type !== 11) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a graduate school student to access this page');
        }
    
        // Fetch the currently authenticated user and the list of advisers
        $user = auth()->user();
        $advisers = User::where('account_type', User::Thesis_DissertationProfessor)->get();
    
        // Fetch the existing appointment for the student if it exists
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();
    
        // Determine if all signatures are filled to allow unlocking the next step
        $allSignaturesFilled = $appointment && $appointment->adviser_signature && $appointment->chair_signature && $appointment->dean_signature;
    
        // Pass the user data, advisers, and appointment to the view, along with the signature flag
        $data = [
            'title' => 'Routing Form 1',
            'user' => $user,
            'advisers' => $advisers,
            'appointment' => $appointment,
            'allSignaturesFilled' => $allSignaturesFilled  // Pass the signature completion status to the view
        ];
    
        // Return the view with the data
        return view('gsstudent.route1.GSSroute1', $data);
    }
    
    public function sign(Request $request)
    {
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();

        // Check and update signatures based on user role
        if ($user->account_type == User::Thesis_DissertationProfessor && !$appointment->adviser_signature) {
            $appointment->adviser_signature = $user->name;
        } elseif ($user->account_type == User::ProgramChair && !$appointment->chair_signature) {
            $appointment->chair_signature = $user->name;
        } elseif ($user->account_type == User::GraduateSchool && !$appointment->dean_signature) {
            $appointment->dean_signature = $user->name;
        }

        $appointment->save();

        return redirect()->route('gsstudent.route1')->with('success', 'Form signed successfully!');
    }

    public function uploadSimilarityManuscript(Request $request)
    {
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();
    
        // Validate file input
        $request->validate([
            'similarity_manuscript' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);
    
        // Handle manuscript file upload
        $file = $request->file('similarity_manuscript');
        $originalFileName = $file->getClientOriginalName(); // Original file name
        $storedFileName = $file->storeAs('public/similarity_manuscripts', $originalFileName); // Store with original name
    
        // Store file path and original file name in the database
        $appointment->similarity_manuscript = $storedFileName;
        $appointment->original_similarity_manuscript_filename = $originalFileName;
        $appointment->save();
    
    // Notify Library (account type 9) with student's name in the message
    $libraryUsers = User::where('account_type', 9)->get();
    $message = "{$user->name} has uploaded their manuscript for similarity checking.";
    Notification::send($libraryUsers, new LibraryNotification($user, $message));
    
        return redirect()->route('gsstudent.route1')->with('success', 'Similarity Manuscript uploaded successfully and Library notified!');
    }
    

}
