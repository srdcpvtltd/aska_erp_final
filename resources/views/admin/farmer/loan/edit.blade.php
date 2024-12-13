@extends('layouts.master')
@section('title')
    {{ __('Edit Farming Allotment') }}
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#g_code').keyup(function() {
                let g_code = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.get_detail') }}",
                    method: 'post',
                    data: {
                        g_code: g_code,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#farming_id').empty();
                        if (response.farmerHtml) {
                            $('#farming_id').append(response.farmerHtml);
                        } else {
                            $('#farming_id').append('<option value="">Select Farmer</option>');
                        }
                        $('#registration_number').val(response.farming.registration_no);
                    }
                });
            });
            $('#loan_category_id').change(function() {
                let loan_category_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.loan.get_product_service_by_category') }}",
                    method: 'post',
                    data: {
                        loan_category_id: loan_category_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        product_services = response.product_services;
                        $('#loan_type_id').empty();
                        $('#loan_type_id').append(
                            '<option value="">Select Item</option>');
                        for (i = 0; i < product_services.length; i++) {
                            $('#loan_type_id').append('<option value="' + product_services[i]
                                .id + '">' + product_services[i].name + '</option>');
                        }
                    }
                });
            });
            $('#loan_type_id').change(function() {
                let loan_type_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.loan.getWarehouseProduct') }}",
                    method: 'post',
                    data: {
                        loan_type_id: loan_type_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        warehouse = response.warehouse;
                        $('#warehouse_id').empty();
                        $('#warehouse_id').append(
                            '<option value="">Select Warehouse</option>');
                        for (i = 0; i < warehouse.length; i++) {
                            $('#warehouse_id').append('<option value="' + warehouse[i]
                                .id + '">' + warehouse[i].name + '(' + response
                                .warehouse_product[i].quantity + ')</option>');
                        }
                    }
                });
            });
            $('#warehouse_id').change(function() {
                let warehouse_Id = $(this).val();
                let product_Id = $('#loan_type_id').val();

                $.ajax({
                    url: "{{ route('admin.farmer.loan.get_product_service_detail') }}",
                    method: 'post',
                    data: {
                        warehouse_id: warehouse_Id,
                        product_id: product_Id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#price_kg').val(response.product_service.sale_price);
                        $('#quantity').attr('max', response.warehouse_product.quantity);
                        $('#max_text').html('Total Allowed Stock : ' + response
                            .warehouse_product.quantity);
                    }
                });
            });
            $('#quantity').keyup(function() {
                let quantity = $(this).val();
                let price = $('#price_kg').val();
                var amount = quantity * price;
                var rounded_amount = Math.round(amount);
                $('#total_amount').val(amount);
                $('#bill_amount').val(amount);
                $('#round_amount').val(rounded_amount);
                $('#BillAmount').val(amount);
                $('#RoundAmount').val(rounded_amount);
            });

            $('#row_div').on('change', '.loan_type_id', function() {
                let $this = $(this).closest('.append_div');
                let warehouse_id = $('#warehouse_id').val();
                let product_Id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.loan.get_product_service_detail') }}",
                    method: 'post',
                    data: {
                        warehouse_id: warehouse_id,
                        product_id: product_Id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log(response);

                        $this.find('.price_kg').val(response.product_service.sale_price);
                        $this.find('.quantity').attr('max', response.warehouse_product
                            .quantity);
                        $this.find('.max_text').html('Total Allowed Stock : ' + response
                            .warehouse_product
                            .quantity);
                    }
                });
            });

            $('#row_div').on('keyup', '.quantity', function() {
                let quantity = $(this).val();
                let bill_amount = parseFloat($('#BillAmount').val()) || 0;
                let round_amount = parseFloat($('#RoundAmount').val()) || 0;
                if (quantity > 0) {
                    let price = $(this).closest('.append_div').find('.price_kg').val();
                    let amount = quantity * price;
                    let sum_bill = bill_amount + amount;
                    let sum_round = Math.round(round_amount + amount);
                    $(this).closest('.append_div').find('.total_amount').val(amount);
                    $('#bill_amount').val(sum_bill);
                    $('#round_amount').val(sum_round);
                } else {
                    $(this).closest('.append_div').find('.total_amount').val(0);
                    $('#bill_amount').val(bill_amount);
                    $('#round_amount').val(round_amount);
                }
            });
            $('#row_div').on('click', '.delete', function() {
                var total_amount = $(this).closest('.append_div').find('.total_amount').val();
                var billAmount = parseFloat($('#bill_amount').val());
                var roundAmount = parseFloat($('#round_amount').val());

                $('#bill_amount').val(billAmount - total_amount);
                $('#round_amount').val(roundAmount - total_amount);
                $('#BillAmount').val(billAmount - total_amount);
                $('#RoundAmount').val(roundAmount - total_amount);

                $(this).closest('.append_div').remove();
            });
            $('#add_more').on('click', function() {
                var sum_bill = $('#bill_amount').val();
                var sum_round = $('#round_amount').val();
                $('#BillAmount').val(sum_bill);
                $('#RoundAmount').val(sum_round);
                $('#row_div .append_div:last .quantity').attr('readonly', true);

                $('#row_div').append('<div class="row pd_right_0 append_div">' +
                    '<div class="col-md-3 pd_right_0">' +
                    '<div class="form-group">' +
                    '{{ Form::label('loan_type_id', __('Item'), ['class' => 'form-label']) }}' +
                    '<select class="form-control select loan_type_id" name="loan_type_id[]"' +
                    'placeholder="Select Loan Type" required>' +
                    '<option value="">{{ __('Select Item') }}</option>' +
                    '</select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group col-md-3">' +
                    '{{ Form::label('unit_price', __('Unit Price'), ['class' => 'form-label']) }}' +
                    '{{ Form::text('price_kg[]', '', ['class' => 'form-control price_kg', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Unit Price']) }}' +
                    '</div>' +
                    '<div class="form-group col-md-3 pd_right_0">' +
                    '{{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}' +
                    '{{ Form::number('quantity[]', '', ['class' => 'form-control quantity', 'min' => '1', 'required' => 'required']) }}' +
                    '<span style="color:red;" class="max_text"></span>' +
                    '</div>' +
                    '<div class="form-group col-md-3">' +
                    '{{ Form::label('total_amount', __('Total Amount'), ['class' => 'form-label']) }}' +
                    '{{ Form::number('total_amount[]', 0.0, ['class' => 'form-control total_amount', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Total Amount']) }}' +
                    '</div>' +
                    '<div class="form-group col-md-6">' +
                    '<button class="btn btn-danger mt-4 delete">Delete</button>' +
                    '</div><hr style="width: 98%;"></div>');

                let loan_category_id = $('#loan_category_id').val();
                $.ajax({
                    url: "{{ route('admin.farmer.loan.get_product_service_by_category') }}",
                    method: 'post',
                    data: {
                        loan_category_id: loan_category_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        product_services = response.product_services;

                        $('#row_div .append_div:last .loan_type_id').empty();
                        $('#row_div .append_div:last .loan_type_id').append(
                            '<option value="">Select Item</option>');

                        for (let i = 0; i < product_services.length; i++) {
                            $('#row_div .append_div:last .loan_type_id').append(
                                '<option value="' + product_services[i].id + '">' +
                                product_services[i].name + '</option>');
                        }
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
            <li class="breadcrumb-item"><a href="{{ route('admin.farmer.loan.index') }}">{{ __('Farming Loan') }}</a></li>
            <li class="breadcrumb-item">{{ __('Edit Farming Loan') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::model($loan, ['route' => ['admin.farmer.loan.update', $loan->id], 'method' => 'PUT', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row" id="row_div">
                        <div class="form-group col-md-6">
                            {{ Form::label('g_code', __('G.Code'), ['class' => 'form-label']) }}
                            {{ Form::text('g_code', $loan->farming->old_g_code, ['class' => 'form-control', 'required' => 'required', 'disabled']) }}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Farmer Name'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required disabled>
                                    <option value="">{{ __('Select Farmer Registration') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option {{ $farming->id == $loan->farming_id ? 'selected' : '' }}
                                            value="{{ $farming->id }}">{{ $farming->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('invoice_no', __('Invoice No.'), ['class' => 'form-label']) }}
                            {{ Form::text('invoice_no', $loan->invoice_no, ['id' => 'invoice_no', 'class' => 'form-control', 'required' => 'required', 'maxlength="5"', 'readonly', 'disabled']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('registration_number', __('Registration No.'), ['class' => 'form-label']) }}
                            {{ Form::text('registration_number', $loan->registration_number, ['class' => 'form-control', 'required' => 'required', 'readonly', 'disabled']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('date', __('Date of Issue'), ['class' => 'form-label']) }}
                            {{ Form::date('date', $loan->date, ['class' => 'form-control', 'required' => 'required', 'disabled']) }}
                        </div>
                        @php
                            $loan_type_id = json_decode($loan->loan_type_id);
                            $price_kg = json_decode($loan->price_kg);
                            $quantity = json_decode($loan->quantity);
                            $total_amount = json_decode($loan->total_amount);
                            $count = count($loan_type_id);
                        @endphp
                        @for ($i = 0; $i < $count; $i++)
                            @if ($i == 0)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('loan_category_id', __('Allotment Category'), ['class' => 'form-label']) }}
                                        <select class="form-control select" name="loan_category_id" id="loan_category_id"
                                            required disabled>
                                            <option value="">{{ __('Select Loan Category') }}</option>
                                            @foreach ($categories as $category)
                                                <option {{ $category->id == $loan->loan_category_id ? 'selected' : '' }}
                                                    value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('loan_type_id', __('Item'), ['class' => 'form-label']) }}
                                        <select class="form-control select" name="loan_type_id[]" id="loan_type_id" disabled
                                            required>
                                            <option value="">{{ __('Select Item') }}</option>
                                            @foreach ($types as $type)
                                                <option {{ $type->id == $loan_type_id[$i] ? 'selected' : '' }}
                                                    value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('warehouse_id', __('Warehouse'), ['class' => 'form-label']) }}
                                        <select class="form-control select" name="warehouse_id" id="warehouse_id" required
                                            disabled>
                                            <option value="">{{ __('Select Warehouse') }}</option>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}"
                                                    {{ $warehouse->id == $loan->warehouse_id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="warehouse_id" value="{{ $loan->warehouse_id }}">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('price_kg', __('Unit Price'), ['class' => 'form-label']) }}
                                    {{ Form::text('price_kg[]', $price_kg[$i], ['class' => 'form-control', 'id' => 'price_kg', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Unit Price', 'disabled']) }}
                                </div>
                                <div class="form-group col-md-3">
                                    {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                                    {{ Form::number('quantity[]', $quantity[$i], ['class' => 'form-control', 'min' => '1', 'required' => 'required', 'id' => 'quantity', 'disabled']) }}
                                    <span style="color:red;" id="max_text"></span>
                                </div>
                                <div class="form-group col-md-3">
                                    {{ Form::label('total_amount', __('Total Amount'), ['class' => 'form-label']) }}
                                    {{ Form::number('total_amount[]', $total_amount[$i], ['class' => 'form-control', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Total Amount', 'id' => 'total_amount', 'disabled']) }}
                                </div>
                                <hr style="width: 98%;">
                            @elseif($i > 0)
                                <div class="row pd_right_0 append_div">
                                    <div class="col-md-3 pd_right_0">
                                        <div class="form-group">
                                            {{ Form::label('loan_type_id', __('Item'), ['class' => 'form-label']) }}
                                            <select class="form-control select loan_type_id" name="loan_type_id[]" disabled
                                                required>
                                                <option value="">{{ __('Select Item') }}</option>
                                                @foreach ($types as $type)
                                                    <option {{ $type->id == $loan_type_id[$i] ? 'selected' : '' }}
                                                        value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        {{ Form::label('price_kg', __('Unit Price'), ['class' => 'form-label']) }}
                                        {{ Form::text('price_kg[]', $price_kg[$i], ['class' => 'form-control price_kg', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Price Kg', 'disabled']) }}
                                    </div>
                                    <div class="form-group col-md-3 pd_right_0">
                                        {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                                        {{ Form::number('quantity[]', $quantity[$i], ['class' => 'form-control quantity', 'min' => '1', 'required' => 'required', 'disabled']) }}
                                        <span style="color:red;" class="max_text"></span>
                                    </div>
                                    <div class="form-group col-md-3">
                                        {{ Form::label('total_amount', __('Total Amount'), ['class' => 'form-label']) }}
                                        {{ Form::number('total_amount[]', $total_amount[$i], ['class' => 'form-control total_amount', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Total Amount', 'disabled']) }}
                                    </div>
                                </div>
                                <hr style="width: 98%;">
                            @endif
                        @endfor
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            {{ Form::label('bill_amount', __('Bill Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('bill_amount', $loan->bill_amount, ['class' => 'form-control', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Bill Amount', 'id' => 'bill_amount']) }}
                            <input type="hidden" id="BillAmount" value="{{ $loan->bill_amount }}">
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::label('round_amount', __('Round Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('round_amount', $loan->round_amount, ['class' => 'form-control', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Round Amount', 'id' => 'round_amount']) }}
                            <input type="hidden" id="RoundAmount" value="{{ $loan->round_amount }}">
                        </div>
                        <hr style="width: 98%;">
                    </div>
                    <div class="form-group float-right">
                        <button type="button" class="btn btn-primary mt-4" id="add_more">Add More</button>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.guarantor.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
