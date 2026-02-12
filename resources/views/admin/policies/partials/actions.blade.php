<div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
    <a class="btn btn-sm btn-info" href="{{ route('admin.policies.show', $policy->id) }}" title="View" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
        <i class="fas fa-eye"></i> View
    </a>
    @can('policy_update')
    <a class="btn btn-sm btn-warning" href="{{ route('admin.policies.edit', $policy->id) }}" title="Edit" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
        <i class="fas fa-edit"></i> Edit
    </a>
    @endcan
    @can('policy_delete')
    <form action="{{ route('admin.policies.destroy', $policy->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Delete" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px; background-color: #ef4444; color: white; border: none; cursor: pointer;">
            <i class="fas fa-trash"></i> Delete
        </button>
    </form>
    @endcan
</div>
