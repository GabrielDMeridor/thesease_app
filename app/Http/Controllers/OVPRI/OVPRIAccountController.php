<?php

namespace App\Http\Controllers\OVPRI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OVPRIAccountController extends Controller
{
    public function OVPRIaccount()
    {
        // Check if the user is logged in and is a Graduate School student (account_type 11)
        if (!auth()->check() || auth()->user()->account_type !== 8) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a OVPRI to access this page');
        }
        
        // Pass the user data to the view
        $data = [
            'title' => 'Account Profile',
            'user' => auth()->user(),  // Get the currently authenticated user
        ];

        return view('ovpri.account.OVPRIaccount', $data);  // Render the account view
    }

    // Change password logic
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'The new password and confirmation do not match.',
            'new_password.min' => 'The new password must be at least 8 characters long.',
        ]);

        $errors = [];

        // Collect validation errors
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
        }

        // Check if current password is incorrect
        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            $errors['current_password'] = ['Your current password does not match our records.'];
        }

        // If there are any errors, return them
        if (!empty($errors)) {
            return response()->json([
                'status' => 'error',
                'errors' => $errors
            ]);
        }

        // Update the user's password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.'
        ]);
    }
}
