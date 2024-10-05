@extends('layouts.master')
@section('title')
    {{ __('Village') }}
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
    <script>
        document.getElementById('exportButton').addEventListener('click', function() {
            // Get the DataTable instance
            var table = $('#village_datatable').DataTable(); // Replace with your DataTable's ID

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
            $('#village_datatable thead tr th').each(function(index) {
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
            XLSX.writeFile(wb, 'village.xlsx');
        });
    </script>
    <script>
        $('#block_id').change(function() {
            let block_id = $(this).val();
            $.ajax({
                url: "{{ route('admin.farmer.location.get_gram_panchyats') }}",
                method: 'post',
                data: {
                    block_id: block_id,
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    gram_panchyats = response.gram_panchyats;
                    $('#grampanchyat_id').empty();
                    $('#grampanchyat_id').append(
                        '<option  value="">Select Gram Panchyat</option>');
                    for (i = 0; i < gram_panchyats.length; i++) {
                        $('#grampanchyat_id').append('<option value="' + gram_panchyats[i]
                            .id + '">' + gram_panchyats[i].name + '</option>');
                    }
                }
            });
        });
        $('#zone_id').change(function() {
            let zone_id = $(this).val();
            $.ajax({
                url: "{{ route('admin.farmer.location.get_centers') }}",
                method: 'post',
                data: {
                    zone_id: zone_id,
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    centers = response.centers;
                    $('#center_id').empty();
                    $('#center_id').append('<option  value="">Select Center</option>');
                    for (i = 0; i < centers.length; i++) {
                        $('#center_id').append('<option value="' + centers[i].id + '">' +
                            centers[i].name + '</option>');
                    }
                }
            });
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
                    <div class="col-12">
                        <form action="{{ route('admin.village.search_filter') }}" method="post">
                            @csrf
                            <div class="row align-items-center">
                                <div class="form-group col-md-2">
                                    {{ Form::label('block_id', __('Block'), ['class' => 'form-label']) }}
                                    {{ Form::select('block_id', $blocks, null, ['class' => 'form-control select']) }}
                                </div>
                                <div class="form-group col-md-2">
                                    {{ Form::label('grampanchyat_id', __('Grampanchyat'), ['class' => 'form-label']) }}
                                    {{ Form::select('grampanchyat_id', ['' => 'Select Gram Panchyat'], null, ['class' => 'form-control select']) }}
                                </div>
                                <div class="form-group col-md-2">
                                    {{ Form::label('zone_id', __('Zone'), ['class' => 'form-label']) }}
                                    {{ Form::select('zone_id', $zones, null, ['class' => 'form-control select']) }}
                                </div>
                                <div class="form-group col-md-2">
                                    {{ Form::label('center_id', __('Center'), ['class' => 'form-label']) }}
                                    {{ Form::select('center_id', ['' => 'Select center'], null, ['class' => 'form-control select']) }}
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('admin.location.village.index') }}">
                                        <button type="button" class="btn btn-primary">Reset</button>
                                    </a>
                                </div>
                                <div class="col-md-1">
                                    <button id="exportButton" class="btn btn-success">Export</button>
                                </div>
                            </div>
                        </form>
                    </div>
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
                                        <td>{{ $village->block_id != '' ? $village->block->name : '-' }}</td>
                                        <td>{{ $village->gram_panchyat_id != '' ? $village->gram_panchyat->name : '-' }}</td>
                                        <td>{{ $village->zone_id != '' ? $village->zone->name : '-' }}</td>
                                        <td>{{ $village->center_id != '' ? $village->center->name : '-' }}</td>
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
