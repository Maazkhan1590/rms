<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('college_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $departments = Department::with(['college', 'coordinator'])->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        abort_if(Gate::denies('college_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $colleges = College::where('is_active', true)->pluck('name', 'id');
        $coordinators = User::whereHas('roles', function($query) {
            $query->where('title', 'Coordinator');
        })->pluck('name', 'id');

        return view('admin.departments.create', compact('colleges', 'coordinators'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('college_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'code' => 'required|string|max:255|unique:departments,code',
            'name' => 'required|string|max:255',
            'coordinator_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        Department::create([
            'college_id' => $request->college_id,
            'code' => $request->code,
            'name' => $request->name,
            'coordinator_id' => $request->coordinator_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        abort_if(Gate::denies('college_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $department->load(['college', 'coordinator', 'users']);

        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        abort_if(Gate::denies('college_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $colleges = College::where('is_active', true)->pluck('name', 'id');
        $coordinators = User::whereHas('roles', function($query) {
            $query->where('title', 'Coordinator');
        })->pluck('name', 'id');

        return view('admin.departments.edit', compact('department', 'colleges', 'coordinators'));
    }

    public function update(Request $request, Department $department)
    {
        abort_if(Gate::denies('college_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'code' => 'required|string|max:255|unique:departments,code,' . $department->id,
            'name' => 'required|string|max:255',
            'coordinator_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $department->update([
            'college_id' => $request->college_id,
            'code' => $request->code,
            'name' => $request->name,
            'coordinator_id' => $request->coordinator_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        abort_if(Gate::denies('college_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $department->delete();

        return back()->with('success', 'Department deleted successfully.');
    }
}

