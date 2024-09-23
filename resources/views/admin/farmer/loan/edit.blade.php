@extends('layouts.master')
@section('title')
    {{ __('Edit Farming Loan') }}
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
                        // $('#agreement_number').val(response.farming.registration_number);
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
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Farmer Registration'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required
                                    placeholder="Select Country">
                                    <option value="">{{ __('Select Farmer Registration') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option {{ $farming->id == $loan->farming_id ? 'selected' : '' }}
                                            value="{{ $farming->id }}">{{ $farming->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('registration_number', __('Registration No.'), ['class' => 'form-label']) }}
                            {{ Form::text('registration_number', $loan->registration_number, ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('agreement_number', __('G_Code No.'), ['class' => 'form-label']) }}
                            {{ Form::text('agreement_number', $loan->agreement_number, ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('date', __('Date of Deposit'), ['class' => 'form-label']) }}
                            {{ Form::date('date', $loan->date, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        @php
                            $loan_category_id = json_decode($loan->loan_category_id);
                            $loan_type_id = json_decode($loan->loan_type_id);
                            $price_kg = json_decode($loan->price_kg);
                            $quantity = json_decode($loan->quantity);
                            $total_amount = json_decode($loan->total_amount);
                            $count = count($loan_category_id);
                        @endphp
                        @for ($i = 0; $i < $count; $i++)
                            @if ($i == 0)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('loan_category_id', __('Loan Category'), ['class' => 'form-label']) }}
                                        <select class="form-control select" name="loan_category_id[]" id="loan_category_id"
                                            required placeholder="Select Country">
                                            <option value="">{{ __('Select Loan Category') }}</option>
                                            @foreach ($categories as $category)
                                                <option {{ $category->id == $loan_category_id[$i] ? 'selected' : '' }}
                                                    value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('loan_type_id', __('Loan Type'), ['class' => 'form-label']) }}
                                        <select class="form-control select" name="loan_type_id[]" id="loan_type_id"
                                            placeholder="Select Type" required>
                                            <option value="">{{ __('Select Loan Type') }}</option>
                                            @foreach ($types as $type)
                                                <option {{ $type->id == $loan_type_id[$i] ? 'selected' : '' }}
                                                    value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('price_kg', __('Price Kg'), ['class' => 'form-label']) }}
                                    {{ Form::text('price_kg[]', $price_kg[$i], ['class' => 'form-control', 'id' => 'price_kg', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Price Kg']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                                    {{ Form::number('quantity[]', $quantity[$i], ['class' => 'form-control', 'min' => '1', 'required' => 'required', 'id' => 'quantity']) }}
                                    <span style="color:red;" id="max_text"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('total_amount', __('Total Amount'), ['class' => 'form-label']) }}
                                    {{ Form::number('total_amount[]', $total_amount[$i], ['class' => 'form-control', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Total Amount', 'id' => 'total_amount']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    <button type="button" class="btn btn-primary mt-4" id="add_more">Add More</button>
                                </div>
                            @elseif($i > 0)
                                <div class="row pd_right_0 append_div">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('loan_category_id', __('Allotment Category'), ['class' => 'form-label']) }}
                                            <select class="form-control select loan_category_id" name="loan_category_id[]"
                                                required placeholder="Select Country">
                                                <option value="">{{ __('Select Category') }}</option>
                                                @foreach ($categories as $category)
                                                    <option {{ $category->id == $loan_category_id[$i] ? 'selected' : '' }}
                                                        value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pd_right_0">
                                        <div class="form-group">
                                            {{ Form::label('loan_type_id', __('Item'), ['class' => 'form-label']) }}
                                            <select class="form-control select loan_type_id" name="loan_type_id[]"
                                                placeholder="Select Loan Type" required>
                                                <option value="">{{ __('Select Item') }}</option>
                                                @foreach ($types as $type)
                                                    <option {{ $type->id == $loan_type_id[$i] ? 'selected' : '' }}
                                                        value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('price_kg', __('Price Kg'), ['class' => 'form-label']) }}
                                        {{ Form::text('price_kg[]', $price_kg[$i], ['class' => 'form-control price_kg', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Price Kg']) }}
                                    </div>
                                    <div class="form-group col-md-6 pd_right_0">
                                        {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                                        {{ Form::number('quantity[]', $quantity[$i], ['class' => 'form-control quantity', 'min' => '1', 'required' => 'required']) }}
                                        <span style="color:red;" class="max_text"></span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('total_amount', __('Total Amount'), ['class' => 'form-label']) }}
                                        {{ Form::number('total_amount[]', $total_amount[$i], ['class' => 'form-control total_amount', 'required' => 'required', 'readonly' => true, 'placeholder' => 'Total Amount']) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        <button class="btn btn-danger mt-4 delete">Delete</button>
                                    </div>
                                </div>
                            @endif
                        @endfor
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
