    <?php

    use App\Http\Controllers\SuperAdmin\SAAuthController;
    use App\Http\Controllers\SuperAdmin\SAProfileController;
    use App\Http\Controllers\SuperAdmin\SAAccountController;
    use App\Http\Controllers\SuperAdmin\VerifyUserController;
    use App\Http\Controllers\SuperAdmin\SAArchiveController;
    use App\Http\Controllers\SuperAdmin\SARoute1Controller;


    use App\Http\Controllers\Admin\AProfileController;
    use App\Http\Controllers\Admin\AAccountController;
    use App\Http\Controllers\Admin\AVerifyUserController;
    use App\Http\Controllers\Admin\ARoute1Controller;
    use App\Http\Controllers\Admin\AArchiveController;




    use App\Http\Controllers\GraduateSchool\GSProfileController;
    use App\Http\Controllers\GraduateSchool\GSAccountController;
    use App\Http\Controllers\GraduateSchool\GSVerifyUserController;
    use App\Http\Controllers\GraduateSchool\GSRoute1Controller;
    use App\Http\Controllers\GraduateSchool\GSArchiveController;





    use App\Http\Controllers\ProgramChair\PCProfileController;
    use App\Http\Controllers\ProgramChair\PCRoute1Controller;


    use App\Http\Controllers\TDProfessor\TDPProfileController;
    use App\Http\Controllers\TDProfessor\TDPRoute1Controller;

    use App\Http\Controllers\AUFCommittee\AUFCProfileController;

    use App\Http\Controllers\Statistician\SProfileController;

    use App\Http\Controllers\OVPRI\OVPRIProfileController;

    use App\Http\Controllers\Library\LProfileController;

    use App\Http\Controllers\LanguageEditor\LEProfileController;

    use App\Http\Controllers\GSStudent\GSSProfileController;
    use App\Http\Controllers\GSStudent\GSSAccountController;
    use App\Http\Controllers\GSStudent\GSSFileUploadController;
    use App\Http\Controllers\GSStudent\GSSRoute1Controller;


    use App\Http\Controllers\PasswordResetController;




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




    //GraduateSchool Routes Dashboard and Sidebar
    Route::get('/graduateschool/dashboard', [GSProfileController::class, 'GSdashboard'])->name('GSdashboard');
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
    //Graduate School ArchiveF
    Route::get('/graduateschool/archive', action: [GSArchiveController::class, 'index'])->name('graduateschool.archive');









    //Programchair Routes Dashboard and Sidebar
    Route::get('/programchair/dashboard', [PCProfileController::class, 'PCdashboard'])->name('PCdashboard');
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
    //TDProfessor Route1
    Route::get('/tdprofessor/route1', action: [TDPRoute1Controller::class, 'show'])->name('tdprofessor.route1');
    // TD Professor Routes
    Route::put('/tdprofessor/requests/{id}', [TDPRoute1Controller::class, 'updateRequestStatus'])->name('professor.request.update');
    //Route for showiing routing form for tdprofessor for a specific advisee
    Route::get('/tdprofessor/advisee/{id}/routing-form', [TDPRoute1Controller::class, 'showAdviseeForm'])->name('professor.showRoutingForm');
    Route::post('/tdprofessor/sign/{appointmentId}', [TDPRoute1Controller::class, 'affixSignature'])->name('professor.affixSignature');
    Route::put('/tdprofessor/advisee/{id}/sign-routing-form', [TDPRoute1Controller::class, 'signRoutingForm'])->name('professor.signRoutingForm');

    //AUFCommittee Routes Dashboard and Sidebar
    Route::get('/aufcommittee/dashboard', [AUFCProfileController::class, 'AUFCdashboard'])->name('AUFCdashboard');

    //Statisctician Routes Dashboard and Sidebar
    Route::get('/aufcommittee/dashboard', [SProfileController::class, 'Sdashboard'])->name('Sdashboard');

    //OVPRI Routes Dashboard and Sidebar
    Route::get('/ovpri/dashboard', [OVPRIProfileController::class, 'OVPRIdashboard'])->name('OVPRIdashboard');

    //Library Routes Dashboard and Sidebar
    Route::get('/library/dashboard', [LProfileController::class, 'Ldashboard'])->name('Ldashboard');

    //Language Editor Routes Dashboard and Sidebar
    Route::get('/languageeditor/dashboard', [LEProfileController::class, 'LEdashboard'])->name('LEdashboard');


    //GS Student Partial Routes
    Route::get('/gssstudent/partialdashboard', [GSSProfileController::class, 'partialDashboard'])->name('gssstudent.partialdashboard');
    Route::put('/gsstudent/file/upload', [GSSFileUploadController::class, 'uploadFile'])->name('gssstudent.file.upload');
    

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

    //Password Reset

    Route::get('/password-reset', [PasswordResetController::class, 'showResetForm'])->name('password.request');
    Route::post('/password-reset', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/password-reset/{token}', [PasswordResetController::class, 'showNewPasswordForm'])->name('password.reset');
    Route::post('/password-reset/update', [PasswordResetController::class, 'resetPassword'])->name('password.update');
















    





