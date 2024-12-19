@extends('layouts.master')
@section('title')
    {{ __('Create Farmer Allotment') }}
@endsection
@section('styles')
    <style>
        .pd_right_0 {
            padding-right: 0;
        }
    </style>
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
                let name = $('#loan_category_id option:selected').text();
                if (name === "Seeds") {
                    $('#warehouse_id').val('');
                    $('.warehouse_div').hide();
                } else {
                    $('.warehouse_div').show();
                }
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
                let name = $('#loan_category_id option:selected').text();

                if (name === "Seeds") {
                    $.ajax({
                        url: "{{ route('admin.seedstock.get_seed_stock_detail') }}",
                        method: 'post',
                        data: {
                            product_id: loan_type_id,
                        },
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#price_kg').val(response.sale_price);
                            $('#quantity').attr('max', response.quantity);
                            $('#max_text').html('Total Allowed Stock : ' + response
                                .quantity);
                        }
                    });
                } else {
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
                }
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
                var maxValue = $('input[name="quantity[]"]').attr('max');
                if(quantity > maxValue){
                    $('#max_text').html();
                    $('#max_text').html('Quantity Must Be Smaller Then Available Stock : ' + maxValue);
                    $('#submit_btn').attr('disabled', true);
                } else {
                    $('#submit_btn').removeAttr('disabled');
                    $('#max_text').html('Total Allowed Stock : ' + maxValue);
                }
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
                let name = $('#loan_category_id option:selected').text();

                if (name === "Seeds") {
                    $.ajax({
                        url: "{{ route('admin.seedstock.get_seed_stock_detail') }}",
                        method: 'post',
                        data: {
                            product_id: product_Id,
                        },
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $this.find('.price_kg').val(response.sale_price);
                            $this.find('.quantity').attr('max', response.quantity);
                            $this.find('.max_text').html('Total Allowed Stock : ' + response
                                .quantity);
                        }
                    });
                } else {
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
                            $this.find('.price_kg').val(response.product_service.sale_price);
                            $this.find('.quantity').attr('max', response.warehouse_product
                                .quantity);
                            $this.find('.max_text').html('Total Allowed Stock : ' + response
                                .warehouse_product
                                .quantity);
                        }
                    });
                }
            });
            $('#row_div').on('keyup', '.quantity', function() {
                var quantity = parseFloat($(this).val());
                var quantity1 = parseFloat($('#quantity').val());
                let bill_amount = parseFloat($('#BillAmount').val()) || 0;
                let round_amount = parseFloat($('#RoundAmount').val()) || 0;
                var maxValue = $(this).attr('max');
                var tot_qty = quantity + quantity1;
                
                if(tot_qty > maxValue){
                    $('.max_text').html();
                    $('.max_text').html('Quantity Must Be Smaller Then Available Stock : ' + maxValue);
                    $('#submit_btn').attr('disabled', true);
                } else {
                    $('#submit_btn').removeAttr('disabled');
                    $('.max_text').html('Total Allowed Stock : ' + maxValue);
                }

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
                $('#quantity').attr('readonly', true);

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
            <li class="breadcrumb-item"><a href="{{ route('admin.farmer.loan.index') }}">{{ __('Farmer Loan') }}</a></li>
            <li class="breadcrumb-item">{{ __('Seeds,Fertiliser & Pesticides Allotment') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::open(['url' => 'admin/farmer/loan', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="created_by" id="created_by" value="{{ Auth::user()->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row" id="row_div">
                        <div class="form-group col-md-6">
                            {{ Form::label('g_code', __('G.Code'), ['class' => 'form-label']) }}
                            {{ Form::text('g_code', '', ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Farmer Name'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required
                                    placeholder="Select Country">
                                    <option value="">{{ __('Select Farmer') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option value="{{ $farming->id }}">{{ $farming->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('invoice_no', __('Invoice No.'), ['class' => 'form-label']) }}
                            {{ Form::text('invoice_no', '', ['id' => 'invoice_no', 'class' => 'form-control', 'required' => 'required', 'maxlength="5"']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('registration_number', __('Registration No.'), ['class' => 'form-label']) }}
                            {{ Form::text('registration_number', '', ['id' => 'registration_number', 'class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('date', __('Date of Issue'), ['class' => 'form-label']) }}
                            {{ Form::date('date', '', ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('loan_category_id', __('Allotment Category'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="loan_category_id" id="loan_category_id" required
                                    placeholder="Select Country">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('loan_type_id', __('Item'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="loan_type_id[]" id="loan_type_id"
                                    placeholder="Select Loan Type" required>
                                    <option value="">{{ __('Select Item') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 warehouse_div">
                            <div class="form-group">
                                {{ Form::label('warehouse_id', __('Warehouse'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="warehouse_id" id="warehouse_id">
                                    <option value="">{{ __('Select Warehouse') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('unit_price', __('Unit Price'), ['class' => 'form-label']) }}
                            {{ Form::text('price_kg[]', '', ['class' => 'form-control', 'id' => 'price_kg', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Unit Price']) }}
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                            {{ Form::number('quantity[]', '', ['class' => 'form-control', 'min' => '1', 'required' => 'required', 'id' => 'quantity']) }}
                            <span style="color:red;" id="max_text"></span>
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::label('total_amount', __('Total Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('total_amount[]', 0.0, ['class' => 'form-control', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Total Amount', 'id' => 'total_amount']) }}
                        </div>
                        <hr style="width: 98%;">
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            {{ Form::label('bill_amount', __('Bill Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('bill_amount', 0.0, ['class' => 'form-control', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Bill Amount', 'id' => 'bill_amount']) }}
                            <input type="hidden" id="BillAmount">
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::label('round_amount', __('Round Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('round_amount', 0.0, ['class' => 'form-control', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Round Amount', 'id' => 'round_amount']) }}
                            <input type="hidden" id="RoundAmount">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group float-right">
                        <button type="button" class="btn btn-primary mt-4" id="add_more">Add More</button>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.loan.index') }}';" class="btn btn-light">
                <input type="submit" id="submit_btn" value="{{ __('Create') }}" class="btn btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
