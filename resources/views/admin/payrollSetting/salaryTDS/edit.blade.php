@extends('layouts.master')

@section('title','Edit')

@section('page')
    <a href="{{ route('admin.salary-tds.index')}}">
        Salary TDS
    </a>
@endsection

@section('sub_page','Edit')

@section('main-content')

    <section class="content">

        @include('admin.section.flash_message')

        @include('admin.payrollSetting.common.breadcrumb')
        <div class="row">
            <div class="col-lg-2">
                @include('admin.payrollSetting.common.setting_menu')
            </div>
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <h4>Salary TDS Update</h4>
                    </div>
                    <div class="card-body">
                        <form  class="forms-sample" action="{{route('admin.salary-tds.update',$salaryTDSDetail->id)}}"  method="POST">
                            @method('PUT')
                            @csrf
                            <div class="row align-items-center justify-content-between mb-3">
                                <div class="col-lg-3 ">
                                    <select class="form-select" id="marital_status" name="marital_status" required >
                                        <option value="" {{old('marital_status') ? '' : 'selected'}}  disabled>Select Marital Status </option>
                                        @foreach(\App\Models\SalaryTDS::MARITAL_STATUS as  $value)
                                            <option value="{{$value}}" {{  ($salaryTDSDetail->marital_status == $value) ? 'selected': '' }}>  {{ucfirst($value)}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-3">
                                    <input type="number"
                                           class="form-control"
                                           id="annual_salary_from"
                                           name="annual_salary_from"
                                           value="{{ $salaryTDSDetail->annual_salary_from }}"
                                           required
                                           placeholder="Enter Annual Salary From">
                                </div>

                                <div class="col-lg-3">
                                    <input type="number"
                                           class="form-control"
                                           id="annual_salary_to"
                                           name="annual_salary_to"
                                           value="{{ $salaryTDSDetail->annual_salary_to }}"
                                           required
                                           placeholder="Enter Annual Salary To">
                                </div>

                                <div class="col-lg-3">
                                    <input type="number"
                                           class="form-control"
                                           id="tds_in_percent"
                                           name="tds_in_percent"
                                           min="0"
                                           step="0.1"
                                           max="100"
                                           value="{{ $salaryTDSDetail->tds_in_percent }}"
                                           required
                                           placeholder="Enter TDS In Percent">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success"> Update </button>
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
