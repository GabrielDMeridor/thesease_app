<?php

namespace App\Http\Controllers\CCFP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CCFPRoute2Controller extends Controller
{
    public function index(Request $request)
    {
        // Ensure the user is a CCFP user
        if (!auth()->check() || auth()->user()->account_type !== 12) {
            return redirect()->route('getLogin')->with('error', 'Unauthorized access.');
        }

        // Fetch students who have marked their community response
        $query = User::where('account_type', 11) // Assuming 11 is the account_type for students
                     ->whereHas('adviserAppointment', function ($query) {
                         $query->where('final_community_response', true);
                     });

        // Apply search filter if a search query is present
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $appointments = AdviserAppointment::whereNotNull('community_extension_service_form_path')
        ->whereNotNull('community_accomplishment_report_path')
        ->get();

        
        $students = $query->paginate(10);
        
    // Check if data is retrieved
    if ($appointments->isEmpty()) {
        return view('ccfp.route2.Croute2', ['appointments' => []])
            ->with('message', 'No appointments found with uploaded forms.');
    }


        return view('ccfp.route2.Croute2', compact('students', 'appointments'));
    }

    public function sign(Request $request, $id)
    {
        // Ensure the user is a CCFP user
        if (!auth()->check() || auth()->user()->account_type !== 12) {
            return redirect()->route('getLogin')->with('error', 'Unauthorized access.');
        }

        // Find the appointment for the student
        $appointment = AdviserAppointment::where('student_id', $id)->firstOrFail();

        // Mark the appointment record as signed
        $appointment->final_ccfp_signature = true;
        $appointment->save();

        return redirect()->route('ccfp.route2.Croute2')->with('success', 'Student record signed successfully.');
    }
}
