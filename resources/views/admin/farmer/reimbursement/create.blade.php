@extends('layouts.master')
@section('title')
    {{ __('Farmer Reimbursement Create') }}
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
            $('#bank_detail').change(function() {
                let bank_id = $(this).val();

                $.ajax({
                    url: "{{ route('admin.farmer.location.get_bank_branches') }}",
                    method: 'post',
                    data: {
                        bank_id: bank_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log(response);
                        $('#branch_detail').empty();
                        $('#branch_detail').append('<option value="">Select Branch</option>');
                        for (i = 0; i < response.length; i++) {
                            $('#branch_detail').append('<option value="' + response[i].id +
                                '">' +
                                response[i].name + '</option>');
                        }
                    }
                });
            });
            $('#branch_detail').change(function() {
                let branch_id = $(this).val();

                $.ajax({
                    url: "{{ route('admin.farmer.location.get_branch_ifsc_code') }}",
                    method: 'post',
                    data: {
                        branch_id: branch_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log(response);
                        $('#ifsc_code').empty();
                        $('#ifsc_code').val(response.ifsc_code);
                    }
                });
            });
        });
    </script>
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('admin.farmer.reimbursement.index') }}">{{ __('Farmer Reimbursement') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Farmer Reimbursement Create') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::open(['url' => 'admin/farmer/reimbursement/store', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="created_by" id="created_by" value="{{ Auth::user()->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('invoice_no', __('Invoice No.'), ['class' => 'form-label']) }}
                            {{ Form::text('invoice_no', '', ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('g_code', __('G_Code No.'), ['class' => 'form-label']) }}
                            {{ Form::text('g_code', '', ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Farmer Registration'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required>
                                    <option value="">{{ __('Select Farmer Registration') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option value="{{ $farming->id }}">{{ $farming->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('registration_number', __('Registration No.'), ['class' => 'form-label']) }}
                            {{ Form::text('registration_number', '', ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('type', __('Payment Type'), ['class' => 'form-label']) }}
                            {{ Form::text('type', 'Reimbursement', ['class' => 'form-control', 'required' => 'required', 'readonly' => 'true']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('date', __('Date of Deposit'), ['class' => 'form-label']) }}
                            {{ Form::date('date', '', ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="col-md-6 bank_detail_fields">
                            <div class="form-group">
                                {{ Form::label('bank', __('Bank'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="bank" id="bank_detail">
                                    <option value="">{{ __('Select Bank') }}</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 bank_detail_fields">
                            {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                            <select class="form-control select" name="branch" id="branch_detail">
                                <option value="">{{ __('Select Branch') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 bank_detail_fields">
                            {{ Form::label('account_number', __('Loan Account Number'), ['class' => 'form-label']) }}
                            {{ Form::text('account_number', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6 bank_detail_fields">
                            {{ Form::label('ifsc_code', __('IFSC Code'), ['class' => 'form-label']) }}
                            {{ Form::text('ifsc_code', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('amount', '', ['class' => 'form-control', 'step' => '0.01', 'required' => 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <input type="button" value="{{ __('Cancel') }}"
                        onclick="location.href = '{{ route('admin.farmer.reimbursement.index') }}';" class="btn btn-light">
                    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection
