@extends('layouts.master')
@php
    $profile = \App\Models\Utility::get_file('uploads/avatar');
@endphp
@section('scripts')
    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_name']").val($("[name='billing_name']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_phone']").val($("[name='billing_phone']").val());
            $("[name='shipping_zip']").val($("[name='billing_zip']").val());
            $("[name='shipping_address']").val($("[name='billing_address']").val());
        })
    </script>
@endsection
@section('title')
    {{ __('Manage Customers') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Customer') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.customer.file.import') }}" class="btn btn-info">
                Import
            </a>
            <a href="{{ route('admin.customer.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
                class="btn btn-success">
                Export
            </a>
            <a href="{{ route('admin.customer.create') }}" class="btn btn-primary">
                Add
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="data_table table datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th> {{ __('Name') }}</th>
                                    <th> {{ __('Contact') }}</th>
                                    <th> {{ __('Email') }}</th>
                                    <th> {{ __('Balance') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $k => $customer)
                                    <tr class="cust_tr" id="cust_detail"
                                        data-url="{{ route('admin.customer.show', $customer['id']) }}"
                                        data-id="{{ $customer['id'] }}">
                                        <td class="Id">
                                            @can('show customer')
                                                <a href="{{ route('admin.customer.show', \Crypt::encrypt($customer['id'])) }}"
                                                    class="btn btn-outline-primary">
                                                    {{ Auth::user()->customerNumberFormat($customer['customer_id']) }}
                                                </a>
                                            @else
                                                <a href="#" class="btn btn-outline-primary">
                                                    {{ Auth::user()->customerNumberFormat($customer['customer_id']) }}
                                                </a>
                                            @endcan
                                        </td>
                                        <td class="font-style">{{ $customer['name'] }}</td>
                                        <td>{{ $customer['contact'] }}</td>
                                        <td>{{ $customer['email'] }}</td>
                                        <td>{{ \Auth::user()->priceFormat($customer['balance']) }}</td>
                                        <td class="Action">
                                            <span>
                                                @if ($customer['is_active'] == 0)
                                                    <i class="ti ti-lock" title="Inactive"></i>
                                                @else
                                                    <ul class="d-flex list-unstyled mb-0">
                                                        @can('show customer')
                                                            <li class="me-2">
                                                                <a href="{{ route('admin.customer.show', \Crypt::encrypt($customer['id'])) }}"
                                                                    data-bs-toggle="tooltip" title="{{ __('View') }}">
                                                                    <i class="link-icon" data-feather="eye"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('edit customer')
                                                            <li class="me-2">
                                                                <a href="{{ route('admin.customer.edit', $customer['id']) }}">
                                                                    <i class="link-icon" data-feather="edit"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete customer')
                                                            <li>
                                                                <a class="deleteBtn" href="#"
                                                                    data-href="{{ route('admin.customer.destroy', $customer['id']) }}"
                                                                    title="{{ __('Delete') }}">
                                                                    <i class="link-icon" data-feather="delete"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                @endif
                                            </span>
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
