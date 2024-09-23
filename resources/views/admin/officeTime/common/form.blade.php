<div class="row">
    <div class="col-lg-6 col-md-6 mb-3">
        <label for="shift" class="form-label">Shift <span style="color: red">*</span></label>
        <select class="form-select" id="exampleFormControlSelect1" name="shift" required>
            <option value="" {{isset($officeTime) ? '': 'selected'}} disabled>Select Shift</option>
            @foreach($shift as $value)
                <option
                    value="{{ $value }}" {{ (isset($officeTime) && ($officeTime->shift ) == $value) ? 'selected':old('shift') }} >{{ ucfirst($value) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-6 col-md-6 mb-3">
        <label for="shift" class="form-label">Category</label>
        <select class="form-select" id="exampleFormControlSelect1" name="category">
            <option value="" disabled>Select Category</option>
            @foreach($category as $value)
                <option
                    value="{{ $value }}" {{ (isset($officeTime) && ($officeTime->category ) == $value) ? 'selected':old('category') }} >{{ removeSpecialChars($value) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
        <label for="opening_time" class="form-label"> Opening Time <span style="color: red">*</span></label>
        <input type="time" class="form-control" id="opening_time" name="opening_time" required
               value="{{ ( isset($officeTime) ? convertTimeFormat($officeTime->opening_time): old('opening_time') )}}"
               autocomplete="off" placeholder="">
    </div>

    <div class="col-lg-6 col-md-6 mb-3">
        <label for="closing_time" class="form-label"> Closing Time <span style="color: red">*</span></label>
        <input type="time" class="form-control" id="closing_time" name="closing_time" required
               value="{{ ( isset($officeTime) ? convertTimeFormat($officeTime->closing_time): old('closing_time') )}}"
               autocomplete="off" placeholder="">
    </div>
</div>
<div class="row">

    <div class="col-lg-12 mb-4">
        <h5> CheckIn/Out Rule</h5>
    </div>

    <div class="col-lg-12">
        <div class="row late_rule">
            <div class="col-md-6">
                <div class="row">

                    <div class="col-lg-12 mb-3">
                        <span class="form-check form-switch">
                            <input id="is_early_check_in" type="checkbox" @if( (isset($officeTime) && $officeTime->is_early_check_in == 1) || (old('is_early_check_in') == 1) ) checked @endif name="is_early_check_in" value="1"
                                class="form-check-input change-status-toggle">
                            <label for="is_early_check_in" class="form-label"> Early Check In</label>
                        </span>
                    </div>
                    <div class="col-lg-12 mb-3 @if( isset($officeTime) && $officeTime->is_early_check_in == 1 ) @else d-none @endif" id="earlyCheckIn">
                        <label for="checkin_before" class="form-label"> Can check in before (in minute)</label>
                        <input type="number" id="before_start" class="form-control numeric" name="checkin_before"
                            value="{{ ( isset($officeTime) ? $officeTime->checkin_before: old('checkin_before') )}}" placeholder="Enter Can check in before (in minute)">
                        <span class="text-danger"></span>
                    </div>
                    <div class="col-lg-12 mb-3">
                        <span class="form-check form-switch">
                            <input id="is_early_check_out" type="checkbox" @if( (isset($officeTime) && $officeTime->is_early_check_out == 1) || (old('is_early_check_out') == 1) ) checked @endif name="is_early_check_out" value="1"
                                class="form-check-input change-status-toggle">
                            <label for="is_early_check_out" class="form-label"> Early Check out</label>
                        </span>
                    </div>

                    <div class="col-lg-12 mb-3 @if( isset($officeTime) && $officeTime->is_early_check_out == 1 ) @else d-none @endif" id="earlyCheckOut">
                        <label for="checkout_before" class="form-label"> Can check out before (in minute)</label>
                        <input type="number" id="checkout_before" class="form-control numeric" name="checkout_before"
                            value="{{ isset($officeTime) ? $officeTime->checkout_before : old('checkout_before') }}" placeholder="Enter Can check out before (in minute)">
                        <span class="text-danger"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <span class="form-check form-switch">
                            <input id="is_late_check_in" type="checkbox" @if( (isset($officeTime) && $officeTime->is_late_check_in == 1) || (old('is_late_check_in') == 1) ) checked @endif name="is_late_check_in" value="1"
                                class="form-check-input change-status-toggle">
                            <label for="is_late_check_in" class="form-label"> Late Check In</label>
                        </span>
                    </div>

                    <div class="col-lg-12 mb-3 @if( isset($officeTime) && $officeTime->is_late_check_in == 1 ) @else d-none @endif" id="lateCheckIn">
                        <label for="checkin_after" class="form-label"> Can check in after (in minute)</label>
                        <input type="number" id="checkin_after" class="form-control numeric" name="checkin_after"
                            value="{{ isset($officeTime) ? $officeTime->checkin_after : old('checkin_after') }}" placeholder="Enter Can check in after (in minute)">
                        <span class="text-danger"></span>
                    </div>
                    <div class="col-lg-12 mb-3">
                        <span class="form-check form-switch">
                            <input id="is_late_check_out" type="checkbox" @if( (isset($officeTime) && $officeTime->is_late_check_out == 1) || (old('is_late_check_out') == 1) ) checked @endif name="is_late_check_out" value="1"
                                class="form-check-input change-status-toggle">
                            <label for="is_late_check_out" class="form-label"> Late Check out</label>
                        </span>
                    </div>
                    <div class="col-lg-12 mb-3 @if( isset($officeTime) && $officeTime->is_late_check_out == 1 ) @else d-none @endif" id="lateCheckOut">
                        <label for="checkout_after" class="form-label"> Can check out after (in minute)</label>
                        <input type="number" id="checkout_after" class="form-control numeric" name="checkout_after"
                            value="{{ isset($officeTime) ? $officeTime->checkout_after : old('checkout_after') }}" placeholder="Enter Can check out after (in minute)">
                        <span class="text-danger"></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

{{--    <div class="col-lg-4 mb-3">--}}
{{--        <label for="exampleFormControlSelect1" class="form-label">Status</label>--}}
{{--        <select class="form-select" id="exampleFormControlSelect1" name="is_active">--}}
{{--            <option value="" {{isset($officeTime) ? '': 'selected'}} disabled>Select status</option>--}}
{{--            <option value="1" @selected( old('is_active', isset($officeTime) && $officeTime->is_active ) === 1)>Active--}}
{{--            </option>--}}
{{--            <option value="0" @selected( old('is_active', isset($officeTime) && $officeTime->is_active ) === 0)>--}}
{{--                Inactive--}}
{{--            </option>--}}
{{--        </select>--}}
{{--    </div>--}}
    {{--    <div class="col-lg-4 mb-3">--}}
    {{--        <label for="holiday" class="form-label">Weekly Holiday Count</label>--}}
    {{--        <input type="number" min="0" class="form-control" id="holiday_count" name="holiday_count" value="{{ ( isset($officeTime) ? $officeTime->holiday_count: old('holiday_count') )}}" autocomplete="off" placeholder="">--}}
    {{--    </div>--}}

{{--    <div class="col-lg-6 mb-3">--}}
{{--        <label for="description" class="form-label">Description</label>--}}
{{--        <textarea class="form-control" name="description" id="tinymceExample"--}}
{{--                  rows="10">{{ ( isset($officeTime) ? $officeTime->description: old('description') )}}</textarea>--}}
{{--    </div>--}}


    <div class="text-start">
        <button type="submit" class="btn btn-primary"><i class="link-icon"
                                                         data-feather="{{isset($officeTime)? 'edit-2':'plus'}}"></i> {{isset($officeTime)? 'Update':'Create'}}
            Office Time
        </button>
    </div>
</div>
