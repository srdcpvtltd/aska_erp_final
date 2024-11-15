<div class="btn-group">
    <button class="btn btn-secondary" type="button">{{ __('Action') }}</button>
    <button class="btn btn-secondary dropdown-toggle dropdown-toggle-split" type="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><span class="sr-only">Toggle Dropdown</span></button>
    <div class="dropdown-menu" x-placement="bottom-start">
        <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-expanded="false"><i
                class="fas fa-ellipsis-h"></i></a>

        @if ($farming->is_validate != 0)
            @can('show-farmer_registration')
                <a href="{{ route('admin.farmer.farming_registration.show', $farming->id) }}" class="dropdown-item">
                    <i class="link-icon" data-feather="eye"></i>View
                </a>
            @endcan
        @else
            @if ($farming->created_by == Auth::user()->id)
                <a href="{{ route('admin.farmer.farming_registration.validate', $farming->id) }}" class="dropdown-item">
                    <i class="link-icon" data-feather="check-square"></i>Validate
                </a>
                <div class="dropdown-divider"></div>
            @endif
            @can('edit-farmer_registration')
                <a href="{{ route('admin.farmer.farming_registration.edit', $farming->id) }}" class="dropdown-item">
                    <i class="link-icon" data-feather="edit"></i>Edit
                </a>
            @endcan
            <div class="dropdown-divider"></div>
            @can('delete-farmer_registration')
                <a href="#" class="deleteBtn dropdown-item"
                    data-href="{{ route('admin.farmer.farming_registration.destroy', $farming->id) }}"
                    data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                    <i class="link-icon" data-feather="delete"></i>Delete
                </a>
            @endcan
        @endif
    </div>
</div>
