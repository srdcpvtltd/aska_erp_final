@extends('layouts.master')
@section('title')
    {{ __('Village') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Village') }}</li>
        </ol>
        <div class="float-end">
            @can('create-village')
                <a href="{{ route('admin.location.village.create') }}" class="btn btn-primary">
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
                                    <th>{{ __('Sl No.') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Gram Panchyat') }}</th>
                                    <th>{{ __('Zone') }}</th>
                                    <th>{{ __('Center') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($villages as $key => $village)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $village->name }}</td>
                                        <td>{{ $village->gram_panchyat->name }}</td>
                                        <td>{{ ($village->zone_id != '') ? $village->zone->name:'-' }}</td>
                                        <td>{{ ($village->center_id != '') ? $village->center->name:'-' }}</td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0">
                                                <li class="me-2">
                                                    <a href="{{ route('admin.location.village.edit', $village->id) }}"
                                                        title="{{ __('Edit') }}">
                                                        <i class="link-icon" data-feather="edit"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="deleteBtn" href="#"
                                                        data-href="{{ route('admin.location.village.destroy', $village->id) }}"
                                                        title="{{ __('Delete') }}">
                                                        <i class="link-icon" data-feather="delete"></i>
                                                    </a>
                                                </li>
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
