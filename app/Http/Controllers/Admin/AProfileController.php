<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AProfileController extends Controller
{
    public function Adashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 2) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a superadmin to access this page');
        }
        
        $data = [
            'title' => 'Dashboard'
        ];
        return view('admin.Adashboard', $data);
    }
}