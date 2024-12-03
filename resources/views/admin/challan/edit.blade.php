@extends('layouts.master')
@section('title')
    {{ __('Challan Edit') }}
@endsection
@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#quantity').on('keyup', function() {
                var quantity = $(this).val();
                var product_id = $('#product_id').val();
                var warehouse_id = $('#warehouse_id').val();

                $.ajax({
                    url: "{{ route('admin.challan.getChallanAmount') }}",
                    method: 'post',
                    data: {
                        product_id: product_id,
                        warehouse_id: warehouse_id,
                        quantity: quantity,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#amount').val(response.total_price);
                    }
                });
            })
        });
    </script>
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
                                        <option value="{{ $warehouse->id }}"
                                            {{ $challan->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="warehouse_id" value="{{ $challan->warehouse_id }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('challan_no', __('Challan No.'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="challan_no"
                                    value="{{ $challan->challan_no }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('product_id', __('Product'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="product_id" id="product_id" disabled>
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ $challan->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="product_id" value="{{ $challan->product_id }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('receive_date', __('Receive Date'), ['class' => 'form-label']) }}
                                <input type="date" class="form-control" name="receive_date"
                                    value="{{ $challan->receive_date }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('vehicle_no', __('Vehicle No.'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="vehicle_no"
                                    value="{{ $challan->vehicle_no }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="quantity" id="quantity" value="{{ $challan->quantity }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="amount" id="amount" value="{{ $challan->amount }}" readonly>
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
