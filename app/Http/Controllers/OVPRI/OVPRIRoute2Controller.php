<?php

namespace App\Http\Controllers\OVPRI;

use App\Http\Controllers\Controller;
use App\Models\AdviserAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\OVPRIApprovalNotificationToRoles;
use App\Models\Setting;


class OVPRIRoute2Controller extends Controller
{
    public function index(Request $request)
    {
        // Search for appointments based on adviser name
        $searchQuery = $request->input('search');
        $appointments = AdviserAppointment::where('final_registration_response', 'responded')
            ->with(['adviser', 'student'])
            ->when($searchQuery, function ($query, $searchQuery) {
                $query->whereHas('adviser', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                });
            })
            ->orderByRaw('final_ovpri_approval IS NOT NULL')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $title = 'Research Registration Approval';
        $final_ovpri_link = Setting::where('key', 'final_ovpri_link')->value('value');

        return view('ovpri.route2.OVPRIRoute2', compact('title', 'appointments', 'final_ovpri_link', 'searchQuery'));
    }

    public function approve($id)
    {
        $appointment = AdviserAppointment::with(['adviser', 'student'])->findOrFail($id);
        $appointment->final_ovpri_approval = 'approved';
        $appointment->save();

        $roles = [1, 2, 3];
        $usersToNotify = User::whereIn('account_type', $roles)->get();
        foreach ($usersToNotify as $user) {
            $user->notify(new OVPRIApprovalNotificationToRoles($appointment->adviser->name, $appointment->student->name));
        }

        return redirect()->back()->with('success', 'Registration approved and notifications sent.');
    }
    public function storeOrUpdateOVPRILink(Request $request)
    {
        $request->validate([
            'final_ovpri_link' => 'required|url',
        ]);

        Setting::updateOrCreate(
            ['key' => 'final_ovpri_link'],
            ['value' => $request->input('final_ovpri_link')]
        );

        return redirect()->back()->with('success', 'OVPRI Link updated successfully.');
    }

}
