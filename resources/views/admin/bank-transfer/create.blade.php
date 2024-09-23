@extends('layouts.master')
@section('title')
    {{ __('Bank Balance Transfer') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Bank Balance Transfer') }}</li>
            <li class="breadcrumb-item">{{ __('Create') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.bank-transfer.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-sm-12">
            <div class=" mt-2 " id="multiCollapseExample1">
                <div class="card">
                    {{ Form::open(['route' => 'admin.bank-transfer.store', 'class' => 'w-100']) }}
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group  col-md-6">
                                {{ Form::label('from_account', __('From Account'), ['class' => 'form-label']) }}
                                {{ Form::select('from_account', $bankAccount, null, ['class' => 'form-control select', 'required' => 'required']) }}
                            </div>
                            <div class="form-group  col-md-6">
                                {{ Form::label('to_account', __('To Account'), ['class' => 'form-label']) }}
                                {{ Form::select('to_account', $bankAccount, null, ['class' => 'form-control select', 'required' => 'required']) }}
                            </div>
                            <div class="form-group  col-md-6">
                                {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
                                {{ Form::number('amount', '', ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
                            </div>
                            <div class="form-group  col-md-6">
                                {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                {{ Form::date('date', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                            <div class="form-group  col-md-6">
                                {{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}
                                {{ Form::text('reference', '', ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group  col-md-12">
                                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                                {{ Form::textarea('description', '', ['class' => 'form-control', 'rows' => 3]) }}
                            </div>

                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.bank-transfer.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
