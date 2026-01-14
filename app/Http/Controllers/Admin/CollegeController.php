<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CollegeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('college_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $colleges = College::with(['dean', 'departments'])->get();

        return view('admin.colleges.index', compact('colleges'));
    }

    public function create()
    {
        abort_if(Gate::denies('college_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deans = User::whereHas('roles', function($query) {
            $query->where('title', 'Dean');
        })->pluck('name', 'id');

        return view('admin.colleges.create', compact('deans'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('college_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'code' => 'required|string|max:255|unique:colleges,code',
            'name' => 'required|string|max:255',
            'dean_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        College::create([
            'code' => $request->code,
            'name' => $request->name,
            'dean_id' => $request->dean_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.colleges.index')
            ->with('success', 'College created successfully.');
    }

    public function show(College $college)
    {
        abort_if(Gate::denies('college_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $college->load(['dean', 'departments', 'users']);

        return view('admin.colleges.show', compact('college'));
    }

    public function edit(College $college)
    {
        abort_if(Gate::denies('college_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deans = User::whereHas('roles', function($query) {
            $query->where('title', 'Dean');
        })->pluck('name', 'id');

        return view('admin.colleges.edit', compact('college', 'deans'));
    }

    public function update(Request $request, College $college)
    {
        abort_if(Gate::denies('college_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'code' => 'required|string|max:255|unique:colleges,code,' . $college->id,
            'name' => 'required|string|max:255',
            'dean_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $college->update([
            'code' => $request->code,
            'name' => $request->name,
            'dean_id' => $request->dean_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.colleges.index')
            ->with('success', 'College updated successfully.');
    }

    public function destroy(College $college)
    {
        abort_if(Gate::denies('college_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $college->delete();

        return back()->with('success', 'College deleted successfully.');
    }
}

