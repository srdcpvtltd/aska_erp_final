@extends('layouts.master')

@section('title', 'Leave Requests')

@section('action', 'Lists')

@section('button')
    @can('create-time_leave_request')
        <a href="{{ route('admin.time-leave-request.create') }}">
            <button class="btn btn-primary">
                <i class="link-icon" data-feather="plus"></i>Create Time Leave Request
            </button>
        </a>
    @endcan
@endsection

@section('main-content')
    <?php
    if (\App\Helpers\AppHelper::ifDateInBsEnabled()) {
        $filterData['min_year'] = '2076';
        $filterData['max_year'] = '2089';
        $filterData['month'] = 'np';
    } else {
        $filterData['min_year'] = '2020';
        $filterData['max_year'] = '2033';
        $filterData['month'] = 'en';
    }
    ?>

    <section class="content">

        @include('admin.section.flash_message')

        @include('admin.timeLeaveRequest.common.breadcrumb')
        <div class="row">
            <div class="col-lg-2">
                @include('admin.leaveRequest.common.leave_menu')
            </div>
            <div class="col-lg-10">
                <div class="search-box p-4 bg-white rounded mb-3 box-shadow">
                    <form class="forms-sample" action="{{ route('admin.time-leave-request.index') }}" method="get">

                        <h5>Time Leave Request Filter</h5>

                        <div class="row align-items-center">

                            <div class="col-xxl col-xl-4 col-md-6 mt-3">
                                <input type="text" placeholder="Requested by" id="requestedBy" name="requested_by"
                                    value="{{ $filterParameters['requested_by'] }}" class="form-control">
                            </div>

                            <div class="col-xxl col-xl-4 col-md-6 mt-3">
                                <input type="number" min="{{ $filterData['min_year'] }}"
                                    max="{{ $filterData['max_year'] }}" step="1"
                                    placeholder="Leave Requested year e.g : {{ $filterData['min_year'] }}" id="year"
                                    name="year" value="{{ $filterParameters['year'] }}" class="form-control">
                            </div>

                            <div class="col-xxl col-xl-4 col-md-6 mt-3">
                                <select class="form-select form-select-lg" name="month" id="month">
                                    <option value="" {{ !isset($filterParameters['month']) ? 'selected' : '' }}>All
                                        Month</option>
                                    @foreach ($months as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ isset($filterParameters['month']) && $key == $filterParameters['month'] ? 'selected' : '' }}>
                                            {{ $value[$filterData['month']] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xxl col-xl-4 col-md-6 mt-3">
                                <select class="form-select form-select-lg" name="status" id="status">
                                    <option value="" {{ !isset($filterParameters['status']) ? 'selected' : '' }}>All
                                        Status</option>
                                    @foreach (\App\Models\LeaveRequestMaster::STATUS as $value)
                                        <option value="{{ $value }}"
                                            {{ isset($filterParameters['status']) && $value == $filterParameters['status'] ? 'selected' : '' }}>
                                            {{ ucfirst($value) }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xxl col-xl-4 mt-3">
                                <div class="d-flex float-end">
                                    <button type="submit" class="btn btn-block btn-secondary me-2">Filter</button>
                                    <a class="btn btn-block btn-primary"
                                        href="{{ route('admin.time-leave-request.index') }}">Reset</a>
                                </div>
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
                                        <th>#</th>
                                        <th>Leave Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Requested By</th>
                                        @can('show-time_leave_request')
                                            <th class="text-center">Reason</th>
                                        @endcan
                                        @can('edit-time_leave_request')
                                            <th class="text-center">Status</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php
                                        $color = [
                                            \App\Enum\LeaveStatusEnum::approved->value => 'success',
                                            \App\Enum\LeaveStatusEnum::rejected->value => 'danger',
                                            \App\Enum\LeaveStatusEnum::pending->value => 'secondary',
                                            \App\Enum\LeaveStatusEnum::cancelled->value => 'danger',
                                        ];
                                        
                                        ?>
                                        @forelse($timeLeaves as $key => $value)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \App\Helpers\AppHelper::timeLeaverequestDate($value->issue_date) }}</td>
                                        <td>{{ \App\Helpers\AppHelper::convertLeaveTimeFormat($value->start_time) }}</td>
                                        <td>{{ \App\Helpers\AppHelper::convertLeaveTimeFormat($value->end_time) }}</td>
                                        <td>{{ $value->leaveRequestedBy ? ucfirst($value->leaveRequestedBy->name) : 'N/A' }}
                                        </td>

                                        @can('show-time_leave_request')
                                            <td class="text-center">
                                                <a href="" id="showLeaveReason"
                                                    data-href="{{ route('admin.time-leave-request.show', $value->id) }}"
                                                    data-id="" title="show leave reason ">
                                                    <i class="link-icon" data-feather="eye"></i>
                                                </a>
                                            </td>
                                        @endcan

                                        @can('edit-time_leave_request')
                                            <td class="text-center">
                                                <a href="" id="leaveRequestUpdate"
                                                    data-href="{{ route('admin.time-leave-request.update-status', $value->id) }}"
                                                    data-status="{{ $value->status }}"
                                                    data-remark="{{ $value->admin_remark }}">
                                                    <button class="btn btn-{{ $color[$value->status] }} btn-xs">
                                                        {{ ucfirst($value->status) }}
                                                    </button>
                                                </a>
                                            </td>
                                        @endcan
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

    </section>
    <div class="dataTables_paginate mt-3">
        {{ $timeLeaves->appends($_GET)->links() }}
    </div>

    @include('admin.timeLeaveRequest.show')
    @include('admin.timeLeaveRequest.common.form-model')
@endsection

@section('scripts')
    @include('admin.timeLeaveRequest.common.scripts')
@endsection
