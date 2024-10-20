<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SAProfileController extends Controller
{
    public function SAdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 1) {
            return redirect()->route('getSALogin')->with('error', 'You must be logged in as a superadmin to access this page');
        }
        
        $data = [
            'title' => 'Dashboard'
        ];
        return view('superadmin.SAdashboard', $data);
    }

    public function SAlogout()
    {
        auth()->logout();
        return redirect()->route('getSALogin')->with('success', 'You have been successfully logged out');
    }
}
