<?php

namespace App\Http\Controllers;

use App\Models\AdjunctProfessor;
use App\Models\ApprovalWorkflow;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class AdjunctProfessorController extends Controller
{
    protected WorkflowService $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function propose()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to propose adjunct professors.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to propose adjunct professors.');
        }

        return view('adjunct-professors.propose');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to propose adjunct professors.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to propose adjunct professors.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'google_scholar' => 'nullable|url|max:500',
            'gs_citation_number' => 'nullable|integer|min:0',
            'gs_h_index' => 'nullable|integer|min:0',
            'gs_papers_2025' => 'nullable|integer|min:0',
            'scopus_scholar' => 'nullable|url|max:500',
            'scopus_citation_number' => 'nullable|integer|min:0',
            'scopus_h_index' => 'nullable|integer|min:0',
            'scopus_papers_2025' => 'nullable|integer|min:0',
            'publication_with_sohar' => 'nullable|integer|min:0',
            'appointment_from' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $professor = AdjunctProfessor::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'google_scholar' => $validated['google_scholar'] ?? null,
            'gs_citation_number' => $validated['gs_citation_number'] ?? null,
            'gs_h_index' => $validated['gs_h_index'] ?? null,
            'gs_papers_2025' => $validated['gs_papers_2025'] ?? null,
            'scopus_scholar' => $validated['scopus_scholar'] ?? null,
            'scopus_citation_number' => $validated['scopus_citation_number'] ?? null,
            'scopus_h_index' => $validated['scopus_h_index'] ?? null,
            'scopus_papers_2025' => $validated['scopus_papers_2025'] ?? null,
            'publication_with_sohar' => $validated['publication_with_sohar'] ?? null,
            'appointment_from' => $validated['appointment_from'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create workflow for proposal
        $this->workflowService->createWorkflow('adjunct_professor', $professor->id, auth()->user());

        return redirect()->route('adjunct-professors.propose')
            ->with('success', 'Adjunct professor proposal submitted successfully! It will be reviewed by the research coordinator.');
    }
}
