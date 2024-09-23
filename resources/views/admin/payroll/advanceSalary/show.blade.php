@extends('layouts.master')

@section('title','Advance Salary')

@section('action','View')

@section('styles')
    <link rel="stylesheet" href="{{asset('assets/css/imageuploadify.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />
@endsection

@section('button')
    <a href="{{route('admin.advance-salaries.index')}}" >
        <button class="btn btn-sm btn-primary" ><i class="link-icon" data-feather="arrow-left"></i> Back</button>
    </a>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{asset('assets/css/imageuploadify.min.css')}}">
@endsection

<?php
    $status = [
        'pending' => 'info',
        'approved' => 'success',
        'rejected' => 'danger',
        'processing' => 'secondary'
    ];
?>

@section('main-content')

    <section class="content">

        @include('admin.section.flash_message')

        @include('admin.payroll.advanceSalary.common.breadcrumb')

        <div class="row position-relative">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="mb-2">Advance Salary Detail</h3>
                    </div>

                    <div class="card-body">
                        {!!  $advanceSalaryDetail->description!!}
                        @if(isset($attachments) && count($attachments) > 0 )
                            <div class="mb-3 col-12 mt-3">
                                <h6 class="">Proof Of Advance Salary Payment </h6>
                                <div class="row mb-4 mt-3">
                                    @foreach($attachments as $key => $data)
                                        @if(in_array(pathinfo(asset(\App\Models\AdvanceSalaryAttachment::UPLOAD_PATH.$data->name), PATHINFO_EXTENSION),['jpeg','png','jpg'])  )
                                            <div class="col-lg-3 mb-4">
                                                <div class="uploaded-image">
                                                    <a href="{{ asset(\App\Models\AdvanceSalaryAttachment::UPLOAD_PATH.$data->name) }}" data-lightbox="image-1" data-title="{{$data->name}}">
                                                        <img class="w-100" style=""
                                                             src="{{ asset(\App\Models\AdvanceSalaryAttachment::UPLOAD_PATH.$data->name) }}"
                                                             alt="document images">
                                                    </a>

                                                    <p>{{$data->name}}</p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="uploaded-files">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-1">
                                                        <div class="file-icon">
                                                            <i class="link-icon" data-feather="file-text"></i>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-10">
                                                        <a target="_blank" href="{{asset(\App\Models\AdvanceSalaryAttachment::UPLOAD_PATH.$data->name)}}">
                                                            {{asset(\App\Models\AdvanceSalaryAttachment::UPLOAD_PATH.$data->name)}}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 sidebar-list position-relative">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Advance Salary Request Detail</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-border">
                            <tbody>
                            <tr>
                                <td>Employee:</td>
                                <td class="text-end text-success">{{ucfirst($advanceSalaryDetail->requestedBy->name)}}</td>
                            </tr>

                            <tr>
                                <td>Total Request Amount:</td>
                                <td class="text-end">
                                    {{\App\Helpers\AppHelper::getCompanyPaymentCurrencySymbol()}}.{{number_format($advanceSalaryDetail->requested_amount)}}
                                </td>
                            </tr>

                            <tr class="border-0">
                                <td>Status:</td>
                                <td class="text-end">
                                        <span class="btn btn-{{$status[$advanceSalaryDetail->status]}} cursor-default btn-xs">
                                            {{ucfirst($advanceSalaryDetail->status)}}
                                        </span>
                                </td>
                            </tr>

                            <tr>
                                <td>Total Released Amount:</td>
                                <td class="text-end">
                                    {{\App\Helpers\AppHelper::getCompanyPaymentCurrencySymbol()}}.{{number_format($advanceSalaryDetail->released_amount)}}
                                </td>
                            </tr>

                            <tr>
                                <td>Advance Requested Date:</td>
                                <td class="text-end text-danger">{{ \App\Helpers\AppHelper::formatDateForView($advanceSalaryDetail->advance_requested_date)}}</td>
                            </tr>

                            <tr>
                                <td>Amount Released Date:</td>
                                <td class="text-end text-danger">{{ $advanceSalaryDetail->amount_granted_date ? \App\Helpers\AppHelper::formatDateForView($advanceSalaryDetail->amount_granted_date)  : 'Not Yet Released'}}</td>
                            </tr>

                            <tr>
                                <td>Verified By:</td>
                                <td class="text-end text-success">
                                    {{$advanceSalaryDetail->verifiedBy ? ucfirst($advanceSalaryDetail->verifiedBy->name) : ''}}
                                </td>
                            </tr>

                            <tr>
                                <td>Remark</td>
                                <td class="text-end">
                                    <span class="text-end text-muted"> {{$advanceSalaryDetail->remark ?? 'N/A'}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td>Is Settled</td>
                                <td class="text-end">
                                    <span class="text-end text-muted"> {{$advanceSalaryDetail->is_settled ? 'Yes': 'No'}}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if(!in_array($advanceSalaryDetail->status, ['approved','rejected']))
                @include('admin.payroll.advanceSalary.edit_form')
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    @include('admin.payroll.advanceSalary.common.scripts')
@endsection

