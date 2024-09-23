@extends('layouts.master')
@section('title')
    {{ __('Bank Detail') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')

    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Bank Detail') }}</li>
        </ol>

        <div class="float-end">
            @can('create-bank_detail')
                <a href="{{ route('admin.farmer.bank_details.create') }}" class="btn btn-sm btn-primary">
                    Add
                </a>
            @endcan
        </div>
    </nav>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Farmer') }}</th>
                                    <th>{{ __('Finance Category') }}</th>
                                    <th>{{ __('Loan Type') }}</th>
                                    <th>{{ __('Bank') }}</th>
                                    <th>{{ __('Account Number') }}</th>
                                    <th>{{ __('IFSC Code') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Co-Operative Name') }}</th>
                                    <th>{{ __('Co-Operative Branch') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($farmings as $farming_detail)
                                    <tr class="font-style">
                                        <td>{{ $farming_detail->name }}</td>
                                        <td>{{ $farming_detail->finance_category }}</td>
                                        <td>{{ $farming_detail->non_loan_type }}</td>
                                        <td>{{ $farming_detail->bank }}</td>
                                        <td>{{ $farming_detail->account_number }}</td>
                                        <td>{{ $farming_detail->ifsc_code }}</td>
                                        <td>{{ $farming_detail->branch }}</td>
                                        <td>{{ $farming_detail->name_of_cooperative }}</td>
                                        <td>{{ $farming_detail->cooperative_address }}</td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                @can('edit-bank_detail')
                                                    <li class="me-2">
                                                        <a href="{{ route('admin.farmer.bank_details.edit', $farming_detail->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                            <i class="link-icon" data-feather="edit"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('delete-bank_detail')
                                                    <li>
                                                        <a class="deleteBtn" href="#"
                                                            data-href="{{ route('admin.farmer.bank_details.destroy', $farming_detail->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                            <i class="link-icon" data-feather="delete"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </li>
                                                @endcan
                                            </ul>
                                        </td>
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
