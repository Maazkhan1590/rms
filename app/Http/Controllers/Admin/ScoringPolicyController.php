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

        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        return view('admin.policies.index');
    }

    /**
     * Get DataTables data for scoring policies
     */
    private function getDataTableData(Request $request)
    {
        $query = ScoringPolicy::with(['creator', 'rules', 'policyVersion']);

        // Get DataTables parameters
        $start = $request->input('start', 0);
        $length = $request->input('length', 15);
        $search = $request->input('search.value');

        // Global search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('subcategory', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Get total count before pagination
        $totalRecords = $query->count();

        // Ordering
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'name', 'type', 'category', 'subcategory', 'points', 'cap', 'policy_version_id', 'effective_from', 'is_active', 'rules', 'actions'];
        $orderBy = $columns[$orderColumn] ?? 'id';
        
        if ($orderBy === 'policy_version_id') {
            $query->leftJoin('policy_versions', 'scoring_policies.policy_version_id', '=', 'policy_versions.id')
                  ->orderBy('policy_versions.version_number', $orderDir)
                  ->select('scoring_policies.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        // Pagination
        $policies = $query->skip($start)->take($length)->get();
        $policies->load(['creator', 'rules', 'policyVersion']);

        // Format data for DataTables
        $data = [];
        foreach ($policies as $policy) {
            $data[] = [
                'id' => $policy->id,
                'name' => $policy->name,
                'type' => ucfirst($policy->type),
                'category' => $policy->category ?? '-',
                'subcategory' => $policy->subcategory ?? '-',
                'points' => number_format($policy->points, 2),
                'cap' => $policy->cap ? number_format($policy->cap, 2) : 'No cap',
                'policy_version' => $policy->policyVersion ? $policy->policyVersion->version_number . ' (' . $policy->policyVersion->year . ')' : 'Not assigned',
                'effective_period' => $policy->effective_from->format('M Y') . ($policy->effective_to ? ' → ' . $policy->effective_to->format('M Y') : ' → Ongoing'),
                'status' => $policy->is_active ? 'Active' : 'Inactive',
                'rules_count' => $policy->rules->count(),
                'actions' => view('admin.policies.partials.actions', compact('policy'))->render(),
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
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
