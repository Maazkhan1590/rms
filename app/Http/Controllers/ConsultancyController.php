<?php

namespace App\Http\Controllers;

use App\Models\Consultancy;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class ConsultancyController extends Controller
{
    protected WorkflowService $workflowService;
    protected FileUploadService $fileUploadService;

    public function __construct(WorkflowService $workflowService, FileUploadService $fileUploadService)
    {
        $this->workflowService = $workflowService;
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request)
    {
        $query = Consultancy::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_consultancy_name', 'like', "%{$search}%")
                  ->orWhere('client_sponsor', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('income_type', $request->type);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('start_date');
                break;
            case 'name':
                $query->orderBy('project_consultancy_name', 'asc');
                break;
            default:
                $query->latest('start_date');
                break;
        }

        $totalCount = $query->count();
        $consultancies = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'consultancies' => $consultancies->map(function($item) {
                    return [
                        'id' => $item->id,
                        'project_consultancy_name' => $item->project_consultancy_name,
                        'client_sponsor' => $item->client_sponsor,
                        'amount_omr' => $item->amount_omr,
                        'url' => route('consultancies.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('consultancies.index', compact('consultancies', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = Consultancy::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('project_consultancy_name', 'like', "%{$search}%");
        }

        if ($request->has('type') && $request->type) {
            $query->where('income_type', $request->type);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('start_date');
                break;
            case 'name':
                $query->orderBy('project_consultancy_name', 'asc');
                break;
            default:
                $query->latest('start_date');
                break;
        }

        $totalCount = $query->count();
        $consultancies = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('consultancies.partials.consultancy-card', ['consultancies' => $consultancies])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $consultancy = Consultancy::with(['submitter', 'user', 'approver', 'evidenceFiles'])
            ->findOrFail($id);

        return view('consultancies.show', compact('consultancy'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit consultancies.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit consultancies.');
        }

        return view('consultancies.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit consultancies.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit consultancies.');
        }

        $validated = $request->validate([
            'project_consultancy_name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'client_sponsor' => 'nullable|string|max:255',
            'amount_omr' => 'nullable|numeric|min:0',
            'income_type' => 'nullable|in:consultancy,service,product',
            'lead_staff' => 'nullable|string|max:255',
            'evidence_link' => 'nullable|url|max:500',
            'sdg_s' => 'nullable|string|max:255',
            'reporting_period' => 'nullable|in:q1,q2,q3,q4',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'commercialized' => 'boolean',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $consultancy = Consultancy::create([
            'project_consultancy_name' => $validated['project_consultancy_name'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'client_sponsor' => $validated['client_sponsor'] ?? null,
            'amount_omr' => $validated['amount_omr'] ?? null,
            'income_type' => $validated['income_type'] ?? null,
            'lead_staff' => $validated['lead_staff'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'sdg_s' => $validated['sdg_s'] ?? null,
            'reporting_period' => $validated['reporting_period'] ?? null,
            'year' => $validated['year'] ?? ($validated['start_date'] ? date('Y', strtotime($validated['start_date'])) : date('Y')),
            'commercialized' => $request->has('commercialized') ? true : false,
            'user_id' => auth()->id(),
            'submitted_by' => auth()->id(),
            'status' => 'draft',
            'evidence_description' => $validated['evidence_description'] ?? null,
            'evidence_required' => true,
            'submitted_at' => now(),
        ]);

        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $this->fileUploadService->uploadEvidenceFile(
                    $file,
                    'consultancy',
                    $consultancy->id,
                    auth()->id(),
                    'other'
                );
            }
            $consultancy->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'consultancy',
                        'submission_id' => $consultancy->id,
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
                $consultancy->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('consultancy', $consultancy->id, auth()->user());

        return redirect()->route('consultancies.show', $consultancy->id)
            ->with('success', 'Consultancy/KT created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, Consultancy $consultancy)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit consultancies.');
        }

        if ($consultancy->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this consultancy.');
        }

        if ($consultancy->status !== 'draft') {
            return redirect()->back()->with('error', 'This consultancy has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'consultancy')
            ->where('submission_id', $consultancy->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('consultancy', $consultancy->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $consultancy->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('consultancies.show', $consultancy->id)
            ->with('success', 'Consultancy/KT submitted for approval successfully!');
    }
}
