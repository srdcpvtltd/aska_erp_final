@extends('layouts.master')

@section('title','Attendance')

@section('action','Employee Regularization Lists')


@section('main-content')

<section class="content">
    <?php
    if ($isBsEnabled) {
        $currentDate = \App\Helpers\AppHelper::getCurrentDateInBS();
    } else {
        $currentDate = \App\Helpers\AppHelper::getCurrentDateInYmdFormat();
    }
    ?>

    @include('admin.section.flash_message')

    @include('admin.attendance.common.regularizationBreadCrumb')
    <div class="search-box p-4 pb-0 bg-white rounded mb-3 box-shadow">
        <form class="forms-sample" action="{{route('admin.regularization.index')}}" method="get">
            <h5 class="mb-3">Regularization Of The Day</h5>
            <div class="row align-items-center">

                <div class="col-lg col-md-4 mb-4">
                    <input id="attendance_date" name="attendance_date" @if($isBsEnabled) class="form-control dayAttendance" type="text" placeholder="yy/mm/dd" @else class="form-control" type="date" @endif />
                </div>
                <!-- status filter end-->
                <div class="col-lg col-md-4 mb-4">
                    <select class="form-select form-select-lg" name="status" id="branch_id">
                        <option value={{NULL}}>Select Status</option>
                        <option value="1">Approve</option>
                        <option value="2">Reject</option>
                        <option value="0">Pending</option>
                    </select>
                </div>
                <!-- status filter end -->

                <div class="col-lg col-md-4 mb-4">
                    <select class="form-select form-select-lg" name="branch_id" id="branch_id">
                        <option value="" {{!isset($filterParameter['branch_id']) ? 'selected': ''}}>Select Branch</option>
                        @foreach($branch as $key => $value)
                        <option value="{{$value->id}}" {{ (isset($filterParameter['branch_id']) && $value->id == $filterParameter['branch_id'] ) ?'selected':'' }}> {{ucfirst($value->name)}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg col-md-4 mb-4">
                    <select class="form-select " name="department_id" id="department_id">
                        <option selected disabled>Select Department</option>
                    </select>
                </div>

                <div class="col-xxl col-lg-5 col-md-6 d-md-flex">
                    <button type="submit" class="btn btn-block btn-success form-control me-md-2 me-0 mb-4">Filter</button>
                    <button type="button" id="download-daywise-attendance-excel" data-href="{{route('admin.attendances.index' )}}" class="btn btn-block btn-secondary form-control me-md-2 me-0 mb-4">Export
                    </button>
                    <a class="btn btn-block btn-primary form-control me-md-2 me-0 mb-4 " href="{{route('admin.attendances.index')}}">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Regularization Date</th>
                            <th>Employee Name</th>
                            <th class="text-center">Check In At</th>
                            <th class="text-center">Check Out At</th>
                            <th class="text-center">Regularization Status</th>
                            <!-- <th class="text-center">Regularized By</th> -->
                            @canany(['create-attendance','edit-attendance','delete-attendance'])
                            <th class="text-center">Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $changeColor = [
                            2 => 'danger',
                            1 => 'success',
                        ]
                        ?>
                        {{Log::info($regularizationDetails)}}
                        @foreach($regularizationDetails as $key => $value)
                        @if($value->regularizations_id != null)
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::parse($value->regularization_date)->format('d-m-Y') }}
                            </td>
                            <td>
                                {{Log::info($value->reason)}}
                                {{ucfirst($value->user_name)}}
                            </td>

                            @if($value->check_in_at)
                            <td class="text-center">
                                <span class="btn btn-outline-secondary btn-xs checkLocation" title="{{ $value->check_in_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'Show checkin location' : strtoupper($value->check_in_type).' checkin' }}" data-bs-toggle="modal" data-href="{{ $value->check_in_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'https://maps.google.com/maps?q='.$value->check_in_latitude.','.$value->check_in_longitude.'&t=&z=20&ie=UTF8&iwloc=&output=embed' : '' }} " data-bs-target="{{ $value->check_in_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? '#addslider' : '' }} ">
                                    {{ ($value->check_in_at) ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $value->check_in_at):''}}
                                </span>
                                <!-- @if($value->reason)
                                <p style="float: left;">{{$value->reason}}</p>
                                @endif -->
                            </td>
                            @else
                            <td class="text-center"></td>
                            @endif

                            @if($value->check_out_at)
                            <td class="text-center">
                                <span class="btn btn-outline-secondary btn-xs checkLocation" title="{{ $value->check_out_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'Show checkout location' : strtoupper($value->check_out_type).' checkout' }}" data-bs-toggle="modal" data-href="{{ $value->check_out_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'https://maps.google.com/maps?q='.$value->check_out_latitude.','.$value->check_out_longitude.'&t=&z=20&ie=UTF8&iwloc=&output=embed' : '' }} " data-bs-target="{{ $value->check_out_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? '#addslider' : '' }} ">
                                    {{ ($value->check_out_at) ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $value->check_out_at)  : ''}}
                                </span>
                            </td>
                            @else
                            <td class="text-center"></td>
                            @endif


                            @if(!is_null($value->regularization_status) && $value->regularization_status != 0 )
                            <td class="text-center">
                                <a class=" disabled changeAttendanceStatus btn btn-{{$changeColor[$value->regularization_status]}} btn-xs" data-href="{{route('admin.attendances.change-status',$value->regularizations_id)}}" title="Change Regularization Status ">
                                    {{($value->regularization_status == \App\Models\Regularization::ATTENDANCE_APPROVED) ? 'Approved':'Rejected'}}
                                </a>
                            </td>
                            @else
                            <td class="text-center">
                                <span class="btn btn-light btn-xs disabled">
                                    Pending
                                </span>
                            </td>
                            @endif

                            <!-- @if($value->created_by)
                            <td class="text-center">
                                <span class="btn btn-warning btn-xs">
                                    {{ ($value->user_id == $value->created_by )  ? 'Self' : 'Admin'}}
                                </span>
                            </td>
                            @else
                            <td class="text-center">

                            </td>
                            @endif -->

                            @canany(['create-attendance','edit-attendance','delete-attendance'])
                            <td class="text-center">
                                <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                    @if($filterParameter['regularization_date'] == $currentDate)
                                    @if(!$value->check_in_at)
                                    @can('create-attendance')
                                    <li class="me-2">
                                        <a href="{{route('admin.employees.check-in',[$value->company_id,$value->user_id,])}}" id="checkIn" data-href="" data-id="">
                                            <button class="btn btn-success btn-xs">Check In</button>
                                        </a>
                                    </li>
                                    @endcan
                                    @endif

                                    @if($value->check_in_at && !$value->check_out_at)
                                    @can('edit-attendance')
                                    <li class="me-2">
                                        <a href="{{route('admin.employees.check-out',[ $value->company_id,$value->user_id])}}" id="checkOut" data-href="" data-id="">
                                            <button class="btn btn-danger btn-xs">Check Out</button>
                                        </a>
                                    </li>
                                    @endcan
                                    @endif
                                    @endif

                                    @if($value->regularization_status != "2")
                                    <li class="me-2">
                                        <i style="cursor: pointer;" class="link-icon" data-feather="edit" onclick="approveRegularization(`{{ route('admin.regularization.approveRegularization', ['id' => $value->regularizations_id]) }}`)" id="approve_btn_{{ $value->regularizations_id }}"></i>
                                    </li>

                                    <li class="me-2">
                                        <i style="cursor: pointer;" class="link-icon" data-feather="delete" onclick="rejectRegularization(`{{ route('admin.regularization.rejectRegularization', ['id' => $value->regularizations_id]) }}`)" id="reject_btn_{{ $value->regularizations_id }}"></i>
                                    </li>
                                    @else
                                    <li class="me-2">
                                        <i style="opacity: 0.5;"  class="link-icon" data-feather="edit" id="approve_btn"></i>
                                    </li>

                                    <li class="me-2">
                                        <i style="opacity: 0.5;" class="link-icon" data-feather="delete"  id="reject_btn"></i>
                                    </li>
                                    @endif
                                </ul>
                            </td>
                            @endcanany
                        </tr>

                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addslider" tabindex="-1" aria-labelledby="addslider" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <iframe id="iframeModalWindow" class="attendancelocation" height="500px" width="100%" src="" name="iframe_modal"></iframe>
                </div>
            </div>
        </div>
    </div>

    @include('admin.attendance.common.edit-attendance-form')

</section>
@endsection

@section('scripts')
@include('admin.attendance.common.scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let regularizationStatus = `{{ $value->regularization_status }}`;
        console.log(regularizationStatus);
        if (regularizationStatus == 2) {
            let approveBtn = document.getElementById('approve_btn_{{ $value->regularizations_id }}');
            let rejectBtn = document.getElementById('reject_btn_{{ $value->regularizations_id }}');

            if (approveBtn) {
                approveBtn.style.pointerEvents = 'none';
                approveBtn.style.opacity = '0.5';
            }

            if (rejectBtn) {
                rejectBtn.style.pointerEvents = 'none';
                rejectBtn.style.opacity = '0.5';
            }
        }
    });

    $('#branch_id').change(function() {
        let selectedBranchId = $('#branch_id option:selected').val();

        let departmentId = "{{  $filterParameter['department_id'] ?? '' }}";
        console.log(departmentId);
        $('#department_id').empty();
        if (selectedBranchId) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/departments/get-All-Departments') }}" + '/' + selectedBranchId,
            }).done(function(response) {
                if (!departmentId) {
                    $('#department_id').append('<option disabled  selected >Select Department</option>');
                }
                response.data.forEach(function(data) {
                    $('#department_id').append('<option ' + ((data.id == departmentId) ? "selected" : '') + ' value="' + data.id + '" >' + data.dept_name + '</option>');
                });
            });
        }
    }).trigger('change');

    function approveRegularization(ur) {
        $.ajax({
            type: 'GET',
            url: ur,
            success: function(response) {
                console.log("Success");
                Swal.fire({
                    icon: 'info',
                    title: response?.message ?? 'Something went wrong.',
                    timer: 3000,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timerProgressBar: true
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                console.log("Failure");
                let message = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                Swal.fire({
                    icon: 'info',
                    title: message,
                    timer: 3000,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            }
        });
    }

    function rejectRegularization(ur) {
        $.ajax({
            type: 'GET',
            url: ur,
            success: function(response) {
                console.log("Success");
                Swal.fire({
                    icon: 'error',
                    title: response?.message ?? 'Something went wrong.',
                    timer: 3000,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timerProgressBar: true
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                console.log("Failure");
                let message = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                Swal.fire({
                    icon: 'info',
                    title: message,
                    timer: 3000,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            }
        });
    }
</script>
@endsection