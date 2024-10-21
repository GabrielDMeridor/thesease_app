<?php

namespace App\Http\Controllers\ProgramChair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;

class PCRoute1Controller extends Controller
{
    public function show()
    {
        $programChair = auth()->user();
    
        // Ensure the user is logged in as a Program Chair
        if (!auth()->check() || auth()->user()->account_type !== 4) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a Program Chair to access this page.');
        }
    
        // Fetch students who are in the Program Chair's program and have verification_status = 'approved'
        $students = User::where('program', $programChair->program)
                        ->where('account_type', 11) // Assuming 11 is the account_type for students
                        ->where('verification_status', 'verified') // Only approved students
                        ->where(function ($query) {
                            $query->whereDoesntHave('adviserAppointment') // No adviser yet
                                  ->orWhereHas('adviserAppointment', function ($subQuery) {
                                      $subQuery->where('status', 'disapproved'); // Adviser disapproved
                                  });
                        })
                        ->get();
    
        // Fetch students with approved advisers
        $approvedStudents = User::where('program', $programChair->program)
                                ->where('account_type', 11) // Students
                                ->where('verification_status', 'verified') // Only approved students
                                ->whereHas('adviserAppointment', function ($query) {
                                    $query->where('status', 'approved'); // Adviser approved
                                })
                                ->get();
    
        // Fetch all available advisers in the same program
        $advisers = User::where('account_type', 5) // Assuming 5 is the account_type for Thesis/Dissertation Professors (advisers)
                        ->get();
    
        // Define the title for the page
        $title = 'Request Adviser for Students';
    
        return view('programchair.route1.PCroute1', compact('students', 'approvedStudents', 'advisers', 'programChair', 'title'));
    }
    
    
    

    public function assignAdviserToStudent(Request $request)
    {
        $programChair = auth()->user();
    
        // Validate the incoming request
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'adviser_id' => 'required|exists:users,id',
        ]);
    
        // Fetch the student and adviser
        $student = User::findOrFail($request->student_id);
        $adviser = User::findOrFail($request->adviser_id);
    
        // Determine the appointment type based on the student's program
        $appointmentType = $this->getAppointmentType($student->program);
    
        // Fetch the existing appointment, if any
        $existingAppointment = AdviserAppointment::where('student_id', $student->id)
            ->where('adviser_id', $request->adviser_id)
            ->first();
    
        // Check if disapproval_count is null and treat it as 0
        $disapprovalCount = $existingAppointment ? ($existingAppointment->disapproval_count ?? 0) : 0;
    
        // Ensure the student hasn't been disapproved more than 2 times
        if ($disapprovalCount >= 2) {
            return redirect()->route('programchair.assignAdviser')->with('error', 'You cannot assign this adviser after 2 disapprovals.');
        }
    
        // Create or update the Adviser Appointment
        AdviserAppointment::updateOrCreate(
            ['student_id' => $student->id],
            [
                'adviser_id' => $adviser->id,
                'appointment_type' => $appointmentType,
                'status' => 'pending', // Set status to pending
                'disapproval_count' => $disapprovalCount, // Keep the current disapproval count
            ]
        );
    
        // Send notification to the adviser
        $adviser->notify(new \App\Notifications\AdviserRequestNotification($programChair, $student, $appointmentType));
    
        return redirect()->route('programchair.assignAdviser')->with('success', 'Adviser has been successfully assigned to the student. Please wait for the adviser decision');
    }
    

    // Method to determine the adviser type based on the student's program
    private function getAppointmentType($program)
    {
        
        // Define rules for program types
        $mnManPrograms = ['MN', 'MAN']; // Clinical Case Study
        $mitMphPrograms = ['MIT', 'MPH']; // Capstone
        $doctoratePrograms = ['PhD-CI-ELT', 'PHD-ED-EM', 'PHD-MGMT', 'DBA', 'DIT', 'DRPH-HPE']; // Dissertation Study

        if (in_array($program, $mnManPrograms)) {
            return 'Clinical Case Study Adviser';
        } elseif (in_array($program, $mitMphPrograms)) {
            return 'Capstone Adviser';
        } elseif (in_array( $program, $doctoratePrograms)) {
            return 'Dissertation Study Adviser';
        }

        // Default to Thesis Study if no specific match
        return 'Thesis Study Adviser';
    }

    public function affixSignature(Request $request)
    {
        // Validate the request to ensure an approved student is selected
        $request->validate([
            'approved_student_id' => 'required|exists:users,id',
        ]);
    
        // Fetch the student's appointment
        $appointment = AdviserAppointment::where('student_id', $request->approved_student_id)
                                         ->where('status', 'approved')
                                         ->first();
    
        if ($appointment) {
            // Affix the Program Chair's signature if not already signed
            if (is_null($appointment->chair_signature)) {
                $appointment->chair_signature = auth()->user()->name;
                $appointment->save();
            }
    
            // Check if all signatures are affixed (Adviser, Program Chair, and Dean)
            if ($appointment->adviser_signature && $appointment->chair_signature && $appointment->dean_signature) {
                // Set the completed_at date if all signatures are present
                $appointment->completed_at = now();
                $appointment->save();
            }
    
            return redirect()->route('programchair.assignAdviser')->with('success', 'Program Chair signature affixed successfully.');
        } else {
            return redirect()->route('programchair.assignAdviser')->with('error', 'No approved appointment found for the selected student.');
        }
    }
    
public function getApprovedStudentDetails(Request $request)
{
    // Validate the selected student
    $request->validate([
        'approved_student_id' => 'required|exists:users,id',
    ]);

    // Fetch the student's appointment
    $appointment = AdviserAppointment::where('student_id', $request->approved_student_id)
                                     ->where('status', 'approved')
                                     ->first();

    if ($appointment) {
        // Return the appointment details in a response
        return response()->json([
            'adviser_signature' => $appointment->adviser_signature ?? 'Pending',
            'chair_signature' => $appointment->chair_signature ?? 'Pending',
            'dean_signature' => $appointment->dean_signature ?? 'Pending',
        ]);
    }

    return response()->json(['error' => 'No approved appointment found for the selected student.'], 404);
}


}
