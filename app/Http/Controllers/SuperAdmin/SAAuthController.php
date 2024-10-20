<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SAAuthController extends Controller
{
    public function getSALogin()
    {
        return view('superadmin.auth.SAlogin');
    }

    public function postSALogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        // First, find the user by email and check if account_type is 1, 2, or 3
        $user = User::where('email', $request->email)->whereIn('account_type', [1, 2, 3])->first();
    
        // If the user exists and the password matches, authenticate
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user); // Log in the user
            
            // Redirect based on the account type
            if ($user->account_type == 1) {
                return redirect()->route('SAdashboard')->with('success', 'Login Successful');
            } elseif ($user->account_type == 2) {
                return redirect()->route('Adashboard')->with('success', 'Login Successful');
            } elseif ($user->account_type == 3) {
                return redirect()->route('GSdashboard')->with('success', 'Login Successful');
            }
        } else {
            // Authentication failed
            return redirect()->back()->with('error', 'Invalid credentials');
        }
    }
    
    
    

    public function getSARegister()
    {
        return view('superadmin.auth.SAregister');
    }

    public function postSARegister(Request $request)
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
            'routing_form_one' => 'nullable|mimes:pdf|max:25000',
            'manuscript' => 'nullable|mimes:pdf|max:25000',
            'adviser_appointment_form' => 'nullable|mimes:pdf|max:25000',
        ]);

        // Handle file uploads
        $immigrationFileName = null;
        $routingFormOneFileName = null;
        $manuscriptFileName = null;
        $adviserAppointmentFileName = null;
        
        $originalImmigrationFileName = null;
        $originalRoutingFormOneFileName = null;
        $originalManuscriptFileName = null;
        $originalAdviserAppointmentFileName = null;

        // Handle immigration file upload
        if ($request->hasFile('immigration_or_studentvisa')) {
            $file = $request->file('immigration_or_studentvisa');
            $originalImmigrationFileName = $file->getClientOriginalName(); // Get original filename
            $immigrationFileName =  $originalImmigrationFileName; // Create unique filename
            $file->storeAs('public/immigrations', $immigrationFileName); // Store the file
        }

        // Handle routing form upload
        if ($request->hasFile('routing_form_one')) {
            $file = $request->file('routing_form_one');
            $originalRoutingFormOneFileName = $file->getClientOriginalName(); // Get original filename
            $routingFormOneFileName = $originalRoutingFormOneFileName; // Create unique filename
            $file->storeAs('public/routing_forms', $routingFormOneFileName); // Store the file
        }

        // Handle manuscript upload
        if ($request->hasFile('manuscript')) {
            $file = $request->file('manuscript');
            $originalManuscriptFileName = $file->getClientOriginalName(); // Get original filename
            $manuscriptFileName = $originalManuscriptFileName; // Create unique filename
            $file->storeAs('public/manuscripts', $manuscriptFileName); // Store the file
        }

        // Handle adviser appointment form upload
        if ($request->hasFile('adviser_appointment_form')) {
            $file = $request->file('adviser_appointment_form');
            $originalAdviserAppointmentFileName = $file->getClientOriginalName(); // Get original filename
            $adviserAppointmentFileName =  $originalAdviserAppointmentFileName; // Create unique filename
            $file->storeAs('public/adviser_appointments', $adviserAppointmentFileName); // Store the file
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
            'routing_form_one' => $routingFormOneFileName, // Store unique file name
            'original_routing_form_one_filename' => $originalRoutingFormOneFileName, // Store original file name
            'manuscript' => $manuscriptFileName, // Store unique file name
            'original_manuscript_filename' => $originalManuscriptFileName, // Store original file name
            'adviser_appointment_form' => $adviserAppointmentFileName, // Store unique file name
            'original_adviser_appointment_form_filename' => $originalAdviserAppointmentFileName, // Store original file name
        ]);

        // Redirect back to the registration page with a success message
        return redirect()->route('getSARegister')->with('success', 'Registration successful.');
    }
}
