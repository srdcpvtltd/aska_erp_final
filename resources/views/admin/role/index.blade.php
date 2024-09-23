@extends('layouts.master')

@section('title', 'Roles')

@section('action', 'Role Listing')

@section('button')
    @can('create-role')
        <a href="{{ route('admin.roles.create') }}">
            <button class="btn btn-primary">
                <i class="link-icon" data-feather="plus"></i>Add Role
            </button>
        </a>
    @endcan
@endsection


@section('main-content')

    <section class="content">
        @include('admin.section.flash_message')

        @include('admin.role.common.breadcrumb')

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Role</th>
                                <th>Created At</th>
                                @canany(['edit-role', 'delete-role'])
                                    <th>Action</th>
                                @endcanany
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $key => $value)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ ucfirst($value->name) }}</td>
                                    <td>{{ date('d-M-Y', strtotime($value->created_at)) }}</td>
                                    @canany(['edit-role', 'delete-role'])
                                        @if ($value->slug !== 'admin')
                                            <td>
                                                <ul class="d-flex list-unstyled mb-0">
                                                    @can('edit-role')
                                                        <li class="me-2">
                                                            <a href="{{ route('admin.roles.edit', $value->id) }}"
                                                                title="Edit Role Detail">
                                                                <i class="link-icon" data-feather="edit"></i>
                                                            </a>
                                                        </li>
                                                    @endcan

                                                    @can('delete-role')
                                                        <li>
                                                            <a class="deleteRole"
                                                                data-href="{{ route('admin.roles.delete', $value->id) }}"
                                                                title="Delete Role">
                                                                <i class="link-icon" data-feather="delete"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    <li>
                                                        <span class="m-lg-3">
                                                            <a href="{{ route('admin.roles.permission', $value->id) }}">
                                                                <button class="btn btn-xs btn-primary ">
                                                                    Assign Permissions
                                                                </button>
                                                            </a>
                                                        </span>
                                                    </li>
                                                </ul>
                                            </td>
                                        @endif
                                    @endcanany
                                </tr>
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
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.toggleStatus').change(function(event) {
                event.preventDefault();
                var status = $(this).prop('checked') === true ? 1 : 0;
                var href = $(this).attr('href');
                Swal.fire({
                    title: 'Are you sure you want to change status ?',
                    showDenyButton: true,
                    confirmButtonText: `Yes`,
                    denyButtonText: `No`,
                    padding: '10px 50px 10px 50px',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    } else if (result.isDenied) {
                        (status === 0) ? $(this).prop('checked', true): $(this).prop('checked',
                            false)
                    }
                })
            })

            $('.deleteRole').click(function(event) {
                event.preventDefault();
                let href = $(this).data('href');
                Swal.fire({
                    title: 'Are you sure you want to Delete Role ?',
                    showDenyButton: true,
                    confirmButtonText: `Yes`,
                    denyButtonText: `No`,
                    padding: '10px 50px 10px 50px',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                })
            })
        });
    </script>
@endsection
