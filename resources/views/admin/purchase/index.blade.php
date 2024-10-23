@extends('layouts.master')
@section('title')
    {{ __('Manage Purchase') }}
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
            <li class="breadcrumb-item">{{ __('Purchase') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.purchase.create', 0) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                title="{{ __('Create') }}">
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
                                    <th> {{ __('Purchase') }}</th>
                                    <th> {{ __('Vendor') }}</th>
                                    <th> {{ __('Category') }}</th>
                                    <th> {{ __('Purchase Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @if (Gate::check('edit purchase') || Gate::check('delete purchase') || Gate::check('show purchase'))
                                        <th> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $purchase)
                                    <tr>
                                        <td class="Id">
                                            <a href="{{ route('admin.purchase.show', \Crypt::encrypt($purchase->id)) }}"
                                                class="btn btn-outline-primary">{{ Auth::user()->purchaseNumberFormat($purchase->purchase_id) }}</a>
                                        </td>
                                        <td> {{ !empty($purchase->vender) ? $purchase->vender->name : '' }} </td>
                                        <td>{{ !empty($purchase->category) ? $purchase->category->name : '' }}</td>
                                        <td>{{ Auth::user()->dateFormat($purchase->purchase_date) }}</td>
                                        <td>
                                            @if ($purchase->status == 0)
                                                <span
                                                    class="purchase_status badge bg-secondary p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                            @elseif($purchase->status == 1)
                                                <span
                                                    class="purchase_status badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                            @elseif($purchase->status == 2)
                                                <span
                                                    class="purchase_status badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                            @elseif($purchase->status == 3)
                                                <span
                                                    class="purchase_status badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                            @elseif($purchase->status == 4)
                                                <span
                                                    class="purchase_status badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                            @endif
                                        </td>
                                        @if (Gate::check('edit purchase') || Gate::check('delete purchase') || Gate::check('show purchase'))
                                            <td class="Action">
                                                <span>
                                                    <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                        <li class="me-2">
                                                            <a href="{{ route('admin.purchase.show', \Crypt::encrypt($purchase->id)) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Show') }}">
                                                                <i class="link-icon" data-feather="eye"></i>
                                                            </a>
                                                        </li>
                                                        <li class="me-2">
                                                            <a href="{{ route('admin.purchase.edit', \Crypt::encrypt($purchase->id)) }}"
                                                                data-bs-toggle="tooltip" title="Edit">
                                                                <i class="link-icon" data-feather="edit"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="deleteBtn" href="#"
                                                                data-href="{{ route('admin.purchase.destroy', $purchase->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                                <i class="link-icon" data-feather="delete"></i>
                                                            </a>
                                                        </li>
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
