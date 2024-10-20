<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SProfileController extends Controller
{
    public function Sdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 7) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a superadmin to access this page');
        }
        
        $data = [
            'title' => 'Dashboard'
        ];
        return view('statistician.Sdashboard', $data);
    }
}
