<?php

namespace App\Http\Controllers;

use App\Models\StudentInvolvement;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class StudentInvolvementController extends Controller
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
        $query = StudentInvolvement::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('date');
                break;
            case 'category':
                $query->orderBy('category', 'asc');
                break;
            default:
                $query->latest('date');
                break;
        }

        $totalCount = $query->count();
        $involvements = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'involvements' => $involvements->map(function($item) {
                    return [
                        'id' => $item->id,
                        'category' => $item->category,
                        'count' => $item->count,
                        'date' => $item->date ? $item->date->format('F d, Y') : null,
                        'url' => route('student-involvements.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('student-involvements.index', compact('involvements', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = StudentInvolvement::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('category', 'like', "%{$search}%");
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('date');
                break;
            case 'category':
                $query->orderBy('category', 'asc');
                break;
            default:
                $query->latest('date');
                break;
        }

        $totalCount = $query->count();
        $involvements = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('student-involvements.partials.involvement-card', ['involvements' => $involvements])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $involvement = StudentInvolvement::with(['submitter', 'user', 'approver', 'evidenceFiles'])
            ->findOrFail($id);

        return view('student-involvements.show', compact('involvement'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit student involvements.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit student involvements.');
        }

        return view('student-involvements.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit student involvements.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit student involvements.');
        }

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'count' => 'required|integer|min:1',
            'date' => 'nullable|date',
            'academic_year' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'evidence_link' => 'nullable|url|max:500',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $involvement = StudentInvolvement::create([
            'category' => $validated['category'],
            'count' => $validated['count'],
            'date' => $validated['date'] ?? null,
            'academic_year' => $validated['academic_year'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
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
                    'student_involvement',
                    $involvement->id,
                    auth()->id(),
                    'other'
                );
            }
            $involvement->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'student_involvement',
                        'submission_id' => $involvement->id,
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
                $involvement->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('student_involvement', $involvement->id, auth()->user());

        return redirect()->route('student-involvements.show', $involvement->id)
            ->with('success', 'Student involvement created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, StudentInvolvement $involvement)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit student involvements.');
        }

        if ($involvement->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this student involvement.');
        }

        if ($involvement->status !== 'draft') {
            return redirect()->back()->with('error', 'This student involvement has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'student_involvement')
            ->where('submission_id', $involvement->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('student_involvement', $involvement->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $involvement->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('student-involvements.show', $involvement->id)
            ->with('success', 'Student involvement submitted for approval successfully!');
    }
}
