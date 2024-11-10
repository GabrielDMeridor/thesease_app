<?php

namespace App\Http\Controllers\ProgramChair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class PCProfileController extends Controller
{
    public function PCdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 4) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a Program Chair to access this page');
        }
        
        $data = [
            'title' => 'Dashboard',
            'announcements' => Announcement::latest()->paginate(5), // Fetch recent announcements

        ];
        return view('programchair.PCdashboard', $data);
    }
}