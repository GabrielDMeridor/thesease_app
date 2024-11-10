<?php

namespace App\Http\Controllers\GSStudent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class GSSProfileController extends Controller
{
    public function GSSdashboard()
    {
        // Ensure the user is authenticated and is a Graduate School Student
        if (!auth()->check() || auth()->user()->account_type !== 11) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a graduate school student to access this page');
        }
    
        // Get the authenticated user
        $user = auth()->user();
    
        // Redirect unverified or disapproved users to the partial dashboard
        if ($user->verification_status === 'unverified' || $user->verification_status === 'disapproved') {
            return redirect()->route('gssstudent.partialdashboard')->with('error', 'You cannot access the full dashboard until your account is verified');
        }
    
        // If verified, show the full dashboard
        $data = [
            'title' => 'Account Profile',
            'announcements' => Announcement::latest()->paginate(5), // Fetch recent announcements

        ];
    
        return view('gsstudent.GSSdashboard', $data);
    }
    
    public function partialDashboard()
    {
        $user = auth()->user();
        
        // Prepare file data
        $data = [
            'title' => 'Limited Access Dashboard',
            'immigrationFile' => $user->immigration_or_studentvisa,
            'manuscriptFile' => $user->manuscript,
        ];
    
        return view('gsstudent.GSSpartialdashboard', $data);
    }
}
