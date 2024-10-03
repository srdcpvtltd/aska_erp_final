@extends('layouts.master')
@section('title')
    {{ __('Village') }}
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
<script>
    document.getElementById('exportButton').addEventListener('click', function() {
        // Select the HTML table element
        var table = document.getElementById('village_datatable'); // Replace with your DataTable's ID
        
        // Create a new workbook
        var wb = XLSX.utils.book_new();

        // Convert the table to a worksheet
        var ws = XLSX.utils.table_to_sheet(table);

        // Append the worksheet to the workbook
        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

        // Export the workbook to an Excel file
        XLSX.writeFile(wb, 'village.xlsx');
    });
</script>
@endsection
@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Village') }}</li>
        </ol>
        <div class="float-end">
            <button id="exportButton" class="btn btn-success">Export</button>

            @can('create-village')
                <a href="{{ route('admin.location.village.create') }}" class="btn btn-primary">
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
                        <table class="data_table table datatable" id="village_datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Sl No.') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Block') }}</th>
                                    <th>{{ __('Gram Panchyat') }}</th>
                                    <th>{{ __('Zone') }}</th>
                                    <th>{{ __('Center') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($villages as $key => $village)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $village->name }}</td>
                                        <td>{{ ($village->block_id != '') ? $village->block->name:'-' }}</td>
                                        <td>{{ ($village->gram_panchyat_id != '') ? $village->gram_panchyat->name:'-' }}</td>
                                        <td>{{ ($village->zone_id != '') ? $village->zone->name:'-' }}</td>
                                        <td>{{ ($village->center_id != '') ? $village->center->name:'-' }}</td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0">
                                                <li class="me-2">
                                                    <a href="{{ route('admin.location.village.edit', $village->id) }}"
                                                        title="{{ __('Edit') }}">
                                                        <i class="link-icon" data-feather="edit"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="deleteBtn" href="#"
                                                        data-href="{{ route('admin.location.village.destroy', $village->id) }}"
                                                        title="{{ __('Delete') }}">
                                                        <i class="link-icon" data-feather="delete"></i>
                                                    </a>
                                                </li>
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
