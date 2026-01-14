<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScoringPolicy;
use App\Models\PolicyVersion;
use App\Models\ScoringRule;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScoringPolicyController extends Controller
{
    /**
     * Display a listing of scoring policies
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('policy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = ScoringPolicy::with(['creator', 'rules', 'policyVersion']);

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by active status
        if ($request->has('active') && $request->active !== '') {
            $query->where('is_active', $request->active);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('subcategory', 'like', "%{$search}%");
            });
        }

        $policies = $query->latest('created_at')->paginate(20);

        // Get filter options
        $types = ['publication', 'grant', 'rtn', 'bonus'];
        $categories = ScoringPolicy::distinct()->pluck('category')->filter()->sort()->values();
        $versions = PolicyVersion::pluck('version_number', 'id');

        return view('admin.policies.index', compact('policies', 'types', 'categories', 'versions'));
    }

    /**
     * Show the form for creating a new scoring policy
     */
    public function create()
    {
        abort_if(Gate::denies('policy_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $types = ['publication', 'grant', 'rtn', 'bonus'];
        $versions = PolicyVersion::orderBy('year', 'desc')->orderBy('version_number', 'desc')->get()->pluck('full_name', 'id');
        $currentYear = now()->year;

        return view('admin.policies.create', compact('types', 'versions', 'currentYear'));
    }

    /**
     * Store a newly created scoring policy
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('policy_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:publication,grant,rtn,bonus',
            'category' => 'nullable|string|max:255',
            'subcategory' => 'nullable|string|max:255',
            'points' => 'required|numeric|min:0',
            'cap' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'version' => 'nullable|string|max:50',
            'policy_version_id' => 'required|exists:policy_versions,id',
        ]);

        $policy = ScoringPolicy::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'category' => $validated['category'] ?? null,
            'subcategory' => $validated['subcategory'] ?? null,
            'points' => $validated['points'],
            'cap' => $validated['cap'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'effective_from' => $validated['effective_from'],
            'effective_to' => $validated['effective_to'] ?? null,
            'version' => $validated['version'] ?? null,
            'policy_version_id' => $validated['policy_version_id'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.policies.index')
            ->with('success', 'Scoring policy created successfully.');
    }

    /**
     * Display the specified scoring policy
     */
    public function show(ScoringPolicy $policy)
    {
        abort_if(Gate::denies('policy_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $policy->load(['creator', 'rules', 'policyVersion']);

        return view('admin.policies.show', compact('policy'));
    }

    /**
     * Show the form for editing the specified scoring policy
     */
    public function edit(ScoringPolicy $policy)
    {
        abort_if(Gate::denies('policy_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $types = ['publication', 'grant', 'rtn', 'bonus'];
        $versions = PolicyVersion::orderBy('year', 'desc')->orderBy('version_number', 'desc')->get()->pluck('full_name', 'id');

        return view('admin.policies.edit', compact('policy', 'types', 'versions'));
    }

    /**
     * Update the specified scoring policy
     */
    public function update(Request $request, ScoringPolicy $policy)
    {
        abort_if(Gate::denies('policy_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:publication,grant,rtn,bonus',
            'category' => 'nullable|string|max:255',
            'subcategory' => 'nullable|string|max:255',
            'points' => 'required|numeric|min:0',
            'cap' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'version' => 'nullable|string|max:50',
            'policy_version_id' => 'required|exists:policy_versions,id',
        ]);

        $policy->update($validated);

        return redirect()->route('admin.policies.index')
            ->with('success', 'Scoring policy updated successfully.');
    }

    /**
     * Remove the specified scoring policy
     */
    public function destroy(ScoringPolicy $policy)
    {
        abort_if(Gate::denies('policy_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $policy->delete();

        return redirect()->route('admin.policies.index')
            ->with('success', 'Scoring policy deleted successfully.');
    }
}
