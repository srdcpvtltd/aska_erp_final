@extends('layouts.master')
@section('title')
    {{ __('Zone') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Zone') }}</li>
        </ol>
        <div class="float-end">
            @can('create-zone')
                <a href="{{ route('admin.location.zone.create') }}" class="btn btn-primary">
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
                                    <th>{{ __('Zone Number') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($zones as $key => $zone)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $zone->name }}</td>
                                        <td>{{ $zone->zone_number }}</td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0">
                                                @can('edit-zone')
                                                <li class="me-2">
                                                    <a href="{{ route('admin.location.zone.edit', $zone->id) }}"
                                                        title="{{ __('Edit') }}">
                                                        <i class="link-icon" data-feather="edit"></i>
                                                    </a>
                                                </li>
                                                @endcan
                                                @can('delete-zone')
                                                <li>
                                                    <a class="deleteBtn" href="#"
                                                        data-href="{{ route('admin.location.zone.destroy', $zone->id) }}"
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
