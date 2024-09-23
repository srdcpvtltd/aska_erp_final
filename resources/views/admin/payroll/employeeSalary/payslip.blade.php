@extends('layouts.master')

@section('title', 'Employee Payroll Generate Increment')

@section('action', 'Payroll Create')

@section('button')
    <div class="float-md-end">
        <a href="{{ route('admin.employee-salaries.index') }}">
            <button class="btn btn-sm btn-primary"><i class="link-icon" data-feather="arrow-left"></i> Back</button>
        </a>
    </div>
@endsection

@section('main-content')

    <section class="content">

        @include('admin.section.flash_message')

        @include('admin.payroll.employeeSalary.common.breadcrumb')

        <div class="card">
            <div class="card-body">

                <h4 class="mb-4">
                    Salary Slip

                    <div class="float-end">
                        @can('create-payroll')
                            <a class="me-2"
                                href="{{ route('admin.employee-salary.payroll-print', $payrollData['payslipData']->id) }}"
                                target="_blank">
                                <i class="link-icon" data-feather="printer"></i>
                            </a>
                        @endcan
                        @can('edit-payroll')
                            <a class="me-2"
                                href="{{ route('admin.employee-salary.payroll-edit', $payrollData['payslipData']->id) }}">
                                <i class="link-icon" data-feather="edit"></i>
                            </a>
                        @endcan
                    </div>
                </h4>
                <form class="forms-sample" action="" method="POST">
                    @csrf
                    <div class="payroll-personal">
                        <div class="row align-items-center justify-content-between border-bottom mb-4">
                            <div class="col-lg col-md-6 mb-4">
                                <div class="d-flex align-items-center">

                                    <img class="wd-50 ht-50 rounded-circle" style="object-fit: cover"
                                        src="{{ asset($imagePath . $payrollData['payslipData']->employee_avatar) }}"
                                        alt="{{ $payrollData['payslipData']->employee_name }}">
                                    <div class="text-start ms-3">
                                        <h5 class="mb-1">{{ $payrollData['payslipData']->employee_name }}</h5>
                                        <p class="">{{ $payrollData['payslipData']->employee_email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg col-md-6 mb-4">
                                <div class="gross-sal p-3 border d-inline-block float-md-end text-center">
                                    <span>Employee Gross Salary</span>
                                    <h4>{{ $payrollData['payslipData']->gross_salary }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="row border-bottom mb-4">
                            <div class="col-lg col-md-6 mb-4">
                                <h6>Marital Status</h6>
                                <span>{{ $payrollData['payslipData']->marital_status }}</span>
                            </div>
                            <div class="col-lg col-md-6 mb-4">
                                <h6>Designation</h6>
                                <span>{{ $payrollData['payslipData']->designation }}</span>
                            </div>
                            <div class="col-lg col-md-6 mb-4">
                                <h6>Joining Date</h6>
                                <span>{{ $payrollData['payslipData']->joining_date }}</span>
                            </div>
                            <div class="col-lg col-md-6 mb-4">
                                <h6>Salary Group</h6>
                                <span>{{ $payrollData['payslipData']->salary_group_name }}</span>
                            </div>
                            <div class="col-lg col-md-6 mb-4">
                                <h6>Salary Cycle</h6>
                                <span>{{ $payrollData['payslipData']->salary_cycle }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row border-bottom mb-4">
                        <div class="col-lg col-md-6 mb-4">
                            <h6>Total Days</h6>
                            <span>{{ $payrollData['payslipData']->total_days }}</span>
                        </div>
                        <div class="col-lg col-md-6 mb-4">
                            <h6>Present</h6>
                            <span>{{ $payrollData['payslipData']->present_days }}</span>
                        </div>
                        <div class="col-lg col-md-6 mb-4">
                            <h6>Absent</h6>
                            <span>{{ $payrollData['payslipData']->absent_days }}</span>
                        </div>
                        <div class="col-lg col-md-6 mb-4">
                            <h6>Paid Leave</h6>
                            <span>{{ $payrollData['payslipData']->paid_leave }}</span>
                        </div>
                        <div class="col-lg col-md-6 mb-4">
                            <h6>Unpaid Leave</h6>
                            <span>{{ $payrollData['payslipData']->unpaid_leave }}</span>
                        </div>
                        <div class="col-lg col-md-6 mb-4">
                            <h6>Holidays</h6>
                            <span>{{ $payrollData['payslipData']->holidays }}</span>
                        </div>

                        <div class="col-lg col-md-6 mb-4">
                            <h6>Weekend</h6>
                            <span>{{ $payrollData['payslipData']->weekends }}</span>
                        </div>
                    </div>

                    <div class="payroll-fil border-bottom mb-4">
                        <div class="row">
                            <div class="col-lg-4 col-md-2 mb-4">
                                <h5 class="mb-3">Status</h5>
                                <span
                                    class="p-2 alert alert-{{ $payrollData['payslipData']->status == \App\Enum\PayslipStatusEnum::generated->value ? 'success' : ($payrollData['payslipData']->status == \App\Enum\PayslipStatusEnum::review->value ? 'warning' : ($payrollData['payslipData']->status == \App\Enum\PayslipStatusEnum::locked->value ? 'danger' : 'primary')) }}">{{ ucfirst($payrollData['payslipData']->status) }}</span>
                            </div>
                            <div class="col-lg-4 col-md-5 mb-4">
                                <h5 class="mb-2">Salary From</h5>

                                <input id="salaryTo" readonly name="salary_from"
                                    value="{{ \App\Helpers\AttendanceHelper::payslipDate($payrollData['payslipData']->salary_from) }}"
                                    class="form-control" type="text">
                            </div>
                            <div class="col-lg-4 col-md-5 mb-4">
                                <h5 class="mb-2">Salary To</h5>
                                <input id="salaryTo" readonly name="salary_to"
                                    value="{{ \App\Helpers\AttendanceHelper::payslipDate($payrollData['payslipData']->salary_to) }}"
                                    class="form-control" type="text">
                            </div>
                        </div>
                    </div>

                    <div class="payroll-earn-ded">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 mb-4">
                                <h4 class="mb-2">Earning</h4>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr class="earning">
                                            <td class="d-flex align-items-center justify-content-between">
                                                <strong>Basic Salary</strong>
                                                <input type="text" readonly class="form-control w-50"
                                                    value="{{ $payrollData['payslipData']->salary_cycle == 'weekly' ? $payrollData['payslipData']->weekly_basic_salary : $payrollData['payslipData']->monthly_basic_salary }}">
                                            </td>
                                        </tr>
                                        @php
                                            if ($payrollData['payslipData']->salary_cycle == 'weekly') {
                                                $totalEarning =
                                                    $payrollData['payslipData']->weekly_basic_salary +
                                                    $payrollData['payslipData']->weekly_fixed_allowance;
                                            } else {
                                                $totalEarning =
                                                    $payrollData['payslipData']->monthly_basic_salary +
                                                    $payrollData['payslipData']->monthly_fixed_allowance;
                                            }
                                        @endphp
                                        @forelse($payrollData['earnings'] as $earning)
                                            <tr class="earning">
                                                <td class="d-flex align-items-center justify-content-between">
                                                    <strong>{{ $earning['name'] }}</strong>
                                                    <input type="number" step="0.1" min="0" readonly
                                                        class="form-control w-50" value="{{ $earning['amount'] }}">
                                                </td>
                                            </tr>
                                            @php $totalEarning += $earning['amount'];  @endphp
                                        @empty
                                        @endforelse
                                        <tr class="earning">
                                            <td class="d-flex align-items-center justify-content-between">
                                                <strong>Fixed Allowance</strong>
                                                <input type="text" readonly step="0.1" min="0"
                                                    class="form-control w-50"
                                                    value="{{ $payrollData['payslipData']->salary_cycle == 'weekly' ? $payrollData['payslipData']->weekly_fixed_allowance : $payrollData['payslipData']->monthly_fixed_allowance }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Earning</strong> <span
                                                    class="float-end"><strong>{{ $totalEarning }}</strong></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-lg-6 col-md-6 mb-4">
                                <h4 class="mb-2">Deduction</h4>
                                <table class="table table-bordered">
                                    <tbody>
                                        @php
                                            $totalDeduction = $payrollData['payslipData']->tds;
                                        @endphp
                                        @forelse($payrollData['deductions'] as $deduction)
                                            <tr class="deductions">
                                                <td class="d-flex align-items-center justify-content-between">
                                                    <strong>{{ $deduction['name'] }}</strong>
                                                    <input type="number" step="0.1" min="0" readonly
                                                        class="form-control w-50" value="{{ $deduction['amount'] }}">
                                                </td>
                                            </tr>
                                            @php $totalDeduction += $deduction['amount']; @endphp
                                        @empty
                                        @endforelse
                                        <tr class="deductions">
                                            <td class="d-flex align-items-center justify-content-between">
                                                <strong>TDS</strong>
                                                <input type="text" readonly step="0.1" min="0"
                                                    class="form-control w-50"
                                                    value="{{ $payrollData['payslipData']->tds }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Total Deductions</strong> <span
                                                    class="float-end"><strong>{{ $totalDeduction }}</strong></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-12 border-top">
                                <div class="row align-items-center">
                                    <div class="col-lg col-md-6 mt-4">
                                        <label class="mb-1 fw-bold">Actual Salary</label> (Total Earning - Total
                                        Deductions)
                                    </div>
                                    <div class="col-lg col-md-6 mt-4">
                                        <span class="h5"
                                            id="actual_salary">{{ $currency . ' ' . $payrollData['payslipData']->gross_salary - $totalDeduction }}</span>
                                    </div>

                                </div>
                            </div>


                            @if ($payrollData['payslipData']->include_tada == 1)
                                <div class="col-lg-6 col-md-6 border-top mt-4 pt-4">
                                    <div class="row align-items-center">
                                        <div class="col-lg-9">
                                            <small style="color:#e82e5f;">Earning*</small><br><label
                                                class="mb-0 fw-bold">TADA</label>
                                        </div>
                                        <div class="col-lg-3">
                                            <input id="tada" readonly name="tada"
                                                value="{{ $payrollData['payslipData']->tada }}" class="form-control"
                                                type="text">
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ($payrollData['payslipData']->include_advance_salary == 1)
                                <div class="col-lg-6 col-md-6 border-top mt-4 pt-4">
                                    <div class="row align-items-center">
                                        <div class="col-lg-9">
                                            <small style="color:#e82e5f;">Deduction*</small><br><label
                                                class="mb-0 fw-bold">Advance Salary</label>
                                        </div>
                                        <div class="col-lg-3">
                                            <input id="advance_salary" readonly name="advance_salary"
                                                value="{{ $payrollData['payslipData']->advance_salary }}"
                                                class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-lg-6 col-md-6 border-top mt-4 pt-4">
                                <div class="row align-items-center">

                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 border-top mt-4 pt-4">
                                <div class="row align-items-center">
                                    <div class="col-lg-9">
                                        <small style="color:#e82e5f;">Deduction*</small><br><label
                                            class="mb-0 fw-bold">Absent</label>
                                        ((grossSalary/ total days) * absent days)
                                    </div>

                                    <div class="col-lg-3">
                                        <input id="absent_deduction" readonly name="absent_deduction"
                                            value="{{ $payrollData['payslipData']->absent_deduction }}"
                                            class="form-control" type="text">
                                    </div>
                                </div>
                            </div>

                            @if (isset($payrollData['payslipData']->ot_status) && $payrollData['payslipData']->ot_status == 1)
                                <div class="col-lg-6 col-md-6 border-top mt-4 pt-4">
                                    <div class="row align-items-center">
                                        <div class="col-lg-9">
                                            <small style="color:#e82e5f;">Earning*</small><br><label
                                                class="mb-0 fw-bold">OverTime</label>
                                        </div>
                                        <div class="col-lg-3">
                                            <input id="overtime" readonly name="overtime"
                                                value="{{ $payrollData['payslipData']->overtime }}" class="form-control"
                                                type="text">
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (isset($underTimeSetting) && $underTimeSetting->is_active == 1)
                                <div class="col-lg-6 col-md-6 border-top mt-4 pt-4">
                                    <div class="row align-items-center">
                                        <div class="col-lg-9">
                                            <small style="color:#e82e5f;">Deduction*</small><br><label
                                                class="mb-0 fw-bold">UnderTime</label>
                                        </div>
                                        <div class="col-lg-3">
                                            <input id="undertime" readonly name="undertime"
                                                value="{{ $payrollData['payslipData']->undertime }}" class="form-control"
                                                type="text">
                                        </div>
                                    </div>
                                </div>
                            @endif



                            <div class="col-lg-12 border-top mt-4 pt-4">
                                <h4 class="mb-1">Net Salary :
                                    {{ $currency . ' ' . $payrollData['payslipData']->net_salary }}</h4>
                                Net Salary = (Actual Salary - Advance Salary + TADA)
                            </div>
                        </div>
                    </diV>

                </form>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('admin.payroll.employeeSalary.common.scripts')
@endsection
