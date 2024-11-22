@extends('layouts.master')
@section('title')
    {{ __('Challan Create') }}
@endsection
{{-- @section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#plant_type').hide();
            $('#ratun_type').hide();
            $('#planting_category').hide();

            $('#g_code').keyup(function() {
                let g_code = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.get_detail') }}",
                    method: 'post',
                    data: {
                        g_code: g_code,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {

                        $('#farming_id').empty();
                        if (response.farmerHtml) {
                            $('#farming_id').append(response.farmerHtml);
                        } else {
                            $('#farming_id').append('<option value="">Select Farmer</option>');
                        }
                        $('#block_id').empty();
                        if (response.blockHtml) {
                            $('#block_id').append(response.blockHtml);
                        } else {
                            $('#block_id').append('<option value="">Select Block</option>');
                        }
                        $('#gram_panchyat_id').empty();
                        if (response.gpHtml) {
                            $('#gram_panchyat_id').append(response.gpHtml);
                        } else {
                            $('#gram_panchyat_id').append(
                                '<option value="">Select Gram Panchyat</option>');
                        }
                        $('#village_id').empty();
                        if (response.villageHtml) {
                            $('#village_id').append(response.villageHtml);
                        } else {
                            $('#village_id').append(
                                '<option value="">Select Village</option>');
                        }
                        $('#zone_id').empty();
                        if (response.zoneHtml) {
                            $('#zone_id').append(response.zoneHtml);
                        } else {
                            $('#zone_id').append('<option  value="">Select Zone</option>');
                        }
                        $('#center_id').empty();
                        if (response.centerHtml) {
                            $('#center_id').append(response.centerHtml);
                        } else {
                            $('#center_id').append('<option value="">Select Center</option>');
                        }

                        $('#can_field_zone_id').empty();
                        if (response.zone_id != null) {
                            $('#can_field_zone_id').append(
                                '<option value="">Select Zone</option>');
                            for (i = 0; i < response.zone.length; i++) {
                                var selected = (response.zone[i].id == response.zone_id) ?
                                    ' selected' : '';
                                $('#can_field_zone_id').append('<option value="' + response
                                    .zone[i].id + '"' + selected + '>' +
                                    response.zone[i].name + '</option>');
                            }
                        } else {
                            $('#can_field_zone_id').append(
                                '<option value="">Select Zone</option>');
                        }

                        $('#can_field_center_id').empty();
                        if (response.center_id != null) {
                            $('#can_field_center_id').append(
                                '<option value="">Select Center</option>');
                            for (i = 0; i < response.center.length; i++) {
                                var selected = (response.center[i].id == response.center_id) ?
                                    ' selected' : '';
                                $('#can_field_center_id').append('<option value="' + response
                                    .center[i].id + '"' + selected + '>' +
                                    response.center[i].name + '</option>');
                            }
                        } else {
                            $('#can_field_center_id').append(
                                '<option  value="">Select Center</option>');
                        }

                        $('#can_field_village_id').empty();
                        if (response.village_id != null) {
                            $('#can_field_village_id').append(
                                '<option value="">Select Village</option>');
                            for (i = 0; i < response.village.length; i++) {
                                var selected = (response.village[i].id == response.village_id) ?
                                    'selected' : '';
                                $('#can_field_village_id').append('<option value="' + response
                                    .village[i].id + '"' + selected + '>' +
                                    response.village[i].name + '</option>');
                            }
                        } else {
                            $('#can_field_village_id').append(
                                '<option  value="">Select Village</option>');
                        }
                    }
                });
            });
            $('#farming_id').change(function() {
                let farming_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.farming.get_detail') }}",
                    method: 'post',
                    data: {
                        farming_id: farming_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#block_id').empty();
                        if (response.blockHtml) {
                            $('#block_id').append(response.blockHtml);
                        } else {
                            $('#block_id').append('<option  value="">Select Block</option>');
                        }
                        $('#gram_panchyat_id').empty();
                        if (response.gpHtml) {
                            $('#gram_panchyat_id').append(response.gpHtml);
                        } else {
                            $('#gram_panchyat_id').append(
                                '<option  value="">Select Gram Panchyat</option>');
                        }
                        $('#village_id').empty();
                        if (response.villageHtml) {
                            $('#village_id').append(response.villageHtml);
                        } else {
                            $('#village_id').append(
                                '<option  value="">Select Village</option>');
                        }
                        $('#zone_id').empty();
                        if (response.zoneHtml) {
                            $('#zone_id').append(response.zoneHtml);
                        } else {
                            $('#zone_id').append('<option  value="">Select Zone</option>');
                        }
                        $('#center_id').empty();
                        if (response.centerHtml) {
                            $('#center_id').append(response.centerHtml);
                        } else {
                            $('#center_id').append('<option  value="">Select Center</option>');
                        }
                    }
                });
            });
            $('#can_field_zone_id').change(function() {
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
                        $('#can_field_center_id').empty();
                        $('#can_field_center_id').append(
                            '<option  value="">Select Center</option>');
                        for (i = 0; i < centers.length; i++) {
                            $('#can_field_center_id').append('<option value="' + centers[i].id +
                                '">' +
                                centers[i].name + '</option>');
                        }
                    }
                });
            });
            $('#can_field_center_id').change(function() {
                let center_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.center.get_villages') }}",
                    method: 'post',
                    data: {
                        center_id: center_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        villages = response.villages;
                        $('#can_field_village_id').empty();
                        $('#can_field_village_id').append(
                            '<option  value="">Select Village</option>');
                        for (i = 0; i < villages.length; i++) {
                            $('#can_field_village_id').append('<option value="' + villages[i]
                                .id + '">' +
                                villages[i].name + '</option>');
                        }
                    }
                });
            });
            $("input[name=type]").on('click', function() {
                var type = $(this).val();
                console.log(type);
                if (type == "Plant") {
                    $("#ratun_type").removeAttr("name");
                    $('#planting_category').show();
                    $('#plant_type').show();
                    $('#plant_type').attr('name', "planting_category");
                    $('#ratun_type').hide();
                } else if (type == "Ratun") {
                    $("#plant_type").removeAttr("name");
                    $('#planting_category').show();
                    $('#ratun_type').show();
                    $('#ratun_type').attr('name', "planting_category");
                    $('#plant_type').hide();
                }
            });
            $('#irregation_mode').change(function() {
                let irregation_mode = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.location.get_irrigations') }}",
                    method: 'post',
                    data: {
                        irregation_mode: irregation_mode,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#irregation').empty();
                        $('#irregation').append('<option  value="">Select Irregation</option>');
                        for (i = 0; i < response.length; i++) {
                            $('#irregation').append('<option value="' + response[i].id + '">' +
                                response[i].name + '</option>');
                        }
                    }
                });
            });
        });
    </script>
@endsection --}}

@section('main-content')
    @include('admin.section.flash_message')

    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.challan.index') }}">{{ __('Challan') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Create') }}</li>
        </ol>
    </nav>

    <div class="row">
        {{ Form::open(['url' => 'admin/challan', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="created_by" id="created_by" value="{{ Auth::user()->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('warehouse_id', __('Select Warehouse'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="warehouse_id" id="warehouse_id" required>
                                    <option value="">{{ __('Select Warehouse') }}</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('challan_no', __('Challan No.'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="challan_no">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('product_id', __('Product'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="product_id" id="product_id">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('receive_date', __('Receive Date'), ['class' => 'form-label']) }}
                                <input type="date" class="form-control" name="receive_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('vehicle_no', __('Vehicle No.'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="vehicle_no">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="quantity">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="amount">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.challan.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
