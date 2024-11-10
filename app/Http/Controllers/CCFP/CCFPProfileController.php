<?php

namespace App\Http\Controllers\CCFP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CCFPProfileController extends Controller
{
    public function Cdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 12) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a CCFP to access this page');
        }
        
        $data = [
            'title' => 'Dashboard'
        ];
        return view('ccfp.Cdashboard', $data);
    }
}
