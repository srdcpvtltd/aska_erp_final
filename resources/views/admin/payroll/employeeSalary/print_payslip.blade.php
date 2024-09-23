<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            box-sizing: border-box;
        }

        .wrapper{
            padding:20px;
            border: 1px solid #f1f1f1;
            margin: 20px 0;
        }

        header div {
            display: flex;
            justify-content: center;
        }

        header img {
            width: 150px;
        }
        header h2, header p {
            text-align: center;
        }

        header h2{margin-top:0;}

        table{width: 100%;}

        .separator {
            border-top: 1px solid #f1f1f1;
            margin: 20px 0;
        }

        .payslip-heading {
            margin: 15px 0;
            text-align:center;        }

        .employee-info {
            margin-bottom: 20px;
            text-align: left;
        }

        .employee-info table{width: 100%;}

        .employee-info table th,  .employee-info table td {
            padding: 10px;
            border:1px solid #f1f1f1;
        }

         .attendance-info {
            margin-bottom: 20px;
            text-align: left;
        }
        .attendance-info table th, .attendance-info table td {
            padding: 10px;
            border: 1px solid #f1f1f1;
            background: #fbfbfb;
        }

        .tables-wrapper {
            display: flex;
            margin: 20px 0;
            gap:20px;
        }

        .table-container {
            flex: 1; /* Adjust as needed */
            width: 50%;
            margin-right: 2px; /* Add margin between tables if needed */
        }

        .table-container table {
            width: 100%;
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
            border-collapse: collapse;
        }


        .table-container th{
            background-color: #efefef;
            padding: 10px;
            text-align: left;
        }
        .table-container td {
            padding: 10px;
            text-align: left;
            border:1px solid #f1f1f1;
        }

        .other_info{
            display: flex;
            margin: 20px 0;
        }
        .other_info table {
            width: 100%;
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
            border-collapse: collapse;
        }

        .other_info th, .other_info td{
            padding: 10px;
            text-align: left;
            border:1px solid #f1f1f1;
        }
        .salary{
            background-color: #efefef;
        }

        .horizontal-line {
            border-top: 1px solid #f1f1f1;
            margin: 20px 0;
        }

        .net-salary {
            text-align: center;
            margin: 0;
            font-weight: bold;
        }

        .net-salary p{margin-bottom:0;}

    </style>
</head>
<body>
    <div class="wrapper">
        <header>
            <!-- Company Logo, Name, Address, Email, and Phone -->
                <div>
                    <img src="{{ asset($companyLogoPath.$payrollData['payslipData']->company_logo) }}" alt="Company Logo">
                </div>


            <h2>{{ $payrollData['payslipData']->company_name }}</h2>
            <p> {{ $payrollData['payslipData']->company_address }} | Email: {{ $payrollData['payslipData']->company_email }} | Phone: {{ $payrollData['payslipData']->company_phone }}</p>
        </header>

        <!-- Horizontal Line -->
        <div class="separator"></div>

        <!-- Payslip Heading -->
        <div class="payslip-heading">
            <h3>
                @if($payrollData['payslipData']->salary_cycle == 'monthly')
                    Payslip for the Month of {{  \App\Helpers\AppHelper::getMonthYear($payrollData['payslipData']->salary_from) }}
                @else
                    Payslip from {{  \App\Helpers\AttendanceHelper::payslipDate($payrollData['payslipData']->salary_from) }} to {{ \App\Helpers\AttendanceHelper::payslipDate($payrollData['payslipData']->salary_to)  }}
                @endif

            </h3>
        </div>

        <!-- Employee Information -->
        <div class="employee-info">
            <table>
                <tr>
                    <th>Employee ID:</th><td>{{ $payrollData['payslipData']->employee_code }}</td>
                    <th>Name:</th><td>{{ $payrollData['payslipData']->employee_name }}</td>
                </tr>
                <tr>
                    <th>Salary Slip:</th><td>XYZ-001</td>
                    <th>Department:</th><td>{{ $payrollData['payslipData']->department }}</td>
                </tr>
                <tr>
                    <th>Designation:</th><td>{{ $payrollData['payslipData']->designation }}</td>
                    <th>Joining Date:</th><td>{{ $payrollData['payslipData']->joining_date }}</td>
                </tr>

            </table>

        </div>
        <div class="attendance-info">
            <table>
                <tr>
                    <th>Total Days</th>
                    <td>{{  $payrollData['payslipData']->total_days }}</td>
                    <th>Present</th>
                    <td>{{ $payrollData['payslipData']->present_days }}</td>
                    <th>Absent</th>
                    <td>{{ $payrollData['payslipData']->absent_days }}</td>
                    <th>Leave</th>
                    <td>{{ $payrollData['payslipData']->leave_days }}</td>
                    <th>Holidays</th>
                    <td>{{ $payrollData['payslipData']->holidays }}</td>
                    <th>Weekend</th>
                    <td>{{ $payrollData['payslipData']->weekends }}</td>
                </tr>

            </table>

        </div>
        <!-- Tables for Earnings and Deductions -->
        <div class="tables-wrapper">
            <!-- Table for Earnings -->
            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>Earnings</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td> {{ ($payrollData['payslipData']->salary_cycle == 'weekly') ? $payrollData['payslipData']->weekly_basic_salary :$payrollData['payslipData']->monthly_basic_salary }}</td>
                        </tr>
                        @php $totalEarning = 0; @endphp
                        @forelse( $payrollData['earnings'] as $earning)
                            <tr>
                                <td>{{ $earning['name'] }}</td>
                                <td>{{ $earning['amount'] }}</td>
                                @php $totalEarning+=$earning['amount']; @endphp
                            </tr>
                        @empty
                        @endforelse
                        <tr>
                            <td>Fixed Allowance</td>
                            <td> {{ ($payrollData['payslipData']->salary_cycle == 'weekly') ? $payrollData['payslipData']->weekly_fixed_allowance :  $payrollData['payslipData']->monthly_fixed_allowance }}</td>
                        </tr>
                        <tr class="totals">
                            <th>Gross Earnings</th>
                            <th> {{ $payrollData['payslipData']->gross_salary }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table for Deductions -->
            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>Deductions</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php $totalDeduction = $payrollData['payslipData']->tds; @endphp
                        @forelse( $payrollData['deductions'] as $deduction)
                            <tr>
                                <td>{{ $deduction['name'] }}</td>
                                <td>{{ $deduction['amount'] }}</td>
                                @php $totalDeduction+=$deduction['amount']; @endphp
                            </tr>
                        @empty
                        @endforelse
                        <tr>
                            <td>TDS</td>
                            <td>{{ $payrollData['payslipData']->tds }}</td>
                        </tr>
                        <tr class="totals">
                            <th>Total Deductions</th>
                            <th> {{ $totalDeduction }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="other_info">
            <table>

                <tr class="salary">
                    <th>Actual Salary <span style="font-weight: normal">(Total Earning - Total Deductions)</span></th>
                    <th>{{ $currency.' '. $payrollData['payslipData']->gross_salary - $totalDeduction }}</th>
                </tr>

                @if($payrollData['payslipData']->include_advance_salary ==1)
                    <tr>
                        <th>Advance Salary <span style="font-weight: normal">(-)</span></th>
                        <td> {{ $payrollData['payslipData']->advance_salary ?? 0 }}</td>
                    </tr>
                @endif

                @if($payrollData['payslipData']->include_tada ==1)
                    <tr>
                        <th>TADA <span style="font-weight: normal">(+)</span></th>
                        <td> {{ $payrollData['payslipData']->tada ?? 0 }}</td>
                    </tr>
                @endif

                <tr>

                </tr>
                <tr>
                    <th>Absent Deduction <span style="font-weight: normal">((grossSalary/ total days) * absent days)</span></th>
                    <th>
{{--                        @php--}}
{{--                            $deductionFee = ($payrollData['payslipData']->gross_salary / $payrollData['payslipData']->total_days) * $payrollData['payslipData']->absent_days;--}}

{{--                        @endphp--}}
                        {{ $payrollData['payslipData']->absent_deduction ?? 0 }}
                    </th>
                </tr>
                @if(isset($payrollData['payslipData']->ot_status) && $payrollData['payslipData']->ot_status  == 1)

                <tr>
                    <th>OverTime Income</th>
                    <th>

                        {{ $payrollData['payslipData']->overtime }}
                    </th>
                </tr>
                @endif
                @if(isset($underTimeSetting) && $underTimeSetting->is_active  == 1)
                <tr>
                    <th>UnderTime Deduction</th>
                    <th>

                        {{ $payrollData['payslipData']->undertime }}
                    </th>
                </tr>
                @endif
            </table>
        </div>

        <!-- Net Salary -->
        <div class="net-salary">
            <p>Net Salary: {{ $currency.' '. $payrollData['payslipData']->net_salary }}</p>
            <p>
                ({{ $numberToWords->get($payrollData['payslipData']->net_salary) }})</p>
            <p style="font-weight: normal">Net Salary = (Actual Salary - Advance Salary + TADA)</p>
        </div>
    </div>
</body>
<script>
    window.print();
    window.onfocus = function () {
        window.close();
    }
</script>
</html>
