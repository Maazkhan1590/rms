<?php



// Cache Clear Route (for deployment/maintenance)
// Usage: /clear-cache?token=YOUR_SECRET_TOKEN
// Set CLEAR_CACHE_TOKEN in .env file for security
Route::get('/clear-cache', function () {
    $token = request()->query('token');
    $expectedToken = env('CLEAR_CACHE_TOKEN', 'change-this-secret-token');
    
    if ($token !== $expectedToken) {
        return response()->json([
            'error' => 'Unauthorized. Invalid token.',
            'message' => 'Please provide a valid token parameter.'
        ], 401);
    }
    
    try {
        \Artisan::call('config:clear');
        \Artisan::call('cache:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');
        
        return response()->json([
            'success' => true,
            'message' => 'All caches cleared successfully!',
            'cleared' => [
                'config' => 'Configuration cache cleared',
                'cache' => 'Application cache cleared',
                'route' => 'Route cache cleared',
                'view' => 'View cache cleared'
            ]
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => 'Error clearing caches: ' . $e->getMessage()
        ], 500);
    }
})->name('clear-cache');

// Public Home Page
Route::get('/', 'HomeController@index')->name('welcome');

// Public Faculty Members Page
Route::get('faculty-members', 'FacultyMemberController@index')->name('faculty-members.index');
Route::get('faculty-members/{user}', 'FacultyMemberController@show')->name('faculty-members.show');
Route::get('faculty-members/{user}/cv/view', 'FacultyMemberController@viewCV')->name('faculty-members.cv.view');
Route::get('faculty-members/{user}/cv/download', 'FacultyMemberController@downloadCV')->name('faculty-members.cv.download');

// Public Publications Routes
Route::get('publications', 'PublicationController@index')->name('publications.index');
Route::get('publications/load-more', 'PublicationController@loadMore')->name('publications.load-more');
Route::get('publications/create', 'PublicationController@create')->name('publications.create');
Route::post('publications', 'PublicationController@store')->name('publications.store');
Route::get('publications/{id}', 'PublicationController@show')->name('publications.show');
Route::post('publications/{publication}/submit', 'PublicationController@submit')->name('publications.submit');

// Public Grants Routes
Route::get('grants', 'GrantController@index')->name('grants.index');
Route::get('grants/load-more', 'GrantController@loadMore')->name('grants.load-more');
Route::get('grants/create', 'GrantController@create')->name('grants.create');
Route::post('grants', 'GrantController@store')->name('grants.store');
Route::get('grants/{grant}', 'GrantController@show')->name('grants.show');
Route::post('grants/{grant}/submit', 'GrantController@submit')->name('grants.submit');

// Public RTN Submissions Routes
Route::get('rtn-submissions', 'RtnSubmissionController@index')->name('rtn-submissions.index');
Route::get('rtn-submissions/load-more', 'RtnSubmissionController@loadMore')->name('rtn-submissions.load-more');
Route::get('rtn-submissions/create', 'RtnSubmissionController@create')->name('rtn-submissions.create');
Route::post('rtn-submissions', 'RtnSubmissionController@store')->name('rtn-submissions.store');
Route::get('rtn-submissions/{rtn}', 'RtnSubmissionController@show')->name('rtn-submissions.show');
Route::post('rtn-submissions/{rtn}/submit', 'RtnSubmissionController@submit')->name('rtn-submissions.submit');

// Public Bonus Recognitions Routes
Route::get('bonus-recognitions', 'BonusRecognitionController@index')->name('bonus-recognitions.index');
Route::get('bonus-recognitions/load-more', 'BonusRecognitionController@loadMore')->name('bonus-recognitions.load-more');
Route::get('bonus-recognitions/create', 'BonusRecognitionController@create')->name('bonus-recognitions.create');
Route::post('bonus-recognitions', 'BonusRecognitionController@store')->name('bonus-recognitions.store');
Route::get('bonus-recognitions/{bonus}', 'BonusRecognitionController@show')->name('bonus-recognitions.show');
Route::post('bonus-recognitions/{bonus}/submit', 'BonusRecognitionController@submit')->name('bonus-recognitions.submit');

// Public Partnerships & MOUs Routes
Route::get('partnerships', 'PartnershipController@index')->name('partnerships.index');
Route::get('partnerships/load-more', 'PartnershipController@loadMore')->name('partnerships.load-more');
Route::get('partnerships/create', 'PartnershipController@create')->name('partnerships.create');
Route::post('partnerships', 'PartnershipController@store')->name('partnerships.store');
Route::get('partnerships/{partnership}', 'PartnershipController@show')->name('partnerships.show');
Route::post('partnerships/{partnership}/submit', 'PartnershipController@submit')->name('partnerships.submit');

// Public Commercializations Routes
Route::get('commercializations', 'CommercializationController@index')->name('commercializations.index');
Route::get('commercializations/load-more', 'CommercializationController@loadMore')->name('commercializations.load-more');
Route::get('commercializations/create', 'CommercializationController@create')->name('commercializations.create');
Route::post('commercializations', 'CommercializationController@store')->name('commercializations.store');
Route::get('commercializations/{commercialization}', 'CommercializationController@show')->name('commercializations.show');
Route::post('commercializations/{commercialization}/submit', 'CommercializationController@submit')->name('commercializations.submit');

// Public Consultancies & KT Routes
Route::get('consultancies', 'ConsultancyController@index')->name('consultancies.index');
Route::get('consultancies/load-more', 'ConsultancyController@loadMore')->name('consultancies.load-more');
Route::get('consultancies/create', 'ConsultancyController@create')->name('consultancies.create');
Route::post('consultancies', 'ConsultancyController@store')->name('consultancies.store');
Route::get('consultancies/{consultancy}', 'ConsultancyController@show')->name('consultancies.show');
Route::post('consultancies/{consultancy}/submit', 'ConsultancyController@submit')->name('consultancies.submit');

// Public Awards Routes
Route::get('awards', 'AwardController@index')->name('awards.index');
Route::get('awards/load-more', 'AwardController@loadMore')->name('awards.load-more');
Route::get('awards/create', 'AwardController@create')->name('awards.create');
Route::post('awards', 'AwardController@store')->name('awards.store');
Route::get('awards/{award}', 'AwardController@show')->name('awards.show');
Route::post('awards/{award}/submit', 'AwardController@submit')->name('awards.submit');

// Public Research Investments Routes
Route::get('research-investments', 'ResearchInvestmentController@index')->name('research-investments.index');
Route::get('research-investments/load-more', 'ResearchInvestmentController@loadMore')->name('research-investments.load-more');
Route::get('research-investments/create', 'ResearchInvestmentController@create')->name('research-investments.create');
Route::post('research-investments', 'ResearchInvestmentController@store')->name('research-investments.store');
Route::get('research-investments/{investment}', 'ResearchInvestmentController@show')->name('research-investments.show');
Route::post('research-investments/{investment}/submit', 'ResearchInvestmentController@submit')->name('research-investments.submit');

// Public Conference Activities Routes
Route::get('conference-activities', 'ConferenceActivityController@index')->name('conference-activities.index');
Route::get('conference-activities/load-more', 'ConferenceActivityController@loadMore')->name('conference-activities.load-more');
Route::get('conference-activities/create', 'ConferenceActivityController@create')->name('conference-activities.create');
Route::post('conference-activities', 'ConferenceActivityController@store')->name('conference-activities.store');
Route::get('conference-activities/{activity}', 'ConferenceActivityController@show')->name('conference-activities.show');
Route::post('conference-activities/{activity}/submit', 'ConferenceActivityController@submit')->name('conference-activities.submit');

// Public Supervision & Exams Routes
Route::get('supervision-exams', 'SupervisionExamController@index')->name('supervision-exams.index');
Route::get('supervision-exams/load-more', 'SupervisionExamController@loadMore')->name('supervision-exams.load-more');
Route::get('supervision-exams/create', 'SupervisionExamController@create')->name('supervision-exams.create');
Route::post('supervision-exams', 'SupervisionExamController@store')->name('supervision-exams.store');
Route::get('supervision-exams/{supervision}', 'SupervisionExamController@show')->name('supervision-exams.show');
Route::post('supervision-exams/{supervision}/submit', 'SupervisionExamController@submit')->name('supervision-exams.submit');

// Public Editorial Appointments Routes
Route::get('editorial-appointments', 'EditorialAppointmentController@index')->name('editorial-appointments.index');
Route::get('editorial-appointments/load-more', 'EditorialAppointmentController@loadMore')->name('editorial-appointments.load-more');
Route::get('editorial-appointments/create', 'EditorialAppointmentController@create')->name('editorial-appointments.create');
Route::post('editorial-appointments', 'EditorialAppointmentController@store')->name('editorial-appointments.store');
Route::get('editorial-appointments/{appointment}', 'EditorialAppointmentController@show')->name('editorial-appointments.show');
Route::post('editorial-appointments/{appointment}/submit', 'EditorialAppointmentController@submit')->name('editorial-appointments.submit');

// Public Student Involvements Routes
Route::get('student-involvements', 'StudentInvolvementController@index')->name('student-involvements.index');
Route::get('student-involvements/load-more', 'StudentInvolvementController@loadMore')->name('student-involvements.load-more');
Route::get('student-involvements/create', 'StudentInvolvementController@create')->name('student-involvements.create');
Route::post('student-involvements', 'StudentInvolvementController@store')->name('student-involvements.store');
Route::get('student-involvements/{involvement}', 'StudentInvolvementController@show')->name('student-involvements.show');
Route::post('student-involvements/{involvement}/submit', 'StudentInvolvementController@submit')->name('student-involvements.submit');

// Public Research Fellows Routes
Route::get('research-fellows', 'ResearchFellowController@index')->name('research-fellows.index');
Route::get('research-fellows/load-more', 'ResearchFellowController@loadMore')->name('research-fellows.load-more');
Route::get('research-fellows/create', 'ResearchFellowController@create')->name('research-fellows.create');
Route::post('research-fellows', 'ResearchFellowController@store')->name('research-fellows.store');
Route::get('research-fellows/{fellow}', 'ResearchFellowController@show')->name('research-fellows.show');
Route::post('research-fellows/{fellow}/submit', 'ResearchFellowController@submit')->name('research-fellows.submit');

// Public SDG Contributions Routes
Route::get('sdg-contributions', 'SdgContributionController@index')->name('sdg-contributions.index');
Route::get('sdg-contributions/load-more', 'SdgContributionController@loadMore')->name('sdg-contributions.load-more');
Route::get('sdg-contributions/create', 'SdgContributionController@create')->name('sdg-contributions.create');
Route::post('sdg-contributions', 'SdgContributionController@store')->name('sdg-contributions.store');
Route::get('sdg-contributions/{contribution}', 'SdgContributionController@show')->name('sdg-contributions.show');
Route::post('sdg-contributions/{contribution}/submit', 'SdgContributionController@submit')->name('sdg-contributions.submit');

// Public Internal Fundings Routes
Route::get('internal-fundings', 'InternalFundingController@index')->name('internal-fundings.index');
Route::get('internal-fundings/load-more', 'InternalFundingController@loadMore')->name('internal-fundings.load-more');
Route::get('internal-fundings/create', 'InternalFundingController@create')->name('internal-fundings.create');
Route::post('internal-fundings', 'InternalFundingController@store')->name('internal-fundings.store');
Route::get('internal-fundings/{funding}', 'InternalFundingController@show')->name('internal-fundings.show');
Route::post('internal-fundings/{funding}/submit', 'InternalFundingController@submit')->name('internal-fundings.submit');

// Public Block Fundings Routes
Route::get('block-fundings', 'BlockFundingController@index')->name('block-fundings.index');
Route::get('block-fundings/load-more', 'BlockFundingController@loadMore')->name('block-fundings.load-more');
Route::get('block-fundings/create', 'BlockFundingController@create')->name('block-fundings.create');
Route::post('block-fundings', 'BlockFundingController@store')->name('block-fundings.store');
Route::get('block-fundings/{funding}', 'BlockFundingController@show')->name('block-fundings.show');
Route::post('block-fundings/{funding}/submit', 'BlockFundingController@submit')->name('block-fundings.submit');

// Public RTN Course Details Routes
Route::get('rtn-course-details', 'RtnCourseDetailController@index')->name('rtn-course-details.index');
Route::get('rtn-course-details/load-more', 'RtnCourseDetailController@loadMore')->name('rtn-course-details.load-more');
Route::get('rtn-course-details/create', 'RtnCourseDetailController@create')->name('rtn-course-details.create');
Route::post('rtn-course-details', 'RtnCourseDetailController@store')->name('rtn-course-details.store');
Route::get('rtn-course-details/{course}', 'RtnCourseDetailController@show')->name('rtn-course-details.show');
Route::post('rtn-course-details/{course}/submit', 'RtnCourseDetailController@submit')->name('rtn-course-details.submit');
Route::post('rtn-course-details/upload-excel', 'RtnCourseDetailController@uploadExcel')->name('rtn-course-details.upload-excel');

// Redirect /home based on user role
Route::get('/home', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        // Faculty members go to their public profile page
        if ($user->hasRole('Faculty') && !$user->isAdmin && !$user->isResearchCoordinator() && !$user->isDean()) {
            if (session('status')) {
                return redirect()->route('faculty-members.show', $user->id)->with('status', session('status'));
            }
            return redirect()->route('faculty-members.show', $user->id);
        }
        
        // Admin, Coordinator, and Dean go to admin dashboard
        if (session('status')) {
            return redirect()->route('admin.home')->with('status', session('status'));
        }
        return redirect()->route('admin.home');
    }
    return redirect()->route('welcome');
});

// Authentication Routes
Auth::routes(['verify' => true]);

// Custom Registration Routes (Multi-step wizard)
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
Route::post('register/validate-step', 'Auth\RegisterController@validateStep')->name('register.validate-step');

// Custom Password Reset Routes
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Email Verification Routes
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', 'block.students', 'require.role']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Demo Dashboard view (Blade-layout based)
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::post('users/{user}/approve', 'UsersController@approve')->name('users.approve');
    Route::post('users/{user}/reject', 'UsersController@reject')->name('users.reject');
    Route::resource('users', 'UsersController');

    // Publications (Admin)
    Route::post('publications/{publication}/approve', 'PublicationController@approve')->name('publications.approve');
    Route::post('publications/{publication}/reject', 'PublicationController@reject')->name('publications.reject');
    Route::delete('publications/destroy', 'PublicationController@massDestroy')->name('publications.massDestroy');
    Route::resource('publications', 'PublicationController');

    // Grants (Admin)
    // Grants (Admin)
    Route::post('grants/{grant}/approve', 'GrantController@approve')->name('grants.approve');
    Route::post('grants/{grant}/reject', 'GrantController@reject')->name('grants.reject');
    Route::resource('grants', 'GrantController');

    // RTN Submissions (Admin)
    Route::post('rtn-submissions/{rtnSubmission}/approve', 'RtnSubmissionController@approve')->name('rtn-submissions.approve');
    Route::post('rtn-submissions/{rtnSubmission}/reject', 'RtnSubmissionController@reject')->name('rtn-submissions.reject');
    Route::resource('rtn-submissions', 'RtnSubmissionController');

    // Bonus Recognitions (Admin)
    Route::post('bonus-recognitions/{bonusRecognition}/approve', 'BonusRecognitionController@approve')->name('bonus-recognitions.approve');
    Route::post('bonus-recognitions/{bonusRecognition}/reject', 'BonusRecognitionController@reject')->name('bonus-recognitions.reject');
    Route::resource('bonus-recognitions', 'BonusRecognitionController');

    // Consultancies & KT
    Route::resource('consultancies', 'ConsultancyController');

    // Commercializations
    Route::resource('commercializations', 'CommercializationController');

    // Partnerships & MOUs (Admin)
    Route::post('partnerships/{partnership}/approve', 'PartnershipController@approve')->name('partnerships.approve');
    Route::post('partnerships/{partnership}/reject', 'PartnershipController@reject')->name('partnerships.reject');
    Route::resource('partnerships', 'PartnershipController');

    // Commercializations (Admin)
    Route::post('commercializations/{commercialization}/approve', 'CommercializationController@approve')->name('commercializations.approve');
    Route::post('commercializations/{commercialization}/reject', 'CommercializationController@reject')->name('commercializations.reject');
    Route::resource('commercializations', 'CommercializationController');

    // Consultancies & KT (Admin)
    Route::post('consultancies/{consultancy}/approve', 'ConsultancyController@approve')->name('consultancies.approve');
    Route::post('consultancies/{consultancy}/reject', 'ConsultancyController@reject')->name('consultancies.reject');
    Route::resource('consultancies', 'ConsultancyController');

    // Awards (Admin)
    Route::post('awards/{award}/approve', 'AwardController@approve')->name('awards.approve');
    Route::post('awards/{award}/reject', 'AwardController@reject')->name('awards.reject');
    Route::resource('awards', 'AwardController');

    // Research Investments (Admin)
    Route::post('research-investments/{investment}/approve', 'ResearchInvestmentController@approve')->name('research-investments.approve');
    Route::post('research-investments/{investment}/reject', 'ResearchInvestmentController@reject')->name('research-investments.reject');
    Route::resource('research-investments', 'ResearchInvestmentController');

    // Conference Activities (Admin)
    Route::post('conference-activities/{activity}/approve', 'ConferenceActivityController@approve')->name('conference-activities.approve');
    Route::post('conference-activities/{activity}/reject', 'ConferenceActivityController@reject')->name('conference-activities.reject');
    Route::resource('conference-activities', 'ConferenceActivityController');

    // Supervision & Exams (Admin)
    Route::post('supervision-exams/{supervision}/approve', 'SupervisionExamController@approve')->name('supervision-exams.approve');
    Route::post('supervision-exams/{supervision}/reject', 'SupervisionExamController@reject')->name('supervision-exams.reject');
    Route::resource('supervision-exams', 'SupervisionExamController');

    // Editorial Appointments (Admin)
    Route::post('editorial-appointments/{appointment}/approve', 'EditorialAppointmentController@approve')->name('editorial-appointments.approve');
    Route::post('editorial-appointments/{appointment}/reject', 'EditorialAppointmentController@reject')->name('editorial-appointments.reject');
    Route::resource('editorial-appointments', 'EditorialAppointmentController');

    // Student Involvements (Admin)
    Route::post('student-involvements/{involvement}/approve', 'StudentInvolvementController@approve')->name('student-involvements.approve');
    Route::post('student-involvements/{involvement}/reject', 'StudentInvolvementController@reject')->name('student-involvements.reject');
    Route::resource('student-involvements', 'StudentInvolvementController');

    // Research Fellows (Admin)
    Route::post('research-fellows/{fellow}/approve', 'ResearchFellowController@approve')->name('research-fellows.approve');
    Route::post('research-fellows/{fellow}/reject', 'ResearchFellowController@reject')->name('research-fellows.reject');
    Route::resource('research-fellows', 'ResearchFellowController');

    // SDG Contributions (Admin)
    Route::post('sdg-contributions/{contribution}/approve', 'SdgContributionController@approve')->name('sdg-contributions.approve');
    Route::post('sdg-contributions/{contribution}/reject', 'SdgContributionController@reject')->name('sdg-contributions.reject');
    Route::resource('sdg-contributions', 'SdgContributionController');

    // Internal Fundings (Admin)
    Route::post('internal-fundings/{funding}/approve', 'InternalFundingController@approve')->name('internal-fundings.approve');
    Route::post('internal-fundings/{funding}/reject', 'InternalFundingController@reject')->name('internal-fundings.reject');
    Route::resource('internal-fundings', 'InternalFundingController');

    // Block Fundings (Admin)
    Route::post('block-fundings/{funding}/approve', 'BlockFundingController@approve')->name('block-fundings.approve');
    Route::post('block-fundings/{funding}/reject', 'BlockFundingController@reject')->name('block-fundings.reject');
    Route::resource('block-fundings', 'BlockFundingController');

    // RTN Course Details (Admin)
    Route::post('rtn-course-details/{course}/approve', 'RtnCourseDetailController@approve')->name('rtn-course-details.approve');
    Route::post('rtn-course-details/{course}/reject', 'RtnCourseDetailController@reject')->name('rtn-course-details.reject');
    Route::post('rtn-course-details/upload-excel', 'RtnCourseDetailController@uploadExcel')->name('rtn-course-details.upload-excel');
    Route::resource('rtn-course-details', 'RtnCourseDetailController');

    // Conference Activities
    Route::resource('conference-activities', 'ConferenceActivityController');

    // Research Investments
    Route::resource('research-investments', 'ResearchInvestmentController');

    // Supervision & Exams
    Route::resource('supervision-exams', 'SupervisionExamController');

    // Editorial Appointments
    Route::resource('editorial-appointments', 'EditorialAppointmentController');

    // Student Involvements
    Route::resource('student-involvements', 'StudentInvolvementController');

    // Internal Fundings
    Route::resource('internal-fundings', 'InternalFundingController');

    // Block Fundings
    Route::resource('block-fundings', 'BlockFundingController');

    // SDG Contributions
    Route::resource('sdg-contributions', 'SdgContributionController');

    // SDG Mappings
    Route::resource('sdg-mappings', 'SdgMappingController');

    // Workflows
    Route::get('workflows/pending', 'WorkflowController@pending')->name('workflows.pending');
    Route::post('workflows/{workflow}/approve', 'WorkflowController@approve')->name('workflows.approve');
    Route::post('workflows/{workflow}/reject', 'WorkflowController@reject')->name('workflows.reject');
    Route::post('workflows/{workflow}/return', 'WorkflowController@return')->name('workflows.return');
    Route::post('workflows/{workflow}/reassign', 'WorkflowController@reassign')->name('workflows.reassign');
    Route::resource('workflows', 'WorkflowController');

    // Scoring Policies
    Route::resource('policies', 'ScoringPolicyController');
    
    // Policy Versions
    Route::post('policy-versions/{policyVersion}/activate', 'PolicyVersionController@activate')->name('policy-versions.activate');
    Route::resource('policy-versions', 'PolicyVersionController');
    
    // Workflow Assignments
    Route::get('workflow-assignments/visualization', 'WorkflowAssignmentController@visualization')->name('workflow-assignments.visualization');
    Route::resource('workflow-assignments', 'WorkflowAssignmentController');

    // Reports (placeholder until controllers are created)
    Route::get('reports', function() {
        return view('admin.reports.index');
    })->name('reports.index');
    Route::get('reports/cv', function() {
        return view('admin.reports.cv');
    })->name('reports.cv');

    // Colleges & Departments
    Route::resource('colleges', 'CollegeController');
    Route::resource('departments', 'DepartmentController');

    // Audit & Activity Logs
    Route::resource('audit-logs', 'AuditLogController')->only(['index', 'show']);
    Route::resource('activity-logs', 'ActivityLogController')->only(['index', 'show']);
    Route::resource('email-logs', 'EmailLogController')->only(['index', 'show']);
    Route::get('email-logs/stats', 'EmailLogController@stats')->name('email-logs.stats');

    // Site Content Management
    Route::resource('sliders', 'SliderController');
    Route::resource('site-contents', 'SiteContentController');
});

// Faculty Portal Routes
Route::group(['prefix' => 'faculty', 'as' => 'faculty.', 'namespace' => 'Faculty', 'middleware' => ['auth', 'require.role']], function () {
    Route::get('/dashboard', function() {
        return redirect()->route('admin.home');
    })->name('dashboard');
    
    // Publications
    Route::get('publications', 'PublicationController@index')->name('publications.index');
    Route::get('publications/all', 'PublicationController@all')->name('publications.all');
    Route::get('publications/create', 'PublicationController@create')->name('publications.create');
    Route::post('publications', 'PublicationController@store')->name('publications.store');
    Route::get('publications/{publication}', 'PublicationController@show')->name('publications.show');
    Route::get('publications/{publication}/edit', 'PublicationController@edit')->name('publications.edit');
    Route::put('publications/{publication}', 'PublicationController@update')->name('publications.update');
    Route::post('publications/{publication}/submit', 'PublicationController@submit')->name('publications.submit');
    
    // Consultancies
    Route::get('consultancies', 'ConsultancyController@index')->name('consultancies.index');
    Route::get('consultancies/create', 'ConsultancyController@create')->name('consultancies.create');
    Route::post('consultancies', 'ConsultancyController@store')->name('consultancies.store');
    
    // Commercializations
    Route::get('commercializations', 'CommercializationController@index')->name('commercializations.index');
    Route::get('commercializations/create', 'CommercializationController@create')->name('commercializations.create');
    Route::post('commercializations', 'CommercializationController@store')->name('commercializations.store');
    
    // Conference Activities
    Route::get('conference-activities', 'ConferenceActivityController@index')->name('conference-activities.index');
    Route::get('conference-activities/create', 'ConferenceActivityController@create')->name('conference-activities.create');
    Route::post('conference-activities', 'ConferenceActivityController@store')->name('conference-activities.store');
});

// Dashboard route (authenticated users)
Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
