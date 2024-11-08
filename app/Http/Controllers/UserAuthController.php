<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserAuthController extends Controller
{
    public function getLogin()
    {
        return view('main-login');
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        // Attempt to authenticate the user for different account types
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'account_type' => 4])) {
            $user = Auth::user();
            if ($user->verification_status === 'verified') {
                return redirect()->route('PCdashboard')->with('success', 'Login Successful');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', 'Your account is not verified');
            }
        }
    
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'account_type' => 5])) {
            $user = Auth::user();
            if ($user->verification_status === 'verified') {
                return redirect()->route('TDPdashboard')->with('success', 'Login Successful');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', 'Your account is not verified');
            }
        }
    
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'account_type' => 6])) {
            $user = Auth::user();
            if ($user->verification_status === 'verified') {
                return redirect()->route('AUFCdashboard')->with('success', 'Login Successful');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', 'Your account is not verified');
            }
        }
    
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'account_type' => 7])) {
            $user = Auth::user();
            if ($user->verification_status === 'verified') {
                return redirect()->route('Sdashboard')->with('success', 'Login Successful');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', 'Your account is not verified');
            }
        }
    
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'account_type' => 8])) {
            $user = Auth::user();
            if ($user->verification_status === 'verified') {
                return redirect()->route('OVPRIdashboard')->with('success', 'Login Successful');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', 'Your account is not verified');
            }
        }
    
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'account_type' => 9])) {
            $user = Auth::user();
            if ($user->verification_status === 'verified') {
                return redirect()->route('Ldashboard')->with('success', 'Login Successful');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', 'Your account is not verified');
            }
        }
    
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'account_type' => 10])) {
            $user = Auth::user();
            if ($user->verification_status === 'verified') {
                return redirect()->route('LEdashboard')->with('success', 'Login Successful');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', 'Your account is not verified');
            }
        }
    
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'account_type' => 11])) {
            $user = Auth::user();
            // Allow login regardless of verification status
            return redirect()->route('GSSdashboard')->with('success', 'Login Successful');
        }
    
        // If none of the login attempts were successful
        return redirect()->back()->with('error', 'Invalid credentials');
    }
    

    public function logout()
    {
        auth()->logout();
        return redirect()->route('getLogin')->with('success', 'You have been successfully logged out');
    }

    public function getRegister()
    {
        return view('main-register');
    }

    public function postRegister(Request $request)
    {
        // Sanitize nationality field (trim spaces and convert to lowercase)
        $nationality = strtolower(trim($request->nationality));
    
        // Apply validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|regex:/^[a-zA-Z0-9._%+-]+@auf\.edu\.ph$/',
            'password' => 'required|string|min:8|confirmed',
            'account_type' => 'required|in:2,3,4,5,6,7,8,9,10,11',
            'degree' => 'nullable|required_if:account_type,11|in:Masteral,Doctorate',
            'program' => 'nullable|required_if:account_type,5,11|string',
            'nationality' => 'nullable|required_if:account_type,11|string',
            // Immigration card required if nationality is not "filipino" (case-insensitive)
            'immigration_or_studentvisa' => [
                'nullable',
                'mimes:jpeg,jpg,png',
                'max:2048',
                // Custom validation rule
                function($attribute, $value, $fail) use ($nationality) {
                    if ($nationality !== 'filipino' && !$value) {
                        $fail('The immigration card is required for non-Filipino nationalities.');
                    }
                },
            ],
            'manuscript' => 'nullable|mimes:pdf|max:25000',
        ]);
        

        // Handle file uploads
        $immigrationFileName = null;
        $manuscriptFileName = null;
        
        $originalImmigrationFileName = null;
        $originalManuscriptFileName = null;

        // Handle immigration file upload
        if ($request->hasFile('immigration_or_studentvisa')) {
            $file = $request->file('immigration_or_studentvisa');
            $originalImmigrationFileName = $file->getClientOriginalName(); // Get original filename
            $immigrationFileName =  $originalImmigrationFileName; // Create unique filename
            $file->storeAs('public/immigrations', $immigrationFileName); // Store the file
        }


        // Handle manuscript upload
        if ($request->hasFile('manuscript')) {
            $file = $request->file('manuscript');
            $originalManuscriptFileName = $file->getClientOriginalName(); // Get original filename
            $manuscriptFileName = $originalManuscriptFileName; // Create unique filename
            $file->storeAs('public/manuscripts', $manuscriptFileName); // Store the file
        }



        // Create the user in the database
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'account_type' => $request->account_type,
            'degree' => $request->degree,
            'program' => $request->program,
            'nationality' => $request->nationality,
            'immigration_or_studentvisa' => $immigrationFileName, // Store unique file name
            'original_visa_filename' => $originalImmigrationFileName, // Store original file name
            'manuscript' => $manuscriptFileName, // Store unique file name
            'original_manuscript_filename' => $originalManuscriptFileName, // Store original file name
        ]);

        // Redirect back to the registration page with a success message
        return redirect()->route('getRegister')->with('success', 'Registration successful.');
    }
}
