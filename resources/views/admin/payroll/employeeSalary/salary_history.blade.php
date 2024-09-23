@extends('layouts.master')

@section('title','Salary Increment Log ')

@section('action','Salary Increment Log')

@section('button')
    <div class="float-end">
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
                <h4 class="mb-4">{{ucfirst($employeeDetail->name)}} Salary Increment Log</h4>
                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-center">Salary Revised On</th>
                            <th class="text-center">Increment By</th>
                            <th class="text-center">Increment Value</th>
                            <th class="text-center">New Salary({{\App\Helpers\AppHelper::getCompanyPaymentCurrencySymbol()}})</th>
                            <th class="text-center">Old Salary({{\App\Helpers\AppHelper::getCompanyPaymentCurrencySymbol()}})</th>
                            <th class="text-center">Remark</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($salaryReviseLists as $key => $value)
                                <tr>
                                    <td>{{++$key}}</td>
                                    <td class="text-center">{{ ($value->salary_revised_on) }}</td>
                                    <td class="text-center">{{ $value->increment_percent.'%' }}</td>
                                    <td class="text-center">{{ \App\Helpers\AppHelper::getCompanyPaymentCurrencySymbol(). $value->increment_amount }}</td>
                                    <td class="text-center">{{ number_format($value->revised_salary) }}</td>
                                    <td class="text-center">{{ number_format($value->base_salary) }}</td>
                                    <td class="text-center">
                                        <a href="#"
                                           id="showRemark"
                                           data-remark="{{$value->remark}} ">
                                            <i class="link-icon" data-feather="eye"></i>
                                        </a>
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

        <div class="modal fade" id="addslider" tabindex="-1" aria-labelledby="addslider" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                    </div>
                    <div class="modal-body">
                        <strong>Remark:</strong> <p class="remark"> </p>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('scripts')
    <script>
        $('body').on('click','#showRemark', function (e) {
            e.preventDefault();
            let remark = $(this).data('remark');
            $('.modal-title').html('Salary Increment Remark');
            $('.remark').text(remark);
            $('#addslider').modal('show');
        }).trigger("change");
    </script>
@endsection







