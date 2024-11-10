<?php

namespace App\Http\Controllers\AUFCommittee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class AUFCProfileController extends Controller
{
    public function AUFCdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 6) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a superadmin to access this page');
        }
        
        $data = [
            'title' => 'Dashboard',
            'announcements' => Announcement::latest()->paginate(5), // Fetch recent announcements

        ];
        return view('aufcommittee.AUFCdashboard', $data);
    }
}