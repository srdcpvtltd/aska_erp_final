@extends('layouts.master')

@section('title', 'Employee Salary')

@section('action', 'List')

@section('main-content')

    <section class="content">

        @include('admin.section.flash_message')

        @include('admin.payroll.employeeSalary.common.breadcrumb')

        <div class="search-box p-4 pb-0 bg-white rounded mb-3 box-shadow">
            <form class="forms-sample" action="{{ route('admin.employee-salaries.index') }}" method="get">
                <h5>Employee Salary Filter</h5>
                <div class="row align-items-center mt-3">
                    <div class="col-lg col-md-4 mb-4">
                        <label for="" class="form-label">Department</label>
                        <select class="form-select" id="salary_cycle" name="salary_cycle">
                            <option value="">All</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department['id'] }}"
                                    {{ $filterParameters['department_id'] == $department['id'] ? 'selected' : '' }}>
                                    {{ ucfirst($department['dept_name']) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg col-md-5 mb-4">
                        <label for="" class="form-label">Employee Name </label>
                        <input type="text" id="employee_name" name="employee_name"
                            value="{{ $filterParameters['employee_name'] }}" class="form-control">
                    </div>



                    <div class="col-lg-2 col-md-3 mt-md-4 mb-4">
                        <div class="d-flex float-md-end">
                            <button type="submit" class="btn btn-block btn-secondary me-2">Filter</button>
                            <a class="btn btn-block btn-primary"
                                href="{{ route('admin.employee-salaries.index') }}">Reset</a>
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
                                <th>Employee Name</th>
                                <th class="text-center">Marital Status</th>
                                {{--                            <th>Gross Salary({{\App\Helpers\AppHelper::getCompanyPaymentCurrencySymbol()}}.)</th> --}}
                                <th class="text-center">Salary Cycle</th>
                                <th class="text-center">Salary Group</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employeeLists as $key => $value)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ ucfirst($value->employee_name) }}</td>
                                    <td class="text-center">{{ ucfirst($value->marital_status) }}</td>
                                    {{--                                    <td>{{ number_format($value->salary) }}</td> --}}
                                    <td class="text-center">
                                        <select class="form-control-sm" name="salary_cycle" id="salaryCycle"
                                            data-employee="{{ $value->employee_id }}"
                                            data-current="{{ $value->salary_cycle }}">
                                            @foreach (\App\Models\EmployeeAccount::SALARY_CYCLE as $salaryCycle)
                                                <option value="{{ $salaryCycle }}"
                                                    {{ $value->salary_cycle == $salaryCycle ? 'selected' : '' }}>
                                                    {{ ucfirst($salaryCycle) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">{{ ucfirst($value->salary_group_name) }}</td>



                                    <td class="text-center">
                                        <a class="nav-link dropdown-toggle" href="#" id="payslipDropdown"
                                            role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" title="More Action"> </a>

                                        <div class="dropdown-menu p-0" aria-labelledby="payslipDropdown">
                                            <ul class="list-unstyled p-1">
                                                @php
                                                    $employeeSalaryStatus = \App\Helpers\AttendanceHelper::checkEmployeeSalary(
                                                        $value->employee_id,
                                                    );
                                                @endphp

                                                @if ($employeeSalaryStatus == 0)
                                                    @can('add-employee_salary')
                                                        <li class="dropdown-item py-2">
                                                            <a title="generate payroll"
                                                                href="{{ route('admin.employee-salaries.add', $value->employee_id) }}">
                                                                <button class="btn btn-primary btn-xs">Add Salary
                                                                </button>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                @else
                                                    @can('edit-employee_salary')
                                                        <li class="dropdown-item py-2">
                                                            <a title="generate payroll"
                                                                href="{{ route('admin.employee-salaries.edit-salary', $value->employee_id) }}">
                                                                <button class="btn btn-primary btn-xs">Edit Salary
                                                                </button>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    <li class="dropdown-item py-2">
                                                        <a title="Update Employee Salary"
                                                            href="{{ route('admin.employee-salaries.increase-salary', $value->employee_id) }}">
                                                            <button class="btn btn-primary btn-xs">Increase Salary
                                                            </button>
                                                        </a>
                                                    </li>
                                                    <li class="dropdown-item py-2">
                                                        <a href="{{ route('admin.employee-salaries.salary-revise-history.show', $value->employee_id) }}"
                                                            class="viewSalaryReviseHistory me-2"
                                                            title="show salary revised log">
                                                            <button class="btn btn-primary btn-xs">Salary Review History
                                                            </button>
                                                        </a>
                                                    </li>
                                                    @can('delete-employee_salary')
                                                        <li class="dropdown-item py-2">
                                                            <a data-href="{{ route('admin.employee-salaries.delete-salary', $value->employee_id) }}"
                                                                class="deleteEmployeeSalary me-2"
                                                                title="show salary revised log">
                                                                <button class="btn btn-primary btn-xs">Delete
                                                                </button>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                @endif

                                            </ul>
                                        </div>
                                    </td>
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
    </section>

@endsection

@section('scripts')
    @include('admin.payroll.employeeSalary.common.scripts')
@endsection
