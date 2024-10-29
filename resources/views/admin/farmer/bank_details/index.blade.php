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
        <a href="{{ route('admin.farmer.bank_details.create') }}" class="btn btn-primary">
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
                    <table class="data_table table datatable">
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
                                <td>{{ $farming_detail->name ?? "N/A" }}</td>
                                <td>{{ $farming_detail->finance_category  ?? "N/A" }}</td>
                                <td>{{ $farming_detail->non_loan_type ?? "N/A" }}</td>
                                <td>{{ $farming_detail->bank_data->name ?? "N/A" }}</td>
                                <td>{{ $farming_detail->account_number ?? "N/A" }}</td>
                                <td>{{ $farming_detail->ifsc_code ?? "N/A" }}</td>
                                <td>{{ $farming_detail->bank_branch->name ?? "N/A" }}</td>
                                <td>{{ $farming_detail->name_of_cooperative ?? "N/A" }}</td>
                                <td>{{ $farming_detail->cooperative_address ?? "N/A" }}</td>
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