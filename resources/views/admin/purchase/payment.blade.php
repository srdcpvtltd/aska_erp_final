@extends('layouts.master')
@section('title')
    {{ __('Add Payment') }}
@endsection

@php
    $settings = App\Models\Utility::settings();
@endphp

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.purchase.index') }}">{{ __('Purchase') }}</a></li>
            <li class="breadcrumb-item">{{ Auth::user()->purchaseNumberFormat($purchase->purchase_id) }}</li>
            <li class="breadcrumb-item">{{ __('Add Payment') }}</li>
        </ol>
    </nav>
    {{ Form::model($purchase, ['route' => ['admin.purchase.payment', $purchase->id], 'method' => 'post', 'class' => 'w-100', 'enctype' => 'multipart/form-data']) }}
    <div class="card-body">
        <div class="row">
            <div class="form-group  col-md-6">
                {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                {{ Form::date('date', '', ['class' => 'form-control', 'required' => 'required']) }}
            </div>
            <div class="form-group  col-md-6">
                {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
                {{ Form::number('amount', $purchase->getDue(), ['class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'readonly']) }}
            </div>
            <div class="form-group  col-md-6">
                {{ Form::label('account_id', __('Account'), ['class' => 'form-label']) }}
                {{ Form::select('account_id', $accounts, null, ['class' => 'form-control select', 'required' => 'required']) }}
            </div>

            <div class="form-group  col-md-6">
                {{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}
                {{ Form::text('reference', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group  col-md-12">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', '', ['class' => 'form-control', 'rows' => 3]) }}
            </div>
            <div class="col-md-6 form-group">
                {{ Form::label('add_receipt', __('Payment Receipt'), ['class' => 'form-label']) }}
                <div class="choose-file ">
                    <label for="file" class="form-label">
                        <input type="file" name="add_receipt" id="image" class="form-control">
                    </label>
                    <p class="upload_file"></p>

                </div>
            </div>


        </div>
        <div class="card-footer">
            <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
            <input type="submit" value="{{ __('Add') }}" class="btn  btn-primary">
        </div>

    </div>
    {{ Form::close() }}
@endsection
