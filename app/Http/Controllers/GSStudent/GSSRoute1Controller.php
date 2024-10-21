<?php

namespace App\Http\Controllers\GSStudent;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdviserAppointment;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Notifications\AdviserRequestNotification;


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
}
