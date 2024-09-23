@php use App\Models\User; @endphp
@php use App\Models\EmployeeAccount; @endphp
<div class="mb-2"><small>All (<span style="color: red">*</span>) fields are required </small></div>
<style>
    .is-invalid {
        border-color: red !important;
    }

    .is-invalid + .error-message {
        display: block;
        color: red !important;
    }

    .error-message {
        display: none;
        color: red !important;
    }
</style>
<div class="card mb-4">
    <div class="card-body pb-2">
        <div class="profile-detail">
            <h5 class="mb-3 border-bottom pb-3">Personal Detail</h5>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="employee_code" class="form-label"> Employee Code</label>
                    <input type="text" class="form-control"
                           id="employee_code"
                           name="employee_code"
                           readonly
                           style="pointer-events: none;"
                           value="{{ ( isset($userDetail) ? $userDetail->employee_code: $employeeCode )}}" autocomplete="off"
                           placeholder="Enter name" required>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="name" class="form-label"> Name <span style="color: red">*</span></label>
                    <input type="text" class="form-control"
                           id="name"
                           name="name"
                           value="{{ ( isset($userDetail) ? $userDetail->name: old('name') )}}" autocomplete="off"
                           placeholder="Enter name" required>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="address" class="form-label"> Address <span style="color: red">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="address"
                           name="address"
                           value="{{ (isset($userDetail) ? ($userDetail->address): old('address'))}}"
                           autocomplete="off" placeholder="Enter Employee Address" required>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span style="color: red">*</span></label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ ( isset($userDetail) ? $userDetail->email: old('email') )}}" required
                           autocomplete="off" placeholder="Enter email">
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="number" class="form-label">Phone No <span style="color: red">*</span></label>
                    <input type="number" class="form-control" id="phone" name="phone"
                           value="{{ isset($userDetail)? $userDetail->phone: old('phone') }}" required
                           autocomplete="off" placeholder="">
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="dob" class="form-label"> Date Of Birth <span style="color: red">*</span></label>
                    <input type="date" class="form-control" id="dob" name="dob"
                           value="{{ ( isset($userDetail) ? ($userDetail->dob): old('dob') )}}" required
                           autocomplete="off" placeholder="">
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="gender" class="form-label">Gender <span style="color: red">*</span></label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="" {{isset($userDetail) || old('gender') ? '' : 'selected'}}  disabled>Select
                            Gender
                        </option>
                        @foreach(User::GENDER as $value)
                            <option
                                value="{{$value}}" {{ isset($userDetail) && ($userDetail->gender ) == $value || old('gender') == $value ? 'selected': '' }}>
                                {{ucfirst($value)}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="marital_status" class="form-label">Marital Status <span
                            style="color: red">*</span></label>
                    <select class="form-select" id="marital_status" name="marital_status" required>
                        <option value="" {{isset($userDetail) || old('marital_status') ? '' : 'selected'}}  disabled>
                            Choose Marital Status
                        </option>
                        @foreach(User::MARITAL_STATUS as $value)
                            <option value="{{ $value }}"
                                {{ isset($userDetail) && ($userDetail->marital_status ) == $value || old('marital_status') == $value ? 'selected': '' }}>
                                {{ucfirst($value)}}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="col-lg-4 mb-3">
                    <label for="avatar" class="form-label">Upload Avatar <span style="color: red">*</span> </label>
                    <input class="form-control"
                           type="file"
                           id="avatar"
                           name="avatar"
                           accept="image/*"
                           value="{{ isset($userDetail) ? $userDetail->avatar: old('avatar') }}" {{isset($userDetail) ? '': 'required'}} >

                    <img class="mt-2 rounded {{(isset($userDetail) && $userDetail->avatar) ? '': 'd-none'}}"
                         id="image-preview"
                         src="{{ (isset($userDetail) && $userDetail->avatar) ? asset(User::AVATAR_UPLOAD_PATH.$userDetail->avatar) : ''}}"
                         style="object-fit: contain"
                         width="100"
                         height="100"
                    >
                </div>
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 mb-3 empl-desc">
                            <label for="remarks" class="form-label">Description</label>
                            <textarea class="form-control" name="remarks" id="tinymceExample"
                                    rows="2">{{ ( isset($userDetail) ? $userDetail->remarks: old('remarks') )}}</textarea>
                        </div>

                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-12 col-md-4 mb-3">
                                    <label for="username" class="form-label">Username <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="{{ ( isset($userDetail) ? $userDetail->username: old('username') )}}"
                                        required
                                        autocomplete="off" placeholder="Enter username">
                                </div>
                                @if(!isset($userDetail))
                                    <div class="col-lg-12 col-md-4 mb-3">
                                        <label for="password" class="form-label">Password <span style="color: red">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            value="{{old('password')}}" autocomplete="off" placeholder="Enter  password" required>
                                    </div>
                                @endif

                                <div class="col-lg-12 col-md-4 mb-3">
                                    <label for="role" class="form-label">Role <span style="color: red">*</span></label>
                                    <select class="form-select" id="role" name="role_id" required>
                                        <option value="" {{isset($userDetail) || old('role_id')  ? '': 'selected'}}  disabled>Select
                                            Role
                                        </option>
                                        @if($roles)
                                            @foreach($roles as $key =>  $value)
                                                <option value="{{$value->id}}"
                                                    {{ isset($userDetail) && ($userDetail->role_id ) == $value->id  || old('role_id') == $value->id ? 'selected': '' }}> {{ucfirst($value->name)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body pb-2">
        <div class="company-detail">
            <h5 class="mb-3 border-bottom pb-3">Company Detail</h5>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="branch_id" class="form-label">Branch <span style="color: red">*</span></label>
                    <select class="form-select" id="branch" name="branch_id" required>
                        <option value="" {{!isset($userDetail) || old('branch_id') ? 'selected': ''}}  disabled>Select
                            Branch
                        </option>
                        @if(isset($companyDetail))
                            @foreach($companyDetail->branches()->get() as $key => $branch)
                                <option value="{{$branch->id}}"
                                    {{ isset($userDetail) && ($userDetail->branch_id ) == $branch->id || old('branch_id') == $branch->id ? 'selected': '' }}>
                                    {{ucfirst($branch->name)}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="department" class="form-label">Departments <span style="color: red">*</span></label>
                    <select class="form-select" id="department" name="department_id" required>

                    </select>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="post" class="form-label">Post <span style="color: red">*</span></label>
                    <select class="form-select" id="post" name="post_id" required>

                    </select>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="supervisor" class="form-label">Supervisor <span style="color: red">*</span></label>
                    <select class="form-select" id="supervisor" name="supervisor_id">

                    </select>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="employment_type" class="form-label">Employment Type <span style="color: red">*</span>
                    </label>
                    <select class="form-select" id="employment_type" name="employment_type" required>
                        <option value="" {{isset($userDetail) || old('employment_type') ? '': 'selected'}}  disabled>
                            select employment type
                        </option>
                        @foreach(User::EMPLOYMENT_TYPE as $value)
                            <option
                                value="{{$value}}" {{ isset($userDetail) && ($userDetail->employment_type ) == $value || old('employment_type') == $value ? 'selected': '' }}>
                                {{ucfirst($value)}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="officeTime" class="form-label">Office Time <span style="color: red">*</span></label>
                    <select class="form-select" id="officeTime" name="office_time_id" required>

                    </select>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="joining_date" class="form-label"> Joining Date</label>
                    <input type="date" class="form-control" id="joining_date" name="joining_date"
                           value="{{(isset($userDetail) ? ($userDetail->joining_date): old('joining_date') )}}"
                           autocomplete="off"
                           placeholder="Enter Joining Date">
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="workspace_type" class="form-label">WorkSpace</label>
                    <select class="form-select" id="workspace_type" name="workspace_type">
                        <option value="" {{isset($userDetail) || old('workspace_type') ? '': 'selected'}}  disabled>
                            select work place
                        </option>
                        <option value="{{User::FIELD}}"
                            {{ isset($userDetail) && ($userDetail->workspace_type ) == User::FIELD || old('workspace_type') == User::FIELD ? 'selected': '' }}>
                            Field
                        </option>
                        <option value="{{User::OFFICE}}"
                            {{ isset($userDetail) && ($userDetail->workspace_type ) == User::OFFICE || old('workspace_type') == User::OFFICE ? 'selected': '' }}>
                           Office
                        </option>

                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

<div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-body pb-0">
                <div class="bank-detail">

                    <h5 class="mb-3 border-bottom pb-3">Leave Detail</h5>
                    <label for="number" class="form-label">Leave Allocated</label>
                    <input type="number" class="form-control mb-2" min="0"
                           id="leave_allocated"
                           name="leave_allocated"
                           oninput="validity.valid||(value='');"
                           value="{{ isset($userDetail) ? $userDetail->leave_allocated: old('leave_allocated') }}"
                           autocomplete="off" placeholder="">

                    <div id="error-message" style="color: red !important; display: none;"></div>
                    <table class="table table-responsive">
                        <h5 class="my-3">{{ __(' Assigned Leaves') }}</h5>
                        <thead>
                        <tr>
                            <th>{{ __('Leave') }}</th>
                            <th>{{ __('No.of Days') }}</th>
                            <th>{{ __('Is Active') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @for ($k=0;$k<count($leaveTypes);$k++)
                            <tr>
                                <td>
                                    {{$leaveTypes[$k]->name}}
                                    <input type="hidden" name="leave_type_id[{{$k}}]" value="{{$leaveTypes[$k]->id}}">
                                </td>
                                @if (isset($employeeLeaveTypes[$k]))
                                    @php $leaveType = $employeeLeaveTypes[$k]; @endphp
                                @endif

                                <td>
                                    <input type="number" min="0" class="form-control leave-days"
                                           value="{{ $leaveType->days ?? '' }}" oninput="validity.valid||(value='');"  placeholder="Total Leave Days" name="days[{{$k}}]">
                                    <span class="error-message" style="display: none; color: red;">This field is required.</span>
                                </td>

                                <td>
                                    <input class="mt-2 is-active-checkbox" type="checkbox" {{ isset($leaveType->is_active) && $leaveType->is_active == 1 ? 'checked' :''}} name="is_active[{{$k}}]" value="1"> Is Active
                                </td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-body pb-0">
                <div class="bank-detail">
                    <h5 class="mb-3 border-bottom pb-3">Bank Detail</h5>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 mb-4">
                            <label for="bank_name" class="form-label">Bank Name <span
                                    style="color: red">*</span></label>
                            <input type="text" class="form-control"
                                   id="bank_name"
                                   name="bank_name"
                                   value="{{ isset($userDetail?->accountDetail) ? $userDetail?->accountDetail?->bank_name: old('bank_name') }}"
                                   autocomplete="off" placeholder="Enter Bank Name" required>
                        </div>

                        <div class="col-lg-6 col-md-6 mb-4">
                            <label for="bank_account_no" class="form-label">Bank Account Number <span
                                    style="color: red">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   id="bank_account_no"
                                   name="bank_account_no"
                                   value="{{ isset($userDetail?->accountDetail) ? $userDetail?->accountDetail?->bank_account_no: old('bank_account_no') }}"
                                   autocomplete="off"
                                   placeholder=" Enter Bank Account Number" required>
                        </div>

                        <div class="col-lg-6 col-md-6 mb-4">
                            <label for="account_holder" class="form-label">Account Holder Name <span style="color: red">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="account_holder"
                                   name="account_holder"
                                   value="{{isset($userDetail) ? $userDetail?->accountDetail?->account_holder: old('account_holder') }}"
                                   autocomplete="off"
                                   required
                                   placeholder="Enter Account Holder">
                        </div>

                        <div class="col-lg-6 col-md-6 mb-4">
                            <label for="bank_account_type" class="form-label">Bank Account Type<span style="color: red">*</span></label>
                            <select class="form-select" id="bank_account_type" name="bank_account_type" required>
                                <option value="" {{isset($userDetail) || old('bank_account_type') ? '': 'selected'}} >
                                    select account type
                                </option>
                                @foreach(EmployeeAccount::BANK_ACCOUNT_TYPE as $value)
                                    <option
                                        value="{{ $value }}" {{ isset($userDetail?->accountDetail) && ($userDetail?->accountDetail?->bank_account_type ) == $value || old('bank_account_type') == $value ? 'selected': '' }}>
                                        {{ucfirst($value)}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<button type="submit" class="btn btn-primary">
    <i class="link-icon" data-feather="plus"></i> {{isset($userDetail)? 'Update':'Create'}} User
</button>
