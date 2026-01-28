<?php

namespace App\Http\Controllers;

use App\Models\Grant;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GrantController extends Controller
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
     * Show grant submission form
     */
    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit grants.');
        }

        return view('grants.create');
    }

    /**
     * Store grant submission
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit grants.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'summary' => 'nullable|string',
            'grant_type' => 'required|in:RG,GRG,URG,EJAAD,external_grant,external_matching_grant,grg_urg_advisor,patent_copyright,grant_application,other',
            'role' => 'required|in:PI,Co-PI,Co-I,Advisor,Mentor,Applicant',
            'sponsor_name' => 'nullable|string|max:255',
            'sponsor' => 'nullable|string|max:255',
            'amount_omr' => 'nullable|numeric|min:0',
            'reference_code' => 'nullable|string|max:255',
            'award_year' => 'required|integer|min:1900|max:' . date('Y'),
            'matching_grant_moa' => 'nullable|string|max:255',
            'patent_registration_number' => 'nullable|string|max:255',
            'patent_su_registered' => 'boolean',
            'grant_status' => 'nullable|in:submitted,accepted,ongoing,completed,draft',
            'application_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'amount_received_omr' => 'nullable|numeric|min:0',
            'kt_income' => 'nullable|boolean',
            'sdgs' => 'nullable|array',
            'sdgs.*' => 'nullable|string|max:255',
            'reporting_period' => 'nullable|in:Q1,Q2,Q3,Q4',
            'faculty' => 'nullable|string|max:255',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
        ]);

        $user = auth()->user();
        
        $grant = Grant::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'summary' => $validated['summary'] ?? null,
            'grant_type' => $validated['grant_type'],
            'role' => $validated['role'],
            'sponsor_name' => $validated['sponsor_name'] ?? $validated['sponsor'] ?? null,
            'sponsor' => $validated['sponsor'] ?? null,
            'amount_omr' => $validated['amount_omr'] ?? null,
            'reference_code' => $validated['reference_code'] ?? null,
            'award_year' => $validated['award_year'],
            'submission_year' => date('Y'),
            'matching_grant_moa' => $validated['matching_grant_moa'] ?? null,
            'patent_registration_number' => $validated['patent_registration_number'] ?? null,
            'patent_su_registered' => $validated['patent_su_registered'] ?? false,
            'grant_status' => $validated['grant_status'] ?? 'draft',
            'application_date' => $validated['application_date'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'amount_received_omr' => $validated['amount_received_omr'] ?? null,
            'kt_income' => $validated['kt_income'] ?? false,
            'sdgs' => $validated['sdgs'] ?? null,
            'reporting_period' => $validated['reporting_period'] ?? null,
            'faculty' => $validated['faculty'] ?? ($user->college->name ?? null),
            'submitted_by' => auth()->id(),
            'status' => 'draft',
            'submitted_at' => now(),
        ]);

        // Calculate units if amount_omr is provided
        if ($grant->amount_omr) {
            $grant->calculateUnits();
            $grant->save();
        }

        // Handle evidence file uploads
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $this->fileUploadService->uploadEvidenceFile(
                    $file,
                    'grant',
                    $grant->id,
                    auth()->id(),
                    'other'
                );
            }
            $grant->update(['evidence_uploaded' => true]);
        }

        // Handle evidence URLs
        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'grant',
                        'submission_id' => $grant->id,
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
            if (!empty(array_filter($request->evidence_urls))) {
                $grant->update(['evidence_uploaded' => true]);
            }
        }

        // Create workflow
        $this->workflowService->createWorkflow('grant', $grant->id, auth()->user());

        // Log activity
        $this->loggingService->logActivity(
            'grant_submission',
            "Submitted grant: {$grant->title}",
            Grant::class,
            $grant->id
        );

        // Log audit
        $this->loggingService->logAudit(
            'grant.created',
            $grant,
            null,
            $grant->toArray()
        );

        return redirect()->route('grants.show', $grant->id)
            ->with('success', 'Grant created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    /**
     * Show grant details
     */
    public function show($id)
    {
        $grant = Grant::with(['submitter', 'workflow'])->findOrFail($id);
        
        // Check if user owns this grant
        if ($grant->submitted_by !== auth()->id() && !auth()->user()->hasAnyRole(['Admin', 'Dean', 'Coordinator'])) {
            return redirect()->route('welcome')->with('error', 'You are not authorized to view this grant.');
        }

        return view('grants.show', compact('grant'));
    }

    /**
     * Submit grant for approval
     */
    public function submit(Request $request, Grant $grant)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit grants.');
        }

        if ($grant->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this grant.');
        }

        if ($grant->status !== 'draft') {
            return redirect()->back()->with('error', 'Grant is already submitted or approved.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'grant')
            ->where('submission_id', $grant->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('grant', $grant->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);
        $workflow->refresh();

        // Update grant status based on workflow status
        $grant->update([
            'status' => $workflow->status == 'pending_coordinator' ? 'pending_coordinator' : 
                       ($workflow->status == 'pending_dean' ? 'pending_dean' : 'submitted'),
            'submitted_at' => now(),
        ]);

        // Log activity
        $this->loggingService->logActivity(
            'grant_submitted',
            "Submitted grant for approval: {$grant->title}",
            Grant::class,
            $grant->id
        );

        return redirect()->route('grants.show', $grant->id)
            ->with('success', 'Grant submitted for approval successfully!');
    }
}
