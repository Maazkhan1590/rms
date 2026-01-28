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

// Public Publications Routes
Route::get('publications', 'PublicationController@index')->name('publications.index');
Route::get('publications/load-more', 'PublicationController@loadMore')->name('publications.load-more');
Route::get('publications/create', 'PublicationController@create')->name('publications.create');
Route::post('publications', 'PublicationController@store')->name('publications.store');
Route::get('publications/{id}', 'PublicationController@show')->name('publications.show');
Route::post('publications/{publication}/submit', 'PublicationController@submit')->name('publications.submit');

// Public Grants Routes
Route::get('grants/create', 'GrantController@create')->name('grants.create');
Route::post('grants', 'GrantController@store')->name('grants.store');
Route::get('grants/{grant}', 'GrantController@show')->name('grants.show');
Route::post('grants/{grant}/submit', 'GrantController@submit')->name('grants.submit');

// Public RTN Submissions Routes
Route::get('rtn-submissions/create', 'RtnSubmissionController@create')->name('rtn-submissions.create');
Route::post('rtn-submissions', 'RtnSubmissionController@store')->name('rtn-submissions.store');
Route::get('rtn-submissions/{rtn}', 'RtnSubmissionController@show')->name('rtn-submissions.show');
Route::post('rtn-submissions/{rtn}/submit', 'RtnSubmissionController@submit')->name('rtn-submissions.submit');

// Public Bonus Recognitions Routes
Route::get('bonus-recognitions/create', 'BonusRecognitionController@create')->name('bonus-recognitions.create');
Route::post('bonus-recognitions', 'BonusRecognitionController@store')->name('bonus-recognitions.store');
Route::get('bonus-recognitions/{bonus}', 'BonusRecognitionController@show')->name('bonus-recognitions.show');
Route::post('bonus-recognitions/{bonus}/submit', 'BonusRecognitionController@submit')->name('bonus-recognitions.submit');

// Redirect /home based on user role
Route::get('/home', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        // Faculty members go to public homepage
        if ($user->hasRole('Faculty') && !$user->isAdmin && !$user->isResearchCoordinator() && !$user->isDean()) {
            if (session('status')) {
                return redirect()->route('welcome')->with('status', session('status'));
            }
            return redirect()->route('welcome');
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

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', 'block.students']], function () {
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

    // Partnerships & MOUs
    Route::resource('partnerships', 'PartnershipController');

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
});

// Faculty Portal Routes
Route::group(['prefix' => 'faculty', 'as' => 'faculty.', 'namespace' => 'Faculty', 'middleware' => ['auth']], function () {
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
