@extends('layouts.master')
@section('title')
    {{ __('Import Customers') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Customer') }}</li>
            <li class="breadcrumb-item">{{ __('Import') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.customer.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                {{ Form::open(['route' => ['admin.customer.import'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-6">
                            {{ Form::label('file', __('Download sample customer CSV file'), ['class' => 'form-label']) }}
                            <a href="{{ asset('upload/sample') . '/sample-customer.csv' }}"
                                class="btn btn-primary">
                                <i class="ti ti-download"></i> {{ __('Download') }}
                            </a>
                        </div>
                        <div class="col-md-12">
                            {{ Form::label('file', __('Select CSV File'), ['class' => 'form-label']) }}
                            <div class="choose-file form-group">
                                <label for="file" class="form-label">
                                    <input type="file" class="form-control" name="file" id="file"
                                        data-filename="upload_file" required>
                                </label>
                                <p class="upload_file"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
                    <input type="submit" value="{{ __('Upload') }}" class="btn  btn-primary">
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
