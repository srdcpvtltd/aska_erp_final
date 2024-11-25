@extends('layouts.master')
@section('title')
    {{ __('Challan Edit') }}
@endsection
@section('main-content')
    @include('admin.section.flash_message')

    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.challan.index') }}">{{ __('Challan') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Edit') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.challan.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </nav>

    <div class="row">
        {{ Form::model($challan, ['route' => ['admin.challan.update', $challan->id], 'method' => 'PUT']) }}
        <div class="col-12">
            <input type="hidden" name="created_by" id="created_by" value="{{ Auth::user()->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('warehouse_id', __('Select Warehouse'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="warehouse_id" id="warehouse_id" disabled>
                                    <option value="">{{ __('Select Warehouse') }}</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ $challan->warehouse_id == $warehouse->id ? 'selected':'' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('challan_no', __('Challan No.'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="challan_no" value="{{ $challan->challan_no }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('product_id', __('Product'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="product_id" id="product_id" disabled>
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ $challan->product_id == $product->id ? 'selected':'' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('receive_date', __('Receive Date'), ['class' => 'form-label']) }}
                                <input type="date" class="form-control" name="receive_date" value="{{ $challan->receive_date }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('vehicle_no', __('Vehicle No.'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="vehicle_no" value="{{ $challan->vehicle_no }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="quantity" value="{{ $challan->quantity }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="amount" value="{{ $challan->amount }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.challan.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
