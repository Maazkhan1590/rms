<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class SliderController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('slider_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sliders = Slider::ordered()->get();

        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        abort_if(Gate::denies('slider_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('slider_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tag' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url|max:500',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:500',
            'button_text_secondary' => 'nullable|string|max:255',
            'button_link_secondary' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->only([
            'title',
            'description',
            'tag',
            'button_text',
            'button_link',
            'button_text_secondary',
            'button_link_secondary',
            'order',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('sliders', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image_url'] = $request->image_url;
        }

        $data['is_active'] = $request->has('is_active');

        Slider::create($data);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider created successfully.');
    }

    public function show(Slider $slider)
    {
        abort_if(Gate::denies('slider_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.sliders.show', compact('slider'));
    }

    public function edit(Slider $slider)
    {
        abort_if(Gate::denies('slider_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        abort_if(Gate::denies('slider_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tag' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url|max:500',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:500',
            'button_text_secondary' => 'nullable|string|max:255',
            'button_link_secondary' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->only([
            'title',
            'description',
            'tag',
            'button_text',
            'button_link',
            'button_text_secondary',
            'button_link_secondary',
            'order',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($slider->image_url && Storage::disk('public')->exists($slider->image_url)) {
                Storage::disk('public')->delete($slider->image_url);
            }
            $data['image_url'] = $request->file('image')->store('sliders', 'public');
        } elseif ($request->filled('image_url')) {
            // Delete old image if exists and new one is external URL
            if ($slider->image_url && !filter_var($slider->image_url, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($slider->image_url)) {
                Storage::disk('public')->delete($slider->image_url);
            }
            $data['image_url'] = $request->image_url;
        }

        $data['is_active'] = $request->has('is_active');

        $slider->update($data);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider updated successfully.');
    }

    public function destroy(Slider $slider)
    {
        abort_if(Gate::denies('slider_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Delete image if exists
        if ($slider->image_url && !filter_var($slider->image_url, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($slider->image_url)) {
            Storage::disk('public')->delete($slider->image_url);
        }

        $slider->delete();

        return back()->with('success', 'Slider deleted successfully.');
    }
}
