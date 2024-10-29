<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdviserAppointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SimilarityCertEXCStudent;
use App\Notifications\SimilarityCertificateUploadedForStudent;



class LRoute1Controller extends Controller
{
    public function index()
    {
        // Ensure the user is authenticated and is a Library user (account_type = 9)
        if (!auth()->check() || auth()->user()->account_type !== 9) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a library user to access this page');
        }
    
        // Retrieve all students who have an uploaded similarity_manuscript
        $appointments = AdviserAppointment::whereNotNull('similarity_manuscript')
                        ->with('student') // Assuming 'student' relationship on AdviserAppointment to User
                        ->get();
    
        return view('library.route1.Lroute1', [
            'title' => 'Route1 Checking',
            'appointments' => $appointments
        ]);
    }
    
// Add this method in LRoute1Controller
public function search(Request $request)
{
    // Ensure the user is authenticated and is a Library user (account_type = 9)
    if (!auth()->check() || auth()->user()->account_type !== 9) {
        return redirect()->route('getLogin')->with('error', 'You must be logged in as a library user to access this page');
    }

    // Retrieve the search keyword
    $keyword = $request->input('query', '');

    // Filter appointments by keyword if provided
    $appointments = AdviserAppointment::whereNotNull('similarity_manuscript')
                    ->when($keyword, function ($query) use ($keyword) {
                        $query->whereHas('student', function ($q) use ($keyword) {
                            $q->where('name', 'like', "{$keyword}%");
                        });
                    })
                    ->with('student')
                    ->get();

    return view('library.route1.Lroute1', [
        'title' => 'Route1 Checking',
        'appointments' => $appointments,
        'keyword' => $keyword // Pass the keyword to the view
    ]);
}

    
    
    

public function uploadSimilarityCertificate(Request $request)
{
    $user = Auth::user();

    // Ensure only Library users (account type 9) can upload the certificate
    if ($user->account_type !== 9) {
        return redirect()->route('library.route1')->with('error', 'Unauthorized access');
    }

    // Retrieve the appointment based on the student ID
    $appointment = AdviserAppointment::where('student_id', $request->student_id)->first();

    if (!$appointment) {
        return redirect()->route('library.route1')->with('error', 'Appointment not found');
    }

    // Validate file input
    $request->validate([
        'similarity_certificate' => 'required|file|mimes:pdf,doc,docx,jpg,png,jpeg|max:2048',
    ]);

    // Handle certificate file upload
    $file = $request->file('similarity_certificate');
    $originalFileName = $file->getClientOriginalName();
    $storedFileName = $file->storeAs('public/similarity_certificates', $originalFileName);

    // Store file path and original file name in the database
    $appointment->similarity_certificate = $storedFileName;
    $appointment->original_similarity_certificate_filename = $originalFileName;
    $appointment->save();

    // Retrieve all users to notify
    $usersToNotify = User::whereIn('account_type', [1, 2, 3])
                          ->orWhere('id', $appointment->adviser_id) // Assuming 'adviser_id' is the adviser’s user ID
                          ->get();

    // Send notification to each user
    foreach ($usersToNotify as $notifyUser) {
        $notifyUser->notify(new SimilarityCertEXCStudent($appointment));
    }
    $appointment->student->notify(new SimilarityCertificateUploadedForStudent($appointment));


    return redirect()->route('library.route1')->with('success', 'Similarity Certificate uploaded successfully!');
}
    
}
