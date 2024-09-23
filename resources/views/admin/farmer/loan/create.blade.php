@extends('layouts.master')
@section('title')
    {{ __('Farmer Allotments') }}
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
            $('#farming_id').change(function() {
                let farming_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.loan.get_farming_detail') }}",
                    method: 'post',
                    data: {
                        farming_id: farming_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#registration_number').val(response.farming.registration_no);
                        $('#agreement_number').val(response.farming.g_code);
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
                            '<option value="">Select Product Service</option>');
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
                    url: "{{ route('admin.farmer.loan.get_product_service_detail') }}",
                    method: 'post',
                    data: {
                        loan_type_id: loan_type_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#price_kg').val(response.product_service.sale_price);
                        $('#quantity').attr('max', response.quantity);
                        $('#max_text').html('Total Allowed Stock : ' + response.quantity);
                    }
                });
            });
            $('#quantity').change(function() {
                let quantity = $(this).val();
                let price = $('#price_kg').val();
                $('#total_amount').val(quantity * price);
            });
            
            //add more field jquery
            $('#row_div').on('change', '.loan_category_id', function() {
                let loan_category_id = $(this).val();
                let $this = $(this).closest('.append_div');
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
                        let $loanTypeSelect = $this.find('.loan_type_id');
                        $loanTypeSelect.empty();
                        $loanTypeSelect.append(
                            '<option value="">Select Product Service</option>');
                        for (let i = 0; i < product_services.length; i++) {
                            $loanTypeSelect.append('<option value="' + product_services[i].id +
                                '">' + product_services[i].name + '</option>');
                        }
                    }
                });
            });
            $('#row_div').on('change', '.loan_type_id', function() {
                let loan_type_id = $(this).val();
                let $this = $(this).closest('.append_div');
                $.ajax({
                    url: "{{ route('admin.farmer.loan.get_product_service_detail') }}",
                    method: 'post',
                    data: {
                        loan_type_id: loan_type_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $this.find('.price_kg').val(response.product_service.sale_price);
                        $this.find('.quantity').attr('max', response.quantity);
                        $this.find('.max_text').html('Total Allowed Stock : ' + response
                            .quantity);
                    }
                });
            });
            $('#row_div').on('change', '.quantity', function() {
                let quantity = $(this).val();
                let price = $(this).closest('.append_div').find('.price_kg').val();
                $(this).closest('.append_div').find('.total_amount').val(quantity * price);
            });
            $('#row_div').on('click', '.delete', function() {
                $(this).closest('.append_div').remove();
            });
            $('#add_more').on('click', function() {
                $('#row_div').append('<div class="row pd_right_0 append_div"><div class="col-md-6">' +
                    '<div class="form-group">' +
                    '{{ Form::label('loan_category_id', __('Allotment Category'), ['class' => 'form-label']) }}' +
                    '<select class="form-control select loan_category_id" name="loan_category_id[]" required' +
                    'placeholder="Select Country">' +
                    '<option value="">{{ __('Select Category') }}</option>' +
                    '@foreach ($categories as $category)' +
                    '<option value="{{ $category->id }}">{{ $category->name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd_right_0">' +
                    '<div class="form-group">' +
                    '{{ Form::label('loan_type_id', __('Item'), ['class' => 'form-label']) }}' +
                    '<select class="form-control select loan_type_id" name="loan_type_id[]"' +
                    'placeholder="Select Loan Type" required>' +
                    '<option value="">{{ __('Select Item') }}</option>' +
                    '</select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group col-md-6">' +
                    '{{ Form::label('price_kg', __('Price Kg'), ['class' => 'form-label']) }}' +
                    '{{ Form::text('price_kg[]', '', ['class' => 'form-control price_kg', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Price Kg']) }}' +
                    '</div>' +
                    '<div class="form-group col-md-6 pd_right_0">' +
                    '{{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}' +
                    '{{ Form::number('quantity[]', '', ['class' => 'form-control quantity', 'min' => '1', 'required' => 'required']) }}' +
                    '<span style="color:red;" class="max_text"></span>' +
                    '</div>' +
                    '<div class="form-group col-md-6">' +
                    '{{ Form::label('total_amount', __('Total Amount'), ['class' => 'form-label']) }}' +
                    '{{ Form::number('total_amount[]', 0.0, ['class' => 'form-control total_amount', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Total Amount']) }}' +
                    '</div>' +
                    '<div class="form-group col-md-6">' +
                    '<button class="btn btn-danger mt-4 delete">Delete</button>' +
                    '</div></div>');
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
                            {{ Form::label('registration_number', __('Registration No.'), ['class' => 'form-label']) }}
                            {{ Form::text('registration_number', '', ['id' => 'registration_number', 'class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('agreement_number', __('G_Code No.'), ['class' => 'form-label']) }}
                            {{ Form::text('agreement_number', '', ['id' => 'agreement_number', 'class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('date', __('Date of Issue'), ['class' => 'form-label']) }}
                            {{ Form::date('date', '', ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('loan_category_id', __('Allotment Category'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="loan_category_id[]" id="loan_category_id" required
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
                        <div class="form-group col-md-6">
                            {{ Form::label('price_kg', __('Price Kg'), ['class' => 'form-label']) }}
                            {{ Form::text('price_kg[]', '', ['class' => 'form-control', 'id' => 'price_kg', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Price Kg']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                            {{ Form::number('quantity[]', '', ['class' => 'form-control', 'min' => '1', 'required' => 'required', 'id' => 'quantity']) }}
                            <span style="color:red;" id="max_text"></span>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('total_amount', __('Total Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('total_amount[]', 0.0, ['class' => 'form-control', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Total Amount', 'id' => 'total_amount']) }}
                        </div>
                        <div class="form-group col-md-6">
                            <button type="button" class="btn btn-primary mt-4" id="add_more">Add More</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.loan.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
