<?php

namespace App\Http\Controllers\AUFCommittee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AUFCProfileController extends Controller
{
    public function AUFCdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 6) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a superadmin to access this page');
        }
        
        $data = [
            'title' => 'Dashboard'
        ];
        return view('aufcommittee.AUFCdashboard', $data);
    }
}