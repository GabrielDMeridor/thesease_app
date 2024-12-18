    <?php

    use App\Http\Controllers\SuperAdmin\SAAuthController;
    use App\Http\Controllers\SuperAdmin\SAProfileController;
    use App\Http\Controllers\SuperAdmin\SAAccountController;
    use App\Http\Controllers\SuperAdmin\VerifyUserController;
    use App\Http\Controllers\SuperAdmin\SAArchiveController;
    use App\Http\Controllers\SuperAdmin\SARoute1Controller;
    use App\Http\Controllers\SuperAdmin\SACalendarController;
    use App\Http\Controllers\SuperAdmin\SAProposalMonitoringController;
    use App\Http\Controllers\SuperAdmin\SARoute2Controller;



    use App\Http\Controllers\Admin\AProfileController;
    use App\Http\Controllers\Admin\AAccountController;
    use App\Http\Controllers\Admin\AVerifyUserController;
    use App\Http\Controllers\Admin\ARoute1Controller;
    use App\Http\Controllers\Admin\AArchiveController;
    use App\Http\Controllers\Admin\ACalendarController;
    use App\Http\Controllers\Admin\AProposalMonitoringController;
    use App\Http\Controllers\Admin\ARoute2Controller;






    use App\Http\Controllers\GraduateSchool\GSProfileController;
    use App\Http\Controllers\GraduateSchool\GSAccountController;
    use App\Http\Controllers\GraduateSchool\GSVerifyUserController;
    use App\Http\Controllers\GraduateSchool\GSRoute1Controller;
    use App\Http\Controllers\GraduateSchool\GSArchiveController;
    use App\Http\Controllers\GraduateSchool\GSCalendarController;
    use App\Http\Controllers\GraduateSchool\GSProposalMonitoringController;
    use App\Http\Controllers\GraduateSchool\GSRoute2Controller;






    use App\Http\Controllers\ProgramChair\PCProfileController;
    use App\Http\Controllers\ProgramChair\PCAccountController;
    use App\Http\Controllers\ProgramChair\PCRoute1Controller;
    use App\Http\Controllers\ProgramChair\PCRoute2Controller;



    use App\Http\Controllers\TDProfessor\TDPProfileController;
    use App\Http\Controllers\TDProfessor\TDPRoute1Controller;
    use App\Http\Controllers\TDProfessor\TDPAccountController;
    use App\Http\Controllers\TDProfessor\TDPCalendarController;
    use App\Http\Controllers\TDProfessor\TDPProposalMonitoringController;
    use App\Http\Controllers\TDProfessor\TDPRoute2Controller;



    

    use App\Http\Controllers\AUFCommittee\AUFCProfileController;
    use App\Http\Controllers\AUFCommittee\AUFCAccountController;
    use App\Http\Controllers\AUFCommittee\AUFCRoute1Controller;



    use App\Http\Controllers\Statistician\SProfileController;
    use App\Http\Controllers\Statistician\SAccountController;
    use App\Http\Controllers\Statistician\SRoute1Controller;
    use App\Http\Controllers\Statistician\SRoute2Controller;



    use App\Http\Controllers\OVPRI\OVPRIProfileController;
    use App\Http\Controllers\OVPRI\OVPRIAccountController;
    use App\Http\Controllers\OVPRI\OVPRIRoute1Controller;
    use App\Http\Controllers\OVPRI\OVPRIRoute2Controller;

    use App\Http\Controllers\LanguageEditor\LERoute2Controller;




    use App\Http\Controllers\Library\LProfileController;
    use App\Http\Controllers\Library\LAccountController;
    use App\Http\Controllers\Library\LRoute1Controller;
    use App\Http\Controllers\Library\LRoute2Controller;



    use App\Http\Controllers\LanguageEditor\LEProfileController;

    use App\Http\Controllers\GSStudent\GSSProfileController;
    use App\Http\Controllers\GSStudent\GSSAccountController;
    use App\Http\Controllers\GSStudent\GSSFileUploadController;
    use App\Http\Controllers\GSStudent\GSSRoute1Controller;
    use App\Http\Controllers\GSStudent\GSSCalendarController;
    use App\Http\Controllers\GSStudent\GSSRoute2Controller;

    use App\Http\Controllers\CCFP\CCFPProfileController;
    use App\Http\Controllers\CCFP\CCFPRoute1Controller;
    use App\Http\Controllers\CCFP\CCFPRoute2Controller;


    use App\Http\Controllers\PasswordResetController;


    use App\Http\Controllers\GraduateSchool\GSThesisRepoController;


    use App\Http\Controllers\UserAuthController;

    // Ensure authentication routes are available, including password reset routes

    Route::get('/', function () {
        return view('main-login');
    });
    // SUPERADMIN ROUTES
    // SuperAdmin Authentication Routes
    Route::get('/superadmin/login', [SAAuthController::class, 'getSALogin'])->name('getSALogin');
    Route::post('/superadmin/login', [SAAuthController::class, 'postSALogin'])->name('postSALogin');
    Route::post('/superadmin/logout', [SAProfileController::class, 'SAlogout'])->name('SAlogout');
    // SuperAdmin Routes Dashboard and Sidebar
    Route::get('/superadmin/dashboard', [SAProfileController::class, 'SAdashboard'])->name('SAdashboard');
    Route::post('/superadmin/announcement/store', [SAProfileController::class, 'storeAnnouncement'])->name('superadmin.storeAnnouncement');

    //SuperAdmin Home
    //SuperAdmin Account
    Route::get('/superadmin/account', action: [SAAccountController::class, 'SAaccount'])->name('superadmin.account');
    Route::put('/superadmin/update-profile', [SAAccountController::class, 'updateProfile'])->name('updateProfile');
    Route::put('/superadmin/change-password', [SAAccountController::class, 'changePassword'])->name('changePassword');

    //SuperAdmin Verify Users
    Route::get('/superadmin/verifyusers', [VerifyUserController::class, 'index'])->name('verify-users.index');
    Route::post('/superadmin/verifyusers', [VerifyUserController::class, 'verifyUsers'])->name('verify-users.verify');
    Route::patch('/superadmin/verifyusers/{user}', [VerifyUserController::class, 'updateVerificationStatus']);
    Route::delete('/superadmin/verifyusers/{id}', [VerifyUserController::class, 'destroy'])->name(name: 'verify-users.destroy');
    Route::post('/superadmin/verifyusers/disapprove', [VerifyUserController::class, 'disapprove'])->name('verify-users.disapprove');

    //SuperAdmin Route 1
    Route::get('/superadmin/route1', action: [SARoute1Controller::class, 'show'])->name('superadmin.route1');
    //Route for showiing routing form for superadmin for a specific student
    Route::get('/superadmin/route1/student/{studentId}', [SARoute1Controller::class, 'showRoutingForm'])->name('superadmin.showRoutingForm');
    Route::post('/superadmin/route1/student/{studentId}/sign', [SARoute1Controller::class, 'sign'])->name('superadmin.sign');
    // SuperAdmin DRPH Step
    Route::post('/superadmin/adviser_appointments/{studentId}/upload-community-extension-link', [SARoute1Controller::class, 'uploadCommunityExtensionLink'])->name('superadmin.uploadCommunityExtensionLink');
    Route::post('/superadmin/route1/student/{studentId}/approve-community-extension', [SARoute1Controller::class, 'approveCommunityExtension'])->name('superadmin.approveCommunityExtension');
   // SuperAdmin Step 6 DRPH : Step 5
   Route::post('/superadmin/adviser_appointments/{studentId}/upload-submission-files-link', [SARoute1Controller::class, 'uploadSubmissionFilesLink'])->name('superadmin.uploadSubmissionFilesLink');
    Route::post('/superadmin/route1/student/{studentId}/approve-submission-files', [SARoute1Controller::class, 'approveSubmissionFiles'])->name('superadmin.approveSubmissionFiles');
    
    //Superadmin Archive
    Route::get('/superadmin/archive', action: [SAArchiveController::class, 'index'])->name('superadmin.archive');
    // SuperaAdmin Registration
    Route::get('superadmin/register', [SAAuthController::class, 'getSARegister'])->name('getSARegister');
    Route::post('superadmin/register', [SAAuthController::class, 'postSARegister'])->name('postSARegister');



    //All Users Authentication Routes
    Route::get('/login', [UserAuthController::class, 'getLogin'])->name('getLogin');
    Route::post('/login', [UserAuthController::class, 'postLogin'])->name('postLogin');
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
    //All Users Registration 
    Route::get('/register', [UserAuthController::class, 'getRegister'])->name('getRegister');
    Route::post('/register', [UserAuthController::class, 'postRegister'])->name('postRegister');








    //Admin Routes Dashboard and Sidebar
    Route::get('/admin/dashboard', [AProfileController::class, 'Adashboard'])->name('Adashboard');
    Route::post('/admin/announcement/store', [AProfileController::class, 'storeAnnouncement'])->name('admin.storeAnnouncement');

    // Admin Account
    Route::get('/admin/account', action: [AAccountController::class, 'Aaccount'])->name('admin.account');
    Route::put('/admin/update-profile', [AAccountController::class, 'updateProfile'])->name('admin.updateProfile');
    Route::put('/admin/change-password', [AAccountController::class, 'changePassword'])->name('admin.changePassword');
    // Admin Veify Uesrs
    Route::get('/admin/verifyusers', [AVerifyUserController::class, 'index'])->name('admin.verify-users.index');
    Route::post('/admin/verifyusers', [AVerifyUserController::class, 'verifyUsers'])->name('admin.verify-users.verify');
    Route::patch('/admin/verifyusers/{user}', [AVerifyUserController::class, 'updateVerificationStatus']);
    Route::delete('/admin/verifyusers/{id}', [AVerifyUserController::class, 'destroy'])->name(name: 'admin.verify-users.destroy');
    Route::post('/admin/verifyusers/disapprove', [AVerifyUserController::class, 'disapprove'])->name('admin.verify-users.disapprove');
    // Admin Route 1
    Route::get('/admin/route1', action: [ARoute1Controller::class, 'show'])->name('admin.route1');
    //Route for showiing routing form for admin for a specific student
    Route::get('/admin/route1/student/{studentId}', [ARoute1Controller::class, 'showRoutingForm'])->name('admin.showRoutingForm');
    Route::post('/admin/route1/student/{studentId}/sign', [ARoute1Controller::class, 'sign'])->name('admin.sign');
    // Admin Archive
    Route::get('/admin/archive', action: [AArchiveController::class, 'index'])->name('admin.archive');
    // Admin DRPH
    Route::post('/admin/adviser_appointments/{studentId}/upload-community-extension-link', [ARoute1Controller::class, 'uploadCommunityExtensionLink'])->name('admin.uploadCommunityExtensionLink');
    Route::post('/admin/route1/student/{studentId}/approve-community-extension', [ARoute1Controller::class, 'approveCommunityExtension'])->name('admin.approveCommunityExtension');
     // Admin Step 6 DRPH : Step 5
   Route::post('/admin/adviser_appointments/{studentId}/upload-submission-files-link', [ARoute1Controller::class, 'uploadSubmissionFilesLink'])->name('admin.uploadSubmissionFilesLink');
   Route::post('/admin/route1/student/{studentId}/approve-submission-files', [ARoute1Controller::class, 'approveSubmissionFiles'])->name('admin.approveSubmissionFiles');



    //GraduateSchool Routes Dashboard and Sidebar
    Route::get('/graduateschool/dashboard', [GSProfileController::class, 'GSdashboard'])->name('GSdashboard');
    Route::post('/graduateschool/announcement/store', [GSProfileController::class, 'storeAnnouncement'])->name('graduateschool.storeAnnouncement');

    // GraduateSchool Account
    Route::get('/graduateschool/account', action: [GSAccountController::class, 'GSaccount'])->name('graduateschool.account');
    Route::put('/graduateschool/update-profile', [GSAccountController::class, 'updateProfile'])->name('graduateschool.updateProfile');
    Route::put('/graduateschool/change-password', [GSAccountController::class, 'changePassword'])->name('graduateschool.changePassword');
    //Graduate School Verify Users
    Route::get('/graduateschool/verifyusers', [GSVerifyUserController::class, 'index'])->name('graduateschool.verify-users.index');
    Route::post('/graduateschool/verifyusers', [GSVerifyUserController::class, 'verifyUsers'])->name('graduateschool.verify-users.verify');
    Route::patch('/graduateschool/verifyusers/{user}', [GSVerifyUserController::class, 'updateVerificationStatus']);
    Route::delete('/graduateschool/verifyusers/{id}', [GSVerifyUserController::class, 'destroy'])->name(name: 'graduateschool.verify-users.destroy');
    Route::post('/graduateschool/verifyusers/disapprove', [GSVerifyUserController::class, 'disapprove'])->name('graduateschool.verify-users.disapprove');
    //Gradute School Route 1
    Route::get('/graduateschoolgraduateschool/route1', action: [GSRoute1Controller::class, 'show'])->name('graduateschool.route1');
    //Route for showiing routing form for admin for a specific student
    Route::get('/graduateschool/route1/student/{studentId}', [GSRoute1Controller::class, 'showRoutingForm'])->name('graduateschool.showRoutingForm');
    Route::post('/graduateschool/route1/student/{studentId}/sign', [GSRoute1Controller::class, 'sign'])->name('graduateschool.sign');
    //Graduate School Archive
    Route::get('/graduateschool/archive', action: [GSArchiveController::class, 'index'])->name('graduateschool.archive');
    // GraduateSchool DRPH
    Route::post('/graduateschool/adviser_appointments/{studentId}/upload-community-extension-link', [GSRoute1Controller::class, 'uploadCommunityExtensionLink'])->name('graduateschool.uploadCommunityExtensionLink');
    Route::post('/graduateschool/route1/student/{studentId}/approve-community-extension', [GSRoute1Controller::class, 'approveCommunityExtension'])->name('graduateschool.approveCommunityExtension');
    // GraduateSCchool Step 6 DRPH : Step 5
    Route::post('/graduateschool/adviser_appointments/{studentId}/upload-submission-files-link', [GSRoute1Controller::class, 'uploadSubmissionFilesLink'])->name('graduateschool.uploadSubmissionFilesLink');
   Route::post('/graduateschool/route1/student/{studentId}/approve-submission-files', [GSRoute1Controller::class, 'approveSubmissionFiles'])->name('graduateschool.approveSubmissionFiles');

   Route::post('/gsstudent/respond-to-final-statistician', [GSSRoute2Controller::class, 'respondToFinalStatistician'])->name('gsstudent.respondToFinalStatistician');
   
   Route::get('statistician/route2', [SRoute2Controller::class, 'index'])->name('statistician.route2.index');

   Route::post('/statistician/route2/approve/{id}', [SRoute2Controller::class, 'approve'])->name('statistician.route2.approve');
   Route::post('/statistician/route2/reject/{id}', [SRoute2Controller::class, 'reject'])->name('statistician.route2.reject');






    //Programchair Routes Dashboard and Sidebar
    Route::get('/programchair/dashboard', [PCProfileController::class, 'PCdashboard'])->name('PCdashboard');
    //ProgramChair Account
    Route::get('/programchair/account', action: [PCAccountController::class, 'PCaccount'])->name('programchair.account');

    //ProgamChair Route 1
    Route::get('/programchair/route1', [PCRoute1Controller::class, 'show'])->name('programchair.route1');
    // Route for showing the assign adviser page for Program Chair
    Route::get('/programchair/assign-adviser', [PCRoute1Controller::class, 'show'])->name('programchair.assignAdviser');
    // Route for processing the adviser assignment
    Route::post('/programchair/assign-adviser', [PCRoute1Controller::class, 'assignAdviserToStudent'])->name('programchair.assignAdviserToStudent');
    Route::post('/programchair/affix-signature', [PCRoute1Controller::class, 'affixSignature'])->name('programchair.affixSignature');
    Route::post('/programchair/get-approved-student-details', [PCRoute1Controller::class, 'getApprovedStudentDetails']);


    //TDProfessor Routes Dashboard and Sidebar
    Route::get('/tdprofessor/dashboard', [TDPProfileController::class, 'TDPdashboard'])->name('TDPdashboard');
    //TDProfessor Account
    Route::get('/tdprofessor/account', action: [TDPAccountController::class, 'TDPaccount'])->name('tdprofessor.account');

    //TDProfessor Route1
    Route::get('/tdprofessor/route1', action: [TDPRoute1Controller::class, 'show'])->name('tdprofessor.route1');
    //TDProfessor Register Response
    Route::post('/professor/mark-registration-responded/{appointmentId}', [TDPRoute1Controller::class, 'markRegistrationResponded'])->name('tdprofessor.markRegistrationResponded');

    // TD Professor Routes
    Route::put('/tdprofessor/requests/{id}', [TDPRoute1Controller::class, 'updateRequestStatus'])->name('professor.request.update');
    //Route for showiing routing form for tdprofessor for a specific advisee
    Route::get('/tdprofessor/advisee/{id}/routing-form1', [TDPRoute1Controller::class, 'showAdviseeForm'])->name('professor.showRoutingForm');
    Route::post('/tdprofessor/sign/{appointmentId}', [TDPRoute1Controller::class, 'affixSignature'])->name('professor.affixSignature');
    Route::put('/tdprofessor/advisee/{id}/sign-routing-form', [TDPRoute1Controller::class, 'signRoutingForm'])->name('professor.signRoutingForm');
    // Approve prposal update
    Route::post('/tdprofessor/approve-proposal-manuscript-update/{appointmentId}', [TDPRoute1Controller::class, 'approveProposalManuscriptUpdate'])->name('tdprofessor.approveProposalManuscriptUpdate');
    Route::post('/tdprofessor/deny-proposal-manuscript-update/{appointmentId}', [TDPRoute1Controller::class, 'denyProposalManuscriptUpdate'])->name('tdprofessor.denyProposalManuscriptUpdate');

    //AUFCommittee Routes Dashboard and Sidebar
    Route::get('/aufcommittee/dashboard', [AUFCProfileController::class, 'AUFCdashboard'])->name('AUFCdashboard');
    //AUFCommittee Account
    Route::get('/aufcommittee/account',  [AUFCAccountController::class, 'AUFCaccount'])->name('aufcommittee.account');
    
    //AUFCommittee Route 1
    Route::get('aufcommittee/route1', [AUFCRoute1Controller::class, 'index'])->name('aufcommittee.route1.index');
    Route::post('aufcommittee/route1/approve/{id}', [AUFCRoute1Controller::class, 'approve'])->name('aufcommittee.route1.approve');
    Route::get('aufcommittee/route1/ajaxSearch', [AUFCRoute1Controller::class, 'ajaxSearch'])->name('aufcommittee.route1.ajaxSearch');

    //Statisctician Routes Dashboard and Sidebar
    Route::get('/statistician/dashboard', [SProfileController::class, 'Sdashboard'])->name('Sdashboard');
    // Statiscian Account
    Route::get('/statistician/account',  [SAccountController::class, 'Saccount'])->name('statistician.account');
    // Statistician Route 1
    Route::get('statistician/route1', [SRoute1Controller::class, 'index'])->name('statistician.route1.index');
    Route::post('statistician/route1/approve/{id}', [SRoute1Controller::class, 'approve'])->name('statistician.route1.approve');
    Route::get('statistician/route1/ajaxSearch', [SRoute1Controller::class, 'ajaxSearch'])->name('statistician.route1.ajaxSearch');
    Route::post('statistician/storeOrUpdateLink', [SRoute1Controller::class, 'storeOrUpdateStatisticianLink'])->name('statistician.storeOrUpdateStatisticianLink');




    //OVPRI Routes Dashboard and Sidebar
    Route::get('/ovpri/dashboard', [OVPRIProfileController::class, 'OVPRIdashboard'])->name('OVPRIdashboard');
    //OVPRI Account
    Route::get('/ovpri/account', action: [OVPRIAccountController::class, 'OVPRIaccount'])->name('ovpri.account');
    //OVPRI Route 1
    Route::get('/ovpri/route1', [OVPRIRoute1Controller::class, 'index'])->name('ovpri.route1');
    Route::get('/ovpri/route1/ajax-search', [OVPRIRoute1Controller::class, 'ajaxSearch'])->name('ovpri.route1.ajaxSearch');
    Route::post('/ovpri/route1/approve/{id}', [OVPRIRoute1Controller::class, 'approve'])->name('ovpri.route1.approve');








    //Library Routes Dashboard and Sidebar
    Route::get('/library/dashboard', [LProfileController::class, 'Ldashboard'])->name('Ldashboard');
    //Library Account
    Route::get('/library/account', action: [LAccountController::class, 'Laccount'])->name('library.account');

    //Library Route 1
    Route::get('/library/route1', [LRoute1Controller::class, 'index'])->name('library.route1');
    Route::post('/library/upload-similarity-certificate', [LRoute1Controller::class, 'uploadSimilarityCertificate'])->name('library.uploadSimilarityCertificate');
    Route::get('/library/search', [LRoute1Controller::class, 'search'])->name(name: 'library.search');






    //Language Editor Routes Dashboard and Sidebar
    Route::get('/languageeditor/dashboard', [LEProfileController::class, 'LEdashboard'])->name('LEdashboard');


    //GS Student Partial Routes
    Route::get('/gssstudent/partialdashboard', [GSSProfileController::class, 'partialDashboard'])->name('gssstudent.partialdashboard');
    Route::post('/gsstudent/file/upload', [GSSFileUploadController::class, 'uploadFile'])->name('gssstudent.file.upload');
    

    //GS Student Routes Dashboard and Sidebar
    Route::get('/gsstudent/dashboard', [GSSProfileController::class, 'GSSdashboard'])->name('GSSdashboard');
    //GS Student Account
    Route::get('/gssstudent/account',[GSSAccountController::class, 'index'])->name('gsstudent.account');
    Route::put('/gssstudent/change-password', [GSSAccountController::class, 'changePassword'])->name('changePassword');
    // GS Student Route 1
    Route::get('/gsstudent/route1', action: [GSSRoute1Controller::class, 'show'])->name('gsstudent.route1');
    // Route to submit Step 1 (adviser appointment)
    Route::post('/gsstudent/route1/submit', [GSSRoute1Controller::class, 'submit'])->name('gsstudent.route1.submit');
    // Route to handle signatures (if applicable for the next steps)
    Route::post('/gsstudent/route1/sign', [GSSRoute1Controller::class, 'sign'])->name('gsstudent.route1.sign');
    // GS Route 1 Step 3
    Route::post('/gsstudent/upload-similarity-manuscript', [GSSRoute1Controller::class, 'uploadSimilarityManuscript'])->name('gsstudent.uploadSimilarityManuscript');
    // GS  Stundent DRPH
    Route::post('/gsstudent/adviser_appointments/{id}/respond-community-extension', [GSSRoute1Controller::class, 'respondToCommunityExtension'])
    ->name('gsstudent.respondToCommunityExtension');
    // GS Student Step 6 DRPH :  Step 5
    Route::post('/gsstudent/upload-signed-routing-form', [GSSRoute1Controller::class, 'uploadSignedRoutingForm'])->name('gsstudent.uploadSignedRoutingForm');
    Route::post('/gsstudent/upload-proposal-manuscript', [GSSRoute1Controller::class, 'uploadProposalManuscript'])->name('gsstudent.uploadProposalManuscript');
    Route::post('/gsstudent/upload-video-presentation', [GSSRoute1Controller::class, 'uploadVideoPresentation'])->name('gsstudent.uploadVideoPresentation');

    Route::post('/gsstudent/adviser_appointments/{id}/respond-submission-files', [GSSRoute1Controller::class, 'respondToSubmissionFiles'])->name('gsstudent.respondToSubmissionFiles');
    // Step 8 : Step 7
    Route::post('/gsstudent/respondToStatistician', [GSSRoute1Controller::class, 'respondToStatistician'])->name('gsstudent.respondToStatistician');



    Route::get('/ccfp/dashboard', [CCFPProfileController::class, 'Cdashboard'])->name('Cdashboard');

        Route::get('/ccfp/route1', [CCFPRoute1Controller::class, 'index'])->name('ccfp.route1');
        Route::post('ccfp/route1/approve/{id}', [CCFPRoute1Controller::class, 'approve'])->name('ccfp.route1.approve');
        Route::get('/ccfp/route1/ajax-search', [CCFPRoute1Controller::class, 'ajaxSearch'])->name('ccfp.route1.ajaxSearch');
        Route::post('ccfp/store-ovpri-link', [CCFPRoute1Controller::class, 'storeOrUpdateCCFPLink'])->name('ccfp.storeOrUpdateCCFPLink');






    //Notificaion Event Handler
    // Mark all notifications as read
    Route::get('/notifications/markAsRead', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAsRead');

    // Clear all notifications
    Route::post('/notifications/clearAll', function () {
        auth()->user()->notifications()->delete();
        return back();
    })->name('notifications.clearAll');

    Route::post('/notifications/markAsRead/{id}', function ($id) {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['status' => 'success']);
    })->name('notifications.markOneAsRead');

    Route::delete('/notifications/{id}', function ($id) {
        $notification = Auth::user()->notifications()->find($id);
    
        if ($notification) {
            $notification->delete();
            return response()->json(['status' => 'success', 'message' => 'Notification deleted successfully']);
        }
    
        return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
    })->name('notifications.destroy');

    //Password Reset

    Route::get('/password-reset', [PasswordResetController::class, 'showResetForm'])->name('password.request');
    Route::post('/password-reset', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/password-reset/{token}', [PasswordResetController::class, 'showNewPasswordForm'])->name('password.reset');
    Route::post('/password-reset/update', [PasswordResetController::class, 'resetPassword'])->name('password.update');







    //AJAX for Step 2 Professor/Adviser Side
    Route::post('/professor/route1/{appointmentId}/consultation-and-sign', [TDPRoute1Controller::class, 'addConsultationDatesAndSign'])->name('professor.addConsultationDatesAndSign');

    Route::post('/professor/save-consultation-date', [TDPRoute1Controller::class, 'saveConsultationDate'])->name('professor.saveConsultationDate');
    Route::post('/professor/remove-consultation-date', [TDPRoute1Controller::class, 'removeConsultationDate'])->name('professor.removeConsultationDate');

    // AJAX Search Superadmin
    Route::get('/superadmin/verifyusers/search', [VerifyUserController::class, 'search'])->name('verify-users.search');
    // AJAX Search Admin
    Route::get('/admin/verifyusers/search', [AVerifyUserController::class, 'search'])->name('admin.verify-users.search');
    // AJAX Search GraduateSchool
    Route::get('/graduateschool/verifyusers/search', [GSVerifyUserController::class, 'search'])->name('graduateschool.verify-users.search');
    // routes/web.php



    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // AJAX search route for SuperAdmin route1 page
    Route::get('/superadmin/route1/search', [SARoute1Controller::class, 'ajaxSearch'])->name('superadmin.route1.search');
     // AJAX search route for Admin route1 page
    Route::get('/admin/route1/search', [ARoute1Controller::class, 'ajaxSearch'])->name('admin.route1.search');
    // AJAX search route for Graduate School route1 page
    Route::get('/graduateschool/route1/search', [GSRoute1Controller::class, 'ajaxSearch'])->name('graduateschool.route1.search');

        // AJAX search route for SuperAdmin archive page
        Route::get('/superadmin/archive/search', [SAArchiveController::class, 'ajaxSearch'])->name('superadmin.archive.search');
        // AJAX search route for Admin archive page
       Route::get('/admin/archive/search', [AArchiveController::class, 'ajaxSearch'])->name('admin.archive.search');
       // AJAX search route for Graduate School archie page
       Route::get('/graduateschool/archive/search', [GSArchiveController::class, 'ajaxSearch'])->name('graduateschool.archive.search');





       Route::get('/superadmin/calendar', [SACalendarController::class, 'index'])->name('superadmin.calendar');
       Route::post('/superadmin/calendar/schedule', [SACalendarController::class, 'storeSchedule'])->name('superadmin.calendar.schedule.store');
       Route::get('/superadmin/calendar/events', [SACalendarController::class, 'getEvents'])->name('superadmin.calendar.events');

       Route::get('/admin/calendar', [ACalendarController::class, 'index'])->name('admin.calendar');
       Route::post('/admin/calendar/schedule', [ACalendarController::class, 'storeSchedule'])->name('admin.calendar.schedule.store');
       Route::get('/admin/calendar/events', [ACalendarController::class, 'getEvents'])->name('admin.calendar.events');


       Route::get('/graduateschool/calendar', [GSCalendarController::class, 'index'])->name('graduateschool.calendar');
       Route::post('/graduateschool/calendar/schedule', [GSCalendarController::class, 'storeSchedule'])->name('graduateschool.calendar.schedule.store');
       Route::get('/graduateschool/calendar/events', [GSCalendarController::class, 'getEvents'])->name('graduateschool.calendar.events');
          
       Route::get('/gsstudent/calendar', [GSSCalendarController::class, 'showStudentSchedule'])->name('gsstudent.calendar');
       Route::get('/gsstudent/calendar/events', [GSSCalendarController::class, 'getEvents']); // Optional, to handle calendar events

       Route::get('/tdprofessor/calendar', [TDPCalendarController::class, 'showTDProfessorCalendar'])->name('tdprofessor.calendar');
       Route::get('/tdprofessor/calendar/events', [TDPCalendarController::class, 'getTDEvents'])->name('tdprofessor.calendar.events');





       // Student routes in GSSRoute1Controller
Route::post('/gsstudent/upload-proposal-manuscript-update', [GSSRoute1Controller::class, 'uploadProposalManuscriptUpdate'])->name('gsstudent.uploadProposalManuscriptUpdate');
//Route::post('/gsstudent/add-student-reply/{panelistId}', [GSSRoute1Controller::class, 'addStudentReply'])->name('gsstudent.addStudentReply');
Route::post('/gsstudent/{appointmentId}/{commentId}/add-reply', [GSSRoute1Controller::class, 'addStudentReply'])->name('gsstudent.addStudentReply');

// Panel routes in TDPProposalMonitoringController
Route::get('/tdprofessorpanel/monitoring', [TDPProposalMonitoringController::class, 'index'])->name('panel.monitoring');
Route::get('/tdprofessorpanel/monitoring/{studentId}', [TDPProposalMonitoringController::class, 'showStudentMonitoringForm'])->name('panel.showStudentMonitoringForm');
//Route::post('/tdprofessorpanel/monitoring/comment/{studentId}', [TDPProposalMonitoringController::class, 'addComment'])->name('panel.addComment');
//Route::post('/tdprofessorpanel/monitoring/remark/{studentId}', action: [TDPProposalMonitoringController::class, 'addRemark'])->name('panel.addRemark');
Route::post('/tdprofessorpanel/monitoring/{studentId}/add-comment', [TDPProposalMonitoringController::class, 'addComment'])->name('panel.addComment');
Route::post('/tdprofessorpanel/monitoring/{studentId}/{commentId}/add-remark', [TDPProposalMonitoringController::class, 'addRemark'])->name('panel.addRemark');


Route::post('/tdprofessorpanel/monitoring/signature/{studentId}', [TDPProposalMonitoringController::class, 'affixSignature'])->name('panel.affixSignature');
Route::get('/tdprofessor/monitoring/search', [TDPProposalMonitoringController::class, 'search'])->name('tdprofessor.monitoring.search');



// SuperAdmin routes for SAProposalMonitoringController
// Routes in web.php
Route::get('/superadmin/monitoring', [SAProposalMonitoringController::class, 'index'])->name('superadmin.monitoring');
Route::get('/superadmin/monitoring/search', action: [SAProposalMonitoringController::class, 'search'])->name('superadmin.monitoring.search');
Route::get('/superadmin/monitoring/{studentId}', [SAProposalMonitoringController::class, 'showStudentMonitoringForm'])->name('superadmin.showStudentMonitoringForm');
Route::post('/superadmin/monitoring/signature/{studentId}', [SAProposalMonitoringController::class, 'affixDeanSignature'])->name('superadmin.affixDeanSignature');

// Admin
Route::get('/admin/monitoring', [AProposalMonitoringController::class, 'index'])->name('admin.monitoring');
Route::get('/admin/monitoring/search', action: [AProposalMonitoringController::class, 'search'])->name('admin.monitoring.search');
Route::get('/admin/monitoring/{studentId}', [AProposalMonitoringController::class, 'showStudentMonitoringForm'])->name('admin.showStudentMonitoringForm');

// GraudateSchool
Route::get('/graduateschool/monitoring', [GSProposalMonitoringController::class, 'index'])->name('graduateschool.monitoring');
Route::get('/graduateschool/monitoring/search', action: [GSProposalMonitoringController::class, 'search'])->name('graduateschool.monitoring.search');
Route::get('/graduateschool/monitoring/{studentId}', [GSProposalMonitoringController::class, 'showStudentMonitoringForm'])->name('graduateschool.showStudentMonitoringForm');


Route::post('/gsstudent/upload-ethics-file/{fileType}', [GSSRoute1Controller::class, 'uploadEthicsFile'])
     ->name('gsstudent.uploadEthicsFile');

// Route for marking the Ethics Review data as sent to AUFC
Route::post('/gsstudent/send-data-to-aufc', [GSSRoute1Controller::class, 'sendDataToAUFC'])
     ->name('gsstudent.sendDataToAUFC');







     Route::post('/superadmin/analytics-data', [SAProfileController::class, 'getAnalyticsData'])->name('superadmin.analyticsData');
     Route::post('/admin/analytics-data', [AProfileController::class, 'getAnalyticsData'])->name('admin.analyticsData');
     Route::post('/graduateschool/analytics-data', [GSProfileController::class, 'getAnalyticsData'])->name('graduateschool.analyticsData');


     Route::post('store-submission-link', [SARoute1Controller::class, 'storeOrUpdateSubmissionLink'])->name('superadmin.storeOrUpdateSubmissionLink');
     Route::post('admin/store-submission-link', [ARoute1Controller::class, 'storeOrUpdateSubmissionLink'])->name('admin.storeOrUpdateSubmissionLink');
     Route::post('graduateschool/store-submission-link', [SARoute1Controller::class, 'storeOrUpdateSubmissionLink'])->name('graduateschool.storeOrUpdateSubmissionLink');
     Route::post('/library/deny-manuscript/{appointmentId}', [LRoute1Controller::class, 'denyManuscript'])->name('library.denyManuscript');

     Route::post('ovpri/store-ovpri-link', [OVPRIRoute1Controller::class, 'storeOrUpdateOVPRILink'])->name('ovpri.storeOrUpdateOVPRILink');


     Route::post('/aufcommittee/route1/uploadEthicsClearance/{id}', [AUFCRoute1Controller::class, 'uploadEthicsClearance'])->name('aufcommittee.route1.uploadEthicsClearance');
     Route::post('/aufcommittee/route1/denyAppointment/{id}', [AUFCRoute1Controller::class, 'denyAppointment'])->name('aufcommittee.route1.denyAppointment');







     /////ROUTE 2222
     Route::get('/superadmin/route2', [SARoute2Controller::class, 'show'])->name('superadmin.route2');
     Route::get('/superadmin/route2/student/{id}', [SARoute2Controller::class, 'showRoutingForm'])->name('superadmin.showRoutingForm2');

     Route::get('/admin/route2', [ARoute2Controller::class, 'show'])->name('admin.route2');
     Route::get('/admin/route2/student/{id}', [ARoute2Controller::class, 'showRoutingForm'])->name('admin.showRoutingForm2');

     
     Route::get('/graduateschool/route2', [GSRoute2Controller::class, 'show'])->name('graduateschool.route2');
     Route::get('/graduateschool/route2/student/{id}', [GSRoute2Controller::class, 'showRoutingForm'])->name('graduateschool.showRoutingForm2');

     Route::get('/gsstudent/route2', action: [GSSRoute2Controller::class, 'show'])->name('gsstudent.route2');

     Route::get('/tdprofessor/route2', action: [TDPRoute2Controller::class, 'show'])->name('tdprofessor.route2');
     //Route for showiing routing form for superadmin for a specific student
     Route::get('/tdprofessor/advisee/{id}/routing-form', [TDPRoute2Controller::class, 'showRoutingForm'])->name('tdprofessor.showRoutingForm2');
     Route::post('/tdprofessor/route2/appointments/{appointment}/add-final-consultation-dates-sign', [TDPRoute2Controller::class, 'addFinalConsultationDatesAndSign'])->name('route2.addFinalConsultationDatesAndSign');
     Route::post('/save-consultation-date', [TDPRoute2Controller::class, 'saveConsultationDate'])->name('professor2.saveConsultationDate');
     Route::post('/gsstudent/upload-manuscript/{appointment}', [GSSRoute2Controller::class, 'uploadManuscript'])->name('gsstudent.uploadManuscript');


     Route::post('/statistician/route2/update-link', [SRoute2Controller::class, 'storeOrUpdateFinalStatisticianLink'])->name('statistician.route2.updateLink');
     Route::post('gsstudent/upload-proof-of-publication/{appointment}', [GSSRoute2Controller::class, 'uploadProofOfPublication'])->name('gsstudent.uploadProofOfPublication');

     Route::get('/programchair/route2', [PCRoute2Controller::class, 'show'])->name('programchair.route2.show');

     Route::post('/programchair/route2/approve/{id}', [PCRoute2Controller::class, 'approve'])->name('programchair.route2.approve');
    Route::post('/programchair/route2/deny/{id}', [PCRoute2Controller::class, 'deny'])->name('programchair.route2.deny');

    Route::post('/tdprofessor/route2/markRegistrationResponded/{appointmentId}', [TDPRoute2Controller::class, 'markFinalRegistrationResponded'])->name('tdprofessor.markRegistrationResponded2');
    Route::get('/ovpri/route2', [OVPRIRoute2Controller::class, 'index'])->name('ovpri.route2');
    Route::post('/ovpri/route2/approve/{id}', [OVPRIRoute2Controller::class, 'approve'])->name('ovpri.route2.approve');
    Route::post('/ovpri/route2/link', [OVPRIRoute2Controller::class, 'storeOrUpdateOVPRILink'])->name('ovpri.route2.storeOrUpdateOVPRILink');
    Route::post('/gsstudent/route2/uploadSimilarityManuscript', [GSSRoute2Controller::class, 'uploadFinalSimilarityManuscript'])->name('gsstudent.uploadFinalSimilarityManuscript');
    Route::post('/gsstudent/route2/uploadSimilarityCertificate', [GSSRoute2Controller::class, 'uploadSimilarityCertificate'])->name('gsstudent.uploadFinalSimilarityCertificate');
    
    
    Route::get('/library/route2', [LRoute2Controller::class, 'index'])->name('library.route2');
    Route::post('/library/route2/upload-certificate', [LRoute2Controller::class, 'uploadFinalSimilarityCertificate'])->name('library.uploadFinalSimilarityCertificate');
    Route::post('/library/route2/deny/{appointmentId}', [LRoute2Controller::class, 'denyFinalManuscript'])->name('library.denyFinalManuscript');

// Route for Program Chair to sign
Route::post('/program-chair/sign/{appointment}', [PCRoute2Controller::class, 'signProgram'])->name('program-chair.sign');

// Route for CCFP to sign
Route::post('gsstudent/route2/respond-community', [GSSRoute2Controller::class, 'respondToCommunity'])->name('gsstudent.respondToCommunity');
Route::get('/ccfp/route2', [CCFPRoute2Controller::class, 'index'])->name('ccfp.route2');
    Route::post('/ccfp/route2/sign/{id}', [CCFPRoute2Controller::class, 'sign'])->name('ccfp.route2.sign');
    Route::post('/gsstudent/uploadCommunityExtensionForms', [GSSRoute2Controller::class, 'uploadCommunityExtensionForms'])->name('gsstudent.uploadCommunityExtensionForms');

    Route::get('/gsstudent/files', [GSSRoute2Controller::class, 'showFiles'])->name('gsstudent.showFiles');
    Route::post('/gsstudent/uploadFinalVideoPresentation', [GSSRoute2Controller::class, 'uploadFinalVideoPresentation'])->name('gsstudent.uploadFinalVideoPresentation');


    Route::get('/languageeditor/submissions', [LERoute2Controller::class, 'show'])->name('le.show');
    Route::post('/languageeditor/approve/{id}', [LERoute2Controller::class, 'approve'])->name('le.approve');
    Route::post('/languageeditor/deny/{id}', [LERoute2Controller::class, 'deny'])->name('le.deny');
    Route::post('/admin/route2/final-global-link', [ARoute2Controller::class, 'storeOrUpdateFinalGlobalLink'])->name('admin.storeOrUpdateFinalGlobalLink');
// Assuming this is inside a group with middleware for authentication and role-based access
Route::post('/gsstudent/respond-to-final-submission-files/{appointment}', [GSSRoute2Controller::class, 'respondToFinalSubmissionFiles'])
    ->name('gsstudent.respondToFinalSubmissionFiles');
    Route::post('/admin/approveFormFee/{id}', [ARoute2Controller::class, 'approveFormFee'])->name('admin.approveFormFee');
    Route::post('/admin/denyFormFee/{id}', [ARoute2Controller::class, 'denyFormFee'])->name('admin.denyFormFee');

    Route::get('/graduate-school/thesis/upload', [GSThesisRepoController::class, 'showThesisUploadForm'])->name('gs.showThesisUploadForm');
Route::post('/graduate-school/thesis/upload', [GSThesisRepoController::class, 'uploadThesis'])->name('gs.uploadThesis');
Route::get('/graduate-school/thesis', [GSThesisRepoController::class, 'index'])->name('gs.thesisIndex');

Route::get('/superadmin/thesis', [GSThesisRepoController::class, 'index'])->name('gs.thesisIndex');
