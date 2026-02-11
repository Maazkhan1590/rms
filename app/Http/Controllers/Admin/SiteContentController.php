<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteContentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('site_content_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = SiteContent::query();

        // Filter by section if provided
        if ($request->has('section') && $request->section) {
            $query->where('section', $request->section);
        }

        $siteContents = $query->ordered()->get();
        $sections = SiteContent::distinct()->pluck('section');

        return view('admin.site-contents.index', compact('siteContents', 'sections'));
    }

    public function create()
    {
        abort_if(Gate::denies('site_content_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.site-contents.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('site_content_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'key' => 'required|string|max:255|unique:site_contents,key',
            'type' => 'required|in:text,html,url,email,phone',
            'value' => 'nullable|string',
            'label' => 'nullable|string|max:255',
            'section' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        SiteContent::create([
            'key' => $request->key,
            'type' => $request->type,
            'value' => $request->value,
            'label' => $request->label,
            'section' => $request->section,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.site-contents.index')
            ->with('success', 'Site content created successfully.');
    }

    public function show(SiteContent $siteContent)
    {
        abort_if(Gate::denies('site_content_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.site-contents.show', compact('siteContent'));
    }

    public function edit(SiteContent $siteContent)
    {
        abort_if(Gate::denies('site_content_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.site-contents.edit', compact('siteContent'));
    }

    public function update(Request $request, SiteContent $siteContent)
    {
        abort_if(Gate::denies('site_content_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'key' => 'required|string|max:255|unique:site_contents,key,' . $siteContent->id,
            'type' => 'required|in:text,html,url,email,phone',
            'value' => 'nullable|string',
            'label' => 'nullable|string|max:255',
            'section' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $siteContent->update([
            'key' => $request->key,
            'type' => $request->type,
            'value' => $request->value,
            'label' => $request->label,
            'section' => $request->section,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.site-contents.index')
            ->with('success', 'Site content updated successfully.');
    }

    public function destroy(SiteContent $siteContent)
    {
        abort_if(Gate::denies('site_content_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $siteContent->delete();

        return back()->with('success', 'Site content deleted successfully.');
    }
}
