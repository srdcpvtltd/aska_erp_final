@extends('layouts.master')
@section('title')
    {{ __('Manage Bank Branch') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Bank Branch') }}</li>
        </ol>
        <div class="float-end">
            @can('create-bank_branch')
                <a href="{{ route('admin.bank_branches.create') }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                    title="{{ __('Add') }}" data-title="{{ __('Add New Bank') }}" class="btn btn-primary">
                    Add
                </a>
            @endcan
        </div>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Sl No.') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Bank Name') }}</th>
                                    <th>{{ __('Ifsc Code') }}</th>
                                    <th> {{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($bank_branch as $key => $data)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ ($data->bank_id != 0) ? $data->bank->name : '-' }}</td>
                                        <td>{{ $data->ifsc_code }}</td>
                                        @if (Gate::check('edit-bank_branch') || Gate::check('delete-bank_branch'))
                                            <td class="Action">
                                                <span>
                                                    <ul class="d-flex list-unstyled mb-0">
                                                        @can('edit-bank_branch')
                                                            <li class="me-2">
                                                                <a href="{{ route('admin.bank_branches.edit', $data->id) }}"
                                                                    title="{{ __('Edit') }}">
                                                                    <i class="link-icon" data-feather="edit"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete-bank_branch')
                                                            <li>
                                                                <a class="deleteBtn" href="#"
                                                                    data-href="{{ route('admin.bank_branches.destroy', $data->id) }}"
                                                                    title="{{ __('Delete') }}">
                                                                    <i class="link-icon" data-feather="delete"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
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
