@extends('layouts.master')
@section('title')
    {{ __('District') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('District') }}</li>
        </ol>
        <div class="float-end">
            @can('create-district')
                <a href="{{ route('admin.location.district.create') }}" title="{{ __('Add') }}" class="btn btn-primary">
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
                                    <th>{{ __('State') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($districts as $key=>$district)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $district->name }}</td>
                                        <td>{{ @$district->state->name }}</td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0">
                                                @can('edit-district')
                                                <li class="me-2">
                                                    <a href="{{ route('admin.location.district.edit', $district->id) }}"
                                                        title="{{ __('Edit') }}">
                                                        <i class="link-icon" data-feather="edit"></i>
                                                    </a>
                                                </li>
                                                @endcan
                                                @can('delete-district')
                                                <li>
                                                    <a class="deleteBtn" href="#"
                                                        data-href="{{ route('admin.location.district.destroy', $district->id) }}"
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
