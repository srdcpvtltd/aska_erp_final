@extends('layouts.master')
@section('title')
    {{ __('Plot Detail') }}
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
    <script>
        document.getElementById('exportButton').addEventListener('click', function() {
            // Get the DataTable instance
            var table = $('#plot_details_datatable').DataTable(); // Replace with your DataTable's ID

            // Get all data from the DataTable (including non-visible rows)
            var data = table.rows({
                search: 'applied'
            }).nodes().toArray(); // Fetch rows as DOM nodes to extract text content directly

            // Create a new workbook
            var wb = XLSX.utils.book_new();

            // Create an array to store rows of data
            var ws_data = [];

            // Get headers from the table, excluding the "Action" column
            var headers = [];
            var actionColumnIndex = -1; // Initialize action column index
            $('#plot_details_datatable thead tr th').each(function(index) {
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
                var filteredRow = [];
                $(row).find('td').each(function(index) {
                    if (index !== actionColumnIndex) { // Exclude "Action" column
                        filteredRow.push($(this).text().trim()); // Get plain text, strip HTML
                    }
                });
                ws_data.push(filteredRow); // Add filtered row to ws_data
            });

            // Convert the data array to a worksheet
            var ws = XLSX.utils.aoa_to_sheet(ws_data);

            // Append the worksheet to the workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // Export the workbook to an Excel file
            XLSX.writeFile(wb, 'Plot_details.xlsx');
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
            <li class="breadcrumb-item active" aria-current="page">{{ __('Plot') }}</li>
        </ol>

        <div class="float-end">
            <button id="exportButton" class="btn btn-success">Export</button>
            @can('create-plot')
                <a href="{{ route('admin.farmer.farming_detail.create') }}" class="btn btn-primary">
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
                        <form action="{{ route('admin.farmer.farming_detail.search_filter') }}" method="post">
                            @csrf
                            <div class="row align-items-center">
                                <div class="form-group col-md-4">
                                    {{ Form::label('zone_id', __('Zone'), ['class' => 'form-label']) }}
                                    <select name="zone_id" id="zone_id" class="form-control">
                                        <option value="">Select Zone</option>
                                        @foreach ($zones as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    {{ Form::label('center_id', __('Center'), ['class' => 'form-label']) }}
                                    {{ Form::select('center_id', ['' => 'Select center'], null, ['class' => 'form-control select']) }}
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('admin.farmer.farming_detail.index') }}">
                                        <button type="button" class="btn btn-primary">Reset</button>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="data_table table datatable" id="plot_details_datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('GCode') }}</th>
                                    <th>{{ __('Farmer') }}</th>
                                    <th>{{ __('Father Name') }}</th>
                                    <th>{{ __('Plot Number') }}</th>
                                    <th>{{ __('Area in Acar') }}</th>
                                    <th>{{ __('Date of Planting') }}</th>
                                    <th>{{ __('Tentative Plant Quantity') }}</th>
                                    <th>{{ __('Seed Category') }}</th>
                                    <th>{{ __('Cutting Order') }}</th>
                                    <th>{{ __('Can Field Village') }}</th>
                                    <th>{{ __('Can Field Center') }}</th>
                                    <th>{{ __('Plant Category') }}</th>
                                    <th>{{ __('Irrigation Name') }}</th>
                                    <th>{{ __('Irrigation Code') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($farming_details as $farming_detail)
                                    <tr class="font-style">
                                        <td>{{ @$farming_detail->farming->old_g_code }}</td>
                                        <td>{{ @$farming_detail->farming->name }}</td>
                                        <td>{{ @$farming_detail->farming->father_name }}</td>
                                        <td>{{ $farming_detail->plot_number }}</td>
                                        <td>{{ number_format($farming_detail->area_in_acar, 2) }}</td>
                                        <td>{{ date('d-m-Y', strtotime ($farming_detail->date_of_harvesting ))}}</td>
                                        <td>{{ number_format($farming_detail->tentative_harvest_quantity, 2) }}</td>
                                        <td>{{ @$farming_detail->seed_category->name }}</td>
                                        <td>
                                            @if (@$farming_detail->is_cutting_order)
                                                <span
                                                    class="status_badge text-capitalize badge bg-success p-2 px-3 rounded">Yes</span>
                                            @else
                                                <span
                                                    class="status_badge text-capitalize badge bg-danger p-2 px-3 rounded">No</span>
                                            @endif
                                        </td>
                                        <td>{{ @$farming_detail->can_field_village->name }}</td>
                                        <td>{{ @$farming_detail->can_field_center->name }}</td>
                                        <td>{{ $farming_detail->planting_category }}</td>
                                        <td>{{ @$farming_detail->irrig->name }}</td>
                                        <td>{{ @$farming_detail->irrig->code }}</td>
                                        <td>{{ date('d-m-Y', strtotime($farming_detail->created_at)) }}</td>
                                        <td class="Action">
                                            <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                                @if ($farming_detail->is_cutting_order != '1')
                                                    @can('edit-plot')
                                                        <li class="me-2">
                                                            <a href="{{ route('admin.farmer.farming_detail.edit', $farming_detail->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                                <i class="link-icon" data-feather="edit"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @if ($farming_detail->croploss == null)
                                                        <li class="me-2">
                                                            <a href="#" data-bs-toggle="tooltip"
                                                                title="{{ __('Report') }}" class="reportmodal"
                                                                data-id="{{ $farming_detail->id }}">
                                                                <i class="link-icon" data-feather="file-text"></i>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @can('delete-plot')
                                                        <li>
                                                            <a class="deleteBtn" href="#"
                                                                data-href="{{ route('admin.farmer.farming_detail.destroy', $farming_detail->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                                <i class="link-icon" data-feather="delete"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                @else
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
    <!-- Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Servey Report</h5>
                    <button type="button" class="close close_btn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.farmer.servey_data') }}" method="post">
                    <input type="hidden" name="id" id="plot_detail_id">
                    @csrf
                    <div class="modal-body">
                        <p>Farmer Name: <span id="farmer_name"></span></p>
                        <p>Plot No: <span id="plot_no"></span></p>
                        <p>Area: <span id="area"></span></p>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <p>Is there any crop loss</p>
                                <input name="croploss" type="radio" value="Yes"> Yes
                                <input name="croploss" type="radio" value="No"> No
                            </div>
                            <div class="form-group col-md-6" id="loss_reason">
                                {{ Form::label('loss_reason', __('Loss Reason'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="loss_reason" id="loss_reason"
                                    placeholder="Select">
                                    <option value="">{{ __('Select Reason') }}</option>
                                    <option value="Flood">Flood</option>
                                    <option value="Insect">Insect</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6" id="loss_area">
                                {{ Form::label('loss_area', __('Loss Area (Acr.)'), ['class' => 'form-label']) }} <br>
                                <input name="loss_area" type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('total_planting_area', __('Total Area for final planting'), ['class' => 'form-label']) }}
                                <br>
                                <input name="total_planting_area" type="text" class="form-control" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('tentative_harvest_quantity', __('Tentative Plant Quantity (In Ton)'), ['class' => 'form-label']) }}
                                {{ Form::text('tentative_harvest_quantity', '', ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
