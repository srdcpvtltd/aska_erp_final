@extends('layouts.master')

@section('title','Attendance')

@section('action','Employee Attendance Lists')


@section('main-content')

    <section class="content">
        <?php
            if($isBsEnabled){
                $currentDate = \App\Helpers\AppHelper::getCurrentDateInBS();
            }else{
                $currentDate = \App\Helpers\AppHelper::getCurrentDateInYmdFormat();
            }
        ?>

        @include('admin.section.flash_message')

        @include('admin.attendance.common.breadcrumb')
        <div class="search-box p-4 pb-0 bg-white rounded mb-3 box-shadow">
            <form class="forms-sample" action="{{route('admin.attendances.index')}}" method="get">
                <h5 class="mb-3">Attendance Of The Day</h5>
                <div class="row align-items-center">

                    <div class="col-lg col-md-4 mb-4">
                        @include('admin.attendance.dateRangeFilter')
                    </div>

                    <div class="col-lg col-md-4 mb-4">
                        <select class="form-select form-select-lg" name="branch_id" id="branch_id">
                            <option value="" {{!isset($filterParameter['branch_id']) ? 'selected': ''}}>Select Branch</option>
                            @foreach($branch as $key =>  $value)
                                <option value="{{$value->id}}" {{ (isset($filterParameter['branch_id']) && $value->id == $filterParameter['branch_id'] ) ?'selected':'' }} > {{ucfirst($value->name)}} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg col-md-4 mb-4">
                        <select class="form-select " name="department_id" id="department_id">
                            <option selected disabled >Select Department</option>
                        </select>
                    </div>

                    <div class="col-xxl col-lg-5 col-md-6 d-md-flex">
                        <button type="submit" class="btn btn-block btn-success form-control me-md-2 me-0 mb-4">Filter</button>
                            <button type="button" id="download-excel-report"
                                    data-href="{{route('admin.attendances.index' )}}"
                                    class="btn btn-block btn-secondary form-control me-md-2 me-0 mb-4">CSV Export
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
                                @can('show-attendance')
                                    <td></td>
                                @endcan
                                <th>Employee Name</th>
                                <th class="text-center">Attendance Date</th>
                                <th class="text-center">Check In At</th>
                                <th class="text-center">Check Out At</th>
                                <th class="text-center">Attendance Status</th>
                                <th class="text-center">Attendance By</th>
                                @canany(['create-attendance','edit-attendance','delete-attendance'])
                                    <th class="text-center">Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $changeColor = [
                            0 => 'danger',
                            1 => 'success',
                        ]
                        ?>
                        @forelse($attendanceDetail as $key => $value)
                            <tr>
                                @can('show-attendance')
                                    <td>
                                        <ul class="d-flex list-unstyled mb-0">
                                            <li class="me-2">
                                                <a href="{{route('admin.attendances.show',$value->user_id)}}"
                                                   title="show detail">
                                                    <i class="link-icon" data-feather="eye"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                @endcan

                                <td>
                                    {{ucfirst($value->user_name)}}
                                </td>
                                <td>
                                    {{date('d-m-Y',strtotime($value->attendance_date))}}
                                </td>
                                @if($value->check_in_at)
                                    <td class="text-center">
                                        <span class="btn btn-outline-secondary btn-xs checkLocation"
                                              title="{{ $value->check_in_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'Show checkin location' : strtoupper($value->check_in_type).' checkin' }}"
                                              data-bs-toggle="modal"
                                              data-href="{{ $value->check_in_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'https://maps.google.com/maps?q='.$value->check_in_latitude.','.$value->check_in_longitude.'&t=&z=20&ie=UTF8&iwloc=&output=embed' : '' }} "
                                              data-bs-target="{{ $value->check_in_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? '#addslider' : '' }} "
                                        >
                                            {{ ($value->check_in_at) ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $value->check_in_at):''}}
                                        </span>


                                    </td>
                                @else
                                    <td class="text-center"></td>
                                @endif

                                @if($value->check_out_at)
                                    <td class="text-center">
                                        <span class="btn btn-outline-secondary btn-xs checkLocation"
                                              title="{{ $value->check_out_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'Show checkout location' : strtoupper($value->check_out_type).' checkout' }}"
                                              data-bs-toggle="modal"
                                              data-href="{{ $value->check_out_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'https://maps.google.com/maps?q='.$value->check_out_latitude.','.$value->check_out_longitude.'&t=&z=20&ie=UTF8&iwloc=&output=embed' : '' }} "
                                              data-bs-target="{{ $value->check_out_type == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? '#addslider' : '' }} "
                                              >
                                           {{  ($value->check_out_at) ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $value->check_out_at)  : ''}}
                                        </span>
                                    </td>
                                @else
                                    <td class="text-center"></td>
                                @endif


                                @if(!is_null($value->attendance_status))
                                    <td class="text-center">
                                        <a class="changeAttendanceStatus btn btn-{{$changeColor[$value->attendance_status]}} btn-xs"
                                           data-href="{{route('admin.attendances.change-status',$value->attendance_id)}}" title="Change Attendance Status">
                                            {{($value->attendance_status == \App\Models\Attendance::ATTENDANCE_APPROVED) ? 'Approved':'Rejected'}}
                                        </a>
                                    </td>
                                @else
                                    <td class="text-center">
                                       <span class="btn btn-light btn-xs disabled">
                                            Pending
                                        </span>
                                    </td>
                                @endif

                                @if($value->created_by)
                                    <td class="text-center">
                                        <span class="btn btn-warning btn-xs">
                                            {{ ($value->user_id == $value->created_by )  ? 'Self' : 'Admin'}}
                                        </span>
                                    </td>
                                @else
                                    <td class="text-center">

                                    </td>
                                @endif

                            @canany(['create-attendance','edit-attendance','delete-attendance'])
                                <td class="text-center">
                                    <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                        @if($filterParameter['start_date'] ==  $currentDate)
                                            @if(!$value->check_in_at)
                                                @can('create-attendance')
                                                    <li class="me-2">
                                                        <a href="{{route('admin.employees.check-in',[$value->company_id,$value->user_id,])}}"
                                                           id="checkIn"
                                                           data-href=""
                                                           data-id="">
                                                            <button class="btn btn-success btn-xs">Check In</button>
                                                        </a>
                                                    </li>
                                                @endcan
                                            @endif

                                            @if($value->check_in_at && !$value->check_out_at)
                                                @can('edit-attendance')
                                                    <li class="me-2">
                                                        <a href="{{route('admin.employees.check-out',[ $value->company_id,$value->user_id])}}"
                                                           id="checkOut"
                                                           data-href=""
                                                           data-id="">
                                                            <button class="btn btn-danger btn-xs">Check Out</button>
                                                        </a>
                                                    </li>
                                                @endcan
                                            @endif
                                        @endif

                                        @if($value->attendance_id)
                                            @can('edit-attendance')
                                                <li class="me-2">
                                                    <a
                                                        href=""
                                                        class="editAttendance"
                                                        data-href="{{ route('admin.attendances.update',$value->attendance_id) }}"
                                                        data-in="{{ date('H:i',strtotime($value->check_in_at))}}"
                                                        data-out="{{ ($value->check_out_at) ? date('H:i',strtotime($value->check_out_at)) : null}}"
                                                        data-remark="{{$value->edit_remark}}"
                                                        data-date="{{$filterParameter['start_date']}}"
                                                        data-name="{{ucfirst($value->user_name)}}"
                                                        title="Edit attendance time"
                                                    >
                                                        <i class="link-icon" data-feather="edit"></i>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('delete-attendance')
                                                <li class="me-2">
                                                    <a class="deleteAttendance" href="{{route('admin.attendance.delete',$value->attendance_id)}}">
                                                        <i class="link-icon"  data-feather="delete"></i>
                                                    </a>
                                                </li>
                                            @endcan
                                        @endif
                                    </ul>
                                </td>
                            @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-center"><b>No records found!</b></p>
                                </td>
                            </tr>
                        @endforelse
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

@section('attendanceScripts')
@include('admin.attendance.common.attendanceScript')
    <script>

        $('#branch_id').change(function() {
            let selectedBranchId = $('#branch_id option:selected').val();

            let departmentId = "{{  $filterParameter['department_id'] ?? '' }}";
            console.log(departmentId);
            $('#department_id').empty();
            if (selectedBranchId) {
                $.ajax({
                    type: 'GET',
                    url: "{{ url('admin/departments/get-All-Departments') }}" + '/' + selectedBranchId ,
                }).done(function(response) {
                    if(!departmentId){
                        $('#department_id').append('<option disabled  selected >Select Department</option>');
                    }
                    response.data.forEach(function(data) {
                        $('#department_id').append('<option ' + ((data.id == departmentId) ? "selected" : '') + ' value="'+data.id+'" >'+data.dept_name+'</option>');
                    });
                });
            }
        }).trigger('change');
    </script>
@endsection

