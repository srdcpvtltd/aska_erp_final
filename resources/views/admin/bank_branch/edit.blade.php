@extends('layouts.master')
@section('title')
    {{ __('Edit Bank Branch') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Bank Branch') }}</li>
            <li class="breadcrumb-item">{{ __('Edit') }}</li>
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
                {{ Form::model($bank_branches, ['route' => ['admin.bank_branches.update', $bank_branches->id], 'method' => 'PUT']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="name" class="form-label"> Bank <span style="color: red">*</span> </label>
                            <select name="bank_id" id="bank" class="form-control">
                                <option value="">Select</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ ($bank_branches->bank_id == $bank->id) ? 'selected':'' }}>{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name" class="form-label"> Branch <span style="color: red">*</span> </label>
                            <input type="text" class="form-control" name="name" value="{{ $bank_branches->name }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name" class="form-label"> IFSC Code <span style="color: red">*</span> </label>
                            <input type="text" class="form-control" name="ifsc_code" value="{{ $bank_branches->ifsc_code }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.bank_branches.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
