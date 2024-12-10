@extends('layouts.master')
@section('title')
    {{ __('Manage Bills') }}
@endsection
@section('scripts')
    <script>
        $('.copy_link').click(function(e) {
            e.preventDefault();
            var copyText = $(this).attr('href');

            document.addEventListener('copy', function(e) {
                e.clipboardData.setData('text/plain', copyText);
                e.preventDefault();
            }, true);

            document.execCommand('copy');
            show_toastr('success', 'Url copied to clipboard', 'success');
        });
    </script>
@endsection
@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Bill') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.bill.export') }}" class="btn btn-success" data-bs-toggle="tooltip"
                title="{{ __('Export') }}">
                Export
            </a>

            @can('create-bill')
                <a href="{{ route('admin.bill.create', 0) }}" class="btn btn-primary" data-bs-toggle="tooltip"
                    title="{{ __('Create') }}">
                    Add
                </a>
            @endcan
        </div>
    </nav>
    <div class="row">
        <div class="col-sm-12">
            <div class=" mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['admin.bill.index'], 'method' => 'GET', 'id' => 'frm_submit']) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">
                                    <div class="col-3"></div>
                                    <div class="col-3"></div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 month">
                                        <div class="btn-box">
                                            {{ Form::label('bill_date', __('Bill Date'), ['class' => 'form-label']) }}
                                            {{ Form::text('bill_date', isset($_GET['bill_date']) ? $_GET['bill_date'] : null, ['class' => 'form-control month-btn', 'id' => 'pc-daterangepicker-1', 'readonly']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                                            {{ Form::select('status', ['' => 'Select Status'] + $status, isset($_GET['status']) ? $_GET['status'] : '', ['class' => 'form-control select']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <a href="#" class="btn btn-primary"
                                            onclick="document.getElementById('frm_submit').submit(); return false;"
                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon">Search</span>
                                        </a>
                                        <a href="{{ route('admin.bill.index') }}" class="btn btn-danger "
                                            data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                            data-original-title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon">Reset</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="data_table table datatable">
                            <thead>
                                <tr>
                                    <th> {{ __('Bill') }}</th>
                                    <th> {{ __('Category') }}</th>
                                    <th> {{ __('Bill Date') }}</th>
                                    <th> {{ __('Due Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @if (Gate::check('edit bill') || Gate::check('delete bill') || Gate::check('show bill'))
                                        <th width="10%"> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bills as $bill)
                                    <tr>
                                        <td class="Id">
                                            <a href="{{ route('admin.bill.show', \Crypt::encrypt($bill->id)) }}"
                                                class="btn btn-outline-primary">{{ AUth::user()->billNumberFormat($bill->bill_id) }}</a>
                                        </td>
                                        <td>{{ !empty($bill->category) ? $bill->category->name : '' }}</td>
                                        <td>{{ Auth::user()->dateFormat($bill->bill_date) }}</td>
                                        <td>{{ Auth::user()->dateFormat($bill->due_date) }}</td>
                                        <td>
                                            @if ($bill->status == 0)
                                                <span
                                                    class="status_badge badge bg-secondary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 1)
                                                <span
                                                    class="status_badge badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 2)
                                                <span
                                                    class="status_badge badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 3)
                                                <span
                                                    class="status_badge badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 4)
                                                <span
                                                    class="status_badge badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$bill->status]) }}</span>
                                            @endif
                                        </td>
                                        @if (Gate::check('edit bill') || Gate::check('delete bill') || Gate::check('show bill'))
                                            <td class="Action">
                                                <span>
                                                    <div class="action-btn bg-success ms-2">
                                                        {!! Form::open([
                                                            'method' => 'get',
                                                            'route' => ['admin.bill.duplicate', $bill->id],
                                                            'id' => 'duplicate-form-' . $bill->id,
                                                        ]) !!}

                                                        <a href="#" class="mx-3 btn align-items-center bs-pass-para "
                                                            data-bs-toggle="tooltip"
                                                            data-original-title="{{ __('Duplicate') }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Duplicate Bill') }}"
                                                            data-original-title="{{ __('Delete') }}"
                                                            data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"
                                                            data-confirm-yes="document.getElementById('duplicate-form-{{ $bill->id }}').submit();">
                                                            Duplicate
                                                            {!! Form::close() !!}
                                                        </a>
                                                    </div>
                                                    <div class="action-btn bg-info ms-2">
                                                        <a href="{{ route('admin.bill.show', \Crypt::encrypt($bill->id)) }}"
                                                            class="mx-3 btn align-items-center" data-bs-toggle="tooltip"
                                                            title="{{ __('Show') }}"
                                                            data-original-title="{{ __('Detail') }}">
                                                            Details
                                                        </a>
                                                    </div>
                                                    <div class="action-btn bg-primary ms-2">
                                                        <a href="{{ route('admin.bill.edit', \Crypt::encrypt($bill->id)) }}"
                                                            class="mx-3 btn align-items-center" data-bs-toggle="tooltip"
                                                            title="Edit" data-original-title="{{ __('Edit') }}">
                                                            Edit
                                                        </a>
                                                    </div>
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a class="mx-3 btn align-items-center deleteBtn" href="#"
                                                            data-href="{{ route('admin.bill.destroy', $bill->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                            Delete
                                                        </a>
                                                    </div>
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
