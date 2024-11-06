@extends('layouts.master')
@section('title')
    {{ __('Manage Bank Branch') }}
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
    <script>
        document.getElementById('exportButton').addEventListener('click', function() {
            // Get the DataTable instance
            var table = $('#bank_branch_datatable').DataTable();

            // Define the index of the "Action" column to exclude from the export
            var actionColumnIndex = table.column(':contains(Action)')
        .index(); // Automatically detect the "Action" column index based on header text

            // Create a new workbook
            var wb = XLSX.utils.book_new();

            // Create an array to store rows of data
            var ws_data = [];

            // Get headers from the table, excluding the "Action" column
            var headers = [];
            $('#bank_branch_datatable thead tr th').each(function(index) {
                if (index !== actionColumnIndex) {
                    headers.push($(this).text()); // Add header if it's not the "Action" column
                }
            });
            ws_data.push(headers); // Add headers to ws_data

            // Add rows of data from the DataTable, excluding the "Action" column
            table.rows({
                search: 'applied'
            }).every(function(rowIdx, tableLoop, rowLoop) {
                var rowData = this.data();
                var filteredRow = [];

                // Loop through each cell in the row, adding only non-"Action" columns
                Object.keys(rowData).forEach(function(key, index) {
                    if (index !== actionColumnIndex) {
                        filteredRow.push(rowData[key]);
                    }
                });

                ws_data.push(filteredRow); // Add filtered row to ws_data
            });

            // Convert the data array to a worksheet
            var ws = XLSX.utils.aoa_to_sheet(ws_data);

            // Append the worksheet to the workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // Export the workbook to an Excel file
            XLSX.writeFile(wb, 'bank_branch.xlsx');
        });
    </script>
@endsection
@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Bank Branch') }}</li>
        </ol>
        <div class="float-end">
            <button id="exportButton" class="btn btn-success">Export</button>
            @can('create-bank_branch')
                <a href="{{ route('admin.bank_branches.create') }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                    title="{{ __('Add') }}" data-title="{{ __('Add New Bank') }}" class="btn btn-primary">
                    Add
                </a>
            @endcan
        </div>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style table-border-style">
                    <div class="col-12">
                        <form action="{{ route('admin.bank_branches.search_filter') }}" method="post">
                            @csrf
                            <div class="row align-items-center">
                                <div class="form-group col-md-4">
                                    {{ Form::label('bank_id', __('Bank'), ['class' => 'form-label']) }}
                                    <select name="bank_id" id="bank_id" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('admin.bank_branches.index') }}">
                                        <button type="button" class="btn btn-primary">Reset</button>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="data_table table datatable" id="bank_branch_datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Sl No.') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Bank Name') }}</th>
                                    <th>{{ __('Ifsc Code') }}</th>
                                    <th> {{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($bank_branch as $key => $data)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->bank_id != 0 ? $data->bank->name : '-' }}</td>
                                        <td>{{ $data->ifsc_code }}</td>
                                        @if (Gate::check('edit-bank_branch') || Gate::check('delete-bank_branch'))
                                            <td class="Action">
                                                <span>
                                                    <ul class="d-flex list-unstyled mb-0">
                                                        @can('edit-bank_branch')
                                                            <li class="me-2">
                                                                <a href="{{ route('admin.bank_branches.edit', $data->id) }}"
                                                                    title="{{ __('Edit') }}">
                                                                    <i class="link-icon" data-feather="edit"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete-bank_branch')
                                                            <li>
                                                                <a class="deleteBtn" href="#"
                                                                    data-href="{{ route('admin.bank_branches.destroy', $data->id) }}"
                                                                    title="{{ __('Delete') }}">
                                                                    <i class="link-icon" data-feather="delete"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    </ul>
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
