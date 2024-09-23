@extends('layouts.master')

@section('title','Employee Payroll Generate Increment')

@section('action','Payroll Create')

@section('button')
    <div class="float-md-end">
        <a href="{{route('admin.employee-salaries.index')}}" >
            <button class="btn btn-sm btn-primary" ><i class="link-icon" data-feather="arrow-left"></i> Back</button>
        </a>
    </div>
@endsection

@section('main-content')

    <section class="content">

        @include('admin.section.flash_message')

        @include('admin.payroll.employeeSalary.common.breadcrumb')

        <div class="card">
            <div class="card-body">
                <h4 class="mb-4"> Payroll Create- {{ $employee->name }}</h4>

                <form class="forms-sample" action="{{ route('admin.employee-salaries.store-salary',$employee->id) }}" method="POST">
                    @csrf
                    <input type="hidden" readonly name="employee_id" value ="{{$employee->id}}">
                    <div class="payroll-fil border-bottom mb-4 pb-4" x-data="createEmployeeSalary('{{$percentType}}', {{ json_encode($salaryComponents) }})">
                        <div class="d-flex align-items-center mb-3">
                            <div class=" p-2">
                                <label for="annualSalary">Annual</label>
                            </div>
                            <div class="">
                                <label class="switch">
                                    <input class="toggleStatus" type="checkbox" x-model="salary_base">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class=" p-2">
                                <label for="weeklySalary">Hourly</label>
                            </div>
                        </div>
                        <!-- Conditional rendering based on the toggle switch -->
                        <div class="row">
                            <div class="col-lg-4 col-md-4 mb-4"  x-show="salary_base">
                                <label for="hourRate" class="form-label">Hourly Rate</label>

                                <input type="number" min="0" step="0.01" x-model="hour_rate" name="hour_rate" class="form-control" @input="calculateAnnualSalary()" oninput="validity.valid||(value='');" placeholder="Enter Hourly Rate"" id="hourRate">
                            </div>
                            <div class="col-lg-4 col-md-4 mb-4"  x-show="salary_base">
                                <label for="weeklySalary" class="form-label">Working Hours in Week</label>

                                <input type="number" min="0" step="0.1" x-model="weekly_hour" class="form-control" @input="calculateAnnualSalary()" oninput="validity.valid||(value='');" placeholder="Enter Hourly Rate" name="weekly_hour" id="weeklyHour">
                            </div>
                            <div class="col-lg-4 col-md-4 mb-4">
                                <label for="annualSalary" class="form-label">Annual Salary (CTC)</label>
                                <input type="number" min="0" step="0.1" x-model="annual_salary" class="form-control" @input="calculateSalary()" oninput="validity.valid||(value='');" placeholder="Enter Annual Salary"  name="annual_salary" id="annualSalary" x-bind:readonly="salary_base ? true : false">

                            </div>

                        </div>

                        <div class="row table-responsive">
                                <table class="table border-end">
                                <thead>
                                    <tr>
                                        <th>Salary Component</th>
                                        <th>Calculation Type</th>
                                        <th>Monthly Amount</th>
                                        <th>Annual Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4"> <h4> Earnings</h4></td>
                                    </tr>
                                    <tr>
                                        <td>Basic Salary</td>
                                        <td>
                                            <div style="display: flex;">
                                                <input type="number" min="0" step="0.1" class="form-control" @input="calculateSalary()" x-model="basic_salary_value" name="basic_salary_value" id="basicSalaryValue" style="width: 60%;">

                                                <select class="form-control" x-model="basic_salary_type" @change="calculateSalary()" name="basic_salary_type" style="width: 35%;">
                                                    <option value="{{ $percentType }}">% of Salary</option>
                                                    <option value="{{ $fixedType }}">{{ ucfirst($fixedType) }}</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" readonly x-model="monthly_basic_salary" class="form-control" name="monthly_basic_salary" id="monthlyBasicSalary">
                                        </td>
                                        <td>
                                            <input type="number" readonly class="form-control" x-model="annual_basic_salary" name="annual_basic_salary" id="annualBasicSalary">
                                        </td>

                                    </tr>
                                    <template x-for="(income, index) in incomes" :key="index">
                                        <tr>
                                            <td x-text="income.name"></td>
                                            <td>
                                                <div style="display: flex;">
                                                    <input style="text-align:center; border:none; background: inherit;" type="text" readonly min="0" step="0.1" class="form-control" x-model="income.value_type" name="value_type">
                                                    <input style="text-align:center; border:none; background: inherit;" type="number" readonly min="0" step="0.1" class="form-control" x-show="income.value_type !== 'fixed' " x-model="income.component_value_monthly" name="component_value_monthly">

                                                </div>
                                            </td>
                                            <td>
                                                <input style="border:none; background: inherit;" type="number" readonly x-model="income.monthly" class="form-control" :name="income.name+'_month_value'">

                                            </td>
                                            <td>
                                                <input style="border:none; background: inherit;" type="number" readonly class="form-control" x-model="income.annual" :name="income.name+'_annual_value'">
                                            </td>

                                        </tr>
                                    </template>

                                    <tr>
                                        <td>Fixed Allowance</td>
                                        <td>Fixed Allowance</td>
                                        <td>
                                            <input style="border:none; background: inherit;" class="form-control" type="number" readonly x-model="monthly_fixed_allowance" name="monthly_fixed_allowance" id="monthlyFixedAllowance">
                                        </td>
                                        <td>
                                            <input style="border:none; background: inherit;" class="form-control" type="number" readonly x-model="annual_fixed_allowance" name="annual_fixed_allowance" id="annualFixedAllowance">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th>
                                            <input style="border:none; background: inherit;" class="form-control" type="number" readonly x-model="monthly_total" name="monthly_total" id="monthlyTotal">
                                        </th>
                                        <th>
                                            <input style="border:none; background: inherit;" class="form-control" type="number" readonly x-model="annual_total" name="annual_total" id="annualTotal">
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan="4"> <h4> Deductions</h4></td>
                                    </tr>
                                    <template x-for="(deduction, index) in deductions" :key="index">
                                        <tr>
                                            <td x-text="deduction.name"></td>
                                            <td>
                                                <div style="display: flex;">
                                                    <input style="text-align:center; border:none; background: inherit;" type="text" readonly min="0" step="0.1" class="form-control" x-model="deduction.value_type" name="value_type">
                                                    <input style="text-align:center; border:none; background: inherit;" type="number" readonly min="0" step="0.1" class="form-control" x-show="deduction.value_type !== 'fixed' " x-model="deduction.component_value_monthly" name="component_value_monthly">

                                                </div>
                                            </td>
                                            <td>
                                                <input style="border:none; background: inherit;" type="number" readonly x-model="deduction.monthly" class="form-control" :name="deduction.name+'_month_value'">
                                            </td>
                                            <td>
                                                <input style="border:none; background: inherit;" type="number" readonly class="form-control" x-model="deduction.annual" :name="deduction.name+'_annual_value'">
                                            </td>

                                        </tr>
                                    </template>
                                    <tr>
                                        <td colspan="2">Total</td>
                                        <td x-text="total_monthly_deduction"></td>
                                        <td x-text="total_annual_deduction"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Net Total</th>
                                        <th x-text="net_monthly_salary"></th>
                                        <th x-text="net_annual_salary"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3 mb-3">
                        <button class="btn btn-primary submit-fn mt-2" type="submit">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('admin.payroll.employeeSalary.common.scripts')
    <script src="{{asset('assets/js/salary_calculation.js')}}"></script>
@endsection

