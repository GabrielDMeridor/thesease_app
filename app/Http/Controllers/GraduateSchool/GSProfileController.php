<?php

namespace App\Http\Controllers\GraduateSchool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GSProfileController extends Controller
{
    public function GSdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 3) {
            return redirect()->route('getSALogin')->with('error', 'You must be logged in as a superadmin to access this page');
        }
        
        $data = [
            'title' => 'Dashboard'
        ];
        return view('graduateschool.GSdashboard', $data);
    }
}