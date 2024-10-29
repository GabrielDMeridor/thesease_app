<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LRoute1Controller extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->account_type !== 9) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as an library to access this page');
        }
        
        $data = [
            'title' => 'Route1 Checking'
        ];
        return view('library.route1.Lroute1', $data);
    }
}