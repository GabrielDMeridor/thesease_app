<?php

namespace App\Http\Controllers\CCFP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class CCFPProfileController extends Controller
{
    public function Cdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 12) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a CCFP to access this page');
        }
        
        $data = [
            'title' => 'Dashboard',
            'announcements' => Announcement::latest()->paginate(5), // Fetch recent announcements

        ];
        return view('ccfp.Cdashboard', $data);
    }
}
