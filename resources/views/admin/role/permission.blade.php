@extends('layouts.master')

@section('title', 'Assign Permission Setting')

@section('action', 'Role Permission Detail')

@section('button')
    <a href="{{ route('admin.roles.index') }}" class="btn btn-primary btn-sm"> <i class="link-icon"
            data-feather="arrow-left"></i> Back </a>
@endsection

@section('main-content')
<style>
    .custom-control-label{
        margin-left: 10px;
        margin-top: 2px;
    }
</style>
    <section class="content">
        @include('admin.section.flash_message')

        @include('admin.role.common.breadcrumb')

        <div class="col-md-12 m-auto">
            <div class="card">
                <div class="card-header"><strong>{{ __('Add/Edit Permissions to ') }} {{ $role->name }} {{ __(' Role') }}
                    </strong> </div>
                <div class="card-body">
                    {!! Form::model($role, ['method' => 'POST', 'route' => ['admin.role.assign-permissions', $role->id]]) !!}

                    @csrf
                    <div>
                        <table class="table table-flush permission-table">
                            <thead class="thead-light">
                                <tr>
                                    <th width="200px">{{ __('Module') }}</th>
                                    <th>{{ __('Permissions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($moduals as $row)
                                    <tr>
                                        <td> {{ __(ucfirst($row)) }}</td>
                                        <td>
                                            <div class="row">
                                                <?php $default_permissions = ['manage', 'create', 'edit', 'delete', 'show']; ?>
                                                @foreach ($default_permissions as $permission)
                                                    @if (in_array($permission . '-' . $row, $allpermissions))
                                                        @php($key = array_search($permission . '-' . $row, $allpermissions))
                                                        <div class="col-3 custom-control custom-checkbox">
                                                            {{ Form::checkbox('permissions[]', $permission . '-' . $row, in_array($permission . '-' . $row, $permissions), ['class' => 'custom-control-input', 'id' => 'permission_' . $key]) }}
                                                            {{ Form::label('permission_' . $key, ucfirst($permission), ['class' => 'custom-control-label']) }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <?php $modules = []; ?>
                                @foreach ($modules as $module)
                                    <?php $s_name = $module; ?>
                                    <tr>
                                        <td>
                                            {{ __(ucfirst($module)) }}
                                        </td>
                                        <td>
                                            <div class="row">
                                                @if (in_array('manage-' . $s_name, $allpermissions))
                                                    @php($key = array_search('manage-' . $s_name, $allpermissions))
                                                    <div class="col-3 custom-control custom-checkbox">
                                                        {{ Form::checkbox('permissions[]', $key, in_array($key, $permissions), ['class' => 'custom-control-input', 'id' => 'permission_' . $key]) }}
                                                        {{ Form::label('permission_' . $key, __('Manage'), ['class' => 'custom-control-label']) }}
                                                    </div>
                                                @endif
                                                @if (in_array('create-' . $module, $allpermissions))
                                                    @php($key = array_search('create-' . $module, $allpermissions))
                                                    <div class="col-3 custom-control custom-checkbox">
                                                        {{ Form::checkbox('permissions[]', $key, in_array($key, $permissions), ['class' => 'custom-control-input', 'id' => 'permission_' . $key]) }}
                                                        {{ Form::label('permission_' . $key, __('Create'), ['class' => 'custom-control-label']) }}
                                                    </div>
                                                @endif
                                                @if (in_array('edit-' . $module, $allpermissions))
                                                    @php($key = array_search('edit-' . $module, $allpermissions))
                                                    <div class="col-3 custom-control custom-checkbox">
                                                        {{ Form::checkbox('permissions[]', $key, in_array($key, $permissions), ['class' => 'custom-control-input', 'id' => 'permission_' . $key]) }}
                                                        {{ Form::label('permission_' . $key, __('Edit'), ['class' => 'custom-control-label']) }}
                                                    </div>
                                                @endif
                                                @if (in_array('delete-' . $module, $allpermissions))
                                                    @php($key = array_search('delete-' . $module, $allpermissions))
                                                    <div class="col-3 custom-control custom-checkbox">
                                                        {{ Form::checkbox('permissions[]', $key, in_array($key, $permissions), ['class' => 'custom-control-input', 'id' => 'permission_' . $key]) }}
                                                        {{ Form::label('permission_' . $key, __('Delete'), ['class' => 'custom-control-label']) }}
                                                    </div>
                                                @endif
                                                @if (in_array('view-' . $module, $allpermissions))
                                                    @php($key = array_search('view-' . $module, $allpermissions))
                                                    <div class="col-3 custom-control custom-checkbox">
                                                        {{ Form::checkbox('permissions[]', $key, in_array($key, $permissions), ['class' => 'custom-control-input', 'id' => 'permission_' . $key]) }}
                                                        {{ Form::label('permission_' . $key, __('show'), ['class' => 'custom-control-label']) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                            </tbody>
                            @endforeach
                        </table>
                        <div class="col-sm-12 mx-auto">
                            {{ Form::submit(__('Update Permission'), ['class' => 'btn btn-primary ']) }}
                            <a class="btn btn-secondary" href="{{ route('admin.roles.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>
                    </form>
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

            $(function() {
                $('.js-check-all').on('click', function() {
                    let isChecked = $(this).parent().parent().parent().siblings().children('.item')
                        .children().find('.module_checkbox').prop('checked');
                    if (isChecked) {
                        $(this).parent().parent().parent().siblings().children('.item').children()
                            .find('.module_checkbox').prop('checked', false);
                    } else {
                        $(this).parent().parent().parent().siblings().children('.item').children()
                            .find('.module_checkbox').prop("checked", true);
                    }
                });
            });

            $('#web').click(function(e) {
                $('.web').slideToggle('slow');
            });

            $('#api').click(function(e) {
                $('.api').slideToggle('slow');
            });
        });
    </script>
@endsection
