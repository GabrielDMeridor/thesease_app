<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AAccountController extends Controller
{
    public function Aaccount()
    {
        if (!auth()->check() || auth()->user()->account_type !== User::Admin) {
            return redirect()->route('getSALogin')->with('error', 'You must be logged in as an admin to access this page.');
        }

        $data = [
            'title' => 'Account Profile',
            'user' => auth()->user(),
        ];

        return view('admin.account.AAccount', $data);
    }

    // Update Profile using AJAX
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
        ]);

        $errors = [];

        // Collect validation errors
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
        }

        if (!empty($errors)) {
            return response()->json([
                'status' => 'error',
                'errors' => $errors
            ]);
        }

        // Update profile information
        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($user->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update profile. Please try again.'
        ]);
    }

    // Change Password using AJAX
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

        // Update password and save
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.'
        ]);
    }
}
