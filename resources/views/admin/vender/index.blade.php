@extends('layouts.master')
@php
    $profile = asset(Storage::url('uploads/avatar/'));
@endphp
@section('title')
{{ __('Manage Vendors') }}
@endsection
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
@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Vendor') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.vender.file.import') }}" class="btn btn-info">
                Import
            </a>

            <a href="{{ route('admin.vender.export') }}" class="btn btn-success">
                Export
            </a>
            @can('create-vender')
                <a href="{{ route('admin.vender.create') }}" class="btn btn-primary">
                    Add
                </a>
            @endcan
        </div>
    </nav>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table data_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Contact') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Balance') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($venders as $k => $Vender)
                                    <tr class="cust_tr" id="vend_detail">
                                        <td class="Id">
                                            @can('show-vender')
                                                <a href="{{ route('admin.vender.show', \Crypt::encrypt($Vender['id'])) }}"
                                                    class="btn btn-outline-primary">
                                                    {{ AUth::user()->venderNumberFormat($Vender['vender_id']) }}
                                                </a>
                                            @else
                                                <a href="#" class="btn btn-outline-primary">
                                                    {{ AUth::user()->venderNumberFormat($Vender['vender_id']) }}
                                                </a>
                                            @endcan
                                        </td>
                                        <td>{{ $Vender['name'] }}</td>
                                        <td>{{ $Vender['contact'] }}</td>
                                        <td>{{ $Vender['email'] }}</td>
                                        <td>{{ \Auth::user()->priceFormat($Vender['balance']) }}</td>
                                        <td class="Action">
                                            <span>
                                                @if ($Vender['is_active'] == 0)
                                                    <i class="fa fa-lock" title="Inactive"></i>
                                                @else
                                                    <ul class="d-flex list-unstyled mb-0">
                                                        @can('show-vender')
                                                            <li class="me-2">
                                                                <a href="{{ route('admin.vender.show', \Crypt::encrypt($Vender['id'])) }}"
                                                                    data-bs-toggle="tooltip" title="{{ __('View') }}">
                                                                    <i class="link-icon" data-feather="eye"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('edit-vender')
                                                            <li class="me-2">
                                                                <a href="{{ route('admin.vender.edit', $Vender['id']) }}"
                                                                    title="{{ __('Edit') }}" data-bs-toggle="tooltip">
                                                                    <i class="link-icon" data-feather="edit"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete-vender')
                                                            <li>
                                                                <a class="deleteBtn" href="#"
                                                                    data-href="{{ route('admin.vender.destroy', $Vender['id']) }}"
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
