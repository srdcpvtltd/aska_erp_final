@extends('layouts.master')
@section('title')
    {{ __('Bank Details Create') }}
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
            $('#bank').change(function() {
                let bank_id = $(this).val();
                console.log(bank_id);
                
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
                        // villages = response.villages;
                        // $('#village_id').empty();
                        // $('#village_id').append('<option  value="">Select Village</option>');
                        // for (i = 0; i < villages.length; i++) {
                        //     $('#village_id').append('<option value="' + villages[i].id + '">' +
                        //         villages[i].name + '</option>');
                        // }
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
            <li class="breadcrumb-item"><a href="{{ route('admin.farmer.bank_details.index') }}">{{ __('Bank Details') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Create') }}</li>
        </ol>
    </nav>

    <div class="row">
        {{ Form::open(['url' => 'admin/farmer/bank_details', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="created_by" id="created_by" value="{{ Auth::user()->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Select Farmer'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required
                                    placeholder="Select Farmer">
                                    <option value="">{{ __('Select Farmer') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option value="{{ $farming->id }}">{{ $farming->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            {{ Form::label('finance_category', __('Finance Category'), ['class' => 'form-label']) }}
                            <br>
                            <input type="radio" name="finance_category" value="Loan"> Loan
                            <input type="radio" name="finance_category" value="Non-loan"> Non-loan
                        </div>
                        <div class="col-md-6 finance_category_fields" style="display:none;">
                            <div class="form-group">
                                {{ Form::label('loan_type', __('Loan Type'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="loan_type" id="loan_type"
                                    placeholder="Select Loan Type">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Co-Operative">Co-Operative</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 bank_detail_fields" style="display:none;">
                            <div class="form-group">
                                {{ Form::label('bank', __('Bank'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="bank" id="bank">
                                    <option value="">{{ __('Select Bank') }}</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 bank_detail_fields" style="display:none;">
                            {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                            {{-- {{ Form::text('branch', '', ['class' => 'form-control']) }} --}}
                            <select class="form-control select" name="branch" id="branch">
                                <option value="">{{ __('Select Bank') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 bank_detail_fields" style="display:none;">
                            {{ Form::label('account_number', __('Loan Account Number'), ['class' => 'form-label']) }}
                            {{ Form::text('account_number', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6 bank_detail_fields" style="display:none;">
                            {{ Form::label('ifsc_code', __('IFSC Code'), ['class' => 'form-label']) }}
                            {{ Form::text('ifsc_code', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6 coperative_fields" style="display:none;">
                            {{ Form::label('name_of_cooperative', __('Co-Operative Name'), ['class' => 'form-label']) }}
                            {{ Form::text('name_of_cooperative', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6 coperative_fields" style="display:none;">
                            {{ Form::label('cooperative_address', __('Co-Operative Branch'), ['class' => 'form-label']) }}
                            {{ Form::text('cooperative_address', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="col-md-6 non_loan_fields" style="display:none;">
                            <div class="form-group">
                                {{ Form::label('bank', __('Bank'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="non_loan_bank" id="non_loan_bank">
                                    <option value="">{{ __('Select Bank') }}</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 non_loan_fields" style="display:none;">
                            {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                            {{-- {{ Form::text('non_loan_branch', '', ['class' => 'form-control']) }} --}}
                            <select class="form-control select" name="non_loan_branch" id="non_loan_branch">
                                <option value="">{{ __('Select Branch') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 non_loan_fields" style="display:none;">
                            {{ Form::label('account_number', __('Saving Account Number'), ['class' => 'form-label']) }}
                            {{ Form::text('non_loan_account_number', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6 non_loan_fields" style="display:none;">
                            {{ Form::label('ifsc_code', __('IFSC Code'), ['class' => 'form-label']) }}
                            {{ Form::text('non_loan_ifsc_code', '', ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.farming_detail.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
