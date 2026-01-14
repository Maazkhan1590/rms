<?php

use App\Http\Controllers\Api\V1\Admin\LoginController;
use App\Http\Controllers\Api\V1\Admin\SignupController;
use App\Http\Controllers\Api\V1\PublicationController;
use App\Http\Controllers\Api\V1\GrantController;
use App\Http\Controllers\Api\V1\RtnSubmissionController;
use App\Http\Controllers\Api\V1\BonusRecognitionController;
use App\Http\Controllers\Api\V1\WorkflowController;

// Public routes
Route::post('/user/login', [LoginController::class, 'login'])->name('user-login');
Route::post('/user/signup', [SignupController::class, 'signup'])->name('user-signup');

// Authenticated API routes
Route::group(['prefix' => 'v1', 'as' => 'api.', 'middleware' => ['auth:sanctum']], function () {
    // Auth
    Route::post('/logout', [LoginController::class, 'logout']);
    
    // Publications
    Route::apiResource('publications', PublicationController::class);
    Route::post('publications/{publication}/submit', [PublicationController::class, 'submit'])->name('publications.submit');
    
    // Grants
    Route::apiResource('grants', GrantController::class);
    Route::post('grants/{grant}/submit', [GrantController::class, 'submit'])->name('grants.submit');
    
    // RTN Submissions
    Route::apiResource('rtn-submissions', RtnSubmissionController::class);
    Route::post('rtn-submissions/{rtnSubmission}/submit', [RtnSubmissionController::class, 'submit'])->name('rtn-submissions.submit');
    
    // Bonus Recognitions
    Route::apiResource('bonus-recognitions', BonusRecognitionController::class);
    Route::post('bonus-recognitions/{bonusRecognition}/submit', [BonusRecognitionController::class, 'submit'])->name('bonus-recognitions.submit');
    
    // Workflows
    Route::get('workflows/pending', [WorkflowController::class, 'pending'])->name('workflows.pending');
    Route::post('workflows/{workflow}/approve', [WorkflowController::class, 'approve'])->name('workflows.approve');
    Route::post('workflows/{workflow}/reject', [WorkflowController::class, 'reject'])->name('workflows.reject');
    Route::post('workflows/{workflow}/return', [WorkflowController::class, 'return'])->name('workflows.return');
    Route::get('workflows/{workflow}/history', [WorkflowController::class, 'history'])->name('workflows.history');
});

// Public API routes (no auth required)
Route::group(['prefix' => 'v1', 'as' => 'api.'], function () {
    Route::get('colleges/{college}/departments', [\App\Http\Controllers\Api\V1\CollegeController::class, 'departments'])->where('college', '[0-9]+');
});
