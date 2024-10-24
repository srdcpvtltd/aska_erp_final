@extends('layouts.master')
@section('title')
    {{ __('Manage Invoices') }}
@endsection
@section('scripts')
    <script>
        function copyToClipboard(element) {

            var copyText = element.id;
            navigator.clipboard.writeText(copyText);
            show_toastr('success', 'Url copied to clipboard', 'success');
        }
    </script>
@endsection
@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Invoice') }}</li>
        </ol>
        <div class="float-end">
            <div class="float-end">
                {{--        <a class="btn btn-primary" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1" data-bs-toggle="tooltip" title="{{__('Filter')}}"> --}}
                {{--            <i class="ti ti-filter"></i> --}}
                {{--        </a> --}}

                <a href="{{ route('admin.invoice.export') }}" class="btn btn-primary" data-bs-toggle="tooltip"
                    title="{{ __('Export') }}">
                    <i class="ti ti-file-export"></i>
                </a>

                @can('create invoice')
                    <a href="{{ route('admin.invoice.create', 0) }}" class="btn btn-primary" data-bs-toggle="tooltip"
                        title="{{ __('Create') }}">
                        <i class="ti ti-plus"></i>
                    </a>
                @endcan
            </div>
        </div>
    </nav>
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['admin.invoice.index'], 'method' => 'GET', 'id' => 'customer_submit']) }}
                        <div class="row d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('issue_date', __('Issue Date'), ['class' => 'form-label']) }}
                                    {{ Form::date('issue_date', isset($_GET['issue_date']) ? $_GET['issue_date'] : '', ['class' => 'form-control month-btn', 'id' => 'pc-daterangepicker-1']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('customer', __('Customer'), ['class' => 'form-label']) }}
                                    {{ Form::select('customer', $customer, isset($_GET['customer']) ? $_GET['customer'] : '', ['class' => 'form-control select']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                                    {{ Form::select('status', ['' => 'Select Status'] + $status, isset($_GET['status']) ? $_GET['status'] : '', ['class' => 'form-control select']) }}
                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-primary"
                                    onclick="document.getElementById('customer_submit').submit(); return false;"
                                    data-toggle="tooltip" data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('admin.invoice.index') }}" class="btn btn-danger" data-toggle="tooltip"
                                    data-original-title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off"></i></span>
                                </a>
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
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th> {{ __('Invoice') }}</th>
                                    {{--                                @if (!\Auth::guard('customer')->check()) --}}
                                    {{--                                    <th>{{ __('Customer') }}</th> --}}
                                    {{--                                @endif --}}
                                    <th>{{ __('Issue Date') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Due Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                        <th>{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td class="Id">
                                            <a href="{{ route('admin.invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                                class="btn btn-outline-primary">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                        </td>
                                        <td>{{ Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                        <td>
                                            @if ($invoice->due_date < date('Y-m-d'))
                                                <p class="text-danger mt-3">
                                                    {{ \Auth::user()->dateFormat($invoice->due_date) }}</p>
                                            @else
                                                {{ \Auth::user()->dateFormat($invoice->due_date) }}
                                            @endif
                                        </td>
                                        <td>{{ \Auth::user()->priceFormat($invoice->getDue()) }}</td>
                                        <td>
                                            @if ($invoice->status == 0)
                                                <span
                                                    class="status_badge badge bg-secondary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 1)
                                                <span
                                                    class="status_badge badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 2)
                                                <span
                                                    class="status_badge badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 3)
                                                <span
                                                    class="status_badge badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 4)
                                                <span
                                                    class="status_badge badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @endif
                                        </td>
                                        @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                            <td class="Action">
                                                <span>
                                                    @php $invoiceID= Crypt::encrypt($invoice->id); @endphp

                                                    @can('copy invoice')
                                                        <div class="action-btn bg-warning ms-2">
                                                            <a href="#"
                                                                id="{{ route('admin.invoice.link.copy', [$invoiceID]) }}"
                                                                class="mx-3 btn align-items-center"
                                                                onclick="copyToClipboard(this)" data-bs-toggle="tooltip"
                                                                title="{{ __('Copy Invoice') }}"
                                                                data-original-title="{{ __('Copy Invoice') }}"><i
                                                                    class="ti ti-link text-white"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('duplicate invoice')
                                                        <div class="action-btn bg-success ms-2">
                                                            {!! Form::open([
                                                                'method' => 'get',
                                                                'route' => ['admin.invoice.duplicate', $invoice->id],
                                                                'id' => 'duplicate-form-' . $invoice->id,
                                                            ]) !!}

                                                            <a href="#"
                                                                class="mx-3 btn align-items-center bs-pass-para"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Duplicate') }}"
                                                                data-bs-toggle="tooltip" title="Duplicate Invoice"
                                                                data-original-title="{{ __('Delete') }}"
                                                                data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"
                                                                data-confirm-yes="document.getElementById('duplicate-form-{{ $invoice->id }}').submit();">
                                                                <i class="ti ti-copy text-white"></i>
                                                                {!! Form::open([
                                                                    'method' => 'get',
                                                                    'route' => ['admin.invoice.duplicate', $invoice->id],
                                                                    'id' => 'duplicate-form-' . $invoice->id,
                                                                ]) !!}
                                                                {!! Form::close() !!}
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('show invoice')
                                                        {{--                                                        @if (\Auth::guard('customer')->check()) --}}
                                                        {{--                                                            <div class="action-btn bg-info ms-2"> --}}
                                                        {{--                                                                    <a href="{{ route('customer.invoice.show', \Crypt::encrypt($invoice->id)) }}" --}}
                                                        {{--                                                                       class="mx-3 btn align-items-center" data-bs-toggle="tooltip" title="Show " --}}
                                                        {{--                                                                       data-original-title="{{ __('Detail') }}"> --}}
                                                        {{--                                                                        <i class="ti ti-eye text-white"></i> --}}
                                                        {{--                                                                    </a> --}}
                                                        {{--                                                                </div> --}}
                                                        {{--                                                        @else --}}
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="{{ route('admin.invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                                                class="mx-3 btn align-items-center"
                                                                data-bs-toggle="tooltip" title="Show "
                                                                data-original-title="{{ __('Detail') }}">
                                                                <i class="ti ti-eye text-white"></i>
                                                            </a>
                                                        </div>
                                                        {{--                                                        @endif --}}
                                                    @endcan
                                                    @can('edit invoice')
                                                        <div class="action-btn bg-primary ms-2">
                                                            <a href="{{ route('admin.invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                                                                class="mx-3 btn align-items-center"
                                                                data-bs-toggle="tooltip" title="Edit "
                                                                data-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('delete invoice')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['admin.invoice.destroy', $invoice->id],
                                                                'id' => 'delete-form-' . $invoice->id,
                                                            ]) !!}
                                                            <a href="#"
                                                                class="mx-3 btn align-items-center bs-pass-para "
                                                                data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                data-original-title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{ $invoice->id }}').submit();">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
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
