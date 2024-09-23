@extends('layouts.master')

@section('title', 'Employees')

@section('action', 'Lists')

@section('button')
    @can('create-employees')
        <div class="create-index float-end">
            <a href="{{ route('admin.users.create') }}">
                <button class="btn btn-primary">
                    <i class="link-icon" data-feather="plus"></i>Add Employee
                </button>
            </a>

            <a href="{{ route('admin.employees.import-csv.show') }}">
                <button class="btn btn-success">
                    <i class="link-icon"></i>Import Employees
                </button>
            </a>
        </div>
    @endcan
@endsection

@section('main-content')

    <section class="content">
        @include('admin.section.flash_message')

        @include('admin.users.common.breadcrumb')

        <div class="search-box p-4 bg-white rounded mb-3 box-shadow pb-2">
            <form class="forms-sample" action="{{ route('admin.users.index') }}" method="get">
                <h5>Employee Lists</h5>
                <div class="row align-items-center mt-4">

                    <div class="col-lg-4 col-md-6 mb-3">
                        <select class="form-control" id="branch" name="branch_id">
                            <option selected disabled>Select Branch</option>
                            @foreach ($branches as $branch)
                                <option {{ $filterParameters['branch_id'] == $branch->id ? 'selected' : '' }}
                                    value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-3">
                        <select class="form-control" id="department" name="department_id">
                            <option selected disabled>Select Department</option>
                        </select>
                    </div>


                    <div class="col-lg-4 col-md-6 mb-3">
                        <input type="text" placeholder="Employee name" id="employeeName" name="employee_name"
                            value="{{ $filterParameters['employee_name'] }}" class="form-control">
                    </div>

                    <div class="col-lg-4 col-md-6 mb-3">
                        <input type="text" placeholder="Employee email " id="email" name="email"
                            value="{{ $filterParameters['email'] }}" class="form-control">
                    </div>

                    <div class="col-lg-4 col-md-6 mb-3">
                        <input type="number" placeholder="Employee phone number " id="phone" name="phone"
                            value="{{ $filterParameters['phone'] }}" class="form-control">
                    </div>

                    <div class="col-lg-2 col-md-6 mb-3">
                        <button type="submit" class="btn btn-block btn-primary form-control">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                @can('show-employees')
                                    <th>#</th>
                                @endcan
                                <th>Full Name</th>
                                <th>Address</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Designation</th>
                                <th class="text-center">Department</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Shift</th>
                                <th class="text-center">WorkPlace</th>
                                <th class="text-center">Is Active</th>
                                @canany(['edit-employees', 'delete-employees'])
                                    <th class="text-center">Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            <tr>

                                <?php
                                $changeColor = [
                                    0 => 'success',
                                    1 => 'primary',
                                ];
                                ?>

                                @forelse($users as $key => $value)
                            <tr>
                                @can('show-employees')
                                    <td>
                                        <a href="{{ route('admin.users.show', $value->id) }}" id="showOfficeTimeDetail">
                                            <i class="link-icon" data-feather="eye"></i>
                                        </a>
                                    </td>
                                @endcan
                                <td>
                                    <p>{{ ucfirst($value->name) }}</p>
                                    <small class="text-muted">({{ ucfirst($value?->role?->name) ?? 'N/A' }})</small>
                                </td>
                                <td>{{ ucfirst($value->address) }}</td>
                                <td class="text-center">{{ $value->email }}</td>
                                <td class="text-center">{{ $value->post ? ucfirst($value->post->post_name) : 'N/A' }}</td>
                                <td class="text-center">
                                    {{ $value->department ? ucfirst($value->department->dept_name) : 'N/A' }}</td>
                                <td class="text-center">{{ $value->role ? ucfirst($value->role->name) : 'N/A' }}</td>
                                <td class="text-center">
                                    {{ $value->officeTime ? ucfirst($value->officeTime->shift) : 'N/A' }}</td>


                                <td class="text-center">
                                    <a class="changeWorkPlace btn btn-{{ $changeColor[$value->workspace_type] }} btn-xs"
                                        data-href="{{ route('admin.users.change-workspace', $value->id) }}"
                                        title="Change workspace">
                                        {{ $value->workspace_type == \App\Models\User::FIELD ? 'Field' : 'Office' }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <label class="switch">
                                        <input class="toggleStatus"
                                            href="{{ route('admin.users.toggle-status', $value->id) }}" type="checkbox"
                                            {{ $value->is_active == 1 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </td>

                                @canany(['edit-employees', 'delete-employees'])
                                    <td class="text-center">
                                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            title="More Action">
                                        </a>

                                        <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                                            <ul class="list-unstyled p-1 mb-0">
                                                @can('edit-employees')
                                                    <li class="dropdown-item py-2">
                                                        <a href="{{ route('admin.users.edit', $value->id) }}">
                                                            <button class="btn btn-primary btn-xs">Edit Detail</button>
                                                        </a>
                                                    </li>
                                                @endcan

                                                @can('delete-employees')
                                                    <li class="dropdown-item py-2">
                                                        <a class="deleteEmployee"
                                                            data-href="{{ route('admin.users.delete', $value->id) }}">
                                                            <button class="btn btn-primary btn-xs">Delete User</button>
                                                        </a>
                                                    </li>
                                                @endcan
                                                <li class="dropdown-item py-2">
                                                    <a class="changePassword"
                                                        data-href="{{ route('admin.users.change-password', $value->id) }}">
                                                        <button class="btn btn-primary btn-xs">Change Password
                                                        </button>
                                                    </a>
                                                </li>
                                                <li class="dropdown-item py-2">
                                                    <a class="forceLogOut"
                                                        data-href="{{ route('admin.users.force-logout', $value->id) }}">
                                                        <button class="btn btn-primary btn-xs">Force Log Out
                                                        </button>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
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


        <div class="dataTables_paginate mt-3">
            {{ $users->appends($_GET)->links() }}
        </div>

    </section>
    @include('admin.users.common.password')
@endsection

@section('scripts')
    @include('admin.users.common.scripts')
@endsection
