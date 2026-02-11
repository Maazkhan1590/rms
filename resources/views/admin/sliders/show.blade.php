@extends('layouts.admin')

@section('page-title', 'View Slider')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Slider Details</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl>
                    <dt>Title</dt>
                    <dd>{{ $slider->title ?? 'N/A' }}</dd>

                    <dt>Tag</dt>
                    <dd>{{ $slider->tag ?? 'N/A' }}</dd>

                    <dt>Description</dt>
                    <dd>{{ $slider->description ?? 'N/A' }}</dd>

                    <dt>Order</dt>
                    <dd>{{ $slider->order }}</dd>

                    <dt>Status</dt>
                    <dd>
                        @if($slider->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </dd>
                </dl>
            </div>
            <div class="col-md-6">
                @if($slider->image_url)
                    <label>Image</label>
                    <div>
                        @if(filter_var($slider->image_url, FILTER_VALIDATE_URL))
                            <img src="{{ $slider->image_url }}" alt="Slider" class="img-fluid" style="max-height: 300px; border-radius: 4px;">
                        @else
                            <img src="{{ asset('storage/' . $slider->image_url) }}" alt="Slider" class="img-fluid" style="max-height: 300px; border-radius: 4px;">
                        @endif
                    </div>
                @endif

                @if($slider->button_text)
                    <dl class="mt-3">
                        <dt>Primary Button</dt>
                        <dd>{{ $slider->button_text }} → {{ $slider->button_link ?? 'N/A' }}</dd>

                        @if($slider->button_text_secondary)
                            <dt>Secondary Button</dt>
                            <dd>{{ $slider->button_text_secondary }} → {{ $slider->button_link_secondary ?? 'N/A' }}</dd>
                        @endif
                    </dl>
                @endif
            </div>
        </div>

        <div class="mt-4">
            @can('slider_update')
            <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
            <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection
