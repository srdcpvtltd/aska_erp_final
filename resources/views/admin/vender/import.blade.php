@extends('layouts.master')
@section('title')
    {{ __('Import Vendor') }}
@endsection
@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.vender.index') }}">{{ __('Vendor') }}</a></li>
            <li class="breadcrumb-item">{{ __('Import') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.vender.index') }}" class="btn btn-success">
                Back
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                {{ Form::open(['route' => ['admin.vender.import'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12 mb-6">
                            {{ Form::label('file', __('Download sample vendor CSV file'), ['class' => 'form-label']) }}
                            <a href="{{ asset('uploads/sample') . '/sample-vendor.csv' }}"
                                class="btn btn-sm btn-primary">
                                <i class="ti ti-download"></i> {{ __('Download') }}
                            </a>
                        </div>
                        <div class="col-md-12">
                            {{ Form::label('file', __('Select CSV File'), ['class' => 'form-label']) }}
                            <div class="choose-file form-group">
                                <label for="file" class="form-label">
                                    <div>{{ __('Choose file here') }}</div>
                                    <input type="file" class="form-control" name="file" id="file"
                                        data-filename="upload_file" required>
                                </label>
                                <p class="upload_file"></p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
                    <input type="submit" value="{{ __('Upload') }}" class="btn  btn-primary">
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
@endsection
