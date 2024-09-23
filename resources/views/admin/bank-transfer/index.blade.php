@extends('layouts.master')
@section('title')
    {{ __('Bank Balance Transfer') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Bank Balance Transfer') }}</li>
        </ol>
        <div class="float-end">
            @can('create-bank_transfer')
                <a href="{{ route('admin.bank-transfer.create') }}" class="btn btn-primary">
                    Add
                </a>
            @endcan
        </div>
    </nav>
    <div class="row">
        <div class="col-sm-12">
            <div class=" mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['admin.bank-transfer.index'], 'method' => 'GET', 'id' => 'transfer_form']) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-8">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 month">
                                        <div class="btn-box">
                                            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                            {{ Form::text('date', isset($_GET['date']) ? $_GET['date'] : null, ['class' => 'form-control', 'id' => 'daterangepicker']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 date">
                                        <div class="btn-box">
                                            {{ Form::label('f_account', __('From Account'), ['class' => 'form-label']) }}
                                            {{ Form::select('f_account', $account, isset($_GET['f_account']) ? $_GET['f_account'] : '', ['class' => 'form-control select']) }}
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('t_account', __('To Account'), ['class' => 'form-label']) }}
                                            {{ Form::select('t_account', $account, isset($_GET['t_account']) ? $_GET['t_account'] : '', ['class' => 'form-control select']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto">

                                        <a href="#" class="btn btn-sm btn-primary"
                                            onclick="document.getElementById('transfer_form').submit(); return false;"
                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon"><i class="link-icon"
                                                    data-feather="search"></i></span>
                                        </a>

                                        <a href="{{ route('admin.bank-transfer.index') }}" class="btn btn-sm btn-danger "
                                            data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                            data-original-title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon"><i class="link-icon"
                                                    data-feather="trash"></i></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th> {{ __('Date') }}</th>
                                    <th> {{ __('From Account') }}</th>
                                    <th> {{ __('To Account') }}</th>
                                    <th> {{ __('Amount') }}</th>
                                    <th> {{ __('Reference') }}</th>
                                    <th> {{ __('Description') }}</th>
                                    @if (Gate::check('edit transfer') || Gate::check('delete transfer'))
                                        <th width="10%"> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($transfers as $transfer)
                                    <tr class="font-style">
                                        <td>{{ \Auth::user()->dateFormat($transfer->date) }}</td>
                                        <td>{{ !empty($transfer->fromBankAccount) ? $transfer->fromBankAccount->bank_name . ' ' . $transfer->fromBankAccount->holder_name : '' }}
                                        </td>
                                        <td>{{ !empty($transfer->toBankAccount) ? $transfer->toBankAccount->bank_name . ' ' . $transfer->toBankAccount->holder_name : '' }}
                                        </td>
                                        <td>{{ \Auth::user()->priceFormat($transfer->amount) }}</td>
                                        <td>{{ $transfer->reference }}</td>
                                        <td>{{ $transfer->description }}</td>
                                        @if (Gate::check('edit-bank_transfer') || Gate::check('delete-bank_transfer'))
                                            <td class="Action">
                                                <span>
                                                    <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                        @can('edit-bank_transfer')
                                                            <li class="me-2">
                                                                <a href="{{ route('admin.bank-transfer.edit', $transfer->id) }}"
                                                                    title="{{ __('Edit') }}">
                                                                    <i class="link-icon" data-feather="edit"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete-bank_transfer')
                                                            <li>
                                                                <a class="deleteBtn" href="#" data-href="{{ route('admin.bank-transfer.destroy', $transfer->id) }}" title="{{ __('Delete') }}">
                                                                    <i class="link-icon"  data-feather="delete"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script>
        $(document).ready(function() {
            $('#daterangepicker').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                },
                autoUpdateInput: false,
            });
            // On Apply event: Update the input field with the selected date
            $('#daterangepicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format(
                    'YYYY-MM-DD'));
            });

            // On Cancel event: Clear the input field
            $('#daterangepicker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(''); // Set the value to empty
            });
        });
    </script>
@endsection
