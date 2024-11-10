<?php

namespace App\Http\Controllers\CCFP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdviserAppointment;
use App\Models\User;
use App\Models\Setting;
use App\Notifications\CommunityExtensionApprovedNotification;
use Illuminate\Support\Facades\Notification;

class CCFPRoute1Controller extends Controller
{
    public function index(Request $request)
    {
        // Ensure the user is logged in as CCFP user
        if (!auth()->check() || auth()->user()->account_type !== 12) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as CCFP to access this page');
        }

        // Fetch appointments where community_extension_response is 1 (responded) and order by pending approvals
        $appointments = AdviserAppointment::where('community_extension_response', 1)
            ->with('student')
            ->orderByRaw('community_extension_approval IS NOT NULL') // Pending (NULL) approvals at the top
            ->paginate(10);

        // Title for the view
        $title = 'Advisers with Community Extension Responded';

        $ccfpLink = Setting::firstOrCreate(
            ['key' => 'ccfp_link'],
            ['value' => null] // Default value if not already set
        );

        return view('ccfp.route1.Croute1', compact('title', 'appointments', 'ccfpLink'));
    }


    public function approve($id)
    {
        $appointment = AdviserAppointment::with(['adviser', 'student'])->findOrFail($id);

        // Update community_extension_approval to "approved"
        $appointment->community_extension_approval = 'approved';
        $appointment->save();


        return redirect()->back()->with('success', 'Community Extension has been approved and notifications sent.');
    }

    public function ajaxSearch(Request $request)
    {
        // Ensure the user is logged in as a CCFP user
        if (!auth()->check() || auth()->user()->account_type !== 9) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Get the search query
        $query = $request->input('search');

        // Fetch appointments with students who have responded to community extension
        $appointments = AdviserAppointment::where('community_extension_response', 1)
            ->whereHas('student', function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->with('student')
            ->orderByRaw('community_extension_approval IS NOT NULL') // Pending (NULL) approvals at the top
            ->get();

        // Return the results as JSON
        return response()->json(['data' => $appointments]);
    }

    public function storeOrUpdateCCFPLink(Request $request)
{
    $request->validate([
        'ccfp_link' => 'required|url',
    ]);

    // Update or create the `ccfp_link` setting in the database
    Setting::updateOrCreate(
        ['key' => 'ccfp_link'],
        ['value' => $request->input('ccfp_link')]
    );

    return redirect()->back()->with('success', 'CCFP Link updated successfully.');
}

}
