<?php

namespace App\Http\Controllers;

use App\Models\Grant;
use App\Models\ApprovalWorkflow;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GrantController extends Controller
{
    protected WorkflowService $workflowService;
    protected LoggingService $loggingService;

    public function __construct(WorkflowService $workflowService, LoggingService $loggingService)
    {
        $this->workflowService = $workflowService;
        $this->loggingService = $loggingService;
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
            'grant_type' => 'required|in:external_grant,external_matching_grant,grg_urg_advisor,patent_copyright,grant_application',
            'role' => 'required|in:PI,Co-PI,Co-I,Advisor,Mentor,Applicant',
            'sponsor_name' => 'nullable|string|max:255',
            'sponsor' => 'nullable|string|max:255',
            'amount_omr' => 'nullable|numeric|min:0',
            'reference_code' => 'nullable|string|max:255',
            'award_year' => 'required|integer|min:1900|max:' . date('Y'),
            'matching_grant_moa' => 'nullable|string|max:255',
            'patent_registration_number' => 'nullable|string|max:255',
            'patent_su_registered' => 'boolean',
        ]);

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
            'submitted_by' => auth()->id(),
            'status' => 'draft',
            'submitted_at' => now(),
        ]);

        // Calculate units if amount_omr is provided
        if ($grant->amount_omr) {
            $grant->calculateUnits();
            $grant->save();
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

        $this->workflowService->submitWorkflow($workflow);

        $grant->update([
            'status' => 'submitted',
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
