<div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
    <a class="btn btn-sm btn-info" href="{{ route('admin.policy-versions.show', $version->id) }}" title="View" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
        <i class="fas fa-eye"></i> View
    </a>
    @can('policy_update')
    <a class="btn btn-sm btn-warning" href="{{ route('admin.policy-versions.edit', $version->id) }}" title="Edit" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
        <i class="fas fa-edit"></i> Edit
    </a>
    @if(!$version->is_active)
    <form action="{{ route('admin.policy-versions.activate', $version->id) }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-sm btn-success" title="Activate" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px; background-color: #22c55e; color: white; border: none; cursor: pointer;">
            <i class="fas fa-check"></i> Activate
        </button>
    </form>
    @endif
    @endcan
</div>
