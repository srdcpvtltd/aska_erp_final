@extends('layouts.master')
@section('title')
    {{ __('Farmer Allotments') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Seeds,Fertiliser & Pesticides Allotment') }}</li>
        </ol>
        <div class="float-end">
            @can('create-allotment')
            <a href="{{ route('admin.farmer.loan.create') }}" class="btn btn-primary">
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
                                    <th>{{ __('Farmer Name') }}</th>
                                    <th>{{ __('Registration No.') }}</th>
                                    <th>{{ __('Agreement No') }}</th>
                                    <th>{{ __('Date of Agreement') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loans as $loan)
                                    @php
                                        $loan_category_id = json_decode($loan->loan_category_id);
                                        $loan_type_id = json_decode($loan->loan_type_id);
                                        $price_kg = json_decode($loan->price_kg);
                                        $quantity = json_decode($loan->quantity);
                                        $total_amount = json_decode($loan->total_amount);
                                        $count = count($loan_category_id);
                                    @endphp

                                    <tr class="font-style">
                                        <td>{{ $loan->farming->name }}</td>
                                        <td>{{ $loan->registration_number }}</td>
                                        <td>{{ $loan->agreement_number }}</td>
                                        <td>{{ $loan->date }}</td>
                                        <td>
                                            @for ($i = 0; $i < $count; $i++)
                                                @php
                                                    $productcategory = App\Models\ProductServiceCategory::where(
                                                        'id',
                                                        $loan_category_id[$i],
                                                    )->first();
                                                @endphp
                                                {{ $productcategory->name }}
                                                @if($i < $count - 1),@endif
                                            @endfor
                                        </td>

                                        <td>
                                            @for ($i = 0; $i < $count; $i++)
                                                @php
                                                    $product = App\Models\ProductService::where(
                                                        'id',
                                                        $loan_type_id[$i],
                                                    )->first();
                                                @endphp
                                                {{ $product->name }}
                                                @if($i < $count - 1),@endif
                                            @endfor
                                        </td>
                                        <td>
                                            @for ($i = 0; $i < $count; $i++)
                                                {{ $price_kg[$i] }}@if($i < $count - 1),@endif
                                            @endfor
                                        </td>
                                        <td>
                                            @for ($i = 0; $i < $count; $i++)
                                                {{ $quantity[$i] }}
                                                @if($i < $count - 1),@endif
                                            @endfor
                                        </td>
                                        <td>
                                            @for ($i = 0; $i < $count; $i++)
                                                {{ $total_amount[$i] }}
                                                @if($i < $count - 1),@endif
                                            @endfor
                                        </td>

                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                @can('edit-allotment')
                                                @if ($loan->invoice_generate_status == 0)
                                                    <li class="me-2">
                                                        <a href="{{ route('admin.farmer.loan.edit', $loan->id) }}">
                                                            <i class="link-icon" data-feather="edit"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="me-2">
                                                    <a href="{{ route('admin.farmer.loan.invoice_generate', $loan->id) }}"
                                                        target="_blank">
                                                        <i class="link-icon" data-feather="file-text"></i>
                                                    </a>
                                                </li>
                                                @endcan
                                                @can('delete-allotment')
                                                <li>
                                                    <a href="" class="deleteBtn"
                                                        data-href="{{ route('admin.farmer.loan.destroy', $loan->id) }}"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                        <i class="link-icon" data-feather="delete"></i>
                                                    </a>
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
