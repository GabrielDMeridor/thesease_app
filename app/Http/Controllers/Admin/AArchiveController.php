<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AArchiveController extends Controller
{
    public function index(Request $request)
    {
        // Ensure the user is authenticated and is an admin
        if (!auth()->check() || auth()->user()->account_type !== User::Admin) {
            return redirect()->route('getSALogin')->with('error', 'You must be logged in as an admin to access this page.');
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
    
        return view('admin.archive.Aarchive', [
            'title' => 'Archive',
            'users' => $users, // Pass users and their file info to the view
        ]);
    }

    public function ajaxSearch(Request $request)
    {
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
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'created_at' => $user->created_at->format('Y-m-d'), // Format the date
                'immigration_or_studentvisa' => $user->immigration_or_studentvisa,
                'routing_form_one' => $user->routing_form_one,
                'manuscript' => $user->manuscript,
                'adviser_appointment_form' => $user->adviser_appointment_form,
            ];
        });
    
        return response()->json($users);
    }
    
}
