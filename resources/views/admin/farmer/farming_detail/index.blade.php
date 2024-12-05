@extends('layouts.master')
@section('title')
    {{ __('Plot Detail') }}
@endsection
@section('styles')
    @include('layouts.datatables_css')
    <style>
        .modal.show .modal-dialog {
            top: 0%;
        }
    </style>
@endsection
@section('scripts')
    @include('layouts.datatables_js')
    {{ $dataTable->scripts() }}
    <script>
        function reportmodal(element) {
            $('#loss_reason').hide();
            $('#loss_area').hide();

            var plot_detail_id = $(element).data('id');
            $('#plot_detail_id').val(plot_detail_id);

            $.ajax({
                url: "{{ route('admin.farmer.farming_detail.getPlotDetails') }}",
                method: 'post',
                data: {
                    id: plot_detail_id,
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#farmer_name').text(response.farmer_name);
                    $('#plot_no').text(response.plot_no);
                    $('#area').text(response.area);
                    $('#total_planting_area').val(response.area);
                    $('#total_planting_areas').val(response.area);
                }
            });
            $("#reportModal").modal('show');
        }
        
        function editreportmodal(element) {
            $('#loss_reason').hide();
            $('#loss_area').hide();

            var plot_detail_id = $(element).data('id');
            $('#plot_detail_id').val(plot_detail_id);

            $.ajax({
                url: "{{ route('admin.farmer.farming_detail.getEditPlotDetails') }}",
                method: 'post',
                data: {
                    id: plot_detail_id,
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#farmer_name').text(response.farmer_name);
                    $('#plot_no').text(response.plot_no);
                    $('#area').text(response.area);
                    if(response.croploss === "Yes"){
                        $('#loss_reason').show();
                        $('#loos_reason').val(response.loss_reason);
                        $('#loss_area').show();
                        $('#loos_area').val(response.loss_area);
                    }
                    $('#tentative_harvest_quantity').val(response.tentative_harvest_quantity);
                    $('#total_planting_area').val(response.total_planting_area);
                    $('#total_planting_areas').val(response.area);
                    $('#mode_of_transport').val(response.mode_of_transport);
                    $('#reserve_seed').val(response.reserve_seed);
                    $('input[name="croploss"]').val([response.croploss]);
                }
            });
            $("#reportModal").modal('show');
        }

        $('input[name="croploss"]').on('click', function() {
            var value = $(this).val();
            if (value === 'Yes') {
                $('#loss_reason').show();
                $('#loss_area').show();
            } else {
                $('#loss_reason').hide();
                $('#loss_area').hide();
            }
        });

        $('#loos_area').on('keyup', function(){
            var loose_area = $(this).val();
            var planting_area = $('#total_planting_areas').val();
            if(loose_area > 0){
                var loss_area = planting_area - loose_area;
                $('#total_planting_area').val(loss_area);
            } else {
                $('#total_planting_area').val(planting_area);
            }
        });

        $('.close_btn').on('click', function() {
            $('#reportModal').modal('hide');
        });

        const table = $('#farming_details-table');

        table.on('preXhr.dt', function(e, settings, data) {
            data.zone_id = $('#zone_id').val();
            data.center_id = $('#center_id').val();
        });

        $('#filter').on('click', function() {
            table.DataTable().ajax.reload();
            return false;
        });
        $('#reset').on('click', function() {
            table.on('preXhr.dt', function(e, settings, data) {
                data.zone_id = '';
                data.center_id = '';
            });

            table.DataTable().ajax.reload();
            return false;
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
                        <form>
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
                                    <button type="button" class="btn btn-primary" id="filter">Filter</button>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger" id="reset">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%', 'class' => 'table table-responsive-sm table-striped']) }}
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
                                <select class="form-control select" name="loss_reason" id="loos_reason"
                                    placeholder="Select">
                                    <option value="">{{ __('Select Reason') }}</option>
                                    <option value="Flood">Flood</option>
                                    <option value="Insect">Insect</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6" id="loss_area">
                                {{ Form::label('loss_area', __('Loss Area (Acr.)'), ['class' => 'form-label']) }} <br>
                                <input name="loss_area" type="text" id="loos_area" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('total_planting_area', __('Total Area for final planting'), ['class' => 'form-label']) }}
                                <br>
                                <input name="total_planting_area" type="text" class="form-control" id="total_planting_area" readonly>
                                <input type="hidden" class="form-control" id="total_planting_areas" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('tentative_harvest_quantity', __('Tentative Plant Quantity (In Ton)'), ['class' => 'form-label']) }}
                                {{ Form::text('tentative_harvest_quantity', '', ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('mode_of_transport', __('Mode Of Transport'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="mode_of_transport" id="mode_of_transport"
                                    placeholder="Select">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="Cart">Cart</option>
                                    <option value="Truck">Truck</option>
                                    <option value="Tractor">Tractor</option>
                                    <option value="Own Transport">Own Transport (OT)</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('reserve_seed', __('Reserve Seed'), ['class' => 'form-label']) }}
                                {{ Form::text('reserve_seed', '', ['class' => 'form-control']) }}
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
