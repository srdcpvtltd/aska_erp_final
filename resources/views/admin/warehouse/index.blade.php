@extends('layouts.master')
@section('title')
    {{ __('Warehouse') }}
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
            <li class="breadcrumb-item">{{ __('Warehouse') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.warehouse.create') }}" data-size="lg" data-url="{{ route('admin.warehouse.create') }}"
                data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                data-title="{{ __('Create Warehouse') }}" class="btn btn-sm btn-primary">
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('City') }}</th>
                                    <th>{{ __('Zip Code') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($warehouses as $warehouse)
                                    <tr class="font-style">
                                        <td>{{ $warehouse->name }}</td>
                                        <td>{{ $warehouse->address }}</td>
                                        <td>{{ $warehouse->city }}</td>
                                        <td>{{ $warehouse->city_zip }}</td>

                                        {{-- @if (Gate::check('show warehouse') || Gate::check('edit warehouse') || Gate::check('delete warehouse')) --}}
                                        <td class="Action">
                                            @can('show-warehouse')
                                            <div class="action-btn bg-warning">
                                                <a href="{{ route('admin.warehouse.show', $warehouse->id) }}"
                                                    class="btn btn-sm d-inline-flex align-items-center"
                                                    data-bs-toggle="tooltip" title="{{ __('View') }}"><i
                                                        class="link-icon" data-feather="eye"></i></a>
                                            </div>
                                            @endcan
                                            @can('edit-warehouse')
                                            <div class="action-btn bg-info">
                                                <a href="{{ route('admin.warehouse.edit', $warehouse->id) }}"
                                                    class="btn btn-sm  align-items-center" data-bs-toggle="tooltip"
                                                    title="{{ __('Edit') }}" data-title="{{ __('Edit Warehouse') }}">
                                                    <i class="link-icon" data-feather="edit"></i>
                                                </a>
                                            </div>
                                            @endcan
                                            @can('delete-warehouse')
                                            <div class="action-btn bg-danger">
                                                {{ Form::open(['route' => ['admin.warehouse.destroy', $warehouse->id],'id' => 'delete-form-' . $warehouse->id, 'class' => 'w-100 delete_btn']) }}
                                                <a type="submit"
                                                    class="btn btn-sm align-items-center bs-pass-para alertButton" data-id="{{ $warehouse->id }}"
                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                        class="link-icon" data-feather="trash"></i></a>
                                                {{ Form::close() }}
                                            </div>
                                            @endcan
                                        </td>
                                        {{-- @endif --}}
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
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.alertButton').forEach(function(a) {
            a.addEventListener('click', function(event) {
                const formId = 'delete-form-' + this.getAttribute('data-id');
                const form = document.getElementById(formId);
                console.log(formId);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
