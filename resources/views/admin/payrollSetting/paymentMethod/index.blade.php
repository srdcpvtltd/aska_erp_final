@extends('layouts.master')
@section('title','Payment Method')
@section('sub_page','Lists')
@section('page')
    <a href="{{ route('admin.payment-methods.index')}}">
       Payment Methods
    </a>
@endsection

@section('main-content')
    <section class="content">
        @include('admin.section.flash_message')

        <div id="showSuccessResponse d-none">
            <div class="alert alert-success successPaymentMethod">
                <p class="successMessage"></p>
            </div>
        </div>

        @include('admin.payrollSetting.common.breadcrumb')
        <div class="row">
            <div class="col-lg-2">
                @include('admin.payrollSetting.common.setting_menu')
            </div>
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <div class="justify-content-end">
                            @can('add-payroll_setting')
                                <a class="btn btn-success"
                                   href="{{ route('admin.payment-methods.create')}}">
                                    <i class="link-icon" data-feather="plus"></i> Add Payment Method
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                @forelse($paymentMethodLists as $key => $value)
                                    <tr data-id="{{$value->id}}">
                                        <td>{{++$key}}</td>
                                        <td class="name">{{ucfirst($value->name)}}</td>
                                        <td class="text-center">
                                            <label class="switch">
                                                <input class="toggleStatus" href="{{route('admin.payment-methods.toggle-status',$value->id)}}"
                                                       type="checkbox" {{($value->status) == 1 ?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>

                                        <td class="text-center">
                                            <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                <li class="me-2">
                                                    @can('edit-payroll_setting')
                                                        <a class="editPaymentMethod"
                                                           href="{{route('admin.payment-methods.update',$value->id)}}"
                                                           data-name="{{$value->name}}"
                                                           title="Edit Detail"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#addslider"
                                                        >
                                                            <i class="link-icon" data-feather="edit"></i>
                                                        </a>
                                                </li>
                                                @endcan
                                                @can('delete-payroll_setting')
                                                    <li>
                                                        <a class="delete" href="#"
                                                           data-href="{{route('admin.payment-methods.delete',$value->id)}}"
                                                           data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                            <i class="link-icon" data-feather="delete"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
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
            </div>
        </div>

    </section>

    @include('admin.payrollSetting.paymentMethod.edit')
@endsection

@section('scripts')
  @include('admin.payrollSetting.paymentMethod.common.scripts')
@endsection






