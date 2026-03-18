<?php

namespace App\Http\Controllers;

use App\Models\RtnCourseDetail;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Support\ValidationRules;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RtnCourseDetailController extends Controller
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
        $query = RtnCourseDetail::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('rtn_type') && $request->rtn_type) {
            $query->where('rtn_type', $request->rtn_type);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('year');
                break;
            case 'course':
                $query->orderBy('course_code', 'asc');
                break;
            default:
                $query->latest('year');
                break;
        }

        $totalCount = $query->count();
        $courses = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'courses' => $courses->map(function($item) {
                    return [
                        'id' => $item->id,
                        'course_code' => $item->course_code,
                        'course_name' => $item->course_name,
                        'rtn_type' => $item->rtn_type,
                        'year' => $item->year,
                        'url' => route('rtn-course-details.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('rtn-course-details.index', compact('courses', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = RtnCourseDetail::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('course_code', 'like', "%{$search}%");
        }

        if ($request->has('rtn_type') && $request->rtn_type) {
            $query->where('rtn_type', $request->rtn_type);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('year');
                break;
            case 'course':
                $query->orderBy('course_code', 'asc');
                break;
            default:
                $query->latest('year');
                break;
        }

        $totalCount = $query->count();
        $courses = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('rtn-course-details.partials.course-card', ['courses' => $courses])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $course = RtnCourseDetail::with([
            'submitter',
            'user',
            'approver',
            'evidenceFiles',
            'workflow.history.performer',
            'workflow.submitter',
            'workflow.assignee',
        ])->findOrFail($id);

        return view('rtn-course-details.show', compact('course'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit RTN course details.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit RTN course details.');
        }

        return view('rtn-course-details.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit RTN course details.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit RTN course details.');
        }

        $validated = $request->validate([
            'course_code' => ValidationRules::referenceCode(required: true, max: 50),
            'course_name' => ValidationRules::title(required: true, max: 255),
            'rtn_type' => 'required|in:RTN_3,RTN_4,RTN_5,RTN_6,other',
            'evidence_link' => ValidationRules::url(required: false, max: 500),
            'notes' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => ValidationRules::url(required: false, max: 500),
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $course = RtnCourseDetail::create([
            'course_code' => $validated['course_code'],
            'course_name' => $validated['course_name'],
            'rtn_type' => $validated['rtn_type'],
            'evidence_link' => $validated['evidence_link'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'year' => $validated['year'] ?? date('Y'),
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
                    'rtn_course_detail',
                    $course->id,
                    auth()->id(),
                    'other'
                );
            }
            $course->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'rtn_course_detail',
                        'submission_id' => $course->id,
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
                $course->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('rtn_course_detail', $course->id, auth()->user());

        return redirect()->route('rtn-course-details.show', $course->id)
            ->with('success', 'RTN course detail created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, RtnCourseDetail $course)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit RTN course details.');
        }

        if ($course->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this RTN course detail.');
        }

        if ($course->status !== 'draft') {
            return redirect()->back()->with('error', 'This RTN course detail has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'rtn_course_detail')
            ->where('submission_id', $course->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('rtn_course_detail', $course->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $course->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('rtn-course-details.show', $course->id)
            ->with('success', 'RTN course detail submitted for approval successfully!');
    }

    public function uploadExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to upload RTN course details.');
        }

        if (!auth()->user()->hasRole(['Research Coordinator', 'Dean', 'Research Director'])) {
            return redirect()->back()->with('error', 'Only coordinators, deans, and research directors can upload Excel files.');
        }

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $imported = 0;
            $errors = [];

            // Skip header row (assuming first row is headers)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                if (empty($row[0]) || empty($row[1])) {
                    continue; // Skip empty rows
                }

                try {
                    $course = RtnCourseDetail::create([
                        'course_code' => $row[0] ?? '',
                        'course_name' => $row[1] ?? '',
                        'rtn_type' => $row[2] ?? 'RTN_3',
                        'year' => $row[3] ?? date('Y'),
                        'notes' => $row[4] ?? null,
                        'user_id' => auth()->id(),
                        'submitted_by' => auth()->id(),
                        'status' => 'draft',
                        'evidence_required' => true,
                        'submitted_at' => now(),
                    ]);

                    $this->workflowService->createWorkflow('rtn_course_detail', $course->id, auth()->user());
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($i + 1) . ": " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} RTN course details.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->route('rtn-course-details.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing Excel file: ' . $e->getMessage());
        }
    }
}
