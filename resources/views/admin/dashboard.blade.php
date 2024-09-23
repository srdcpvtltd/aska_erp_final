@extends('layouts.master')

@section('title', 'Digital Dashboard')

<?php
$attendanceDetail = \App\Helpers\AppHelper::employeeTodayAttendanceDetail();
$checkInAt = $attendanceDetail['check_in_at'] ?? '';
$checkOutAt = $attendanceDetail['check_out_at'] ?? '';
$attendanceDate = $attendanceDetail['attendance_date'] ?? '';
$viewCheckIn = $checkInAt ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $checkInAt) : '-:-:-';
$viewCheckOut = $checkOutAt ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $checkOutAt) : '-:-:-';
?>
@section('nav-head', 'Welcome to Dashboard : ' . ucfirst($dashboardDetail?->company_name))
@section('main-content')

<section class="content">
    <?php
    $projectPriority = [
        'low' => 'info',
        'medium' => 'warning',
        'high' => 'primary',
        'urgent' => 'primary',
    ];
    ?>

    <div id="flashAttendanceMessage" class="d-none">
        <div class="alert alert-danger errorStartWorking">
            <p class="errorStartWorkingMessage"></p>
        </div>

        <div class="alert alert-danger errorStopWorking">
            <p class="errorStopWorkingMessage"></p>
        </div>

        <div class="alert alert-success successStartWorking">
            <p class="successStartWorkingMessage"></p>
        </div>

        <div class="alert alert-success successStopWorking">
            <p class="successStopWorkingMessage"></p>
        </div>
    </div>

    <div id="loader" style="display:none;">
        <div class="loading">
            <div class="loading-content"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-9 col-xl-8 d-flex">
            <div class="row">
                <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 mb-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-md-start text-center">
                            <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                <h6 class="card-title mb-2 mb-md-0">Total Employees</h6>
                            </div>

                            <div class="row align-items-center d-md-flex">
                                <div class="col-lg-6 col-md-6">
                                    <h3>{{ number_format($dashboardDetail?->total_employee) }}</h3>
                                </div>
                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                    <i class="link-icon" data-feather="users"> </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 mb-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-md-start text-center">
                            <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                <h6 class="card-title mb-2 mb-md-0">Total Departments</h6>
                            </div>
                            <div class="row align-items-center d-md-flex">
                                <div class="col-lg-6 col-md-6">
                                    <h3>{{ number_format($dashboardDetail?->total_departments) }}</h3>
                                </div>
                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                    <i class="link-icon" data-feather="layers"> </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-md-start text-center">
                            <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                <h6 class="card-title mb-2 mb-md-0">Total Holidays</h6>
                            </div>
                            <div class="row align-items-center d-md-flex">
                                <div class="col-lg-6 col-md-6">
                                    <h3>{{ number_format($dashboardDetail?->total_holidays) ?? 0 }}</h3>
                                </div>
                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                    <i class="link-icon" data-feather="umbrella"> </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-md-start text-center">
                            <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                <h6 class="card-title mb-2 mb-md-0">Paid Leaves</h6>
                            </div>
                            <div class="row align-items-center d-md-flex">
                                <div class="col-lg-6 col-md-6">
                                    <h3>{{ number_format($dashboardDetail?->total_paid_leaves) ?? 0 }}</h3>
                                </div>
                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                    <i class="link-icon" data-feather="file-text"> </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-md-start text-center">
                            <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                <h6 class="card-title mb-2 mb-md-0">On Leave Today</h6>
                            </div>
                            <div class="row align-items-center d-md-flex">
                                <div class="col-lg-6 col-md-6">
                                    <h3>{{ number_format($dashboardDetail?->total_on_leave) ?? 0 }}</h3>
                                </div>
                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                    <i class="link-icon" data-feather="file-minus"> </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-md-start text-center">
                            <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                <h6 class="card-title mb-2 mb-md-0">Pending Leave Requests</h6>
                            </div>
                            <div class="row align-items-center d-md-flex">
                                <div class="col-lg-6 col-md-6">
                                    <h3>{{ number_format($dashboardDetail?->total_pending_leave_requests) ?? 0 }}</h3>
                                </div>
                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                    <i class="link-icon" data-feather="twitch"> </i>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-md-start text-center">
                            <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                <h6 class="card-title mb-2 mb-md-0">Total Check In Today</h6>
                            </div>
                            <div class="row align-items-center d-md-flex">
                                <div class="col-lg-6 col-md-6">
                                    <h3>{{ number_format($dashboardDetail?->total_checked_in_employee) ?? 0 }}</h3>
                                </div>
                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                    <i class="link-icon" data-feather="log-in"> </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-md-start text-center">
                            <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                <h6 class="card-title mb-2 mb-md-0">Total Check Out Today</h6>
                            </div>
                            <div class="row align-items-center d-md-fle">
                                <div class="col-lg-6 col-md-6">
                                    <h3>{{ number_format($dashboardDetail?->total_checked_out_employee) ?? 0 }}</h3>
                                </div>
                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                    <i class="link-icon" data-feather="log-out"> </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-xxl-3 col-xl-4 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body text-center clock-display">
                    <div id="clockContainer" class="mb-3"
                        style="background: url({{ asset('assets/images/clock.jpg') }}) no-repeat;background-size: 100%;">
                        <div id="hour"></div>
                        <div id="minute"></div>
                        <div id="second"></div>
                    </div>

                    <p id="date" class="text-primary fw-bolder mb-3"></p>

                    <div class="punch-btn mb-2 d-flex align-items-center justify-content-around">
                        <button href="{{ route('admin.dashboard.takeAttendance', 'checkIn') }}"
                            class="btn btn-lg btn-success  {{ $checkInAt ? 'd-none' : '' }}" id="startWorkingBtn"
                            data-audio="{{ asset('assets/audio/beep.mp3') }}">
                            Punch In
                        </button>
                        <button href="{{ route('admin.dashboard.takeAttendance', 'checkOut') }}"
                            class="btn btn-lg btn-danger {{ $checkOutAt ? 'd-none' : '' }}" id="stopWorkingBtn"
                            data-audio="{{ asset('assets/audio/beep.mp3') }}">
                            Punch Out
                        </button>
                    </div>

                    <div class="check-text d-flex align-items-center justify-content-around">
                        <span>Check In At<p class="text-success fw-bold h5" id="checkInTime">{{ $viewCheckIn }} </p>
                        </span>
                        <span>Check Out At<p class="text-danger fw-bold h5" id="checkOutTime">{{ $viewCheckOut }}
                            </p></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<script src="{{ asset('assets/vendors/chartjs/Chart.min.js') }}"></script>

@section('scripts')
@include('admin.dashboard_scripts')
@endsection
