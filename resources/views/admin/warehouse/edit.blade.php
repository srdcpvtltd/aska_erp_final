@extends('layouts.master')
@section('title')
    {{ __('Edit Warehouse') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    {{ Form::model($warehouse, ['method' => 'PATCH', 'route' => ['admin.warehouse.update', $warehouse->id], 'class' => 'w-100']) }}
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Edit Warehouse') }}</li>
        </ol>
    </nav>
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
            @error('name')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
            @enderror
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
            {{ Form::textarea('address', null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
            {{ Form::text('city', null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('city_zip', __('Zip Code'), ['class' => 'form-label']) }}
            {{ Form::text('city_zip', null, ['class' => 'form-control']) }}
        </div>

    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
    </div>
    {{ Form::close() }}
@endsection
