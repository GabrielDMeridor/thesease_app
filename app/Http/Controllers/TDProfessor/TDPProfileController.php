<?php

namespace App\Http\Controllers\TDProfessor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class TDPProfileController extends Controller
{
    public function TDPdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 5) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a thesis/dissertation professor to access this page');
        }
        
        $data = [
            'title' => 'Dashboard',
            'announcements' => Announcement::latest()->paginate(5), // Fetch recent announcements

        ];
        return view('tdprofessor.TDPdashboard', $data);
    }
}