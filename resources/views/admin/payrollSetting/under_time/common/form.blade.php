<div class="row">

    <div class="col-lg-6 col-md-6 mb-3">
        <label for="title" class="form-label"> Title <span style="color: red">*</span></label>
        <input type="text"
               class="form-control"
               id="title" step="0.1" min="0" name="title" required
               value="{{ isset($underTime) ? $underTime->title: old('title') }}"
               autocomplete="off"
               placeholder="Enter Title">
        @error('title')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
        <label for="applied_after_minutes" class="form-label"> Minutes After which Under Time is Applied <span style="color: red">*</span></label>
        <input type="number"
               class="form-control"
               id="applied_after_minutes" step="0.1" min="0" name="applied_after_minutes" required
               value="{{ isset($underTime) ?$underTime->applied_after_minutes: old('applied_after_minutes') }}"
               autocomplete="off"
               placeholder="Enter Hours after which UT is applied">
        @error('applied_after_minutes')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
        <label for="penalty_type" class="form-label"> Penalty Type <span style="color: red">*</span> </label>
        <select class="col-md-12 form-select penalty_type" id="penalty_type" name="penalty_type" required>
                <option selected disabled> Select </option>
                <option {{ (old('penalty_type') || (isset($underTime) && $underTime->penalty_type) == 0) ? 'selected' :'' }} value="0">Percent</option>
                <option {{ (old('penalty_type') || (isset($underTime) && $underTime->penalty_type) == 1) ? 'selected' :'' }} value="1">Amount</option>
        </select>
    </div>
    <div class="col-lg-6 col-md-6 mb-3 penalty_percent {{ (old('penalty_type') ||isset($underTime) && $underTime->penalty_type == 0) ? '' :'d-none' }}">
        <label for="penalty_percent" class="form-label"> Penalty Percent (per hour) <span style="color: red">*</span></label>
        <input type="number"
               class="form-control"
               id="penalty_percent" step="0.1" min="0" name="penalty_percent"
               value="{{ isset($underTime) ? $underTime->penalty_percent: old('penalty_percent') }}"
               autocomplete="off"
               placeholder="Enter Under Time Penalty Percent">
        @error('penalty_percent')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="col-lg-6 col-md-6 mb-3 penalty_rate {{ (old('penalty_type') || isset($underTime) && $underTime->penalty_type == 1) ? '' :'d-none' }}" >
        <label for="ut_penalty_rate" class="form-label">Penalty Rate (per hour) <span style="color: red">*</span></label>
        <input type="number"
               class="form-control"
               id="ut_penalty_rate" step="0.1" min="0" name="ut_penalty_rate"
               value="{{ isset($underTime) ?$underTime->ut_penalty_rate: old('ut_penalty_rate') }}"
               autocomplete="off"
               placeholder="Enter Under Time Penalty Rate">
        @error('ut_penalty_rate')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>



{{--    <div class="col-lg-12 mb-3">--}}
{{--        <label for="" class="form-label"> Assign Employee <span style="color: red">*</span> </label>--}}
{{--        <select class="col-md-12 form-select" id="underTimeEmployee" name="undertime_employee[]" multiple="multiple"--}}
{{--                required>--}}
{{--            @foreach($employees as $key => $value)--}}
{{--                <option value="{{$key}}"--}}
{{--                    {{--}}
{{--                        (isset($underTime) && in_array($key,$underTimeEmployeeId)) ||--}}
{{--                        old('undertime_employee') !== null && in_array($key,old('undertime_employee'))  ? 'selected' : ''--}}
{{--                     }}--}}
{{--                >--}}
{{--                    {{ucfirst($value)}}--}}
{{--                </option>--}}
{{--            @endforeach--}}
{{--        </select>--}}
{{--    </div>--}}

    <div class="col-lg-6 col-md-6 mb-3">
        <label for="is_active" class="form-label"> Status <span style="color: red">*</span></label>
        <input type="radio" class="mx-2" id="is_active_1" {{ (old('is_active') || (isset($underTime) && $underTime->is_active) == 1)? 'checked' : '' }} name="is_active"  value="1">Active

        <input type="radio" class="mx-2" id="is_active_0" {{ (old('is_active') || (isset($underTime) && $underTime->is_active) == 0)? 'checked' : '' }} name="is_active" value="0">In-Active
    </div>
    
    <div class="col-12">
        <button type="submit" class="btn btn-primary ">
            <i class="link-icon" data-feather="{{ isset($underTime) ? 'edit-2':'plus'}}"></i>
            {{ isset($underTime) ? 'Update':'Save' }}
        </button>
    </div>
</div>
