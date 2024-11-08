@extends('layouts.master')
@section('title')
    {{ __('Edit Plot') }}
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#g_code').keyup(function() {
                let g_code = $(this).val();
                console.log(g_code);
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
                            $('#farming_id').append('<option  value="">Select Farmer</option>');
                        }
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
                        $('#can_field_zone_id').empty();
                        if (response.zone_id) {
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
                        if (response.center_id) {
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
                        if (response.village_id) {
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
                    $('#plant_type').show();
                    $('#plant_type').attr('name', "planting_category");
                    $('#ratun_type').hide();
                } else if (type == "Ratun") {
                    $("#plant_type").removeAttr("name");
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
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.farmer.farming_detail.index') }}">{{ __('Plot') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Edit') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::model($farming_detail, ['route' => ['admin.farmer.farming_detail.update', $farming_detail->id], 'method' => 'PUT', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('g_code', __('G_Code'), ['class' => 'form-label']) }}
                                {{ Form::text('g_code', $farming_detail->farming->old_g_code, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Farming'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required
                                    placeholder="Select Farmer">
                                    <option value="">{{ __('Select Farmer') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option {{ $farming_detail->farming_id == $farming->id ? 'selected' : '' }}
                                            value="{{ $farming->id }}">{{ $farming->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('block_id', __('Block'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="block_id" id="block_id" readonly>
                                    @if (@$farming_detail->farming->block)
                                        <option value="{{ @$farming_detail->farming->block_id }}">
                                            {{ @$farming_detail->farming->block->name }}</option>
                                    @else
                                        <option value="">{{ __('Select Block') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('gram_panchyat_id', __('Gram Panchyat'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="gram_panchyat_id" id="gram_panchyat_id" readonly>
                                    @if (@$farming_detail->farming->gram_panchyat)
                                        <option value="{{ @$farming_detail->farming->gram_panchyat_id }}">
                                            {{ @$farming_detail->farming->gram_panchyat->name }}</option>
                                    @else
                                        <option value="">{{ __('Select Gram Panchyat') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('village_id', __('Village'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="village_id" id="village_id"
                                    placeholder="Select Village" readonly>
                                    @if (@$farming_detail->farming->village)
                                        <option value="{{ @$farming_detail->farming->village_id }}">
                                            {{ @$farming_detail->farming->village->name }}</option>
                                    @else
                                        <option value="">{{ __('Select Village') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('zone_id', __('Zone'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="zone_id" id="zone_id" readonly>
                                    @if (@$farming_detail->farming->zone)
                                        <option value="{{ @$farming_detail->farming->zone_id }}">
                                            {{ @$farming_detail->farming->zone->name }}</option>
                                    @else
                                        <option value="">{{ __('Select Zone') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('center_id', __('Center'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="center_id" id="center_id" readonly>
                                    @if (@$farming_detail->farming->center)
                                        <option value="{{ @$farming_detail->farming->center_id }}">
                                            {{ @$farming_detail->farming->center->name }}</option>
                                    @else
                                        <option value="">{{ __('Select Center') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('plot_number', __('Plot Number'), ['class' => 'form-label']) }}
                            {{ Form::text('plot_number', $farming_detail->plot_number, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('zone_id', __('Can Field Zone'), ['class' => 'form-label']) }}
                                <select class="form-control select" id="can_field_zone_id" name="can_field_zone_id">
                                    <option value="">{{ __('Select Zone') }}</option>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}"
                                            {{ $farming_detail->can_field_zone_id == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('center_id', __('Can Field Center'), ['class' => 'form-label']) }}
                                <select class="form-control select" id="can_field_center_id" name="can_field_center_id">
                                    <option value="">{{ __('Select Center') }}</option>
                                    @foreach ($centers as $center)
                                        <option value="{{ $center->id }}"
                                            {{ $farming_detail->can_field_center_id == $center->id ? 'selected' : '' }}>
                                            {{ $center->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('village_id', __('Can Field Village'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="can_field_village_id" id="can_field_village_id"
                                    placeholder="Select Village">
                                    <option value="">{{ __('Select Village') }}</option>
                                    @foreach ($village as $villagee)
                                        <option value="{{ $villagee->id }}"
                                            {{ $farming_detail->can_field_village_id == $villagee->id ? 'selected' : '' }}>
                                            {{ $villagee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- <div class="form-group col-md-6">
                                                            {{ Form::label('kata_number', __('Kata Number'), ['class' => 'form-label']) }}
                                                            {{ Form::text('kata_number', $farming_detail->kata_number, ['class' => 'form-control', 'required' => 'required']) }}
                                                        </div> -->
                        <div class="form-group col-md-6">
                            {{ Form::label('area_in_acar', __('Area in acar'), ['class' => 'form-label']) }}
                            {{ Form::text('area_in_acar', $farming_detail->area_in_acar, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('date_of_harvesting', __('Date of Planting'), ['class' => 'form-label']) }}
                            {{ Form::date('date_of_harvesting', $farming_detail->date_of_harvesting, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <!-- <div class="form-group col-md-6">
                                                            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                                                            {{ Form::number('quantity', $farming_detail->quantity, ['class' => 'form-control', 'required' => 'required']) }}
                                                        </div> -->
                        {{-- <div class="form-group col-md-6">
                            {{ Form::label('tentative_harvest_quantity', __('Tentative Plant Quantity (In Ton)'), ['class' => 'form-label']) }}
                            {{ Form::number('tentative_harvest_quantity', $farming_detail->tentative_harvest_quantity, ['class' => 'form-control', 'required' => 'required']) }}
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('seed_category_id', __('Seed Variety'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="seed_category_id" id="seed_category_id" required
                                    placeholder="Select Seed Category">
                                    <option value="">{{ __('Select Seed Variety') }}</option>
                                    @foreach ($seed_categories as $seed_category)
                                        <option
                                            {{ $farming_detail->seed_category_id == $seed_category->id ? 'selected' : '' }}
                                            value="{{ $seed_category->id }}">{{ $seed_category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('type', __('Planting Type'), ['class' => 'form-label']) }} <br>
                            <input name="type" type="radio" value="Plant"
                                {{ $farming_detail->type == 'Plant' ? 'checked' : '' }}> Plant
                            <input name="type" type="radio" value="Ratun"
                                {{ $farming_detail->type == 'Ratun' ? 'checked' : '' }}> Ratoon
                        </div>
                        <div class="form-group col-md-6" id="planting_category">
                            {{ Form::label('type', __('Planting Category'), ['class' => 'form-label']) }}
                            @if ($farming_detail->type == 'Plant')
                                <select class="form-control select" id="plant_type">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="Plant"
                                        {{ $farming_detail->planting_category == 'Plant' ? 'selected' : '' }}>Plant
                                    </option>
                                    <option value="Seed"
                                        {{ $farming_detail->planting_category == 'Seed' ? 'selected' : '' }}>Seed</option>
                                </select>
                            @elseif($farming_detail->type == 'Ratun')
                                <select class="form-control select" id="ratun_type">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="R-1"
                                        {{ $farming_detail->planting_category == 'R-1' ? 'selected' : '' }}>R-1</option>
                                    <option value="R-2"
                                        {{ $farming_detail->planting_category == 'R-2' ? 'selected' : '' }}>R-2</option>
                                    <option value="R-3"
                                        {{ $farming_detail->planting_category == 'R-3' ? 'selected' : '' }}>R-3</option>
                                    <option value="R-4"
                                        {{ $farming_detail->planting_category == 'R-4' ? 'selected' : '' }}>R-4</option>
                                    <option value="R-5"
                                        {{ $farming_detail->planting_category == 'R-5' ? 'selected' : '' }}>R-5</option>
                                </select>
                            @endif
                        </div>
                        <div class="form-group col-md-6 irregation_fields">
                            {{ Form::label('irregation', __('Mode of Irregation'), ['class' => 'form-label']) }}
                            <select class="form-control select" name="irregation_mode" id="irregation_mode"
                                placeholder="Select Seed Category">
                                <option value="">{{ __('Select Irregation') }}</option>
                                <option value="Major Irrigation"
                                    {{ $farming_detail->irregation_mode == 'Major Irrigation' ? 'selected' : '' }}>Major
                                    Irrigation</option>
                                <option value="Medium Irrigation"
                                    {{ $farming_detail->irregation_mode == 'Medium Irrigation' ? 'selected' : '' }}>Medium
                                    Irrigation</option>
                                <option value="Minor Irrigation"
                                    {{ $farming_detail->irregation_mode == 'Minor Irrigation' ? 'selected' : '' }}>Minor
                                    Irrigation</option>
                                <option value="Bore Well"
                                    {{ $farming_detail->irregation_mode == 'Bore Well' ? 'selected' : '' }}>Bore Well
                                </option>
                                <option value="Other Irrigation"
                                    {{ $farming_detail->irregation_mode == 'Other Irrigation' ? 'selected' : '' }}>Other
                                    Irrigation</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('irregation', __('Irregation'), ['class' => 'form-label']) }}
                            <select class="form-control select" name="irregation" id="irregation">
                                <option value="">{{ __('Select Irregation') }}</option>
                                @foreach ($irrigations as $irrigation)
                                    <option value="{{ $irrigation->id }}"
                                        {{ $farming_detail->irregation == $irrigation->id ? 'selected' : '' }}>
                                        {{ $irrigation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.farming_detail.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
