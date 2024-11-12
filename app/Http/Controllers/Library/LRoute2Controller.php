<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdviserAppointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\FinalSimilarityCertUploaded;
use App\Notifications\FinalSimilarityCertDenialNotification;

class LRoute2Controller extends Controller
{
    public function index(Request $request)
    {
        // Ensure the user is authenticated and is a Library user (account_type = 9)
        if (!auth()->check() || auth()->user()->account_type !== 9) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a library user to access this page');
        }

        // Check for search keyword and retrieve matching appointments
        $keyword = $request->input('query', '');
        $appointments = AdviserAppointment::whereNotNull('final_similarity_manuscript')
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('student', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->with('student')
            ->orderByRaw('final_similarity_manuscript IS NOT NULL')
            ->get();

        return view('library.route2.Lroute2', [
            'title' => 'Route 2 Checking',
            'appointments' => $appointments,
            'keyword' => $keyword
        ]);
    }
    public function uploadFinalSimilarityCertificate(Request $request)
    {
        $user = Auth::user();

        // Ensure only Library users (account type 9) can upload the certificate
        if ($user->account_type !== 9) {
            return redirect()->route('library.route2')->with('error', 'Unauthorized access');
        }

        // Retrieve the appointment based on the student ID
        $appointment = AdviserAppointment::where('student_id', $request->student_id)->first();

        if (!$appointment) {
            return redirect()->route('library.route2')->with('error', 'Appointment not found');
        }

        // Validate file input
        $request->validate([
            'final_similarity_certificate' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Handle certificate file upload
        $file = $request->file('final_similarity_certificate');
        $originalFileName = $file->getClientOriginalName();
        $storedFileName = $file->storeAs('public/final_similarity_certificates', $originalFileName);

        // Store file path and original file name in the database
        $appointment->final_similarity_certificate = $storedFileName;
        $appointment->final_similarity_certificate_original_name = $originalFileName; // Ensure this matches the database column name
        $appointment->save();

        // Notify the student and necessary users

        return redirect()->route('library.route2')->with('success', 'Final Similarity Certificate uploaded successfully!');
    }

    public function denyFinalManuscript(Request $request, $appointmentId)
    {
        // Validate the request to ensure a reason is provided
        $request->validate([
            'denialReason' => 'required|string|max:255',
        ]);

        // Find the appointment and the associated student
        $appointment = AdviserAppointment::findOrFail($appointmentId);

        // Send a notification with the denial reason

        return redirect()->route('library.route2')->with('success', 'Denial notification sent to student.');
    }


}
