@extends('layouts.master')
@section('title')
    {{ __('Warehouse Transfer Create') }}
@endsection

@section('scripts')
    <script>
        $(document).on('change', 'select[name=from_warehouse]', function() {
            var warehouse_id = $(this).val();
            $.ajax({
                url: '{{ route('admin.warehouse-transfer.getproduct') }}',
                type: 'POST',
                data: {
                    "warehouse_id": warehouse_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#product_id').empty();

                    $("#product_div").html('');
                    $('#product_div').append(
                        '<label for="product" class="form-label">{{ __('Product') }}</label>');
                    $('#product_div').append(
                        '<select class="form-control" id="product_id" name="product_id"></select>');
                    $('#product_id').append('<option value="">{{ __('Select Product') }}</option>');

                    $.each(data.ware_products, function(key, value) {
                        $('#product_id').append('<option value="' + key + '">' + value + '</option>');
                    });

                    $('select[name=to_warehouse]').empty();
                    $.each(data.to_warehouses, function(key, value) {
                        var option = '<option value="' + key + '">' + value + '</option>';
                        $('select[name=to_warehouse]').append(option);
                    });
                }

            });
        });

        $(document).on('change', '#product_id', function() {
            var product_id = $(this).val();
            var warehouse_id = $('#from_warehouse_id').val();
            getQuantity(product_id, warehouse_id);
        });

        function getQuantity(pid, wid) {
            $.ajax({
                url: '{{ route('admin.warehouse-transfer.getquantity') }}',
                type: 'POST',
                data: {
                    "product_id": pid,
                    "warehouse_id": wid,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    console.log(data);
                    $('#quantity').val(data);
                }
            });
        }
    </script>
@endsection
@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.warehouse-transfer.index') }}">{{ __('Transfer') }}</a></li>
            <li class="breadcrumb-item">{{ __('Create') }}</li>
        </ol>
    </nav>
    {{ Form::open(['route' => 'admin.warehouse-transfer.store', 'class' => 'w-100']) }}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('from_warehouse', __('From Warehouse'), ['class' => 'form-label']) }}<span
                class="text-danger">*</span>
            <select class="form-control select" name="from_warehouse" id="from_warehouse_id" placeholder="Select Warehouse">
                <option value="">{{ __('Select Warehouse') }}</option>
                @foreach ($from_warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('to_warehouse', __('To Warehouse'), ['class' => 'form-label']) }}<span
                class="text-danger">*</span>
            {{-- {{ Form::select('to_warehouse', $to_warehouses, null, ['class' => 'form-control select', 'required' => 'required']) }} --}}
            <select class="form-control select" name="to_warehouse" id="to_warehouse_id" placeholder="Select Warehouse">
                <option value="">{{ __('Select Warehouse') }}</option>
                @foreach ($to_warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6" id="product_div">
            {{ Form::label('product', __('Product'), ['class' => 'form-label']) }}
            <select class="form-control select" name="product_id" id="product_id" placeholder="Select Product">
            </select>
        </div>

        <div class="form-group col-md-6" id="qty_div">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
            {{ Form::number('quantity', null, ['class' => 'form-control', 'id' => 'quantity']) }}
        </div>


        <div class="form-group col-lg-6">
            {{ Form::label('date', __('Date')) }}
            {{ Form::date('date', null, ['class' => 'form-control datepicker w-100 mt-2']) }}
        </div>

    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    </div>
    {{ Form::close() }}
@endsection
