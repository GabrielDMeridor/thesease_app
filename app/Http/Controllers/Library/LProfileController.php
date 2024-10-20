<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LProfileController extends Controller
{
    public function Ldashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 9) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a superadmin to access this page');
        }
        
        $data = [
            'title' => 'Dashboard'
        ];
        return view('library.Ldashboard', $data);
    }
}