@extends('layouts.master')
@section('title')
    {{ __('Edit Bank Account') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Accounting system') }}</li>
            <li class="breadcrumb-item">{{ __('Bank Account') }}</li>
            <li class="breadcrumb-item">{{ __('Edit') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.bank-account.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{ Form::model($bankAccount, ['route' => ['admin.bank-account.update', $bankAccount->id], 'method' => 'PUT']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('chart_account_id', __('Account'), ['class' => 'form-label']) }}
                            {{ Form::select('chart_account_id', $chart_accounts, null, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('holder_name', __('Bank Holder Name'), ['class' => 'form-label']) }}
                            {{ Form::text('holder_name', null, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group  col-md-6">
                            {{ Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) }}
                            {{ Form::text('bank_name', null, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group  col-md-6">
                            {{ Form::label('account_number', __('Account Number'), ['class' => 'form-label']) }}
                            {{ Form::text('account_number', null, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group  col-md-6">
                            {{ Form::label('opening_balance', __('Opening Balance'), ['class' => 'form-label']) }}
                            {{ Form::number('opening_balance', null, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
                        </div>
                        <div class="form-group  col-md-6">
                            {{ Form::label('contact_number', __('Contact Number'), ['class' => 'form-label']) }}
                            {{ Form::text('contact_number', null, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group  col-md-12">
                            {{ Form::label('bank_address', __('Bank Address'), ['class' => 'form-label']) }}
                            {{ Form::textarea('bank_address', null, ['class' => 'form-control', 'rows' => 3, 'required' => 'required']) }}
                        </div>
                        @if (!$customFields->isEmpty())
                            <div class="col-md-12">
                                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                    @include('admin.customFields.formBuilder')
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.bank-account.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
