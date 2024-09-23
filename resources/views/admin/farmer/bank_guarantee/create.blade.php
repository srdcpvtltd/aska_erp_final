@extends('layouts.master')
@section('title')
    {{ __('Farmer Issue Bank Guarantee') }}
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('admin.farmer.allotment.index') }}">{{ __('Farmer Allotment') }}</a></li>
            <li class="breadcrumb-item">{{ __('Farmer Issue Bank Guarantee') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::open(['url' => 'admin/farmer/payment', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="type" id="type" value="Bank Guarantee">
            <input type="hidden" name="created_by" id="created_by" value="{{ Auth::user()->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Farmer Registration'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required
                                    placeholder="Select Country">
                                    <option value="">{{ __('Select Farmer Registration') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option value="{{ $farming->id }}">
                                            {{ $farming->name . '-' . $farming->farmer_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('bank', __('Bank'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="bank" id="bank" required
                                    placeholder="Select Country">
                                    <option value="">{{ __('Select Bank') }}</option>
                                    <option value="State Bank of India (SBI)">State Bank of India (SBI)</option>
                                    <option value="Punjab National Bank (PNB)">Punjab National Bank (PNB)</option>
                                    <option value="Bank of Baroda (BOB)">Bank of Baroda (BOB)</option>
                                    <option value="Canara Bank">Canara Bank</option>
                                    <option value="Union Bank of India">Union Bank of India</option>
                                    <option value="HDFC Bank">HDFC Bank</option>
                                    <option value="ICICI Bank">ICICI Bank</option>
                                    <option value="Axis Bank">Axis Bank</option>
                                    <option value="Kotak Mahindra Bank">Kotak Mahindra Bank</option>
                                    <option value="IndusInd Bank">IndusInd Bank</option>
                                    <option value="Yes Bank">Yes Bank</option>
                                    <option value="IDBI Bank">IDBI Bank</option>
                                    <option value="Central Bank of India">Central Bank of India</option>
                                    <option value="Indian Bank">Indian Bank</option>
                                    <option value="Bank of India">Bank of India</option>
                                    <option value="Oriental Bank of Commerce (OBC)">Oriental Bank of Commerce (OBC)</option>
                                    <option value="Corporation Bank">Corporation Bank</option>
                                    <option value="Andhra Bank">Andhra Bank</option>
                                    <option value="Allahabad Bank">Allahabad Bank</option>
                                    <option value="Syndicate Bank">Syndicate Bank</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('date', __('Loan Disbursement Date'), ['class' => 'form-label']) }}
                            {{ Form::date('date', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('amount', __('Loan Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('amount', 0.0, ['class' => 'form-control', 'step' => '0.01']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('loan_account_number', __('Loan Account No'), ['class' => 'form-label']) }}
                            {{ Form::text('loan_account_number', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('ifsc', __('IFSC'), ['class' => 'form-label']) }}
                            {{ Form::text('ifsc', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                            {{ Form::text('branch', '', ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.allotment.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
