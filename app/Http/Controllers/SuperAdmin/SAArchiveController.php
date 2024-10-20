<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SAArchiveController extends Controller
{
    public function index(Request $request)
    {
        // Ensure the user is authenticated and is a superadmin
        if (!auth()->check() || auth()->user()->account_type !== User::SuperAdmin) {
            return redirect()->route('getSALogin')->with('error', 'You must be logged in as a superadmin to access this page.');
        }
    
        // Get the keyword from the request for filtering
        $keyword = $request->input('keyword');
    
        // Fetch users who have uploaded at least one file and match the search keyword
        $users = User::where(function ($query) {
            $query->whereNotNull('immigration_or_studentvisa')
                ->orWhereNotNull('routing_form_one')
                ->orWhereNotNull('manuscript')
                ->orWhereNotNull('adviser_appointment_form');
        })
        ->when($keyword, function ($query, $keyword) {
            return $query->where('name', 'like', "%{$keyword}%");
        })
        ->orderBy('created_at', 'desc') // Order users by latest created
        ->paginate(10); // Paginate 10 users per page
    
        return view('superadmin.archive.SAarchive', [
            'title' => 'Archive',
            'users' => $users, // Pass users and their file info to the view
        ]);
    }
    
    
}
