@extends('layouts.master')
@section('title')
    {{ __('Farmer Guarantor') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Farmer Guarantor') }}</li>
        </ol>
        <div class="float-end">
            @can('create-farmer_guarantor')
            <a href="{{ route('admin.farmer.guarantor.create') }}" class="btn btn-sm btn-primary">
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Farmer Name') }}</th>
                                    <th>{{ __('Registration No.') }}</th>
                                    <th>{{ __('Age') }}</th>
                                    <th>{{ __('Father Name') }}</th>
                                    <th>{{ __('Post Office') }}</th>
                                    <th>{{ __('Police Station') }}</th>
                                    <th>{{ __('District') }}</th>
                                    <th>{{ __('Block') }}</th>
                                    <th>{{ __('Village') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($guarantors as $guarantor)
                                    <tr class="font-style">
                                        <td>{{ $guarantor->naming->name }}</td>
                                        <td>{{ $guarantor->farming->name }}</td>
                                        <td>{{ $guarantor->registration_number }}</td>
                                        <td>{{ $guarantor->age }}</td>
                                        <td>{{ $guarantor->father_name }}</td>
                                        <td>{{ $guarantor->post_office }}</td>
                                        <td>{{ $guarantor->police_station }}</td>
                                        <td>{{ $guarantor->district->name }}</td>
                                        <td>{{ $guarantor->block->name }}</td>
                                        <td>{{ $guarantor->village->name }}</td>

                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                @can('edit-farmer_guarantor')
                                                    <li class="me-2">
                                                        <a href="{{ route('admin.farmer.guarantor.edit', $guarantor->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                            <i class="link-icon" data-feather="edit"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('delete-farmer_guarantor')
                                                    <li>
                                                        <a class="deleteBtn"
                                                            data-href="{{ route('admin.farmer.guarantor.destroy', $guarantor->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}">
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
