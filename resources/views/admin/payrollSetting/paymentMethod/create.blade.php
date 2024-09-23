@extends('layouts.master')

@section('title','Create')

@section('page')
    <a href="{{ route('admin.payment-methods.index')}}">
        Payment Method
    </a>
@endsection

@section('sub_page','Create')

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
                        <h4>Payment Method Create</h4>
                    </div>
                    <div class="card-body">
                        <form id="paymentMethodAdd" class="forms-sample " action="{{route('admin.payment-methods.store')}}"  method="POST">
                            @csrf
                            <div id="addPaymentMethod">
                                <div class="row paymentMethodList align-items-center justify-content-between mb-3">
                                    <div class="col-lg-7">
                                        <input type="text" class="form-control" id="name"  name="name[]" value="" required  placeholder="Enter Payment Method Name">
                                    </div>
                                    <div class="col-lg-2 text-center addButtonSection float-end">
                                        <button type="button" class="btn btn-sm btn-primary" id="add" title="Add more payment Method"> Add </button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success" id="paymentMethodSubmit"> Submit </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </section>
@endsection

@section('scripts')
    @include('admin.payrollSetting.paymentMethod.common.scripts')
@endsection
