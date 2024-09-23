@extends('layouts.master')
@section('title')
    {{ __('Farmer Reimbursement') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Farmer Reimbursement') }}</li>
        </ol>
        <div class="float-end">
            @can('create-reimbursement')
                <a href="{{ route('admin.farmer.reimbursement.create') }}" class="btn btn-sm btn-primary">
                    Add
                </a>
            @endcan
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
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Farmer Name') }}</th>
                                    <th>{{ __('Registration No.') }}</th>
                                    <th>{{ __('Agreement No.') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr class="font-style">
                                        <td>{{ $payment->type }}</td>
                                        <td>{{ $payment->farming->name }}</td>
                                        <td>{{ $payment->registration_number }}</td>
                                        <td>{{ $payment->agreement_number }}</td>
                                        <td>{{ $payment->date }}</td>
                                        <td>{{ $payment->amount }}</td>

                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                @can('edit-reimbursement')
                                                    <li class="me-2">
                                                        <a href="{{ route('admin.farmer.payment.edit', $payment->id) }}">
                                                            <i class="link-icon" data-feather="edit"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('delete-reimbursement')
                                                    <li>
                                                        <a data-href="{{ route('admin.farmer.payment.destroy', $payment->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i class="link-icon" data-feather="delete"></i></a>
                                                        {!! Form::close() !!}
                                                    </li>
                                                @endcan
                                            </ul>
                                        </td>
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
