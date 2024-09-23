@extends('layouts.master')
@section('title')
    {{ __('Manage Bank Account') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Accounting system') }}</li>
            <li class="breadcrumb-item">{{ __('Bank Account') }}</li>
        </ol>
        <div class="float-end">
            @can('create-bank_account')
                <a href="{{ route('admin.bank-account.create') }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                    title="{{ __('Create') }}" data-title="{{ __('Create New Bank Account') }}" class="btn btn-primary">
                    Add
                </a>
            @endcan
        </div>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Chart Of Account') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Bank') }}</th>
                                    <th>{{ __('Account Number') }}</th>
                                    <th>{{ __('Current Balance') }}</th>
                                    <th>{{ __('Contact Number') }}</th>
                                    <th>{{ __('Bank Branch') }}</th>
                                    <th width="10%"> {{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($accounts as $account)
                                    <tr class="font-style">
                                        <td>{{ !empty($account->chartAccount) ? $account->chartAccount->name : '-' }}</td>
                                        <td>{{ $account->holder_name }}</td>
                                        <td>{{ $account->bank_name }}</td>
                                        <td>{{ $account->account_number }}</td>
                                        <td>{{ \Auth::user()->priceFormat($account->opening_balance) }}</td>
                                        <td>{{ $account->contact_number }}</td>
                                        <td>{{ $account->bank_address }}</td>
                                        @if (Gate::check('edit-bank_account') || Gate::check('delete-bank_account'))
                                            <td class="Action">
                                                <span>
                                                    @if ($account->holder_name != 'Cash')
                                                        <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                            @can('edit-bank_account')
                                                                <li class="me-2">
                                                                    <a href="{{ route('admin.bank-account.edit', $account->id) }}" title="{{ __('Edit') }}">
                                                                        <i class="link-icon" data-feather="edit"></i>
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                            @can('delete-bank_account')
                                                                <li>
                                                                    <a class="deleteBtn" href="#" data-href="{{ route('admin.bank-account.destroy', $account->id) }}" title="{{ __('Delete') }}">
                                                                        <i class="link-icon"  data-feather="delete"></i>
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                        </ul>
                                                    @else
                                                        -
                                                    @endif
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
