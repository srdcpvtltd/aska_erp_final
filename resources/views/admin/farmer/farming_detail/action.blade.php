<div class="btn-group">
    <button class="btn btn-secondary" type="button">{{ __('Action') }}</button>
    <button class="btn btn-secondary dropdown-toggle dropdown-toggle-split" type="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><span class="sr-only">Toggle Dropdown</span></button>
    <div class="dropdown-menu" x-placement="bottom-start">
        <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-expanded="false"><i
                class="fas fa-ellipsis-h"></i></a>
        @if ($farming_detail->is_cutting_order != '1')
            @can('edit-plot')
                <a class="dropdown-item" href="{{ route('admin.farmer.farming_detail.edit', $farming_detail->id) }}"
                    data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                    Edit
                </a>
            @endcan
            @if ($farming_detail->croploss == null)
                <a class="dropdown-item" href="#" data-bs-toggle="tooltip" title="{{ __('Report') }}"
                    class="reportmodal" data-id="{{ $farming_detail->id }}" onclick="reportmodal(this)">
                    Report
                </a>
            @endif
            <a class="dropdown-item" href="#" data-bs-toggle="tooltip" title="{{ __('Report') }}"
                class="reportmodal" data-id="{{ $farming_detail->id }}" onclick="editreportmodal(this)">
                Edit Report
            </a>
            @can('delete-plot')
                <a class="deleteBtn dropdown-item" href="#"
                    data-href="{{ route('admin.farmer.farming_detail.destroy', $farming_detail->id) }}"
                    data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                    Delete
                </a>
            @endcan
        @else
        @endif
    </div>
</div>
