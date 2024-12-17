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
                        <table class="data_table table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Current Quantity') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productServices as $productService)
                                    <tr class="font-style">
                                        <td>{{ $productService->name }}</td>
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
            $(document).on('click', '.popup', function(event) {
                event.preventDefault(); 
                var url = $(this).data('url'); 
                var title = $(this).data('title'); 
                
                $('#exampleModalLabel').text(title);
                $.ajax({
                    url: url,
                    success: function(data) {
                        $('#commonModal .modal-body').html(data);
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