@extends('layouts.master')
@section('title')
    {{ __('Manage Product & Services') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Product & Services') }}</li>
        </ol>
        <div class="float-end">
            <a href="#" data-size="md" data-bs-toggle="tooltip" title="{{ __('Import') }}"
                data-url="{{ route('admin.productservice.file.import') }}" data-ajax-popup="true"
                data-title="{{ __('Import product CSV file') }}" class="btn btn-primary popup">
                Import
            </a>
            <a href="{{ route('admin.productservice.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
                class="btn btn-primary">
                Export
            </a>

            <a href="{{ route('admin.productservice.create') }}" title="{{ __('Create New Product') }}" class="btn btn-primary">
                Add
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-sm-12">
            <div class=" mt-2 {{ isset($_GET['category']) ? 'show' : '' }}" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['admin.productservice.index'], 'method' => 'GET', 'id' => 'product_service']) }}
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}
                                    {{ Form::select('category', $category, null, ['class' => 'form-control select', 'id' => 'choices-multiple', 'required' => 'required']) }}
                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-primary"
                                    onclick="document.getElementById('product_service').submit(); return false;"
                                    data-bs-toggle="tooltip" title="{{ __('apply') }}">
                                    <span class="btn-inner--icon">search</span>
                                </a>
                                <a href="{{ route('admin.productservice.index') }}" class="btn btn-danger"
                                    data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon">reset</span>
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
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Sku') }}</th>
                                    <th>{{ __('Sale Price') }}</th>
                                    <th>{{ __('Purchase Price') }}</th>
                                    <th>{{ __('Tax') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Unit') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productServices as $productService)
                                    <tr class="font-style">
                                        <td>{{ $productService->name }}</td>
                                        <td>{{ $productService->sku }}</td>
                                        <td>{{ \Auth::user()->priceFormat($productService->sale_price) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($productService->purchase_price) }}</td>
                                        <td>
                                            @if (!empty($productService->tax_id))
                                                @php
                                                    $taxes = \App\Models\Utility::tax($productService->tax_id);
                                                @endphp

                                                @foreach ($taxes as $tax)
                                                    <span
                                                        class="">{{ !empty($tax) ? $tax->name : '' . ' (' . $tax->rate . '%)' }}</span><br>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ !empty($productService->category) ? $productService->category->name : '' }}
                                        </td>
                                        <td>{{ !empty($productService->unit) ? $productService->unit->name : '' }}</td>
                                        @if ($productService->type == 'product')
                                            <td>{{ $productService->quantity }}</td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        <td>{{ ucwords($productService->type) }}</td>

                                        {{-- @if (Gate::check('edit product & service') || Gate::check('delete product & service')) --}}
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                <li class="me-2">
                                                    <a href="#" class="popup"
                                                        data-url="{{ route('admin.productservice.detail', $productService->id) }}"
                                                        data-ajax-popup="true" data-bs-toggle="tooltip"
                                                        title="{{ __('Warehouse Details') }}"
                                                        data-title="{{ __('Warehouse Details') }}">
                                                        <i class="link-icon" data-feather="eye"></i>
                                                    </a>
                                                </li>

                                                @can('edit-product_service')
                                                <li class="me-2">
                                                    <a href="{{ route('admin.productservice.edit', $productService->id) }}" title="{{ __('Edit') }}">
                                                        <i class="link-icon" data-feather="edit"></i>
                                                    </a>
                                                </li>
                                                @endcan
                                                @can('delete-product_service')
                                                <li>
                                                    <a href="#" class="deleteBtn"
                                                        data-href="{{ route('admin.productservice.destroy', $productService->id) }}"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                        <i class="link-icon" data-feather="delete"></i>
                                                    </a>
                                                </li>
                                                @endcan
                                            </ul>
                                        </td>
                                        {{-- @endif --}}
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
@section('scripts')
    <script>
        $(document).ready(function() {
            // When the link is clicked
            $(document).on('click', '.popup', function(event) {
                event.preventDefault(); // Prevent the default link behavior
                var url = $(this).data('url'); // Get the URL from the data attribute
                var title = $(this).data('title'); // Get the title from the data attribute
                
                // Update modal title
                $('#exampleModalLabel').text(title);

                // Load content via AJAX
                $.ajax({
                    url: url,
                    success: function(data) {
                        console.log(data);
                        // Inject the HTML content into the modal body
                        $('#commonModal .modal-body').html(data);

                        // Open the modal
                        $('#commonModal').modal('show');
                    },
                    error: function() {
                        $('#commonModal .modal-body').html(
                            '<p>An error occurred while loading the content.</p>');
                    }
                });
            });
        });
    </script>
@endsection
