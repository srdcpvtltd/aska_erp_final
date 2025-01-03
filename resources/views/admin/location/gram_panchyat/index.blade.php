@extends('layouts.master')
@section('title')
    {{ __('GP (Gram Panchyat)') }}
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
<script>
    document.getElementById('exportButton').addEventListener('click', function() {
        // Get the DataTable instance
        var table = $('#gp_datatable').DataTable(); // Replace with your DataTable's ID

        // Get all data from the DataTable (including non-visible rows)
        var data = table.rows({
            search: 'applied'
        }).data().toArray(); // Fetch all data, filtered based on search

        // Create a new workbook
        var wb = XLSX.utils.book_new();

        // Create an array to store rows of data
        var ws_data = [];

        // Get headers from the table, excluding the "Action" column
        var headers = [];
        var actionColumnIndex = -1; // Initialize action column index
        $('#gp_datatable thead tr th').each(function(index) {
            var headerText = $(this).text();
            if (headerText.toLowerCase() !== 'action') {
                headers.push(headerText); // Only add non-"Action" headers
            } else {
                actionColumnIndex = index; // Store the index of the "Action" column
            }
        });
        ws_data.push(headers); // Add headers to ws_data

        // Add rows of data from the DataTable, excluding the "Action" column
        data.forEach(function(row) {
            var filteredRow = Object.values(row).filter(function(value, index) {
                return index !== actionColumnIndex; // Exclude "Action" column by index
            });
            ws_data.push(filteredRow); // Add filtered row to ws_data
        });

        // Convert the data array to a worksheet
        var ws = XLSX.utils.aoa_to_sheet(ws_data);

        // Append the worksheet to the workbook
        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

        // Export the workbook to an Excel file
        XLSX.writeFile(wb, 'Gram_panchyat.xlsx');
    });
</script>

@endsection
@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('GP (Gram Panchyat)') }}</li>
        </ol>
        <div class="float-end">
            <button id="exportButton" class="btn btn-success">Export</button>
            @can('create-gram_panchyat')
                <a href="{{ route('admin.location.gram_panchyat.create') }}" title="{{ __('Add') }}" class="btn btn-primary">
                    Add
                </a>
            @endcan
        </div>
    </nav>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="col-12">
                        <form action="{{ route('admin.location.gram_panchyat.search_filter') }}" method="post">
                            @csrf
                            <div class="row align-items-center">
                                <div class="form-group col-md-3">
                                    {{ Form::label('block_id', __('Block'), ['class' => 'form-label']) }}
                                    <select name="block_id" id="block_id" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($blocks as $block)
                                            <option value="{{ $block->id }}">{{ $block->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('admin.location.gram_panchyat.index') }}">
                                        <button type="button" class="btn btn-primary">Reset</button>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="data_table table datatable" id="gp_datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('SL No.') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Block') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gram_panchyats as $key=>$gram_panchyat)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $gram_panchyat->name }}</td>
                                        <td>{{ $gram_panchyat->block->name }}</td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0">
                                                @can('edit-gram_panchyat')
                                                <li class="me-2">
                                                    <a href="{{ route('admin.location.gram_panchyat.edit', $gram_panchyat->id) }}"
                                                        title="{{ __('Edit') }}">
                                                        <i class="link-icon" data-feather="edit"></i>
                                                    </a>
                                                </li>
                                                @endcan
                                                @can('delete-gram_panchyat')
                                                <li>
                                                    <a class="deleteBtn" href="#"
                                                        data-href="{{ route('admin.location.gram_panchyat.destroy', $gram_panchyat->id) }}"
                                                        title="{{ __('Delete') }}">
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