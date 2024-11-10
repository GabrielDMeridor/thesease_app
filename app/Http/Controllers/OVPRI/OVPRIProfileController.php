<?php

namespace App\Http\Controllers\OVPRI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class OVPRIProfileController extends Controller
{
    public function OVPRIdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 8) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a OVPRI to access this page');
        }
        
        $data = [
            'title' => 'Dashboard',
            'announcements' => Announcement::latest()->paginate(5), // Fetch recent announcements

        ];
        return view('ovpri.OVPRIdashboard', $data);
    }
}
