<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;
use App\Notifications\StatisticianApprovalNotificationToStudent;
use App\Models\Setting;

class SRoute2Controller extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check() || auth()->user()->account_type !== 7) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a Statistician to access this page');
        }

        $search = $request->input('search');

        // Fetch all students with responded status, filtered by search if provided
        $appointments = AdviserAppointment::where('final_student_statistician_response', 'responded')
            ->when($search, function($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->with('student')
            ->paginate(10);

        $title = 'Students Awaiting Statistician Approval';
        $final_statisticianLink = Setting::where('key', 'final_statistician_link')->value('value');


        return view('statistician.route2.Sroute2', compact('appointments', 'title', 'search', 'final_statisticianLink'));
    }

    public function approve($id)
    {
        // Find the appointment by ID
        $appointment = AdviserAppointment::findOrFail($id);
        
        // Update the approval status to "approved"
        $appointment->final_statistician_approval = 'approved';
        $appointment->save();

        // Notify the student of approval
        $student = User::find($appointment->student_id);
        if ($student) {
            $student->notify(new StatisticianApprovalNotificationToStudent());
        }

        return redirect()->back()->with('success', 'Consultation has been approved for the student.');
    }

    public function reject($id)
    {
        // Find the appointment by ID
        $appointment = AdviserAppointment::findOrFail($id);
        
        // Update the approval status to "rejected"
        $appointment->final_statistician_approval = 'rejected';
        $appointment->save();

        return redirect()->back()->with('success', 'Consultation has been rejected for the student.');
    }
    public function storeOrUpdateFinalStatisticianLink(Request $request)
    {
        $request->validate([
            'final_statistician_link' => 'required|url',
        ]);

        // Update or create the final_statistician_link setting
        Setting::updateOrCreate(
            ['key' => 'final_statistician_link'],
            ['value' => $request->input('final_statistician_link')]
        );

        return redirect()->back()->with('success', 'Final Statistician Link updated successfully.');
    }
}
