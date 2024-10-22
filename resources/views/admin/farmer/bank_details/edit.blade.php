@extends('layouts.master')
@section('title')
    {{ __('Edit Bank Details') }}
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('input[type=radio][name="finance_category"]').on('change', function(event) {
                var value = $(this).val();
                if (value == "Non-loan") {
                    $('.finance_category_fields').hide();
                    $('.coperative_fields').hide();
                    $('.bank_detail_fields').hide();
                    $('.non_loan_fields').show();
                } else {
                    $('.finance_category_fields').show();
                    $('.non_loan_fields').hide();
                }
            });
            $('#loan_type').on('change', function(event) {
                var value = $(this).val();
                if (value == "Bank") {
                    $('.coperative_fields').hide();
                    $('.bank_detail_fields').show();
                } else {
                    $('.bank_detail_fields').hide();
                    $('.coperative_fields').show();
                }
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
            $('#non_loan_bank').change(function() {
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
                        $('#non_loan_branch').empty();
                        $('#non_loan_branch').append('<option value="">Select Branch</option>');
                        for (i = 0; i < response.length; i++) {
                            $('#non_loan_branch').append('<option value="' + response[i].id +
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
            $('#non_loan_branch').change(function() {
                let branch_id = $(this).val();
                let ifsc = $('#non_loan_ifsc_code').val();
                console.log(ifsc);

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
                        $('#non_loan_ifsc_code').empty();
                        $('#non_loan_ifsc_code').val(response.ifsc_code);
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
                    href="{{ route('admin.farmer.bank_details.index') }}">{{ __('Bank Details') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Edit') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::model($farmings, ['route' => ['admin.farmer.bank_details.update', $farmings->id], 'method' => 'PUT', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Farming'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required
                                    placeholder="Select Farmer">
                                    <option value="">{{ __('Select Farmer') }}</option>
                                    @foreach ($farming as $farm)
                                        <option {{ $farm->id == $farmings->id ? 'selected' : '' }}
                                            value="{{ $farm->id }}">{{ $farm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            {{ Form::label('finance_category', __('Finance Category'), ['class' => 'form-label']) }}
                            <br>
                            <input type="radio" name="finance_category" value="Loan"
                                {{ $farmings->finance_category === 'Loan' ? 'checked' : '' }}> Loan
                            <input type="radio" name="finance_category" value="Non-loan"
                                {{ $farmings->finance_category === 'Non-loan' ? 'checked' : '' }}> Non-loan
                        </div>
                        @if ($farmings->finance_category === 'Loan')
                            <div class="col-md-6 finance_category_fields">
                                <div class="form-group">
                                    {{ Form::label('loan_type', __('Loan Type'), ['class' => 'form-label']) }}
                                    <select class="form-control select" name="loan_type" id="loan_type"
                                        placeholder="Select Loan Type">
                                        <option value="">{{ __('Select') }}</option>
                                        <option value="Bank" {{ $farmings->non_loan_type === 'Bank' ? 'selected' : '' }}>
                                            Bank</option>
                                        <option value="Co-Operative"
                                            {{ $farmings->non_loan_type === 'Co-Operative' ? 'selected' : '' }}>
                                            Co-Operative</option>
                                    </select>
                                </div>
                            </div>
                            @if ($farmings->non_loan_type === 'Bank')
                                <div class="col-md-6 bank_detail_fields">
                                    <div class="form-group">
                                        {{ Form::label('bank', __('Bank'), ['class' => 'form-label']) }}
                                        <select class="form-control select" name="bank" id="bank_detail"
                                            placeholder="Select Bank">
                                            <option value="">{{ __('Select Bank') }}</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}"
                                                    {{ $farmings->bank == $bank->id ? 'selected' : '' }}>
                                                    {{ $bank->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 bank_detail_fields">
                                    {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                                    {{-- {{ Form::text('branch', $farmings->branch, ['class' => 'form-control']) }} --}}
                                    <select class="form-control select" name="branch" id="branch_detail">
                                        <option value="">{{ __('Select Branch') }}</option>
                                        @foreach ($bank_branchs as $bank_branch)
                                            <option value="{{ $bank_branch->id }}"
                                                {{ $farmings->branch == $bank_branch->id ? 'selected' : '' }}>
                                                {{ $bank_branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6 bank_detail_fields">
                                    {{ Form::label('account_number', __('Loan Account Number'), ['class' => 'form-label']) }}
                                    {{ Form::text('account_number', $farmings->account_number, ['class' => 'form-control']) }}
                                </div>
                                <div class="form-group col-md-6 bank_detail_fields">
                                    {{ Form::label('ifsc_code', __('IFSC Code'), ['class' => 'form-label']) }}
                                    {{ Form::text('ifsc_code', $farmings->ifsc_code, ['class' => 'form-control']) }}
                                </div>
                            @endif
                        @endif
                        @if ($farmings->non_loan_type === 'Co-Operative')
                            <div class="form-group col-md-6 coperative_fields">
                                {{ Form::label('name_of_cooperative', __('Co-Operative Name'), ['class' => 'form-label']) }}
                                {{ Form::text('name_of_cooperative', $farmings->name_of_cooperative, ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group col-md-6 coperative_fields">
                                {{ Form::label('cooperative_address', __('Co-Operative Branch'), ['class' => 'form-label']) }}
                                {{ Form::text('cooperative_address', $farmings->cooperative_address, ['class' => 'form-control']) }}
                            </div>
                        @endif
                        @if ($farmings->finance_category === 'Non-loan')
                            <div class="col-md-6 non_loan_fields">
                                <div class="form-group">
                                    {{ Form::label('bank', __('Bank'), ['class' => 'form-label']) }}
                                    <select class="form-control select" name="non_loan_bank" id="non_loan_bank"
                                        placeholder="Select Bank">
                                        <option value="">{{ __('Select Bank') }}</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}"
                                                {{ $farmings->bank == $bank->id ? 'selected' : '' }}>{{ $bank->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6 non_loan_fields">
                                {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                                {{-- {{ Form::text('non_loan_branch', $farmings->branch, ['class' => 'form-control']) }} --}}
                                <select class="form-control select" name="non_loan_branch" id="non_loan_branch">
                                    <option value="">{{ __('Select Branch') }}</option>
                                    @foreach ($bank_branchs as $bank_branch)
                                        <option value="{{ $bank_branch->id }}"
                                            {{ $farmings->branch == $bank_branch->id ? 'selected' : '' }}>
                                            {{ $bank_branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6 non_loan_fields">
                                {{ Form::label('account_number', __('Saving Account Number'), ['class' => 'form-label']) }}
                                {{ Form::text('non_loan_account_number', $farmings->account_number, ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group col-md-6 non_loan_fields">
                                {{ Form::label('ifsc_code', __('IFSC Code'), ['class' => 'form-label']) }}
                                {{ Form::text('non_loan_ifsc_code', $farmings->ifsc_code, ['class' => 'form-control']) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.farming_detail.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
