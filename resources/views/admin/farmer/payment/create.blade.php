@extends('layouts.master')
@section('title')
    {{ __('Farmer Security Deposit Create') }}
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#farming_id').change(function() {
                let farmer_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.g_code') }}",
                    method: 'post',
                    data: {
                        farmer_id: farmer_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        registration_number = response.registration_id;
                        g_code_number = response.g_code;
                        $('#registration_number').val(registration_number);
                        $('#agreement_number').val(g_code_number);
                    }
                });
            });
            $('#receipt_type').change(function() {
                let receipt_type = $(this).val();
                console.log(receipt_type);
                if (receipt_type == "gl_no") {
                    $('#date_of_deposite').hide();
                    $('#amount').hide();
                } else {
                    $('#date_of_deposite').show();
                    $('#amount').show();
                }
            });
        });
    </script>
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.farmer.payment.index') }}">
                    {{ __('Farmer Security Deposit') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Farmer Security Deposit Create') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::open(['url' => 'admin/farmer/payment', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="created_by" id="created_by" value="{{ Auth::user()->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('receipt_type', __('Receipt Type'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="receipt_type" id="receipt_type" required
                                    placeholder="Select Country">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="gl_no">{{ __('GL No.') }}</option>
                                    <option value="receipt_no">{{ __('Receipt No.') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('receipt_no', __('Receipt No.'), ['class' => 'form-label']) }}
                            {{ Form::text('receipt_no', '', ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('agreement_number', __('G.Code'), ['class' => 'form-label']) }}
                            {{ Form::text('agreement_number', '', ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6" id="date_of_deposite">
                            {{ Form::label('date', __('Date of Deposit'), ['class' => 'form-label']) }}
                            {{ Form::date('date', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6" id="amount">
                            {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('amount', '', ['class' => 'form-control', 'step' => '0.01']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.payment.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
