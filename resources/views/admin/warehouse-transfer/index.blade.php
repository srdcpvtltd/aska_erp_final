@extends('layouts.master')
@section('title')
    {{ __('Warehouse Transfer') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Warehouse Transfer') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.warehouse-transfer.create') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                data-title="{{ __('Create Warehouse Transfer') }}" class="btn btn-sm btn-primary">
                Add
            </a>
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
                                    <th>{{ __('From Warehouse') }}</th>
                                    <th>{{ __('To Warehouse') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($warehouse_transfers as $warehouse_transfer)
                                    <tr class="font-style">
                                        <td>{{ !empty($warehouse_transfer->fromWarehouse) ? $warehouse_transfer->fromWarehouse->name : '' }}
                                        </td>
                                        <td>{{ !empty($warehouse_transfer->toWarehouse) ? $warehouse_transfer->toWarehouse->name : '' }}
                                        </td>
                                        @if (!empty($warehouse_transfer->product))
                                            <td>{{ !empty($warehouse_transfer->product) ? $warehouse_transfer->product->name : '' }}
                                            </td>
                                        @endif
                                        <td>{{ $warehouse_transfer->quantity }}</td>
                                        <td>{{ Auth::user()->dateFormat($warehouse_transfer->date) }}</td>

                                        @if (Gate::check('edit warehouse') || Gate::check('delete warehouse'))
                                            <td class="Action">
                                                @can('edit-warehouse')
                                                    <div class="action-btn bg-info ms-2">
                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                            data-url="{{ route('admin.warehouse-transfer.edit', $warehouse_transfer->id) }}"
                                                            data-ajax-popup="true" data-size="lg " data-bs-toggle="tooltip"
                                                            title="{{ __('Edit') }}"
                                                            data-title="{{ __('Edit Warehouse') }}">
                                                            <i class="link-icon" data-feather="edit"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('delete-warehouse')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['admin.warehouse-transfer.destroy', $warehouse_transfer->id],
                                                            'id' => 'delete-form-' . $warehouse_transfer->id,
                                                        ]) !!}
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm  align-items-center bs-pass-para"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                class="ti ti-trash text-white"></i></a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
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
