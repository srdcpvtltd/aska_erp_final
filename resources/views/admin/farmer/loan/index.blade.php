@extends('layouts.master')
@section('title')
    {{ __('Farmer Allotments') }}
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
    <script>
        document.getElementById('exportButton').addEventListener('click', function() {
            // Get the DataTable instance
            var table = $('#allotment-table').DataTable();

            // Define the index of the "Action" column to exclude from the export
            var actionColumnIndex = table.column(':contains(Action)')
                .index(); // Automatically detect the "Action" column index based on header text

            // Specify the index of the "Round Amount" column to convert its values to numbers
            var roundAmountColumnIndex = table.column(':contains(Round Amount)')
                .index(); // Adjust the column header name accordingly

            // Create a new workbook
            var wb = XLSX.utils.book_new();

            // Create an array to store rows of data
            var ws_data = [];

            // Get headers from the table, excluding the "Action" column
            var headers = [];
            $('#allotment-table thead tr th').each(function(index) {
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
                        var cellData = rowData[key];

                        // Automatically convert "Round Amount" column data to a number
                        if (index === roundAmountColumnIndex) {
                            cellData = parseFloat(cellData.replace(/,/g, '')) ||
                                0; // Remove commas and parse as float
                        }

                        // Replace <br> tags with newlines for all cells
                        cellData = cellData.toString().replace(/<br\s*\/?>/g, '\n');
                        filteredRow.push(cellData);
                    }
                });

                ws_data.push(filteredRow); // Add filtered row to ws_data
            });

            // Convert the data array to a worksheet
            var ws = XLSX.utils.aoa_to_sheet(ws_data);

            // Ensure "Round Amount" column is set to numeric in Excel
            Object.keys(ws).forEach(function(cell) {
                if (cell.match(/^[A-Z]+\d+$/)) { // Check for valid cell reference
                    var colIndex = XLSX.utils.decode_cell(cell).c; // Get column index
                    if (colIndex === roundAmountColumnIndex) {
                        ws[cell].t = 'n'; // Set cell type to numeric
                    }
                }
            });

            // Append the worksheet to the workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // Export the workbook to an Excel file
            XLSX.writeFile(wb, 'allotment.xlsx');
        });
    </script>
@endsection
@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Seeds,Fertiliser & Pesticides Allotment') }}</li>
        </ol>
        <div class="float-end">
            <button id="exportButton" class="btn btn-success">Export</button>
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
                        <table class="data_table table datatable" id="allotment-table">
                            <thead>
                                <tr>
                                    <th>{{ __('G Code No') }}</th>
                                    <th>{{ __('Farmer Name') }}</th>
                                    <th>{{ __('Invoice No.') }}</th>
                                    <th>{{ __('Date of Issue') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Round Amount') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loans as $loan)
                                    @php
                                        $loan_type_id = json_decode($loan->loan_type_id);
                                        $price_kg = json_decode($loan->price_kg);
                                        $quantity = json_decode($loan->quantity);
                                        $total_amount = json_decode($loan->total_amount);
                                        $count = count($loan_type_id);
                                        $total = 0;
                                    @endphp

                                    <tr class="font-style">
                                        <td>{{ $loan->farming->old_g_code }}</td>
                                        <td>{{ $loan->farming->name }}</td>
                                        <td>{{ $loan->invoice_no }}</td>
                                        <td>{{ $loan->date }}</td>
                                        <td>{{ $loan->category->name }}</td>

                                        <td>
                                            @for ($i = 0; $i < $count; $i++)
                                                @php
                                                    $product = App\Models\ProductService::where(
                                                        'id',
                                                        $loan_type_id[$i],
                                                    )->first();
                                                @endphp
                                                {{ $product->name }}
                                                @if ($i < $count - 1)
                                                    <br>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>
                                            @for ($i = 0; $i < $count; $i++)
                                                {{ $price_kg[$i] }}
                                                @if ($i < $count - 1)
                                                    <br>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>
                                            @for ($i = 0; $i < $count; $i++)
                                                {{ $quantity[$i] }}
                                                @if ($i < $count - 1)
                                                    <br>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>
                                            @for ($i = 0; $i < $count; $i++)
                                                {{ $total_amount[$i] }}
                                                @if ($i < $count - 1)
                                                    <br>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>
                                            @for ($i = 0; $i < $count; $i++)
                                                @php
                                                    $total += $total_amount[$i];
                                                @endphp
                                            @endfor
                                            {{ round($total) }}
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
