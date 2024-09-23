@extends('layouts.master')
@section('title')
    {{ __('Warehouse Stock Details') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Warehouse Stock Details') }}</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($warehouse as $warehouses)
                                    <tr class="font-style">
                                        @if (!empty($warehouses->product()))
                                            <td>{{ !empty($warehouses->product()) ? $warehouses->product()->name : '' }}</td>
                                            <td>{{ $warehouses->quantity }}</td>
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
