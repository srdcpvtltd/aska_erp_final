@extends('layouts.master')
@section('title')
    {{ __('Edit Customers') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Customer') }}</li>
            <li class="breadcrumb-item">{{ __('Edit') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.customer.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                {{ Form::model($customer, ['route' => ['admin.customer.update', $customer->id], 'method' => 'PUT']) }}
                <div class="modal-body">
                    <h6 class="sub-title">{{ __('Basic Info') }}</h6>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <div class="form-group">
                                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                                {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <div class="form-group">
                                {{ Form::label('contact', __('Contact'), ['class' => 'form-label']) }}
                                {{ Form::number('contact', null, ['class' => 'form-control', 'required' => 'required']) }}

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <div class="form-group">
                                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
                                {{ Form::text('email', null, ['class' => 'form-control']) }}

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <div class="form-group">
                                {{ Form::label('tax_number', __('Tax Number'), ['class' => 'form-label']) }}
                                {{ Form::text('tax_number', null, ['class' => 'form-control']) }}

                            </div>
                        </div>
                        @if (!$customFields->isEmpty())
                            <div class="col-lg-4 col-md-4 col-sm-6">
                                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                    @include('admin.customFields.formBuilder')
                                </div>
                            </div>
                        @endif
                    </div>

                    <h6 class="sub-title">{{ __('Billing Address') }}</h6>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                {{ Form::label('billing_name', __('Name'), ['class' => '', 'class' => 'form-label']) }}
                                {{ Form::text('billing_name', null, ['class' => 'form-control']) }}

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                {{ Form::label('billing_phone', __('Phone'), ['class' => 'form-label']) }}
                                {{ Form::text('billing_phone', null, ['class' => 'form-control']) }}

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('billing_address', __('Address'), ['class' => 'form-label']) }}
                                {{ Form::textarea('billing_address', null, ['class' => 'form-control', 'rows' => 3]) }}

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                {{ Form::label('billing_city', __('City'), ['class' => 'form-label']) }}
                                {{ Form::text('billing_city', null, ['class' => 'form-control']) }}

                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                {{ Form::label('billing_state', __('State'), ['class' => 'form-label']) }}
                                {{ Form::text('billing_state', null, ['class' => 'form-control']) }}

                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                {{ Form::label('billing_country', __('Country'), ['class' => 'form-label']) }}
                                {{ Form::text('billing_country', null, ['class' => 'form-control']) }}

                            </div>
                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                {{ Form::label('billing_zip', __('Zip Code'), ['class' => 'form-label']) }}
                                {{ Form::text('billing_zip', null, ['class' => 'form-control']) }}

                            </div>
                        </div>

                    </div>

                    @if (App\Models\Utility::getValByName('shipping_display') == 'on')
                        <div class="col-md-12 text-end">
                            <input type="button" id="billing_data" value="{{ __('Shipping Same As Billing') }}"
                                class="btn btn-primary">
                        </div>
                        <h6 class="sub-title">{{ __('Shipping Address') }}</h6>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('shipping_name', __('Name'), ['class' => 'form-label']) }}
                                    {{ Form::text('shipping_name', null, ['class' => 'form-control']) }}

                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('shipping_phone', __('Phone'), ['class' => 'form-label']) }}
                                    {{ Form::text('shipping_phone', null, ['class' => 'form-control']) }}

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{ Form::label('shipping_address', __('Address'), ['class' => 'form-label']) }}
                                    <label class="form-label" for="example2cols1Input"></label>
                                    <div class="input-group">
                                        {{ Form::textarea('shipping_address', null, ['class' => 'form-control', 'rows' => 3]) }}
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('shipping_city', __('City'), ['class' => 'form-label']) }}
                                    {{ Form::text('shipping_city', null, ['class' => 'form-control']) }}

                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('shipping_state', __('State'), ['class' => 'form-label']) }}
                                    {{ Form::text('shipping_state', null, ['class' => 'form-control']) }}

                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('shipping_country', __('Country'), ['class' => 'form-label']) }}
                                    {{ Form::text('shipping_country', null, ['class' => 'form-control']) }}

                                </div>
                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('shipping_zip', __('Zip Code'), ['class' => 'form-label']) }}
                                    {{ Form::text('shipping_zip', null, ['class' => 'form-control']) }}
                                </div>
                            </div>

                        </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.customer.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection