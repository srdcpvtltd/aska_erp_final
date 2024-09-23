@extends('layouts.master')
@section('title', 'Asset Types')
@section('action', 'Assignments')
@section('button')
    <a href="{{ route('admin.asset_assignment.create') }}">
        <button class="btn btn-primary">
            <i class="link-icon" data-feather="plus"></i>Assign to User
        </button>
    </a>
@endsection

@section('main-content')

    <section class="content">
        @include('admin.section.flash_message')
        @include('admin.assetManagement.types.common.breadcrumb')
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th class="text-center">Asset</th>
                                <th class="text-center">Assigned Date</th>
                                <th class="text-center">Returned</th>
                                <th class="text-center">Damaged</th>
                                @canany(['edit-asset_management', 'delete-asset_management'])
                                    <th class="text-center">Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @forelse($assetLists as $key => $value)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ ucfirst($value->assign_to) }}</td>
                                <td class="text-center">
                                    {{ $value->asset_name }}
                                </td>
                                <td class="text-center">
                                    {{ $value->assign_date }}
                                </td>
                                @if ($value->returned != null)
                                    <td class="text-center">
                                        Yes
                                    </td>
                                @else
                                    <td class="text-center">
                                        No
                                    </td>
                                @endif
                                @if ($value->damaged != null || $value->damaged == 1)
                                    <td class="text-center">
                                        Yes
                                    </td>
                                @else
                                    <td class="text-center">
                                        No
                                    </td>
                                @endif

                                <td class="text-center">
                                    <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                        @can('edit-asset_management')
                                            <li class="me-2">
                                                <a href="{{ route('admin.asset-types.edit', $value->id) }}" title="Edit Detail">
                                                    <i class="link-icon" data-feather="edit"></i>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('delete-asset_management')
                                            <li>
                                                <a class="delete" data-href="{{ route('admin.asset-types.delete', $value->id) }}"
                                                    title="Delete">
                                                    <i class="link-icon" data-feather="delete"></i>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </td>

                            @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-center"><b>No records found!</b></p>
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('admin.assetManagement.types.common.scripts')
@endsection
