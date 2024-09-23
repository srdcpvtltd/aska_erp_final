@extends('layouts.master')
@section('title')
    {{ __('Create Bank Branch') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Bank Branch') }}</li>
            <li class="breadcrumb-item">{{ __('Create') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.bank_branches.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{ Form::open(['route' => 'admin.bank_branches.store', 'class' => 'w-100']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="name" class="form-label"> Bank <span style="color: red">*</span> </label>
                            <select name="bank_id" id="bank" class="form-control">
                                <option value="">Select</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name" class="form-label"> Branch <span style="color: red">*</span> </label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name" class="form-label"> IFSC Code <span style="color: red">*</span> </label>
                            <input type="text" class="form-control" name="ifsc_code">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.bank_branches.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
