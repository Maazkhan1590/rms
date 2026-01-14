<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkflowAssignment;
use App\Models\User;
use App\Models\College;
use App\Models\Department;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkflowAssignmentController extends Controller
{
    /**
     * Display a listing of workflow assignments
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('workflow_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = WorkflowAssignment::with(['user', 'assigner']);

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // Filter by active status
        if ($request->has('active') && $request->active !== '') {
            $query->where('is_active', $request->active);
        }

        // Filter by college
        if ($request->has('college') && $request->college) {
            $query->where('college', $request->college);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $assignments = $query->latest('assigned_at')->paginate(20);

        // Get filter options
        $roles = ['research_coordinator', 'dean', 'admin'];
        $colleges = College::where('is_active', true)->pluck('name', 'name');
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('title', ['Coordinator', 'Dean', 'Admin']);
        })->pluck('name', 'id');

        return view('admin.workflow-assignments.index', compact('assignments', 'roles', 'colleges', 'users'));
    }

    /**
     * Show the form for creating a new workflow assignment
     */
    public function create()
    {
        abort_if(Gate::denies('workflow_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = ['research_coordinator', 'dean', 'admin'];
        $colleges = College::where('is_active', true)->pluck('name', 'name');
        $departments = Department::where('is_active', true)->pluck('name', 'name');
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('title', ['Coordinator', 'Dean', 'Admin']);
        })->pluck('name', 'id');

        return view('admin.workflow-assignments.create', compact('roles', 'colleges', 'departments', 'users'));
    }

    /**
     * Store a newly created workflow assignment
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('workflow_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:research_coordinator,dean,admin',
            'college' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Check if assignment already exists
        $existing = WorkflowAssignment::where('user_id', $validated['user_id'])
            ->where('role', $validated['role'])
            ->where('college', $validated['college'] ?? null)
            ->where('department', $validated['department'] ?? null)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This workflow assignment already exists.');
        }

        $assignment = WorkflowAssignment::create([
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
            'college' => $validated['college'] ?? null,
            'department' => $validated['department'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);

        return redirect()->route('admin.workflow-assignments.index')
            ->with('success', 'Workflow assignment created successfully.');
    }

    /**
     * Display the specified workflow assignment
     */
    public function show(WorkflowAssignment $workflowAssignment)
    {
        abort_if(Gate::denies('workflow_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $workflowAssignment->load(['user', 'assigner']);

        return view('admin.workflow-assignments.show', compact('workflowAssignment'));
    }

    /**
     * Show the form for editing the specified workflow assignment
     */
    public function edit(WorkflowAssignment $workflowAssignment)
    {
        abort_if(Gate::denies('workflow_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = ['research_coordinator', 'dean', 'admin'];
        $colleges = College::where('is_active', true)->pluck('name', 'name');
        $departments = Department::where('is_active', true)->pluck('name', 'name');
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('title', ['Coordinator', 'Dean', 'Admin']);
        })->pluck('name', 'id');

        return view('admin.workflow-assignments.edit', compact('workflowAssignment', 'roles', 'colleges', 'departments', 'users'));
    }

    /**
     * Update the specified workflow assignment
     */
    public function update(Request $request, WorkflowAssignment $workflowAssignment)
    {
        abort_if(Gate::denies('workflow_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:research_coordinator,dean,admin',
            'college' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $workflowAssignment->update($validated);

        return redirect()->route('admin.workflow-assignments.index')
            ->with('success', 'Workflow assignment updated successfully.');
    }

    /**
     * Remove the specified workflow assignment
     */
    public function destroy(WorkflowAssignment $workflowAssignment)
    {
        abort_if(Gate::denies('workflow_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $workflowAssignment->delete();

        return redirect()->route('admin.workflow-assignments.index')
            ->with('success', 'Workflow assignment deleted successfully.');
    }

    /**
     * Show workflow process visualization
     */
    public function visualization()
    {
        abort_if(Gate::denies('workflow_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Get all active assignments for context
        $assignments = WorkflowAssignment::with(['user'])
            ->where('is_active', true)
            ->latest('assigned_at')
            ->get();

        return view('admin.workflow-assignments.visualization', compact('assignments'));
    }
}
