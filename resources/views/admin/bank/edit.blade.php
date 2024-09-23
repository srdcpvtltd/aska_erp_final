@extends('layouts.master')
@section('title')
    {{ __('Edit Bank') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Bank') }}</li>
            <li class="breadcrumb-item">{{ __('Edit') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.banks.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{ Form::model($bank, ['route' => ['admin.banks.update', $bank->id], 'method' => 'PUT']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="name" class="form-label"> Bank Name <span style="color: red">*</span> </label>
                            {{ Form::text('name', $bank->name, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.banks.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
