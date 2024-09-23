@extends('layouts.master')
@section('title','Salary Group')
@section('sub_page','Lists')
@section('page')
    <a href="{{ route('admin.salary-groups.index')}}">
        Salary Group
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
                            @can('add-salary_group')
                                <a class="btn btn-success"
                                   href="{{ route('admin.salary-groups.create')}}">
                                    <i class="link-icon" data-feather="plus"></i> Add Salary Group
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Salary Component</th>
                                    <th class="text-center">Is Active</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    @forelse($salaryGroupLists as $key => $value)
                                        <td>{{++$key}}</td>
                                        <td>
                                            {{ucfirst($value->name)}}<br>
                                            <small>Employee Count : {{($value->group_employees_count)}}</small>
                                        </td>
                                        <td>
                                            <ul>
                                                @foreach($value?->salaryComponents as $key => $componentValue)
                                                    <li>{{ucwords($componentValue?->name)}}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="text-center">
                                            <label class="switch">
                                                <input class="toggleStatus" href="{{route('admin.salary-groups.toggle-status',$value->id)}}"
                                                       type="checkbox"{{($value->is_active) == 1 ?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                @can('edit-salary_group')
                                                    <li class="me-2">
                                                        <a href="{{route('admin.salary-groups.edit',$value->id)}}"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                            <i class="link-icon" data-feather="edit"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('delete-salary_group')
                                                    <li>
                                                        <a class="delete" href="#"
                                                           data-href="{{route('admin.salary-groups.delete',$value->id)}}"
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
                </div>
            </div>
        </div>

    </section>
@endsection

@section('scripts')
    @include('admin.payrollSetting.salaryGroup.common.scripts')
@endsection






