@extends('layouts.master')
@section('title')
    {{ __('Farmer Registration Details') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Farmer Registration Detail') }}</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="farmer-detail-tab" data-bs-toggle="pill" href="#detail"
                                role="tab" aria-controls="pills-home"
                                aria-selected="true">{{ __('Farmer Details') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="farmer-guarantor-tab" data-bs-toggle="pill" href="#plot_details"
                                role="tab" aria-controls="pills-profile"
                                aria-selected="false">{{ __('Plot Details') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="farmer-guarantor-tab" data-bs-toggle="pill" href="#guarantor"
                                role="tab" aria-controls="pills-profile"
                                aria-selected="false">{{ __('Guarantors') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="farmer-payment-tab" data-bs-toggle="pill" href="#payment" role="tab"
                                aria-controls="pills-contact" aria-selected="false">{{ __('Payments') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="farmer-payment-tab" data-bs-toggle="pill" href="#loans" role="tab"
                                aria-controls="pills-contact" aria-selected="false">{{ __('Loans') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="farmer-bank_guarantee-tab" data-bs-toggle="pill" href="#bank_guarantee"
                                role="tab" aria-controls="pills-bank_guarantee"
                                aria-selected="false">{{ __('Issued Bank Guarantee') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="detail" role="tabpanel"
                            aria-labelledby="pills-home-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('admin.farmer.registration.partials.detail')
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="guarantor" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('admin.farmer.registration.partials.guarantors')
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="plot_details" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('admin.farmer.registration.partials.plot_details')
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('admin.farmer.registration.partials.payments')
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="loans" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('admin.farmer.registration.partials.loans')
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="bank_guarantee" role="tabpanel"
                            aria-labelledby="pills-bank_guarantee-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('admin.farmer.registration.partials.bank_guarantee')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
