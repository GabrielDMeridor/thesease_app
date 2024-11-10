<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\Announcement;
use App\Notifications\AnnouncementNotification;
use App\Models\User;

class AProfileController extends Controller
{
    public function Adashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 2) {
            return redirect()->route('getSALogin')->with('error', 'You must be logged in as an admin to access this page');
        }
        
        $data = [
            'title' => 'Dashboard',
            'announcements' => Announcement::latest()->paginate(5), // Fetch recent announcements

        ];
        return view('admin.Adashboard', $data);
    }
    public function getAnalyticsData(Request $request)
    {
        $program = $request->input('program');
    
        // Analytics for Normal Students
        if ($program !== 'DRPH-HPE') {
            $studentsByStep = AdviserAppointment::whereHas('student', function ($query) use ($program) {
                $query->where('program', $program);
            })
            ->selectRaw('
                SUM(IF(adviser_signature IS NULL AND chair_signature IS NULL AND dean_signature IS NULL, 1, 0)) as step_1,
                SUM(IF(adviser_signature IS NOT NULL AND chair_signature IS NOT NULL AND dean_signature IS NOT NULL 
                        AND adviser_endorsement_signature IS NULL, 1, 0)) as step_2,
                SUM(IF(adviser_endorsement_signature IS NOT NULL 
                        AND similarity_certificate IS NULL, 1, 0)) as step_3,
                SUM(IF(similarity_certificate IS NOT NULL 
                        AND ovpri_approval IS NULL, 1, 0)) as step_4,
                SUM(IF(ovpri_approval IS NOT NULL 
                        AND proposal_defense_date IS NULL, 1, 0)) as step_5,
                SUM(IF(proposal_defense_date IS NOT NULL 
                        AND dean_monitoring_signature IS NULL, 1, 0)) as step_6,
                SUM(IF(dean_monitoring_signature IS NOT NULL 
                        AND statistician_approval IS NULL, 1, 0)) as step_7,
                SUM(IF(statistician_approval IS NOT NULL 
                        AND (aufc_status = "pending" OR aufc_status IS NULL), 1, 0)) as step_8
            ')
            ->first()
            ->toArray();
    
        // Analytics for DrPH-HPE Students
        } else {
            $studentsByStep = AdviserAppointment::whereHas('student', function ($query) use ($program) {
                $query->where('program', $program);
            })
            ->selectRaw('
                SUM(IF(adviser_signature IS NULL AND chair_signature IS NULL AND dean_signature IS NULL, 1, 0)) as step_1,
                SUM(IF(adviser_signature IS NOT NULL AND chair_signature IS NOT NULL AND dean_signature IS NOT NULL 
                        AND adviser_endorsement_signature IS NULL, 1, 0)) as step_2,
                SUM(IF(adviser_endorsement_signature IS NOT NULL 
                        AND similarity_certificate IS NULL, 1, 0)) as step_3,
                SUM(IF(similarity_certificate IS NOT NULL 
                        AND ovpri_approval IS NULL, 1, 0)) as step_4,
                SUM(IF(ovpri_approval IS NOT NULL 
                        AND community_extension_approval IS NULL, 1, 0)) as step_5,
                SUM(IF(community_extension_approval IS NOT NULL 
                        AND proposal_defense_date IS NULL, 1, 0)) as step_6,
                SUM(IF(proposal_defense_date IS NOT NULL 
                        AND dean_monitoring_signature IS NULL, 1, 0)) as step_7,
                SUM(IF(dean_monitoring_signature IS NOT NULL 
                        AND statistician_approval IS NULL, 1, 0)) as step_8,
                SUM(IF(statistician_approval IS NOT NULL 
                        AND (aufc_status = "pending" OR aufc_status IS NULL), 1, 0)) as step_9
            ')
            ->first()
            ->toArray();
        }
    
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
    

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement = Announcement::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        // Notify GraduateSchool students and thesis/dissertation professors
        $recipients = User::whereIn('account_type', [11, 5])->get();
        foreach ($recipients as $user) {
            $user->notify(new AnnouncementNotification($announcement));
        }

        return redirect()->back()->with('success', 'Announcement created and notifications sent.');
    }

}