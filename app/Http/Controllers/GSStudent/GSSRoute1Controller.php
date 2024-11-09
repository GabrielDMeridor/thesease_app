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
use App\Notifications\ProposalManuscriptUpdateNotification;
use App\Notifications\StudentReplyNotification;
use App\Notifications\StatisticianResponseNotification;
use App\Notifications\AUFCFileSubmissionNotification;
use App\Models\Setting;






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
        $globalSubmissionLink = Setting::where('key', 'submission_files_link')->value('value');
        $ovpriLink = Setting::where('key', 'ovpri_link')->value('value');

        // Check if the student is in the DrPH program
        $isDrPH = $user->program === 'DRPH-HPE';
    
        $data = [
            'title' => 'Routing Form 1',
            'user' => $user,
            'advisers' => $advisers,
            'appointment' => $appointment,
            'allSignaturesFilled' => $allSignaturesFilled,
            'isDrPH' => $isDrPH,  // Pass the DrPH status to the view
            'globalSubmissionLink' => $globalSubmissionLink,
            'ovpriLink' => $ovpriLink,
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
        if ($appointment->proposal_video_presentation) {
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
        $appointment = AdviserAppointment::findOrFail($appointmentId);
    
        // Retrieve submission_files_link from the settings table
        $submissionFilesLink = Setting::where('key', 'submission_files_link')->value('value');
    
        // Check if submission_files_link is null; if it is, do not proceed
        if (is_null($submissionFilesLink)) {
            return redirect()->route('gsstudent.route1')
                             ->with('error', 'Submission files link is not available. Please try again later.');
        }
    
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
        $panelSignatures = is_string($appointment->panel_signatures) ? json_decode($appointment->panel_signatures, true) : $appointment->panel_signatures ?? [];
    
        if ($panelSignatures && count(array_filter($panelSignatures)) == count($panelSignatures)) {
            return redirect()->route('gsstudent.route1')->with('error', 'Panelist signatures are complete. No further uploads allowed.');
        }
    
        // Validate the uploaded file
        $request->validate([
            'proposal_manuscript_update' => 'required|file|mimes:pdf|max:2048',
        ]);
    
        // Handle the file upload
        $file = $request->file('proposal_manuscript_update');
        $originalFileName = $file->getClientOriginalName();
        $storedFileName = $file->storeAs('public/proposal_manuscript_updates', time() . '_' . $originalFileName);
    
        // Store file details and exact upload timestamp
        $appointment->proposal_manuscript_updates = json_encode([
            'file_path' => $storedFileName,
            'original_name' => $originalFileName,
            'uploaded_at' => \Carbon\Carbon::now()->toDateTimeString(), // Store full date and time of the upload
        ]);
    
        // Update the exact date and time of the upload in `update_date_saved`
        $appointment->update_date_saved = \Carbon\Carbon::now(); // Stores full date and time
    
        $appointment->save();
    
        // Notify all panel members
        $panelMembersIds = is_string($appointment->panel_members) 
            ? json_decode($appointment->panel_members, true) 
            : $appointment->panel_members;
    
        // Retrieve users with those IDs and send notifications
        $panelMembers = User::whereIn('id', $panelMembersIds)->get();
        Notification::send($panelMembers, new ProposalManuscriptUpdateNotification($user));
    
        return redirect()->route('gsstudent.route1')->with('success', 'Proposal manuscript update uploaded successfully!');
    }
    
    

    public function addStudentReply(Request $request, $panelistId)
    {
        $appointment = AdviserAppointment::where('student_id', Auth::id())->first();
        $replies = $appointment->student_replies ? json_decode($appointment->student_replies, true) : [];
        $replies[$panelistId] = $request->reply;
        $appointment->student_replies = json_encode($replies);
        $appointment->save();
    
        // Notify the specific panelist
        $panelist = User::find($panelistId);
        if ($panelist) {
            Notification::send($panelist, new StudentReplyNotification(Auth::user()));
        }
    
        return back()->with('success', 'Reply added successfully!');
    }
    public function respondToStatistician(Request $request)
    {
        $user = Auth::user();
        $appointment = AdviserAppointment::where('student_id', $user->id)->first();
    
        // Ensure the response is only recorded if not already responded
        if (is_null($appointment->student_statistician_response)) {
            $appointment->student_statistician_response = 'responded';
            $appointment->save();
    
            // Find all users with account type 7 (Statisticians)
            $statisticians = User::where('account_type', 7)->get();
    
            // Notify each statistician
            foreach ($statisticians as $statistician) {
                $statistician->notify(new StatisticianResponseNotification($user->name));
            }
        }
    
        return redirect()->route('gsstudent.route1')
                         ->with('success', 'Your response to the consultation with the statistician has been recorded.');
    }
    public function uploadEthicsFile(Request $request, $fileType)
{
    $user = Auth::user();
    $appointment = AdviserAppointment::where('student_id', $user->id)->first();

    // Define validation rules based on the file type
    $validationRules = [
        'ethics_proof_of_payment' => 'file|mimes:pdf,png,jpg,jpeg|max:2048',
        'ethics_curriculum_vitae' => 'file|mimes:pdf,png,jpg,jpeg|max:2048',
        'ethics_research_services_form' => 'file|mimes:pdf|max:2048',
        'ethics_application_form' => 'file|mimes:pdf|max:2048',
        'ethics_study_protocol_form' => 'file|mimes:pdf|max:2048',
        'ethics_informed_consent_form' => 'file|mimes:pdf|max:2048',
        'ethics_sample_informed_consent' => 'file|mimes:pdf|max:2048',

    ];

    // Validate the request
    $request->validate([$fileType => $validationRules[$fileType]]);

    // Upload the file
    if ($file = $request->file($fileType)) {
        $originalFileName = $file->getClientOriginalName();
        $uniqueFileName = time() . '_' . $originalFileName;
        $storedFilePath = $file->storeAs('public/ethics_files', $uniqueFileName);

        // Save the file path and original file name in the appointment model
        $appointment->$fileType = $storedFilePath;
        $appointment->{$fileType . '_filename'} = $originalFileName;
        $appointment->save();
    }

    return redirect()->route('gsstudent.route1')->with('success', ucfirst(str_replace('_', ' ', $fileType)) . ' uploaded successfully!');
}

public function sendDataToAUFC(Request $request)
{
    $user = Auth::user();
    $appointment = AdviserAppointment::where('student_id', $user->id)->first();

    // Check if all required files are uploaded
    $requiredFiles = [
        'ethics_proof_of_payment',
        'ethics_curriculum_vitae',
        'ethics_research_services_form',
        'ethics_application_form',
        'ethics_study_protocol_form',
        'ethics_informed_consent_form',
        'ethics_sample_informed_consent',
    ];

    foreach ($requiredFiles as $fileField) {
        if (empty($appointment->$fileField)) {
            return redirect()->back()->with('error', 'Please upload all required files before sending the data.');
        }
    }

    // If all required files are present, mark data as sent and set aufc_status to "pending"
    $appointment->ethics_send_data_to_aufc = true;
    $appointment->aufc_status = 'pending'; // Set aufc_status to "pending"
    $appointment->save();

    // Send notifications to account types 1, 2, and 3 (Superadmin, Admin, and Graduate School)
    $notifiableRoles = [1, 2, 3];
    $notifiableUsers = User::whereIn('account_type', $notifiableRoles)->get();

    foreach ($notifiableUsers as $notifiableUser) {
        $notifiableUser->notify(new AUFCFileSubmissionNotification($user->name, $notifiableUser->account_type));
    }

    // Send notification to account type 6 (AUF Ethics Review Committee)
    $committeeUsers = User::where('account_type', 6)->get();

    foreach ($committeeUsers as $committeeUser) {
        $committeeUser->notify(new AUFCFileSubmissionNotification($user->name, $committeeUser->account_type));
    }

    return redirect()->route('gsstudent.route1')->with('success', 'Data has been sent to AUFC and status set to pending.');
}



    

    
}