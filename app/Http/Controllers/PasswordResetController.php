<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // Show the form to request a password reset link
    public function showResetForm()
    {
        return view('passwordreset');
    }

    // Handle the password reset request and send the reset link
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
    
        // Generate a token
        $token = Str::random(60);
    
        // Insert the token and email into password_reset_tokens table
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);
    
        // Prepare the reset link
        $resetLink = url('/password-reset/' . $token);
    
        // Send the reset link via email using a view
        Mail::send('emails.password_reset', ['resetLink' => $resetLink], function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Password Reset Request');
        });
    
        return back()->with('success', 'Password reset link sent to your email.');
    }
    

    // Show the form to reset the password (after clicking the email link)
    public function showNewPasswordForm($token)
    {
        // Check if the token exists in the password_reset_tokens table
        $resetToken = DB::table('password_reset_tokens')
                        ->where('token', $token)
                        ->first();
    
        // If the token is invalid or does not exist, redirect to the login page or an error page
        if (!$resetToken) {
            return redirect('/login')->with('error', 'Invalid or already used password reset link.');
        }
    
        // If the token is valid, display the password reset form
        return view('newpassword', ['token' => $token]);
    }
    
    // Handle the password reset form submission
// Handle the password reset form submission
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Check the token in the password_reset_tokens table
        $reset = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$reset) {
            return back()->with('error', 'Invalid token or email.');
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token from password_reset_tokens table after successful reset
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Redirect to the login page after password reset
        return redirect('/login')->with('success', 'Password has been reset successfully.');
    }

}
