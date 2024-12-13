@extends('layouts.master')
@section('title')
    {{ __('Challan') }}
@endsection

@section('main-content')
    <style>
        .action-btn {
            width: 29px;
            height: 28px;
            border-radius: 9.3552px;
            color: #fff;
            display: inline-table;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
    </style>
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Challan') }}</li>
        </ol>
        <div class="float-end">
            <a class="btn btn-success text-white">
                Export
            </a>
            @can('create-challan')
                <a href="{{ route('admin.challan.create') }}" data-size="lg" data-url="{{ route('admin.challan.create') }}"
                    data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                    data-title="{{ __('Create Challan') }}" class="btn btn-primary">
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
                                    <th>{{ __('Sl No.') }}</th>
                                    <th>{{ __('Warehouse') }}</th>
                                    <th>{{ __('Vendor') }}</th>
                                    <th>{{ __('Challan No.') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Receive Date') }}</th>
                                    <th>{{ __('Vehicle No.') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th class="text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($challans as $key => $challan)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $challan->warehouse->name }}</td>
                                        <td>{{ $challan->vendor->name }}</td>
                                        <td>{{ $challan->challan_no }}</td>
                                        <td>{{ $challan->product->name }}</td>
                                        <td>{{ $challan->receive_date }}</td>
                                        <td>{{ $challan->vehicle_no }}</td>
                                        <td>{{ $challan->quantity }}</td>
                                        <td>{{ $challan->amount }}</td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                @can('edit-challan')
                                                    <li class="me-2">
                                                        <a href="{{ route('admin.challan.edit', $challan->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-title="{{ __('Edit Challan') }}">
                                                            <i class="link-icon" data-feather="edit"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('delete-challan')
                                                    <li>
                                                        <a class="deleteBtn" href="#"
                                                            data-href="{{ route('admin.challan.destroy', $challan->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                            <i class="link-icon" data-feather="delete"></i>
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
