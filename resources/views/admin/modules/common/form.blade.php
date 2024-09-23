<div class="row">
    <div class="col-lg-6 col-md-6 mb-4">
        <label for="name" class="form-label"> Name <span style="color: red">*</span> </label>
        <input type="text" class="form-control" id="name" required name="name"
            value="{{ isset($module) ? $module->name : '' }}" autocomplete="off" placeholder="">
    </div>
    @if (!isset($module))
        <div class="form-group row">
            {{ Form::label('permissions', __('Permissions'), ['class' => 'col-md-3 col-form-label']) }}
            <div class="col-md-9 col-form-label">
                <div class="form-check form-check-inline mr-1">
                    {{ Form::checkbox('permissions[]', 'M'), ['class' => 'form-check-label', 'id' => 'inline-checkbox1'] }}
                    {{ Form::label('manage', __('Manage'), ['class' => 'form-check-label']) }}
                </div>
                <div class="form-check form-check-inline mr-1">
                    {{ Form::checkbox('permissions[]', 'C'), ['class' => 'form-check-label', 'id' => 'inline-checkbox2'] }}
                    {{ Form::label('create', __('Create'), ['class' => 'form-check-label']) }}
                </div>
                <div class="form-check form-check-inline mr-1">
                    {{ Form::checkbox('permissions[]', 'D'), ['class' => 'form-check-label', 'id' => 'inline-checkbox3'] }}
                    {{ Form::label('delete', __('Delete'), ['class' => 'form-check-label']) }}
                </div>
                <div class="form-check form-check-inline mr-1">
                    {{ Form::checkbox('permissions[]', 'S'), ['class' => 'form-check-label', 'id' => 'inline-checkbox4'] }}
                    {{ Form::label('show', __('Show'), ['class' => 'form-check-label']) }}
                </div>
                <div class="form-check form-check-inline mr-1">
                    {{ Form::checkbox('permissions[]', 'E'), ['class' => 'form-check-label', 'id' => 'inline-checkbox5'] }}
                    {{ Form::label('edit', __('Edit'), ['class' => 'form-check-label']) }}
                </div>
            </div>
        </div>
    @endif
    <div class="col-lg-6 col-md-6 text-start mb-4 mt-md-4">
        <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i>
            {{ isset($module) ? 'Update' : 'Create' }} Modules</button>
    </div>
</div>
