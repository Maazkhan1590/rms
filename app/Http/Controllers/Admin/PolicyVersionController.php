<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PolicyVersion;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PolicyVersionController extends Controller
{
    /**
     * Display a listing of policy versions
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('policy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = PolicyVersion::with(['creator', 'scoringPolicies']);

        // Filter by active status
        if ($request->has('active') && $request->active !== '') {
            $query->where('is_active', $request->active);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('version_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $versions = $query->latest('year')->latest('created_at')->paginate(20);

        return view('admin.policy-versions.index', compact('versions'));
    }

    /**
     * Show the form for creating a new policy version
     */
    public function create()
    {
        abort_if(Gate::denies('policy_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentYear = now()->year;
        $existingVersions = PolicyVersion::pluck('version_number')->toArray();

        return view('admin.policy-versions.create', compact('currentYear', 'existingVersions'));
    }

    /**
     * Store a newly created policy version
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('policy_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'version_number' => 'required|string|max:50|unique:policy_versions,version_number',
            'year' => 'required|integer|min:2000|max:' . (now()->year + 10),
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // If this is set as active, deactivate other versions
        if ($validated['is_active'] ?? false) {
            PolicyVersion::where('is_active', true)->update(['is_active' => false]);
        }

        $version = PolicyVersion::create([
            'version_number' => $validated['version_number'],
            'year' => $validated['year'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? false,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.policy-versions.index')
            ->with('success', 'Policy version created successfully.');
    }

    /**
     * Display the specified policy version
     */
    public function show(PolicyVersion $policyVersion)
    {
        abort_if(Gate::denies('policy_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $policyVersion->load(['creator', 'scoringPolicies']);

        return view('admin.policy-versions.show', compact('policyVersion'));
    }

    /**
     * Show the form for editing the specified policy version
     */
    public function edit(PolicyVersion $policyVersion)
    {
        abort_if(Gate::denies('policy_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.policy-versions.edit', compact('policyVersion'));
    }

    /**
     * Update the specified policy version
     */
    public function update(Request $request, PolicyVersion $policyVersion)
    {
        abort_if(Gate::denies('policy_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'version_number' => 'required|string|max:50|unique:policy_versions,version_number,' . $policyVersion->id,
            'year' => 'required|integer|min:2000|max:' . (now()->year + 10),
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // If this is set as active, deactivate other versions
        if ($validated['is_active'] ?? false) {
            PolicyVersion::where('is_active', true)
                ->where('id', '!=', $policyVersion->id)
                ->update(['is_active' => false]);
        }

        $policyVersion->update($validated);

        return redirect()->route('admin.policy-versions.index')
            ->with('success', 'Policy version updated successfully.');
    }

    /**
     * Remove the specified policy version
     */
    public function destroy(PolicyVersion $policyVersion)
    {
        abort_if(Gate::denies('policy_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Check if version has policies
        if ($policyVersion->scoringPolicies()->count() > 0) {
            return redirect()->route('admin.policy-versions.index')
                ->with('error', 'Cannot delete policy version that has scoring policies associated with it.');
        }

        $policyVersion->delete();

        return redirect()->route('admin.policy-versions.index')
            ->with('success', 'Policy version deleted successfully.');
    }

    /**
     * Activate a policy version
     */
    public function activate(PolicyVersion $policyVersion)
    {
        abort_if(Gate::denies('policy_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Deactivate all other versions
        PolicyVersion::where('is_active', true)
            ->where('id', '!=', $policyVersion->id)
            ->update(['is_active' => false]);

        $policyVersion->update(['is_active' => true]);

        return redirect()->route('admin.policy-versions.index')
            ->with('success', 'Policy version activated successfully.');
    }
}
