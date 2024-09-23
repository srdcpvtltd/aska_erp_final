@extends('layouts.master')
@section('title')
    {{ __('Seed Category') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Seed Category') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.farmer.seed_category.create') }}" data-bs-toggle="tooltip" title="{{ __('Add') }}" class="btn btn-primary">
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
                                    <th>{{ __('Sl No.') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($seed_categories as $key=>$seed_category)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $seed_category->name }}</td>
                                        <td>{{ $seed_category->category }}</td>
                                        <td>{{ $seed_category->type }}</td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0">
                                                <li class="me-2">
                                                    <a href="{{ route('admin.farmer.seed_category.edit', $seed_category->id) }}"
                                                        data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                        <i class="link-icon" data-feather="edit"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="" data-href="{{ route('admin.farmer.seed_category.destroy', $seed_category->id) }}"
                                                        class="deleteBtn" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
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
