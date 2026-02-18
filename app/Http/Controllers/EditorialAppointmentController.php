<?php

namespace App\Http\Controllers;

use App\Models\EditorialAppointment;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class EditorialAppointmentController extends Controller
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
        $query = EditorialAppointment::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('journal_conference', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('start_date');
                break;
            case 'journal':
                $query->orderBy('journal_conference', 'asc');
                break;
            default:
                $query->latest('start_date');
                break;
        }

        $totalCount = $query->count();
        $appointments = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'appointments' => $appointments->map(function($item) {
                    return [
                        'id' => $item->id,
                        'journal_conference' => $item->journal_conference,
                        'role' => $item->role,
                        'start_date' => $item->start_date ? $item->start_date->format('F d, Y') : null,
                        'url' => route('editorial-appointments.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('editorial-appointments.index', compact('appointments', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = EditorialAppointment::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('journal_conference', 'like', "%{$search}%");
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('start_date');
                break;
            case 'journal':
                $query->orderBy('journal_conference', 'asc');
                break;
            default:
                $query->latest('start_date');
                break;
        }

        $totalCount = $query->count();
        $appointments = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('editorial-appointments.partials.appointment-card', ['appointments' => $appointments])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $appointment = EditorialAppointment::with(['submitter', 'user', 'approver', 'evidenceFiles'])
            ->findOrFail($id);

        return view('editorial-appointments.show', compact('appointment'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit editorial appointments.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit editorial appointments.');
        }

        return view('editorial-appointments.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit editorial appointments.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit editorial appointments.');
        }

        $validated = $request->validate([
            'journal_conference' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'evidence_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $appointment = EditorialAppointment::create([
            'journal_conference' => $validated['journal_conference'],
            'role' => $validated['role'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'year' => $validated['year'] ?? ($validated['start_date'] ? date('Y', strtotime($validated['start_date'])) : date('Y')),
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
                    'editorial_appointment',
                    $appointment->id,
                    auth()->id(),
                    'other'
                );
            }
            $appointment->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'editorial_appointment',
                        'submission_id' => $appointment->id,
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
                $appointment->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('editorial_appointment', $appointment->id, auth()->user());

        return redirect()->route('editorial-appointments.show', $appointment->id)
            ->with('success', 'Editorial appointment created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, EditorialAppointment $appointment)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit editorial appointments.');
        }

        if ($appointment->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this editorial appointment.');
        }

        if ($appointment->status !== 'draft') {
            return redirect()->back()->with('error', 'This editorial appointment has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'editorial_appointment')
            ->where('submission_id', $appointment->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('editorial_appointment', $appointment->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $appointment->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('editorial-appointments.show', $appointment->id)
            ->with('success', 'Editorial appointment submitted for approval successfully!');
    }
}
