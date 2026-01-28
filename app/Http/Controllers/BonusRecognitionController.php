<?php

namespace App\Http\Controllers;

use App\Models\BonusRecognition;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class BonusRecognitionController extends Controller
{
    protected WorkflowService $workflowService;
    protected LoggingService $loggingService;
    protected FileUploadService $fileUploadService;

    public function __construct(WorkflowService $workflowService, LoggingService $loggingService, FileUploadService $fileUploadService)
    {
        $this->workflowService = $workflowService;
        $this->loggingService = $loggingService;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Show bonus recognition submission form
     */
    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit bonus recognitions.');
        }

        return view('bonus-recognitions.create');
    }

    /**
     * Store bonus recognition submission
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit bonus recognitions.');
        }

        $validated = $request->validate([
            'recognition_type' => 'required|in:editorial_board,external_examiner,regulatory_body,workshop_seminar,keynote_plenary,journal_reviewer',
            'title' => 'required|string|max:500',
            'organization' => 'nullable|string|max:255',
            'journal_conference_name' => 'nullable|string|max:255',
            'event_name' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
        ]);

        $bonus = BonusRecognition::create([
            'recognition_type' => $validated['recognition_type'],
            'title' => $validated['title'],
            'organization' => $validated['organization'] ?? null,
            'journal_conference_name' => $validated['journal_conference_name'] ?? null,
            'event_name' => $validated['event_name'] ?? null,
            'year' => $validated['year'],
            'submission_year' => date('Y'),
            'user_id' => auth()->id(),
            'status' => 'draft',
            'description' => $validated['description'] ?? null,
            'submitted_at' => now(),
        ]);

        // Handle evidence file uploads
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $this->fileUploadService->uploadEvidenceFile(
                    $file,
                    'bonus',
                    $bonus->id,
                    auth()->id(),
                    'other'
                );
            }
        }

        // Handle evidence URLs
        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'bonus',
                        'submission_id' => $bonus->id,
                        'file_path' => $url,
                        'file_name' => 'URL: ' . $url,
                        'file_type' => 'text/url',
                        'file_size' => 0,
                        'file_category' => 'other',
                        'uploaded_by' => auth()->id(),
                        'uploaded_at' => now(),
                    ]);
                }
            }
        }

        // Create workflow
        $this->workflowService->createWorkflow('bonus', $bonus->id, auth()->user());

        // Log activity
        $this->loggingService->logActivity(
            'bonus_submission',
            "Submitted bonus recognition: {$bonus->title}",
            BonusRecognition::class,
            $bonus->id
        );

        // Log audit
        $this->loggingService->logAudit(
            'bonus.created',
            $bonus,
            null,
            $bonus->toArray()
        );

        return redirect()->route('bonus-recognitions.show', $bonus->id)
            ->with('success', 'Bonus recognition created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    /**
     * Show bonus recognition details
     */
    public function show($id)
    {
        $bonus = BonusRecognition::with(['user', 'workflow'])->findOrFail($id);
        
        if ($bonus->user_id !== auth()->id() && !auth()->user()->hasAnyRole(['Admin', 'Dean', 'Coordinator'])) {
            return redirect()->route('welcome')->with('error', 'You are not authorized to view this bonus recognition.');
        }

        return view('bonus-recognitions.show', compact('bonus'));
    }

    /**
     * Submit bonus recognition for approval
     */
    public function submit(Request $request, BonusRecognition $bonus)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit bonus recognitions.');
        }

        if ($bonus->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this bonus recognition.');
        }

        if ($bonus->status !== 'draft') {
            return redirect()->back()->with('error', 'Bonus recognition is already submitted or approved.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'bonus')
            ->where('submission_id', $bonus->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('bonus', $bonus->id, auth()->user());
        }

        $this->workflowService->submitWorkflow($workflow);

        $bonus->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Log activity
        $this->loggingService->logActivity(
            'bonus_submitted',
            "Submitted bonus recognition for approval: {$bonus->title}",
            BonusRecognition::class,
            $bonus->id
        );

        return redirect()->route('bonus-recognitions.show', $bonus->id)
            ->with('success', 'Bonus recognition submitted for approval successfully!');
    }
}
