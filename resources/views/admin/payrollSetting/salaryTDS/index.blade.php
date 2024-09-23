@extends('layouts.master')
@section('title', 'Salary TDS')
@section('sub_page', 'Lists')
@section('page')
    <a href="{{ route('admin.salary-tds.index') }}">
        Salary TDS
    </a>
@endsection

@section('main-content')
    <section class="content">
        @include('admin.section.flash_message')

        @include('admin.payrollSetting.common.breadcrumb')
        <div class="row">
            <div class="col-lg-2">
                @include('admin.payrollSetting.common.setting_menu')
            </div>

            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <div class="justify-content-end">
                            @can('add-salary_tds')
                                <a class="btn btn-success" href="{{ route('admin.salary-tds.create') }}">
                                    <i class="link-icon" data-feather="plus"></i> Add Salary TDS
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-header bg-transparent">
                            <h4 class="text-center">Salary TDS Detail For Single</h4>
                        </div>
                        <div class="table-responsive ">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Annual Salary From</th>
                                        <th>Annual Salary To</th>
                                        <th class="text-center">TDS(%)</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($singleSalaryTDS as $key => $value)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ number_format($value->annual_salary_from) }}</td>
                                            <td>{{ number_format($value->annual_salary_to) }}</td>
                                            <td class="text-center">{{ $value->tds_in_percent }}</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input class="toggleStatus"
                                                        href="{{ route('admin.salary-tds.toggle-status', $value->id) }}"
                                                        type="checkbox" {{ $value->status == 1 ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                    @can('edit-salary_tds')
                                                        <li class="me-2">
                                                            <a href="{{ route('admin.salary-tds.edit', $value->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                                <i class="link-icon" data-feather="edit"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('delete-salary_tds')
                                                        <li>
                                                            <a class="delete" href="#"
                                                                data-href="{{ route('admin.salary-tds.delete', $value->id) }}"
                                                                title="Delete">
                                                                <i class="link-icon" data-feather="delete"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </td>
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

                    <div class="card-body">
                        <div class="card-header bg-transparent">
                            <h4 class="text-center">Salary TDS Detail For Married</h4>
                        </div>
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Annual Salary From</th>
                                        <th>Annual Salary To</th>
                                        <th class="text-center">TDS(%)</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @forelse($marriedSalaryTDS as $key => $value)
                                            <td>{{ ++$key }}</td>
                                            <td>{{ number_format($value->annual_salary_from) }}</td>
                                            <td>{{ number_format($value->annual_salary_to) }}</td>
                                            <td class="text-center">{{ $value->tds_in_percent }}</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input class="toggleStatus"
                                                        href="{{ route('admin.salary-tds.toggle-status', $value->id) }}"
                                                        type="checkbox"{{ $value->status == 1 ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <ul class="d-flex list-unstyled mb-0">
                                                    <li class="me-2">
                                                        <a href="{{ route('admin.salary-tds.edit', $value->id) }}"
                                                            title="Edit Detail">
                                                            <i class="link-icon" data-feather="edit"></i>
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="delete" href="#"
                                                            data-href="{{ route('admin.salary-tds.delete', $value->id) }}"
                                                            title="Delete">
                                                            <i class="link-icon" data-feather="delete"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
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

            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('admin.payrollSetting.salaryTDS.common.scripts')
@endsection
