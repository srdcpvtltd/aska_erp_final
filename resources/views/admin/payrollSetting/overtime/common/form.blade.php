<div class="row">

    <div class="col-lg-4 col-md-6 mb-3">
        <label for="title" class="form-label"> Title <span style="color: red">*</span></label>
        <input type="text"
               class="form-control"
               id="title" step="0.1" min="0" name="title" required
               value="{{ isset($overtime) ? $overtime->title : old('title') }}"
               autocomplete="off"
               placeholder="Enter Max Daily OverTime Hour">
        @error('title')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <label for="max_daily_ot_hours" class="form-label"> Max Daily OverTime Hour <span style="color: red">*</span></label>
        <input type="number"
               class="form-control"
               id="max_daily_ot_hours" step="0.1" min="0" name="max_daily_ot_hours" required
               value="{{ isset($overtime) ? $overtime->max_daily_ot_hours: old('max_daily_ot_hours') }}"
               autocomplete="off"
               placeholder="Enter Max Daily OverTime Hour">
        @error('max_daily_ot_hours')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <label for="max_weekly_ot_hours" class="form-label"> Max Weekly OverTime Hour <span style="color: red">*</span></label>
        <input type="number"
               class="form-control"
               id="max_weekly_ot_hours" step="0.1" min="0" name="max_weekly_ot_hours" required
               value="{{ isset($overtime) ? $overtime->max_weekly_ot_hours: old('max_weekly_ot_hours') }}"
               autocomplete="off"
               placeholder="Enter Max Weekly OverTime Hour">
        @error('max_weekly_ot_hours')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <label for="max_monthly_ot_hours" class="form-label"> Max Monthly OverTime Hour <span style="color: red">*</span></label>
        <input type="number"
               class="form-control"
               id="max_monthly_ot_hours" step="0.1" min="0" name="max_monthly_ot_hours" required
               value="{{ isset($overtime) ? $overtime->max_monthly_ot_hours: old('max_monthly_ot_hours') }}"
               autocomplete="off"
               placeholder="Enter Max Monthly OverTime Hour">
        @error('max_monthly_ot_hours')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <label for="valid_after_hour" class="form-label">OverTime Valid After ? Hour <span style="color: red">*</span></label>
        <input type="number"
               class="form-control"
               id="valid_after_hour" step="0.1" min="0" name="valid_after_hour" required
               value="{{ isset($overtime) ? $overtime->valid_after_hour: old('valid_after_hour') }}"
               autocomplete="off"
               placeholder="Enter Hours After which OT is valid">
        @error('valid_after_hour')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <label for="" class="form-label"> Rate Type <span style="color: red">*</span> </label>
        <select class="col-md-12 form-select" id="pay_type" name="pay_type" required>
            <option selected disabled> Select </option>
            <option {{ (old('pay_type')  || (isset($overtime) && $overtime->pay_type) == 0) ? 'selected' :'' }} value="0">Percent</option>
            <option {{ (old('pay_type')  || (isset($overtime) && $overtime->pay_type) == 1) ? 'selected' :'' }} value="1">Amount</option>
        </select>
    </div>
    <div class="col-lg-6 mb-3 pay_percent {{ (old('pay_type')  || (isset($overtime) && $overtime->pay_type) == 0) ? '' :'d-none' }}" >
        <label for="pay_percent" class="form-label"> OverTime Rate Percent (per hour) <span style="color: red">*</span></label>
        <input type="number"
               class="form-control"
               id="pay_percent" step="0.1" min="0" name="pay_percent"
               value="{{ isset($overtime) ? $overtime->pay_percent: old('pay_percent') }}"
               autocomplete="off"
               placeholder="Enter Under Time Rate percent">
        @error('pay_percent')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="col-lg-6 col-md-6 mb-3 pay_rate {{ (old('pay_type')  || (isset($overtime) && $overtime->pay_type) == 1) ? '' :'d-none' }}">
        <label for="overtime_pay_rate" class="form-label"> OverTime Pay Rate (per hour) <span style="color: red">*</span></label>
        <input type="number"
               class="form-control"
               id="overtime_pay_rate" step="0.1" min="0" name="overtime_pay_rate"
               value="{{ isset($overtime) ? $overtime->overtime_pay_rate: old('overtime_pay_rate') }}"
               autocomplete="off"
               placeholder="Enter OverTime Pay Rate">
        @error('overtime_pay_rate')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
        <label for="" class="form-label"> Assign Employee <span style="color: red">*</span> </label>
        <select class="col-md-12 from-select" id="overTimeEmployee" name="overtime_employee[]" multiple="multiple"
                required>
            @foreach($employees as $key => $value)
                <option value="{{$key}}"
                    {{
                        (isset($overtime) && in_array($key,$overTimeEmployeeId)) ||
                        old('overtime_employee') !== null && in_array($key,old('overtime_employee'))  ? 'selected' : ''
                     }}
                >
                    {{ucfirst($value)}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="is_active" class="form-label"> Status <span style="color: red">*</span></label>
        <input type="radio" class="mx-2" id="is_active_1" {{ (old('is_active') || isset($overtime) && $overtime->is_active == 1)? 'checked' : '' }} name="is_active"  value="1">Active

        <input type="radio" class="mx-2" id="is_active_0" {{ (old('is_active') || isset($overtime) && $overtime->is_active == 0)? 'checked' : '' }} name="is_active" value="0">In-Active
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary ">
            <i class="link-icon" data-feather="{{ isset($overtime) ? 'edit-2':'plus'}}"></i>
            {{ isset($overtime) ? 'Update':'Save' }}
        </button>
    </div>
</div>
