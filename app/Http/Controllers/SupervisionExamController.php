<?php

namespace App\Http\Controllers;

use App\Models\SupervisionExam;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class SupervisionExamController extends Controller
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
        $query = SupervisionExam::with(['submitter', 'user'])
            ->where('workflow_status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('thesis_title', 'like', "%{$search}%")
                  ->orWhere('university', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('degree') && $request->degree) {
            $query->where('degree', $request->degree);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('start_year');
                break;
            case 'student':
                $query->orderBy('student_name', 'asc');
                break;
            default:
                $query->latest('start_year');
                break;
        }

        $totalCount = $query->count();
        $supervisions = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'supervisions' => $supervisions->map(function($item) {
                    return [
                        'id' => $item->id,
                        'student_name' => $item->student_name,
                        'thesis_title' => $item->thesis_title,
                        'role' => $item->role,
                        'degree' => $item->degree,
                        'university' => $item->university,
                        'url' => route('supervision-exams.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('supervision-exams.index', compact('supervisions', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = SupervisionExam::with(['submitter', 'user'])
            ->where('workflow_status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('student_name', 'like', "%{$search}%");
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('degree') && $request->degree) {
            $query->where('degree', $request->degree);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('start_year');
                break;
            case 'student':
                $query->orderBy('student_name', 'asc');
                break;
            default:
                $query->latest('start_year');
                break;
        }

        $totalCount = $query->count();
        $supervisions = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('supervision-exams.partials.supervision-card', ['supervisions' => $supervisions])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $supervision = SupervisionExam::with(['submitter', 'user', 'approver', 'evidenceFiles'])
            ->findOrFail($id);

        return view('supervision-exams.show', compact('supervision'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit supervision/exam records.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit supervision/exam records.');
        }

        return view('supervision-exams.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit supervision/exam records.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit supervision/exam records.');
        }

        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'role' => 'required|in:Main,Co-Supervisor,Co,External Examiner,External',
            'degree' => 'required|in:MSc,PhD,MPhil,Other',
            'university' => 'nullable|string|max:255',
            'thesis_title' => 'nullable|string',
            'start_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'end_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'status' => 'required|in:Ongoing,Completed,Discontinued',
            'academic_year' => 'nullable|string|max:255',
            'evidence_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $supervision = SupervisionExam::create([
            'student_name' => $validated['student_name'],
            'role' => $validated['role'],
            'degree' => $validated['degree'],
            'university' => $validated['university'] ?? null,
            'thesis_title' => $validated['thesis_title'] ?? null,
            'start_year' => $validated['start_year'] ?? null,
            'end_year' => $validated['end_year'] ?? null,
            'status' => $validated['status'],
            'academic_year' => $validated['academic_year'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
            'submitted_by' => auth()->id(),
            'workflow_status' => 'draft',
            'evidence_description' => $validated['evidence_description'] ?? null,
            'evidence_required' => true,
            'submitted_at' => now(),
        ]);

        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $this->fileUploadService->uploadEvidenceFile(
                    $file,
                    'supervision_exam',
                    $supervision->id,
                    auth()->id(),
                    'other'
                );
            }
            $supervision->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'supervision_exam',
                        'submission_id' => $supervision->id,
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
                $supervision->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('supervision_exam', $supervision->id, auth()->user());

        return redirect()->route('supervision-exams.show', $supervision->id)
            ->with('success', 'Supervision/Exam record created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, SupervisionExam $supervision)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit supervision/exam records.');
        }

        if ($supervision->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this supervision/exam record.');
        }

        if ($supervision->workflow_status !== 'draft') {
            return redirect()->back()->with('error', 'This supervision/exam record has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'supervision_exam')
            ->where('submission_id', $supervision->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('supervision_exam', $supervision->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $supervision->update([
            'workflow_status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('supervision-exams.show', $supervision->id)
            ->with('success', 'Supervision/Exam record submitted for approval successfully!');
    }
}
