<?php

namespace App\Http\Controllers\LanguageEditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LEProfileController extends Controller
{
    public function LEdashboard()
    {
        if (!auth()->check() || auth()->user()->account_type !== 10) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a superadmin to access this page');
        }
        
        $data = [
            'title' => 'Dashboard'
        ];
        return view('languageeditor.LEdashboard', $data);
    }
}