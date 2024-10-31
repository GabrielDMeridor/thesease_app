<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\UserVerified;
use App\Mail\UserDisapproved;
use Illuminate\Support\Facades\Mail;
use App\Notifications\DisapprovalNotification;


class VerifyUserController extends Controller
{
    public function index(Request $request)
    {
        // Ensure the user is authenticated and is a superadmin (account_type = 1)
        if (!auth()->check() || auth()->user()->account_type !== User::SuperAdmin) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a superadmin to access this page');
        }

        // Get the filters from the request
        $filterType = $request->input('account_type');  // Account Type Filter
        $statuses = $request->input('status', []);      // Verification Status Filter
        $keyword = $request->input('keyword');          // Keyword Search

        // Fetch users based on the filters with pagination
        $users = User::when($filterType, function ($query, $filterType) {
            return $query->where('account_type', $filterType);
        })
        ->when($statuses, function ($query, $statuses) {
            return $query->whereIn('verification_status', $statuses);
        })
        ->when($keyword, function ($query, $keyword) {
            return $query->where('name', 'like', "%{$keyword}%");
        })
        ->orderByRaw("FIELD(verification_status, 'unverified', 'disapproved', 'verified')")
        ->paginate(10);  // Paginate 10 users per page

        return view('superadmin.verify-users.verify', [
            'title' => 'Verify Users',
            'users' => $users,
        ]);
    }

    public function verifyUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'verification_status' => 'required|in:verified,unverified,disapproved',
        ]);
    
        $verificationStatus = $request->verification_status;
        $userIds = $request->user_ids;
    
        // Update the verification status for the selected users
        User::whereIn('id', $userIds)->update(['verification_status' => $verificationStatus]);
    
        // Send emails based on the new verification status
        $users = User::whereIn('id', $userIds)->get();
    
        foreach ($users as $user) {
            if ($verificationStatus === 'verified') {
                Mail::to($user->email)->send(new UserVerified($user));
            } elseif ($verificationStatus === 'disapproved') {
                Mail::to($user->email)->send(new UserDisapproved($user, $request->disapprove_reason));
            }
        }
    
        return redirect()->route('verify-users.index')->with('success', 'Users updated and emails sent successfully.');
    }

    public function updateVerificationStatus(Request $request, User $user)
    {
        $request->validate([
            'verification_status' => 'required|in:verified,unverified,disapproved', // Now includes 'disapproved'
        ]);

        $user->verification_status = $request->verification_status;
        $user->save();

            // Send email based on the new verification status
    if ($user->verification_status === 'verified') {
        Mail::to($user->email)->send(new UserVerified($user));
    } elseif ($user->verification_status === 'disapproved') {
        Mail::to($user->email)->send(new UserDisapproved($user, $request->disapprove_reason));
    }


        return redirect()->route('verify-users.index')->with('success', 'User status updated successfully.');
    }

    public function destroy($id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Delete the user from the database
        $user->delete();

        // Redirect back to the users page with a success message
        return redirect()->route('verify-users.index')->with('success', 'User deleted successfully.');
    }


    public function disapprove(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'disapprove_reason' => 'required|string',
        ]);
    
        // Find the user by ID
        $user = User::findOrFail($request->user_id);
    
        // Update the user's verification status to 'disapproved'
        $user->verification_status = 'disapproved';
        $user->save();
    
        // Send a database notification to the user with the disapproval reason
        $user->notify(new DisapprovalNotification($request->disapprove_reason));
    
        // Send disapproval email to the user, passing both the user and the reason
        Mail::to($user->email)->send(new UserDisapproved($user, $request->disapprove_reason));
    
        // Redirect with success message
        return redirect()->route('verify-users.index')->with('success', 'User disapproved and notified successfully.');
    }
    

public function search(Request $request)
{
    $keyword = $request->input('keyword');

    // Filter users by keyword and apply sorting by verification_status
    $users = User::where('name', 'like', "{$keyword}%")
        ->orderByRaw("FIELD(verification_status, 'unverified', 'disapproved', 'verified')")
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'account_type' => User::getAccountTypeName($user->account_type),
                'degree' => $user->degree ?? 'N/A',
                'program' => $user->program ?? 'N/A',
                'nationality' => $user->nationality ?? 'N/A',
                'created_at' => $user->created_at->format('Y-m-d'),
                'verification_status' => $user->verification_status,
            ];
        });

    // Return sorted data as JSON for AJAX response
    return response()->json($users);
}



}
