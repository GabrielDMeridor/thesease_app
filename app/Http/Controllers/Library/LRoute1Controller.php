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
use App\Notifications\SimilarityDenialNotification;


class LRoute1Controller extends Controller
{
    public function index()
    {
        // Ensure the user is authenticated and is a Library user (account_type = 9)
        if (!auth()->check() || auth()->user()->account_type !== 9) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a library user to access this page');
        }

        // Retrieve appointments with the hierarchical order: no certificate first
        $appointments = AdviserAppointment::whereNotNull('similarity_manuscript')
            ->with('student')
            ->orderByRaw('similarity_certificate IS NOT NULL') // NULL certificates come first
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

    $keyword = $request->input('query', '');

    $appointments = AdviserAppointment::whereNotNull('similarity_manuscript')
        ->when($keyword, function ($query) use ($keyword) {
            $query->whereHas('student', function ($q) use ($keyword) {
                $q->where('name', 'like', "{$keyword}%");
            });
        })
        ->with('student')
        ->orderByRaw('similarity_certificate IS NOT NULL')
        ->get();

    // Check if request is AJAX
    if ($request->ajax()) {
        $html = '';
        foreach ($appointments as $appointment) {
            $formId = 'certificateUploadForm' . $appointment->student_id;
            $html .= '
                <tr>
                    <td>' . $appointment->student->name . '</td>
                    <td>' . $appointment->student->email . '</td>
                    <td>';
            if ($appointment->similarity_manuscript) {
                $html .= '<a href="#" data-toggle="modal" data-target="#manuscriptModal' . $appointment->id . '">' . basename($appointment->similarity_manuscript) . '</a>';
            } else {
                $html .= '<span>No manuscript uploaded</span>';
            }
            $html .= '</td>
                    <td>
                        <form action="' . route('library.uploadSimilarityCertificate') . '" method="POST" enctype="multipart/form-data" id="' . $formId . '">
                            ' . csrf_field() . '
                            <input type="hidden" name="student_id" value="' . $appointment->student_id . '">';
            if ($appointment->similarity_certificate) {
                $html .= '<a href="#" data-toggle="modal" data-target="#certificateModal' . $appointment->id . '">' . basename($appointment->similarity_certificate) . '</a>';
            } else {
                $html .= '<input type="file" name="similarity_certificate" class="form-control" required accept=".pdf">';
            }
            $html .= '</form>
                    </td>
                    <td>
                        <button type="submit" form="' . $formId . '" class="btn btn-primary save-button">Save</button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#denyModal' . $appointment->id . '">Deny</button>
                    </td>
                </tr>';
        }
        return response()->json(['html' => $html]);
    }

    return view('library.route1.Lroute1', [
        'title' => 'Route1 Checking',
        'appointments' => $appointments,
        'keyword' => $keyword
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
    
public function denyManuscript(Request $request, $appointmentId)
{
    // Validate the request to ensure a reason is provided
    $request->validate([
        'denialReason' => 'required|string|max:255',
    ]);

    // Find the appointment and the associated student
    $appointment = AdviserAppointment::findOrFail($appointmentId);
    $student = $appointment->student;

    // Send a notification with the similarity denial reason
    $student->notify(new SimilarityDenialNotification($request->denialReason));

    return redirect()->route('library.route1')->with('success', 'Denial notification sent to student.');
}

}
