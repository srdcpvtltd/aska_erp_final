@extends('layouts.master')
@section('title')
    {{ __('Manage Product Stock') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Product Stock') }}</li>
        </ol>
    </nav>
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
                                    <th>{{ __('Current Quantity') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productServices as $productService)
                                    <tr class="font-style">
                                        <td>{{ $productService->name }}</td>
                                        <td>{{ $productService->sku }}</td>
                                        <td>{{ $productService->quantity }}</td>

                                        <td class="Action">
                                            <div>
                                                <a data-size="md" href="#"
                                                    class="btn btn-primary popup"
                                                    data-url="{{ route('admin.productstock.edit', $productService->id) }}"
                                                    data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                    title="{{ __('Update Quantity') }}" data-title="{{ __('Update Quantity') }}">
                                                    Add
                                                </a>
                                            </div>
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