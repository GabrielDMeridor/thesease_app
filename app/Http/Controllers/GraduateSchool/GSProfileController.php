<?php

namespace App\Http\Controllers\GraduateSchool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;

class GSProfileController extends Controller
{
    public function GSdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 3) {
            return redirect()->route('getSALogin')->with('error', 'You must be logged in as a superadmin to access this page');
        }
        
        $data = [
            'title' => 'Dashboard'
        ];
        return view('graduateschool.GSdashboard', $data);
    }
    public function getAnalyticsData(Request $request)
    {
        $program = $request->input('program');
        
        // Query to get students' counts per step for the given program
        $studentsByStep = AdviserAppointment::whereHas('student', function ($query) use ($program) {
            $query->where('program', $program);
        })
        ->selectRaw('
            SUM(IF(adviser_signature IS NULL AND chair_signature IS NULL AND dean_signature IS NULL, 1, 0)) as step_1,
            SUM(IF(adviser_endorsement_signature IS NULL, 1, 0)) as step_2,
            SUM(IF(similarity_certificate IS NULL, 1, 0)) as step_3,
            SUM(IF(ovpri_approval IS NULL, 1, 0)) as step_4,
            SUM(IF(proposal_defense_date IS NULL, 1, 0)) as step_5,
            SUM(IF(dean_monitoring_signature IS NULL, 1, 0)) as step_6,
            SUM(IF(statistician_approval IS NULL, 1, 0)) as step_7,
            SUM(IF(aufc_status = "pending" OR aufc_status IS NULL, 1, 0)) as step_8
        ')
        ->first()
        ->toArray();

        // Nationality analytics: Count Filipino and foreign students
        $nationalityData = [
            'filipino' => AdviserAppointment::whereHas('student', function ($query) use ($program) {
                $query->where('program', $program)->where('nationality', 'Filipino');
            })->count(),
            'foreign' => AdviserAppointment::whereHas('student', function ($query) use ($program) {
                $query->where('program', $program)->where('nationality', '!=', 'Filipino');
            })->count(),
        ];

        return response()->json([
            'stepsData' => $studentsByStep,
            'nationalityData' => $nationalityData
        ]);
    }
}