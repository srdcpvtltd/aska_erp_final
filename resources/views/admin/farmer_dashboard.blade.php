@extends('layouts.master')

@section('title', 'Digital Farmer Dashboard')

<?php
$attendanceDetail = \App\Helpers\AppHelper::employeeTodayAttendanceDetail();
$checkInAt = $attendanceDetail['check_in_at'] ?? '';
$checkOutAt = $attendanceDetail['check_out_at'] ?? '';
$attendanceDate = $attendanceDetail['attendance_date'] ?? '';
$viewCheckIn = $checkInAt ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $checkInAt) : '-:-:-';
$viewCheckOut = $checkOutAt ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $checkOutAt) : '-:-:-';
$farmings = App\Models\Farming::where('is_validate', 1)->get();
$plots = App\Models\FarmingDetail::get();
$area = App\Models\FarmingDetail::sum('area_in_acar');
$tentative_planting = App\Models\FarmingDetail::sum('tentative_harvest_quantity');
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
        <div class="projectManagement">
            <h4 class="mb-4">Farmer Management </h4>
            <div class="row">
                <div class="col-xxl-6 col-xl-6 d-flex mb-4">
                    <div class="card card-table flex-fill">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Farmer Detail</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="planting"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-6 col-xl-6 d-flex">
                    <div class="row">
                        <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-md-start text-center">
                                    <h6 class="card-title mb-2">Total Farmers</h6>
                                    <div class="row align-items-center d-md-flex">
                                        <div class="col-lg-6 col-md-6">
                                            <h3>{{ count($farmings) }}</h3>
                                        </div>
                                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                            <i class="link-icon" data-feather="layers"> </i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-md-start text-center">
                                    <h6 class="card-title mb-2">Total Plots</h6>
                                    <div class="row align-items-center d-md-flex">
                                        <div class="col-lg-6 col-md-6">
                                            <h3>{{ count($plots) }}</h3>
                                        </div>
                                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                            <i class="link-icon" data-feather="layers"> </i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-md-start text-center">
                                    <h6 class="card-title mb-2">Total Areas(Acr.)</h6>
                                    <div class="row align-items-center d-md-flex">
                                        <div class="col-lg-6 col-md-6">
                                            <h3>{{ $area }}</h3>
                                        </div>
                                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                            <i class="link-icon" data-feather="layers"> </i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-md-start text-center">
                                    <h6 class="card-title mb-2">Tentative Planting(Acr.)</h6>
                                    <div class="row align-items-center d-md-flex">
                                        <div class="col-lg-6 col-md-6">
                                            <h3>{{ $tentative_planting }}</h3>
                                        </div>
                                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                            <i class="link-icon" data-feather="layers"> </i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-md-start text-center">
                                    <h6 class="card-title mb-2">Cutting Order</h6>
                                    <div class="row align-items-center d-md-flex">
                                        <div class="col-lg-6 col-md-6">
                                            <h3>0</h3>
                                        </div>
                                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                            <i class="link-icon" data-feather="layers"> </i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-md-start text-center">
                                    <h6 class="card-title mb-2">Cancelled Projects</h6>
                                    <div class="row align-items-center d-md-flex">
                                        <div class="col-lg-6 col-md-6">
                                            <h3>0</h3>
                                        </div>
                                        <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                            <i class="link-icon" data-feather="layers"> </i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-table flex-fill">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>{{ __('Farmer Name') }}</th>
                                <th>{{ __('G. Code') }}</th>
                                <th>{{ __('Mobile') }}</th>
                                <th>{{ __('Zone') }}</th>
                                <th>{{ __('Center') }}</th>
                                <th>{{ __('Village') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($farmings as $farming)
                                <tr class="font-style">
                                    <td>{{ $farming->name }}</td>
                                    <td>
                                        @if ($farming->g_code != null)
                                            {{ $farming->g_code }}
                                        @else
                                            <span class="status_badge text-capitalize badge bg-danger p-2 px-3 rounded">Not
                                                Assigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $farming->mobile }}</td>
                                    <td>{{ $farming->zone->name }}</td>
                                    <td>{{ $farming->center->name }}</td>
                                    <td>{{ $farming->village->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

<script src="{{ asset('assets/vendors/chartjs/Chart.min.js') }}"></script>

@section('scripts')
    {{-- @include('admin.dashboard_scripts') --}}
    <script>
        let content = document.getElementById('planting')?.getContext('2d');
        let category = ["Plants", "Seed", "R1", "R2", "R3", "R4", "R5"];
        let Colors = ["#7ee5e5", "#f77eb9", "#4d8af0", "green", 'red', 'yellow'];
        let Data = [
            {{ $plant }},
            {{ $seed }},
            {{ $r1 }},
            {{ $r2 }},
            {{ $r3 }},
            {{ $r4 }},
            {{ $r5 }}
        ];
        let newChart = new Chart(content, {
            type: 'bar',
            data: {
                labels: category,
                datasets: [{
                    // label: 'Seed',
                    backgroundColor: Colors,
                    data: Data,
                    borderWidth: 1,
                    borderRadius: 10,
                    borderSkipped: true,
                }],

            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1000, // Set the maximum value of the y-axis
                    }
                },
                plugins: {
                    legend: {
                        position: 'none',
                    },
                    title: {
                        display: false,
                        text: 'Farmer Chart'
                    }
                },
                barThickness: 50,

            }
        });
    </script>
@endsection
