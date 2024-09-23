@extends('layouts.master')

@section('title','Create')

@section('page')
    <a href="{{ route('admin.salary-tds.index')}}">
        Salary TDS
    </a>
@endsection

@section('sub_page','Create')

@section('main-content')

    <section class="content">

        <div id="showSuccessResponse d-none">
            <div class="alert alert-success successSalaryTDS">
                <p class="successMessage"></p>
            </div>
        </div>

        <div id="showErrorResponse d-none">
            <div class="alert alert-danger errorSalaryTDS">
                <p class="errorMessage"></p>
            </div>
        </div>

        @include('admin.section.flash_message')

        @include('admin.payrollSetting.common.breadcrumb')
        <div class="row">
            <div class="col-lg-2">
                @include('admin.payrollSetting.common.setting_menu')
            </div>
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <h4>Salary TDS Create</h4>
                    </div>
                    <div class="card-body">
                        <form id="salaryTDSAdd" class="forms-sample" action="{{route('admin.salary-tds.store')}}"  method="POST">
                            @csrf
                            <div class="col-lg-3 mb-4">
                                <select class="form-select" id="marital_status" name="marital_status" required >
                                    <option value="" {{old('marital_status') ? '' : 'selected'}}  disabled>Select Marital Status </option>
                                    @foreach(\App\Models\SalaryTDS::MARITAL_STATUS as  $value)
                                        <option value="{{$value}}" {{  (old('marital_status') == $value) ? 'selected': '' }}>  {{ucfirst($value)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="addSalaryTDS">
                                <div class="row salaryTDSList align-items-center justify-content-between mb-3">
                                    <div class="col-lg-3">
                                        <input type="number"
                                               class="form-control"
                                               id="annual_salary_from"
                                               name="annual_salary_from[]"
                                               value=""
                                               placeholder="Enter Annual Salary From">
                                    </div>

                                    <div class="col-lg-3">
                                        <input type="number"
                                               class="form-control"
                                               id="annual_salary_to"
                                               name="annual_salary_to[]"
                                               value=""
                                               placeholder="Enter Annual Salary To">
                                    </div>

                                    <div class="col-lg-3">
                                        <input type="number"
                                               class="form-control"
                                               id="tds_in_percent"
                                               name="tds_in_percent[]"
                                               min="0"
                                               step="0.1"
                                               max="100"
                                               value=""
                                               required
                                               placeholder="Enter TDS In Percent">
                                    </div>

                                    <div class="col-lg-2 text-center addButtonSection float-end">
                                        <button type="button" class="btn btn-md btn-primary" id="add" title="Add More TDS Detail"> Add </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success" id="salaryTDSSubmit"> Submit </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </section>
@endsection

@section('scripts')
    @include('admin.payrollSetting.salaryTDS.common.scripts')
@endsection
