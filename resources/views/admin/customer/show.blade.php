@extends('layouts.master')
@section('title')
    {{ __('Show Customer-Detail') }}
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
            <li class="breadcrumb-item"><a href="{{ route('admin.customer.index') }}">{{ __('Customer') }}</a></li>
            <li class="breadcrumb-item">{{ $customer['name'] }}</li>
        </ol>
        <div class="float-end">
            @can('create invoice')
                <a href="{{ route('admin.invoice.create', $customer->id) }}" class="btn btn-success">
                    {{ __('Create Invoice') }}
                </a>
            @endcan
            @can('create proposal')
                <a href="{{ route('admin.proposal.create', $customer->id) }}" class="btn btn-secondary">
                    {{ __('Create Proposal') }}
                </a>
            @endcan

            @can('edit customer')
                <a href="{{ route('admin.customer.edit', $customer['id']) }}" class="btn btn-warning">
                    Edit
                </a>
            @endcan

            @can('delete customer')
                <a class="deleteBtn btn btn-danger bs-pass-para" href="#"
                    data-href="{{ route('admin.customer.destroy', $customer->id) }}" title="{{ __('Delete') }}">
                    Delete
                </a>
            @endcan
        </div>
    </nav>
    <div class="row">
        <div class="col-md-4 col-lg-4 col-xl-4 mb-4">
            <div class="card customer-detail-box customer_card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Customer Info') }}</h5>
                    <p class="card-text mb-0">{{ $customer['name'] }}</p>
                    <p class="card-text mb-0">{{ $customer['email'] }}</p>
                    <p class="card-text mb-0">{{ $customer['contact'] }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-4 col-xl-4 mb-4">
            <div class="card customer-detail-box customer_card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Billing Info') }}</h5>
                    <p class="card-text mb-0">{{ $customer['billing_name'] }}</p>
                    <p class="card-text mb-0">{{ $customer['billing_address'] }}</p>
                    <p class="card-text mb-0">
                        {{ $customer['billing_city'] . ', ' . $customer['billing_state'] . ', ' . $customer['billing_zip'] }}
                    </p>
                    <p class="card-text mb-0">{{ $customer['billing_country'] }}</p>
                    <p class="card-text mb-0">{{ $customer['billing_phone'] }}</p>
                </div>
            </div>

        </div>
        <div class="col-md-4 col-lg-4 col-xl-4 mb-4">
            <div class="card customer-detail-box customer_card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Shipping Info') }}</h5>
                    <p class="card-text mb-0">{{ $customer['shipping_name'] }}</p>
                    <p class="card-text mb-0">{{ $customer['shipping_address'] }}</p>
                    <p class="card-text mb-0">
                        {{ $customer['shipping_city'] . ', ' . $customer['shipping_state'] . ', ' . $customer['shipping_zip'] }}
                    </p>
                    <p class="card-text mb-0">{{ $customer['shipping_country'] }}</p>
                    <p class="card-text mb-0">{{ $customer['shipping_phone'] }}</p>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card pb-0">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Company Info') }}</h5>

                    <div class="row">
                        @php
                            $totalInvoiceSum = $customer->customerTotalInvoiceSum($customer['id']);
                            $totalInvoice = $customer->customerTotalInvoice($customer['id']);
                            $averageSale = $totalInvoiceSum != 0 ? $totalInvoiceSum / $totalInvoice : 0;
                        @endphp
                        <div class="col-md-3 col-sm-6">
                            <div class="p-4">
                                <p class="card-text mb-0">{{ __('Customer Id') }}</p>
                                <h6 class="report-text mb-3">
                                    {{ Auth::user()->customerNumberFormat($customer['customer_id']) }}</h6>
                                <p class="card-text mb-0">{{ __('Total Sum of Invoices') }}</p>
                                <h6 class="report-text mb-0">{{ \Auth::user()->priceFormat($totalInvoiceSum) }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-4">
                                <p class="card-text mb-0">{{ __('Date of Creation') }}</p>
                                <h6 class="report-text mb-3">{{ \Auth::user()->dateFormat($customer['created_at']) }}</h6>
                                <p class="card-text mb-0">{{ __('Quantity of Invoice') }}</p>
                                <h6 class="report-text mb-0">{{ $totalInvoice }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-4">
                                <p class="card-text mb-0">{{ __('Balance') }}</p>
                                <h6 class="report-text mb-3">{{ \Auth::user()->priceFormat($customer['balance']) }}</h6>
                                <p class="card-text mb-0">{{ __('Average Sales') }}</p>
                                <h6 class="report-text mb-0">{{ \Auth::user()->priceFormat($averageSale) }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-4">
                                <p class="card-text mb-0">{{ __('Overdue') }}</p>
                                <h6 class="report-text mb-3">
                                    {{ \Auth::user()->priceFormat($customer->customerOverdue($customer['id'])) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style table-border-style">
                    <h5 class="d-inline-block mb-5">{{ __('Proposal') }}</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Proposal') }}</th>
                                    <th>{{ __('Issue Date') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @if (Gate::check('edit proposal') || Gate::check('delete proposal') || Gate::check('show proposal'))
                                        <th width="10%"> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customer->customerProposal($customer->id) as $proposal)
                                    <tr>
                                        <td class="Id">
                                            <a href="{{ route('admin.proposal.show', \Crypt::encrypt($proposal->id)) }}"
                                                class="btn btn-outline-primary">{{ Auth::user()->proposalNumberFormat($proposal->proposal_id) }}
                                            </a>
                                        </td>
                                        <td>{{ Auth::user()->dateFormat($proposal->issue_date) }}</td>
                                        <td>{{ Auth::user()->priceFormat($proposal->getTotal()) }}</td>
                                        <td>
                                            @if ($proposal->status == 0)
                                                <span
                                                    class="badge bg-primary p-2 px-3 rounded status_badge">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                            @elseif($proposal->status == 1)
                                                <span
                                                    class="badge bg-warning p-2 px-3 rounded status_badge">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                            @elseif($proposal->status == 2)
                                                <span
                                                    class="badge bg-danger p-2 px-3 rounded status_badge">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                            @elseif($proposal->status == 3)
                                                <span
                                                    class="badge bg-info p-2 px-3 rounded status_badge">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                            @elseif($proposal->status == 4)
                                                <span
                                                    class="badge bg-success p-2 px-3 rounded status_badge">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                            @endif
                                        </td>
                                        @if (Gate::check('edit proposal') || Gate::check('delete proposal') || Gate::check('show proposal'))
                                            <td class="Action">
                                                <span>
                                                    @if ($proposal->is_convert == 0)
                                                        @can('convert invoice')
                                                            <div class="action-btn bg-warning ms-2">
                                                                <a href="{{ route('admin.proposal.convert', $proposal->id) }}"
                                                                    class="mx-3 btn  align-items-center bs-pass-para">
                                                                    Convert to Invoice
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan
                                                    @else
                                                        @can('convert invoice')
                                                            <div class="action-btn bg-warning ms-2">
                                                                <a href="{{ route('admin.invoice.show', \Crypt::encrypt($proposal->converted_invoice_id)) }}"
                                                                    class="mx-3 btn  align-items-center">
                                                                    Already convert to Invoice
                                                                </a>
                                                            </div>
                                                        @endcan
                                                    @endif
                                                    @can('duplicate proposal')
                                                        <div class="action-btn bg-success ms-2">
                                                            <a href="{{ route('admin.proposal.duplicate', $proposal->id) }}"
                                                                class="mx-3 btn align-items-center bs-pass-para">
                                                                Duplicate Proposal
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('show proposal')
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="{{ route('admin.proposal.show', \Crypt::encrypt($proposal->id)) }}"
                                                                class="mx-3 btn align-items-center">
                                                                Detail
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('edit proposal')
                                                        <div class="action-btn bg-primary ms-2">
                                                            <a href="{{ route('admin.proposal.edit', \Crypt::encrypt($proposal->id)) }}"
                                                                class="mx-3 btn align-items-center">
                                                                Edit
                                                            </a>
                                                        </div>
                                                    @endcan

                                                    @can('delete proposal')
                                                        <div class="action-btn bg-danger ms-2">
                                                            <a class="deleteBtn mx-3 btn align-items-center" href="#"
                                                                data-href="{{ route('admin.proposal.destroy', $proposal->id) }}">
                                                                Delete
                                                            </a>
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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style table-border-style">
                    <h5 class="d-inline-block mb-5">{{ __('Invoice') }}</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Invoice') }}</th>
                                    <th>{{ __('Issue Date') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Due Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                        <th width="10%"> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customer->customerInvoice($customer->id) as $invoice)
                                    <tr>
                                        <td class="Id">
                                            <a href="{{ route('admin.invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                                class="btn btn-outline-primary">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                                            </a>
                                        </td>
                                        <td>{{ \Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                        <td>
                                            @if ($invoice->due_date < date('Y-m-d'))
                                                <p class="text-danger">
                                                    {{ \Auth::user()->dateFormat($invoice->due_date) }}</p>
                                            @else
                                                {{ \Auth::user()->dateFormat($invoice->due_date) }}
                                            @endif
                                        </td>
                                        <td>{{ \Auth::user()->priceFormat($invoice->getDue()) }}</td>
                                        <td>
                                            @if ($invoice->status == 0)
                                                <span
                                                    class="badge bg-primary p-2 px-3 rounded status_badge">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 1)
                                                <span
                                                    class="badge bg-warning p-2 px-3 rounded status_badge">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 2)
                                                <span
                                                    class="badge bg-danger p-2 px-3 rounded status_badge">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 3)
                                                <span
                                                    class="badge bg-info p-2 px-3 rounded status_badge">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 4)
                                                <span
                                                    class="badge bg-success p-2 px-3 rounded status_badge">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @endif
                                        </td>
                                        @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                            <td class="Action">
                                                <span>
                                                    @can('copy invoice')
                                                        <div class="action-btn bg-warning ms-2">
                                                            <a class="mx-3 btn align-items-center"
                                                                id="{{ route('admin.invoice.link.copy', $invoice->id) }}"
                                                                onclick="copyToClipboard(this)" data-bs-toggle="tooltip"
                                                                data-original-title="{{ __('Copy Invoice') }}">
                                                                {{ __('Copy Invoice') }}
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('duplicate invoice')
                                                        <div class="action-btn bg-success ms-2">
                                                            <a href="#" class="mx-3 btn align-items-center"
                                                                data-bs-toggle="tooltip"
                                                                data-original-title="{{ __('Duplicate') }}"
                                                                title="{{ __('Duplicate Invoice') }}"
                                                                data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"
                                                                data-confirm-yes="document.getElementById('duplicate-form-{{ $invoice->id }}').submit();">
                                                                {{ __('Duplicate Invoice') }}
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
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="{{ route('admin.invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                                                class="mx-3 btn align-items-center">
                                                                {{ __('Detail') }}
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('edit invoice')
                                                        <div class="action-btn bg-primary ms-2">
                                                            <a href="{{ route('admin.invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                                                                class="mx-3 btn align-items-center">
                                                                {{ __('Edit') }}
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('delete invoice')
                                                        <div class="action-btn bg-danger ms-2">
                                                            <a class="deleteBtn mx-3 btn align-items-center" href="#"
                                                                data-href="{{ route('admin.invoice.destroy', $invoice->id) }}"
                                                                title="{{ __('Delete') }}">
                                                                {{ __('Delete') }}
                                                            </a>
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
