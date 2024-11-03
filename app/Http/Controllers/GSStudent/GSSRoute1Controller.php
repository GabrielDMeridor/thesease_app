<?php

namespace App\Http\Controllers\GSStudent;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdviserAppointment;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Notifications\LibraryNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CommunityExtensionRespondedNotification;
use App\Notifications\ProposalSubmissionCompletedNotification; // Import the notification class
use App\Notifications\SubmissionFilesRespondedNotification;






class GSSRoute1Controller extends Controller
{
    public function show()
    {
        if (!auth()->check() || auth()->user()->account_type !== 11) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a graduate school student to access this page');
        }
    
        $user = auth()->user();
        $advisers = User::where('account_type', User::Thesis_DissertationProfessor)->get();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();
        $allSignaturesFilled = $appointment && $appointment->adviser_signature && $appointment->chair_signature && $appointment->dean_signature;
    
        // Check if the student is in the DrPH program
        $isDrPH = $user->program === 'DrPH';
    
        $data = [
            'title' => 'Routing Form 1',
            'user' => $user,
            'advisers' => $advisers,
            'appointment' => $appointment,
            'allSignaturesFilled' => $allSignaturesFilled,
            'isDrPH' => $isDrPH,  // Pass the DrPH status to the view
        ];
    
        return view('gsstudent.route1.GSSroute1', $data);
    }
    
    
    public function sign(Request $request)
    {
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();

        // Check and update signatures based on user role
        if ($user->account_type == User::Thesis_DissertationProfessor && !$appointment->adviser_signature) {
            $appointment->adviser_signature = $user->name;
        } elseif ($user->account_type == User::ProgramChair && !$appointment->chair_signature) {
            $appointment->chair_signature = $user->name;
        } elseif ($user->account_type == User::GraduateSchool && !$appointment->dean_signature) {
            $appointment->dean_signature = $user->name;
        }

        $appointment->save();

        return redirect()->route('gsstudent.route1')->with('success', 'Form signed successfully!');
    }

    public function uploadSimilarityManuscript(Request $request)
    {
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();
    
        // Validate file input
        $request->validate([
            'similarity_manuscript' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);
    
        // Handle manuscript file upload
        $file = $request->file('similarity_manuscript');
        $originalFileName = $file->getClientOriginalName(); // Original file name
        $storedFileName = $file->storeAs('public/similarity_manuscripts', $originalFileName); // Store with original name
    
        // Store file path and original file name in the database
        $appointment->similarity_manuscript = $storedFileName;
        $appointment->original_similarity_manuscript_filename = $originalFileName;
        $appointment->save();
    
    // Notify Library (account type 9) with student's name in the message
    $libraryUsers = User::where('account_type', 9)->get();
    $message = "{$user->name} has uploaded their manuscript for similarity checking.";
    Notification::send($libraryUsers, new LibraryNotification($user, $message));
    
        return redirect()->route('gsstudent.route1')->with('success', 'Similarity Manuscript uploaded successfully and Library notified!');
    }
    public function respondToCommunityExtension(Request $request, $appointmentId)
    {
        // Ensure that the user is authenticated as a GSStudent
        if (!auth()->check() || auth()->user()->account_type !== User::GraduateSchoolStudent) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a graduate school student to access this feature.');
        }
    
        // Retrieve authenticated user as a User instance
        $user = User::find(auth()->id());
        $appointment = AdviserAppointment::findOrFail($appointmentId);
    
        if (!$appointment->community_extension_response) {
            $appointment->community_extension_response = 1;
            $appointment->community_extension_approval = 'pending';
            $appointment->save();
    
            // Fetch relevant users (SuperAdmin, Admin, GraduateSchool)
            $superAdmins = User::where('account_type', User::SuperAdmin)->get();
            $admins = User::where('account_type', User::Admin)->get();
            $graduateSchoolUsers = User::where('account_type', User::GraduateSchool)->get();
    
            // Notify each group of users
            Notification::send($superAdmins->merge($admins)->merge($graduateSchoolUsers), new CommunityExtensionRespondedNotification($user));
        }
    
        return redirect()->route('gsstudent.route1')
                         ->with('success', 'Your response has been recorded, and the approval status is now pending.');
    }
    public function uploadSignedRoutingForm(Request $request)
    {
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();
    
        $request->validate([
            'signed_routing_form_1' => 'required|file|mimes:pdf|max:2048',
        ]);
    
        $file = $request->file('signed_routing_form_1');
        $originalFileName = $file->getClientOriginalName();
        $uniqueFileName = time() . '_' . $originalFileName;
        $storedFilePath = $file->storeAs('public/signed_routing_forms', $uniqueFileName);
    
        $appointment->signed_routing_form_1 = $storedFilePath;
        $appointment->original_signed_routing_form_1 = $originalFileName;
        $appointment->save();
    
        $this->checkAndNotifyProposalSubmissionCompletion($appointment);
    
        return redirect()->route('gsstudent.route1')->with('success', 'Signed Routing Form uploaded successfully!');
    }
    
    public function uploadProposalManuscript(Request $request)
    {
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();
    
        $request->validate([
            'proposal_manuscript' => 'required|file|mimes:pdf|max:2048',
        ]);
    
        $file = $request->file('proposal_manuscript');
        $originalFileName = $file->getClientOriginalName();
        $uniqueFileName = time() . '_' . $originalFileName;
        $storedFilePath = $file->storeAs('public/proposal_manuscripts', $uniqueFileName);
    
        $appointment->proposal_manuscript = $storedFilePath;
        $appointment->original_proposal_manuscript = $originalFileName;
        $appointment->save();
    
        $this->checkAndNotifyProposalSubmissionCompletion($appointment);
    
        return redirect()->route('gsstudent.route1')->with('success', 'Proposal Manuscript uploaded successfully!');
    }
    
    public function uploadVideoPresentation(Request $request)
    {
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();
    
        $request->validate([
            'proposal_video_presentation' => 'required|file|mimes:mp4,avi,mov|max:10240',
        ]);
    
        $file = $request->file('proposal_video_presentation');
        $originalFileName = $file->getClientOriginalName();
        $uniqueFileName = time() . '_' . $originalFileName;
        $storedFilePath = $file->storeAs('public/video_presentations', $uniqueFileName);
    
        $appointment->proposal_video_presentation = $storedFilePath;
        $appointment->original_proposal_video_presentation = $originalFileName;
        $appointment->save();
    
        $this->checkAndNotifyProposalSubmissionCompletion($appointment);
    
        return redirect()->route('gsstudent.route1')->with('success', 'Video Presentation uploaded successfully!');
    }
    
    // Helper function to check proposal submission completion and notify
    protected function checkAndNotifyProposalSubmissionCompletion($appointment)
    {
        if ($appointment->signed_routing_form_1 && $appointment->proposal_manuscript && $appointment->proposal_video_presentation) {
            // All files are uploaded
            $appointment->proposal_submission_completed = true;
            $appointment->save();
    
            // Notify SuperAdmin, Admin, and GraduateSchool users
            $notifyUsers = User::whereIn('account_type', [1, 2, 3])->get();
            Notification::send($notifyUsers, new ProposalSubmissionCompletedNotification($appointment));
        }
    }
    public function respondToSubmissionFiles(Request $request, $appointmentId)
    {
        // Ensure that the user is authenticated as a GSStudent
        if (!auth()->check() || auth()->user()->account_type !== User::GraduateSchoolStudent) {
            return redirect()->route('getLogin')->with('error', 'You must be logged in as a graduate school student to access this feature.');
        }
        
        $user = User::find(auth()->id());

        // Retrieve the authenticated user as a User instance
        $appointment = AdviserAppointment::findOrFail($appointmentId);
    
        // Check if submission_files_response has not been set
        if (!$appointment->submission_files_response) {
            // Set submission_files_response to 1 and submission_files_approval to "pending"
            $appointment->submission_files_response = 1;
            $appointment->submission_files_approval = 'pending';
            $appointment->save();


            $superAdmins = User::where('account_type', User::SuperAdmin)->get();
            $admins = User::where('account_type', User::Admin)->get();
            $graduateSchoolUsers = User::where('account_type', User::GraduateSchool)->get();
    
            // Notify each group of users
            Notification::send($superAdmins->merge($admins)->merge($graduateSchoolUsers), new SubmissionFilesRespondedNotification($user));
        }
    
        return redirect()->route('gsstudent.route1')
                         ->with('success', 'Your response has been recorded, and the approval status for submission files is now pending.');
    }
    public function uploadProposalManuscriptUpdate(Request $request)
    {
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();

        // Check if all panel signatures are completed
        if ($appointment->panel_signatures && count(array_filter($appointment->panel_signatures)) == count($appointment->panel_signatures)) {
            return redirect()->route('gsstudent.route1')->with('error', 'Panelist signatures are complete. No further uploads allowed.');
        }

        $request->validate([
            'proposal_manuscript_update' => 'required|file|mimes:pdf|max:2048',
        ]);

        $file = $request->file('proposal_manuscript_update');
        $originalFileName = $file->getClientOriginalName();
        $storedFileName = $file->storeAs('public/proposal_manuscript_updates', time() . '_' . $originalFileName);

        $appointment->proposal_manuscript_updates = json_encode([
            'file_path' => $storedFileName,
            'original_name' => $originalFileName,
            'uploaded_at' => now(),
        ]);
        
        $appointment->save();

        return redirect()->route('gsstudent.route1')->with('success', 'Proposal manuscript update uploaded successfully!');
    }

    public function addStudentReply(Request $request, $panelistId)
    {
        $appointment = AdviserAppointment::where('student_id', Auth::id())->first();
        $replies = $appointment->student_replies ? json_decode($appointment->student_replies, true) : [];
        $replies[$panelistId] = $request->reply;
        $appointment->student_replies = json_encode($replies);
        $appointment->save();

        return back()->with('success', 'Reply added successfully!');
    }

    
}