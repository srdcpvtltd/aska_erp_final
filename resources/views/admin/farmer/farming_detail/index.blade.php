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
        function reportmodal() {
            $("#reportModal").modal('show');
        }

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
