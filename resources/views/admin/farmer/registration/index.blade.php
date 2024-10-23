@extends('layouts.master')
@section('title')
    {{ __('Farmer Registration') }}
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
    <script>
        document.getElementById('exportButton').addEventListener('click', function() {
            // Get the DataTable instance
            var table = $('#farmer_datatable').DataTable(); // Replace with your DataTable's ID

            // Get all data from the DataTable (including non-visible rows)
            var data = table.rows({
                search: 'applied'
            }).data().toArray(); // Fetch all data, filtered based on search

            // Create a new workbook
            var wb = XLSX.utils.book_new();

            // Create an array to store rows of data
            var ws_data = [];

            // Get headers from the table, excluding the "Action" and "Status" columns
            var headers = [];
            var actionColumnIndex = -1; // Initialize action column index
            var statusColumnIndex = -1; // Initialize status column index
            $('#farmer_datatable thead tr th').each(function(index) {
                var headerText = $(this).text();
                if (headerText.toLowerCase() !== 'action' && headerText.toLowerCase() !== 'status') {
                    headers.push(headerText); // Only add non-"Action" and non-"Status" headers
                } else {
                    if (headerText.toLowerCase() === 'action') {
                        actionColumnIndex = index; // Store the index of the "Action" column
                    }
                    if (headerText.toLowerCase() === 'status') {
                        statusColumnIndex = index; // Store the index of the "Status" column
                    }
                }
            });
            ws_data.push(headers); // Add headers to ws_data

            // Add rows of data from the DataTable, excluding the "Action" and "Status" columns
            data.forEach(function(row) {
                var filteredRow = Object.values(row).filter(function(value, index) {
                    return index !== actionColumnIndex && index !==
                        statusColumnIndex; // Exclude "Action" and "Status" columns by index
                });
                ws_data.push(filteredRow); // Add filtered row to ws_data
            });

            // Convert the data array to a worksheet
            var ws = XLSX.utils.aoa_to_sheet(ws_data);

            // Append the worksheet to the workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // Export the workbook to an Excel file
            XLSX.writeFile(wb, 'grower_details.xlsx');
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
        $('#grampanchyat_id').change(function() {
            let gram_panchyat_id = $(this).val();
            $.ajax({
                url: "{{ route('admin.farmer.location.get_villages') }}",
                method: 'post',
                data: {
                    gram_panchyat_id: gram_panchyat_id,
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    villages = response.villages;
                    $('#village_id').empty();
                    $('#village_id').append('<option  value="">Select Village</option>');
                    for (i = 0; i < villages.length; i++) {
                        $('#village_id').append('<option value="' + villages[i].id + '">' +
                            villages[i].name + '</option>');
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
            <li class="breadcrumb-item">{{ __('Farmer Registration') }}</li>
        </ol>
        {{-- <div class="col-md-5">
            <form action="{{ route('admin.farmer.farming_registration.search_filter') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Filter</label>
                        <select name="filter" id="filter" class="form-control wd-200">
                            <option value="">Select</option>
                            <option value="1">Validate</option>
                            <option value="0">Nonvalidate</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Submit</button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.farmer.farming_registration.index') }}">
                            <button type="button" class="btn btn-danger" style="margin-top: 20px;">Reset</button>
                        </a>
                    </div>
                </div>
            </form>
        </div> --}}
        <div class="float-end">
            <button id="exportButton" class="btn btn-success">Export</button>
            @can('create-farmer_registration')
                <a href="{{ route('admin.farmer.farming_registration.create') }}" class="btn btn-primary">
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
                        <form action="{{ route('admin.farmer.search_filter') }}" method="post">
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
                                    {{ Form::label('village_id', __('Village'), ['class' => 'form-label']) }}
                                    {{ Form::select('village_id', ['' => 'Select Village'], null, ['class' => 'form-control select']) }}
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
                                    <a href="{{ route('admin.farmer.farming_registration.index') }}">
                                        <button type="button" class="btn btn-primary">Reset</button>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered data_table" id="farmer_datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Sl No.') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Father Name') }}</th>
                                    <th>{{ __('G. Code') }}</th>
                                    <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Age') }}</th>
                                    <th>{{ __('Gender') }}</th>
                                    <th>{{ __('Qualification') }}</th>
                                    <th>{{ __('State') }}</th>
                                    <th>{{ __('District') }}</th>
                                    <th>{{ __('Block') }}</th>
                                    <th>{{ __('Zone') }}</th>
                                    <th>{{ __('Center') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($farmings as $key => $farming)
                                    <tr class="font-style">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $farming->name }}</td>
                                        <td>{{ $farming->father_name }}</td>
                                        <td>
                                            @if ($farming->g_code != null)
                                                {{ $farming->g_code }}
                                            @else
                                                <span
                                                    class="status_badge text-capitalize badge bg-danger p-2 px-3 rounded">Not
                                                    Assigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $farming->mobile }}</td>
                                        <td>{{ $farming->age }}</td>
                                        <td>{{ $farming->gender }}</td>
                                        <td>{{ $farming->qualification }}</td>
                                        <td>{{ $farming->state->name }}</td>
                                        <td>{{ $farming->district->name }}</td>
                                        <td>{{ $farming->block->name }}</td>
                                        <td>{{ $farming->zone->name }}</td>
                                        <td>{{ $farming->center->name }}</td>
                                        <td>
                                            @if ($farming->is_validate)
                                                <span
                                                    class="status_badge text-capitalize badge bg-success p-2 px-3 rounded">Validated</span>
                                            @else
                                                <span
                                                    class="status_badge text-capitalize badge bg-danger p-2 px-3 rounded">Not
                                                    Validated</span>
                                            @endif
                                        </td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                @if ($farming->is_validate != 0)
                                                    @can('show-farmer_registration')
                                                        <li class="me-2">
                                                            <a
                                                                href="{{ route('admin.farmer.farming_registration.show', $farming->id) }}">
                                                                <i class="link-icon" data-feather="eye"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                @else
                                                    @if ($farming->created_by == Auth::user()->id)
                                                        <li class="me-2">
                                                            <a href="{{ route('admin.farmer.farming_registration.validate', $farming->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Validate') }}">
                                                                <i class="link-icon" data-feather="check-square"></i>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @can('edit-farmer_registration')
                                                        <li class="me-2">
                                                            <a
                                                                href="{{ route('admin.farmer.farming_registration.edit', $farming->id) }}">
                                                                <i class="link-icon" data-feather="edit"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('delete-farmer_registration')
                                                        <li>
                                                            <a href="#" class="deleteBtn"
                                                                data-href="{{ route('admin.farmer.farming_registration.destroy', $farming->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                                <i class="link-icon" data-feather="delete"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                @endif
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
