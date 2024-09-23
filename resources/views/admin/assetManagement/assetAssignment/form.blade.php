<div class="row">

    <div class="col-lg-4 col-md-6 mb-4">
        <label for="type" class="form-label">Asset Type <span style="color: red">*</span></label>
        <select class="form-select" id="type" name="type_id" required>
            <option value="" {{isset($assetDetail) ? '': 'selected'}} disabled>Select Type</option>
            @foreach($assetType as $key => $value)
            <option value="{{$value->id}}" {{ isset($assetDetail) && ($assetDetail->type_id ) == $value->id || old('type_id') == $value->id ? 'selected': '' }}>
                {{ucfirst($value->name)}}
            </option>
            @endforeach
        </select>
    </div>

    <!-- asset -->
    <div class="col-lg-4 col-md-6 mb-4">
        <label for="asset" class="form-label">Asset</label>
        <select class="form-select" id="assign_asset" name="asset">

        </select>
    </div>


    <div class="col-lg-4 col-md-6 mb-4">
        <label for="assigned_to" class="form-label">Assign To </label>
        <select class="form-select" id="assigned_to" name="assigned_to">
            <option value="" {{isset($assetDetail) || old('assigned_to') ? '': 'selected'}}>Select Employee</option>
            @foreach($employees as $key => $value)
            <option value="{{$value->id}}" {{ isset($assetDetail) && ($assetDetail->assigned_to ) == $value->id || old('assigned_to') == $value->id ? 'selected': old('assigned_to') }}>
                {{ucfirst($value->name)}}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <label for="purchased_date" class="form-label">Assign Date<span style="color: red">*</span></label>
        <input type="date" class="form-control" id="assign_date" name="assign_date" value="{{ ( isset($assetDetail) ? ($assetDetail->purchased_date): old('purchased_date') )}}" required autocomplete="off">
    </div>

    @canany(['edit-asset_management','create-asset_management'])
    <div class="text-start">
        <button type="submit" class="btn btn-primary">
            <i class="link-icon" data-feather="plus"></i>
            {{isset($assetDetail)? 'Update':'Assign'}}
        </button>
    </div>
    @endcanany
</div>