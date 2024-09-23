<div class="row">

    <div class="col-lg-6 mb-3">
        <label for="title" class="form-label"> Name <span style="color: red">*</span></label>
        <input type="text"
               class="form-control"
               id="name" name="name" required
               value="{{ ( isset($salaryComponentDetail) ?  $salaryComponentDetail->name: old('name') )}}"
               autocomplete="off"
               placeholder="Enter Salary Component Name">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="component_type" class="form-label">Component Type <span style="color: red">*</span></label>
        <select class="form-select" id="component_type" name="component_type" required >
            <option value="" {{isset($salaryComponentDetail) || old('component_type') ? '' : 'selected'}}  disabled>Select Component Type</option>
            @foreach(\App\Models\SalaryComponent::COMPONENT_TYPE as $key => $value)
                <option value="{{$key}}"
                    {{ isset($salaryComponentDetail) && ($salaryComponentDetail->component_type ) == $key || old('component_type') == $key ? 'selected': '' }}>
                    {{ucfirst($value)}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="value_type" class="form-label">Value Type <span style="color: red">*</span></label>
        <select class="form-select" id="value_type" name="value_type" required >
            <option value="" {{isset($salaryComponentDetail) || old('value_type') ? '' : 'selected'}}  disabled>Select Value Type</option>
            @foreach(\App\Models\SalaryComponent::VALUE_TYPE as $key => $value)
                <option value="{{$key}}"
                    {{ isset($salaryComponentDetail) && ($salaryComponentDetail->value_type ) == $key || old('value_type') == $key ? 'selected': '' }}>
                    {{ucfirst($value)}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="component_value_monthly" class="form-label">Component Value(monthly) <span style="color: red">*</span> </label>
        <input type="number" min="0" step="0.1" class="form-control" id="component_value_monthly" name="component_value_monthly"
               required
               value="{{ (isset( $salaryComponentDetail) ?  $salaryComponentDetail->component_value_monthly: old('component_value_monthly') )}}"
               autocomplete="off"
        >
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary ">
            <i class="link-icon" data-feather="{{isset($salaryComponentDetail)? 'edit-2':'plus'}}"></i>
            {{isset($salaryComponentDetail) ? 'Update':'Add'}}
        </button>
    </div>
</div>







