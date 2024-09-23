@extends('layouts.master')
@section('title')
    {{ __('Center') }}
@endsection
@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Center') }}</li>
        </ol>
        <div class="float-end">
            @can('create-center')
                <a href="{{ route('admin.location.center.create') }}" class="btn btn-primary">
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
                                    <th>{{ __('SL No.') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Center Number') }}</th>
                                    <th>{{ __('Zone') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($centers as $key => $center)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $center->name }}</td>
                                        <td>{{ $center->center_number }}</td>
                                        <td>{{ @$center->zone->name }}</td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0">
                                                @can('edit-center')
                                                <li class="me-2">
                                                    <a href="{{ route('admin.location.center.edit', $center->id) }}"
                                                        title="{{ __('Edit') }}">
                                                        <i class="link-icon" data-feather="edit"></i>
                                                    </a>
                                                </li>
                                                @endcan
                                                @can('delete-center')
                                                <li>
                                                    <a class="deleteBtn" href="#"
                                                        data-href="{{ route('admin.location.center.destroy', $center->id) }}"
                                                        title="{{ __('Delete') }}">
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
