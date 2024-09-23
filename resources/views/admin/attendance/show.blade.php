@extends('layouts.master')

@section('title', 'Attendance')

@section('action', 'Employee Attendance Detail')

@section('button')
    <a href="{{ route('admin.attendances.index') }}">
        <button class="btn btn-sm btn-primary"><i class="link-icon" data-feather="arrow-left"></i> Back</button>
    </a>
@endsection

@section('main-content')
    <?php
    if ($isBsEnabled) {
        $filterData['min_year'] = '2076';
        $filterData['max_year'] = '2089';
        $filterData['month'] = 'np';
        $nepaliDate = \App\Helpers\AppHelper::getCurrentNepaliYearMonth();
        $filterData['current_year'] = $nepaliDate['year'];
        $filterData['current_month'] = $nepaliDate['month'];
    } else {
        $filterData['min_year'] = '2020';
        $filterData['max_year'] = '2033';
        $filterData['current_year'] = now()->format('Y');
        $filterData['current_month'] = now()->month;
        $filterData['month'] = 'en';
    }
    ?>

    <section class="content">

        @include('admin.section.flash_message')

        @include('admin.attendance.common.breadcrumb')
        <div class="search-box p-4 pb-0 bg-white rounded mb-3 box-shadow">
            <h5>Attendance Of {{ ucfirst($userDetail->name) }}</h5>
            <form class="forms-sample mb-4" action="{{ route('admin.attendances.show', $userDetail->id) }}" method="get">
                <div class="row align-items-center mt-3">
                    <div class="col-lg-3 col-md-3 mb-4">
                        <input type="number" min="{{ $filterData['min_year'] }}" max="{{ $filterData['max_year'] }}"
                            step="1" placeholder="Attendance year e.g : {{ $filterData['min_year'] }}" id="year"
                            name="year" value="{{ $filterParameter['year'] }}" class="form-control">
                    </div>

                    <div class="col-lg-3 col-md-3 mb-4">
                        <select class="form-select form-select-lg" name="month" id="month">
                            <option value="" {{ !isset($filterParameter['month']) ? 'selected' : '' }}>All Month
                            </option>
                            @foreach ($months as $key => $value)
                                <option value="{{ $key }}"
                                    {{ isset($filterParameter['month']) && $key == $filterParameter['month'] ? 'selected' : '' }}>
                                    {{ $value[$filterData['month']] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-3 mb-4">
                        <button type="submit" class="btn btn-block btn-success form-control">Filter</button>
                    </div>

                    <div class="col-lg-2 col-md-3 mb-4">
                        <button type="button" id="download-excel"
                            data-href="{{ route('admin.attendances.show', $userDetail->id) }}"
                            class="btn btn-block btn-secondary form-control">
                            CSV Export
                        </button>
                    </div>


                    <div class="col-lg-2 col-md-3 mb-4">
                        <a class="btn btn-block btn-primary"
                            href="{{ route('admin.attendances.show', $userDetail->id) }}">Reset</a>
                    </div>

                </div>
            </form>

        </div>

        <div class="row">
            <div class=" col-xl-3 col-md-3 mb-4 d-flex">
                <div class="card w-100">
                    <div class="card-body text-md-start text-center">
                        <h6 class="card-title w-100">Total Days In Month
                        </h6>
                        <h5 class="text-primary">
                            {{ $attendanceSummary ? number_format($attendanceSummary['totalDays']) : 0 }}</h5>
                    </div>
                </div>
            </div>
            <div class=" col-xl-3 col-md-3 mb-4 d-flex">
                <div class="card w-100">
                    <div class="card-body text-md-start text-center">
                        <h6 class="card-title w-100">Present Days
                        </h6>
                        <h5 class="text-primary">
                            {{ $attendanceSummary ? number_format($attendanceSummary['totalPresent']) : 0 }}</h5>
                    </div>
                </div>
            </div>
            <div class=" col-xl-3 col-md-3 mb-4 d-flex">
                <div class="card w-100">
                    <div class="card-body text-md-start text-center">
                        <h6 class="card-title w-100">Absent Days
                        </h6>
                        <h5 class="text-primary">
                            {{ $attendanceSummary ? number_format($attendanceSummary['totalAbsent']) : 0 }}</h5>

                    </div>

                </div>
            </div>
            <div class=" col-xl-3 col-md-3 mb-4 d-flex">
                <div class="card w-100">
                    <div class="card-body text-md-start text-center">
                        <h6 class="card-title w-100">Weekend Days
                        </h6>
                        <h5 class="text-primary">
                            {{ $attendanceSummary ? number_format($attendanceSummary['totalWeekend']) : 0 }}</h5>
                    </div>
                </div>
            </div>

            <div class=" col-xl-3 col-md-3 mb-4 d-flex">
                <div class="card w-100">
                    <div class="card-body text-md-start text-center">
                        <h6 class="card-title w-100">Holiday Days
                        </h6>
                        <h5 class="text-primary">
                            {{ $attendanceSummary ? number_format($attendanceSummary['totalHoliday']) : 0 }}</h5>
                    </div>
                </div>
            </div>
            <div class=" col-xl-3 col-md-3 mb-4 d-flex">
                <div class="card w-100">
                    <div class="card-body text-md-start text-center">
                        <h6 class="card-title w-100">Leave Days
                        </h6>
                        <h5 class="text-primary">
                            {{ $attendanceSummary ? number_format($attendanceSummary['totalLeave']) : 0 }}</h5>
                    </div>
                </div>
            </div>
            <div class=" col-xl-3 col-md-3 mb-4 d-flex">
                <div class="card w-100">
                    <div class="card-body text-md-start text-center">
                        <h6 class="card-title w-100">Working Hours
                        </h6>
                        <h6 class="text-primary">{{ $attendanceSummary ? $attendanceSummary['totalWorkingHours'] : '-' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class=" col-xl-3 col-md-3 mb-4 d-flex">
                <div class="card w-100">
                    <div class="card-body text-md-start text-center">
                        <h6 class="card-title w-100">Worked Hours
                        </h6>
                        <h6 class="text-primary">{{ $attendanceSummary ? $attendanceSummary['totalWorkedHours'] : '-' }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <h4 class="fw-bold text-center mb-4">Attendance Details of {{ $monthName }}</h4>
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th> Date </th>
                                        <th style="text-align: center;">Check In At</th>
                                        <th style="text-align: center;">Check Out At</th>
                                        <th style="text-align: center;">Worked Hour</th>
                                        <th style="text-align: center;">Status</th>
                                        <th style="text-align: center;">Attendance By</th>
                                        @can('edit-attendance')
                                            <th style="text-align: center;">Action</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $changeColor = [
                                        0 => 'danger',
                                        1 => 'success',
                                    ];
                                    ?>
                                    @forelse($attendanceDetail as $key => $value)
                                        <tr>

                                            <td>{{ \App\Helpers\AttendanceHelper::formattedAttendanceDate($isBsEnabled, $value['attendance_date']) }}
                                            </td>
                                            {{--                                            <td>{{$value['attendance_date']}} </td> --}}

                                            @if (isset($value['check_in_at']))
                                                @if ($value['check_in_at'])
                                                    <td class="text-center">
                                                        <span class="btn btn-outline-secondary btn-xs checkLocation"
                                                            title="{{ $value['check_in_type'] == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'Show checkin location' : strtoupper($value['check_in_type']) . ' checkin' }}"
                                                            data-bs-toggle="modal"
                                                            data-href="{{ $value['check_in_type'] == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'https://maps.google.com/maps?q=' . $value['check_in_latitude'] . ',' . $value['check_in_longitude'] . '&t=&z=20&ie=UTF8&iwloc=&output=embed' : '' }} "
                                                            data-bs-target="{{ $value['check_in_type'] == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? '#addslider' : '' }} ">
                                                            {{ $value['check_in_at'] ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $value['check_in_at']) : '' }}
                                                        </span>
                                                    </td>
                                                @else
                                                    <td></td>
                                                @endif

                                                @if ($value['check_out_at'])
                                                    <td class="text-center">
                                                        <span class="btn btn-outline-secondary btn-xs checkLocation"
                                                            title="{{ $value['check_out_type'] == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'Show checkout location' : strtoupper($value['check_out_type']) . ' checkout' }}"
                                                            data-bs-toggle="modal"
                                                            data-href="{{ $value['check_out_type'] == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? 'https://maps.google.com/maps?q=' . $value['check_out_latitude'] . ',' . $value['check_out_longitude'] . '&t=&z=20&ie=UTF8&iwloc=&output=embed' : '' }} "
                                                            data-bs-target="{{ $value['check_out_type'] == \App\Enum\EmployeeAttendanceTypeEnum::wifi->value ? '#addslider' : '' }} ">
                                                            {{ $value['check_out_at'] ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $value['check_out_at']) : '' }}
                                                        </span>
                                                    </td>
                                                @else
                                                    <td></td>
                                                @endif

                                                <td class="text-center">
                                                    @if ($value['check_out_at'])
                                                        {{ \App\Helpers\AttendanceHelper::getWorkedHourInHourAndMinute($value['check_in_at'], $value['check_out_at']) }}
                                                    @endif
                                                </td>

                                                @if (!is_null($value['attendance_status']))
                                                    <td class="text-center">
                                                        <a class="changeAttendanceStatus btn btn-{{ $changeColor[$value['attendance_status']] }} btn-xs"
                                                            data-href="{{ route('admin.attendances.change-status', $value['id']) }}"
                                                            title="Change Attendance Status">
                                                            {{ $value['attendance_status'] == \App\Models\Attendance::ATTENDANCE_APPROVED ? 'Approved' : 'Rejected' }}
                                                        </a>
                                                    </td>
                                                @else
                                                    <td>
                                                        <span class="btn btn-light btn-xs disabled">
                                                            Pending
                                                        </span>
                                                    </td>
                                                @endif

                                                @if ($value['created_by'])
                                                    <td class="text-center">
                                                        <span class="btn btn-warning btn-xs">
                                                            {{ $value['user_id'] == $value['created_by'] ? 'Self' : 'Admin' }}
                                                        </span>
                                                    </td>
                                                @else
                                                    <td>

                                                    </td>
                                                @endif
                                            @else
                                                <td class="text-center"> <i class="link-icon" data-feather="x"></i></td>
                                                <td class="text-center"> <i class="link-icon" data-feather="x"></i></td>
                                                <td class="text-center"> <i class="link-icon" data-feather="x"></i></td>

                                                <?php
                                                
                                                $reason = \App\Helpers\AttendanceHelper::getHolidayOrLeaveDetail($value['attendance_date'], $userDetail->id);
                                                ?>
                                                @if ($reason)
                                                    <td class="text-center">
                                                        <span class="btn btn-outline-secondary btn-xs">
                                                            {{ $reason }}
                                                        </span>
                                                    </td>
                                                @endif
                                                <td class="text-center"> <i class="link-icon" data-feather="x"></i></td>
                                            @endif

                                            @can('edit-attendance')
                                                <td class="text-center">
                                                    <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                        @if (isset($value['id']))
                                                            <li class="me-2">
                                                                <a href="" class="editAttendance"
                                                                    data-href="{{ route('admin.attendances.update', $value['id']) }}"
                                                                    data-in="{{ date('H:i', strtotime($value['check_in_at'])) }}"
                                                                    data-out="{{ $value['check_out_at'] ? date('H:i', strtotime($value['check_out_at'])) : null }}"
                                                                    data-remark="{{ $value['edit_remark'] }}"
                                                                    data-date="{{ \App\Helpers\AttendanceHelper::formattedAttendanceDate($isBsEnabled, $value['attendance_date']) }} "
                                                                    data-name="{{ ucfirst($userDetail->name) }}"
                                                                    title="Edit attendance time">
                                                                    <i class="link-icon" data-feather="edit"></i>
                                                                </a>
                                                            </li>
                                                        @else
                                                            @if (isset($reason) && $reason == 'Absent')
                                                                <li class="me-2">
                                                                    <a href="" class="addEmployeeAttendance"
                                                                        data-href="{{ route('admin.attendances.store') }}"
                                                                        data-name="{{ ucfirst($userDetail->name) }}"
                                                                        data-date="{{ date('Y-m-d', strtotime($value['attendance_date'])) }}"
                                                                        data-user_id="{{ $userDetail->id }}"
                                                                        title="Add attendance time">
                                                                        <i class="link-icon" data-feather="plus-circle"></i>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endif
                                                    </ul>
                                                </td>
                                            @endcan
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
            </div>
        </div>

        <div class="modal fade" id="addslider" tabindex="-1" aria-labelledby="addslider" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <iframe id="iframeModalWindow" class="attendancelocation" height="500px" width="100%"
                            src="" name="iframe_modal"></iframe>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.attendance.common.edit-attendance-form')
        @include('admin.attendance.common.create-attendance-form')
    </section>
@endsection

@section('scripts')
    @include('admin.attendance.common.scripts')
@endsection
