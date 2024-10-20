<?php

namespace App\Http\Controllers\ProgramChair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PCProfileController extends Controller
{
    public function PCdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 4) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a Program Chair to access this page');
        }
        
        $data = [
            'title' => 'Dashboard'
        ];
        return view('programchair.PCdashboard', $data);
    }
}