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
        });
    </script>
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.farmer.bank_details.index') }}">{{ __('Bank Details') }}</a>
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
                                        <select class="form-control select" name="bank" id="bank"
                                            placeholder="Select Bank">
                                            <option value="">{{ __('Select Bank') }}</option>
                                        <option value="State Bank of India (SBI)" {{ ($farmings->bank === "State Bank of India (SBI)") ? 'selected':'' }}>State Bank of India (SBI)</option>
                                        <option value="Punjab National Bank (PNB)" {{ ($farmings->bank === "Punjab National Bank (PNB)") ? 'selected':'' }}>Punjab National Bank (PNB)</option>
                                        <option value="Bank of Baroda (BOB)" {{ ($farmings->bank === "Bank of Baroda (BOB)") ? 'selected':'' }}>Bank of Baroda (BOB)</option>
                                        <option value="Canara Bank" {{ ($farmings->bank === "Canara Bank") ? 'selected':'' }}>Canara Bank</option>
                                        <option value="Union Bank of India" {{ ($farmings->bank === "Union Bank of India") ? 'selected':'' }}>Union Bank of India</option>
                                        <option value="HDFC Bank" {{ ($farmings->bank === "HDFC Bank") ? 'selected':'' }}>HDFC Bank</option>
                                        <option value="ICICI Bank" {{ ($farmings->bank === "ICICI Bank") ? 'selected':'' }}>ICICI Bank</option>
                                        <option value="Axis Bank" {{ ($farmings->bank === "Axis Bank") ? 'selected':'' }}>Axis Bank</option>
                                        <option value="Kotak Mahindra Bank" {{ ($farmings->bank === "Kotak Mahindra Bank") ? 'selected':'' }}>Kotak Mahindra Bank</option>
                                        <option value="IndusInd Bank" {{ ($farmings->bank === "IndusInd Bank") ? 'selected':'' }}>IndusInd Bank</option>
                                        <option value="Yes Bank" {{ ($farmings->bank === "Yes Bank") ? 'selected':'' }}>Yes Bank</option>
                                        <option value="IDBI Bank" {{ ($farmings->bank === "IDBI Bank") ? 'selected':'' }}>IDBI Bank</option>
                                        <option value="Central Bank of India" {{ ($farmings->bank === "Central Bank of India") ? 'selected':'' }}>Central Bank of India</option>
                                        <option value="Indian Bank" {{ ($farmings->bank === "Indian Bank") ? 'selected':'' }}>Indian Bank</option>
                                        <option value="Bank of India" {{ ($farmings->bank === "Bank of India") ? 'selected':'' }}>Bank of India</option>
                                        <option value="Oriental Bank of Commerce (OBC)" {{ ($farmings->bank === "Oriental Bank of Commerce (OBC)") ? 'selected':'' }}>Oriental Bank of Commerce (OBC)
                                        </option>
                                        <option value="Corporation Bank" {{ ($farmings->bank === "Corporation Bank") ? 'selected':'' }}>Corporation Bank</option>
                                        <option value="Andhra Bank" {{ ($farmings->bank === "Andhra Bank") ? 'selected':'' }}>Andhra Bank</option>
                                        <option value="Allahabad Bank" {{ ($farmings->bank === "Allahabad Bank") ? 'selected':'' }}>Allahabad Bank</option>
                                        <option value="Syndicate Bank" {{ ($farmings->bank === "Syndicate Bank") ? 'selected':'' }}>Syndicate Bank</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 bank_detail_fields">
                                    {{ Form::label('account_number', __('Loan Account Number'), ['class' => 'form-label']) }}
                                    {{ Form::text('account_number', $farmings->account_number, ['class' => 'form-control']) }}
                                </div>
                                <div class="form-group col-md-6 bank_detail_fields">
                                    {{ Form::label('ifsc_code', __('IFSC Code'), ['class' => 'form-label']) }}
                                    {{ Form::text('ifsc_code', $farmings->ifsc_code, ['class' => 'form-control']) }}
                                </div>
                                <div class="form-group col-md-6 bank_detail_fields">
                                    {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                                    {{ Form::text('branch', $farmings->branch, ['class' => 'form-control']) }}
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
                                    <select class="form-control select" name="non_loan_bank" id="bank"
                                        placeholder="Select Bank">
                                        <option value="">{{ __('Select Bank') }}</option>
                                        <option value="State Bank of India (SBI)" {{ ($farmings->bank === "State Bank of India (SBI)") ? 'selected':'' }}>State Bank of India (SBI)</option>
                                        <option value="Punjab National Bank (PNB)" {{ ($farmings->bank === "Punjab National Bank (PNB)") ? 'selected':'' }}>Punjab National Bank (PNB)</option>
                                        <option value="Bank of Baroda (BOB)" {{ ($farmings->bank === "Bank of Baroda (BOB)") ? 'selected':'' }}>Bank of Baroda (BOB)</option>
                                        <option value="Canara Bank" {{ ($farmings->bank === "Canara Bank") ? 'selected':'' }}>Canara Bank</option>
                                        <option value="Union Bank of India" {{ ($farmings->bank === "Union Bank of India") ? 'selected':'' }}>Union Bank of India</option>
                                        <option value="HDFC Bank" {{ ($farmings->bank === "HDFC Bank") ? 'selected':'' }}>HDFC Bank</option>
                                        <option value="ICICI Bank" {{ ($farmings->bank === "ICICI Bank") ? 'selected':'' }}>ICICI Bank</option>
                                        <option value="Axis Bank" {{ ($farmings->bank === "Axis Bank") ? 'selected':'' }}>Axis Bank</option>
                                        <option value="Kotak Mahindra Bank" {{ ($farmings->bank === "Kotak Mahindra Bank") ? 'selected':'' }}>Kotak Mahindra Bank</option>
                                        <option value="IndusInd Bank" {{ ($farmings->bank === "IndusInd Bank") ? 'selected':'' }}>IndusInd Bank</option>
                                        <option value="Yes Bank" {{ ($farmings->bank === "Yes Bank") ? 'selected':'' }}>Yes Bank</option>
                                        <option value="IDBI Bank" {{ ($farmings->bank === "IDBI Bank") ? 'selected':'' }}>IDBI Bank</option>
                                        <option value="Central Bank of India" {{ ($farmings->bank === "Central Bank of India") ? 'selected':'' }}>Central Bank of India</option>
                                        <option value="Indian Bank" {{ ($farmings->bank === "Indian Bank") ? 'selected':'' }}>Indian Bank</option>
                                        <option value="Bank of India" {{ ($farmings->bank === "Bank of India") ? 'selected':'' }}>Bank of India</option>
                                        <option value="Oriental Bank of Commerce (OBC)" {{ ($farmings->bank === "Oriental Bank of Commerce (OBC)") ? 'selected':'' }}>Oriental Bank of Commerce (OBC)
                                        </option>
                                        <option value="Corporation Bank" {{ ($farmings->bank === "Corporation Bank") ? 'selected':'' }}>Corporation Bank</option>
                                        <option value="Andhra Bank" {{ ($farmings->bank === "Andhra Bank") ? 'selected':'' }}>Andhra Bank</option>
                                        <option value="Allahabad Bank" {{ ($farmings->bank === "Allahabad Bank") ? 'selected':'' }}>Allahabad Bank</option>
                                        <option value="Syndicate Bank" {{ ($farmings->bank === "Syndicate Bank") ? 'selected':'' }}>Syndicate Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6 non_loan_fields">
                                {{ Form::label('account_number', __('Saving Account Number'), ['class' => 'form-label']) }}
                                {{ Form::text('non_loan_account_number', $farmings->account_number, ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group col-md-6 non_loan_fields">
                                {{ Form::label('ifsc_code', __('IFSC Code'), ['class' => 'form-label']) }}
                                {{ Form::text('non_loan_ifsc_code', $farmings->ifsc_code, ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group col-md-6 non_loan_fields">
                                {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                                {{ Form::text('non_loan_branch', $farmings->branch, ['class' => 'form-control']) }}
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
