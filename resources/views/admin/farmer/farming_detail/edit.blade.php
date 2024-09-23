@extends('layouts.master')
@section('title')
    {{ __('Edit Plot') }}
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {

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
            $('#can_field_block_id').change(function() {
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
                        $('#can_field_gram_panchyat_id').empty();
                        $('#can_field_gram_panchyat_id').append(
                            '<option  value="">Select Gram Panchyat</option>');
                        for (i = 0; i < gram_panchyats.length; i++) {
                            $('#can_field_gram_panchyat_id').append('<option value="' +
                                gram_panchyats[i]
                                .id + '">' + gram_panchyats[i].name + '</option>');
                        }
                    }
                });
            });
            $('#can_field_gram_panchyat_id').change(function() {
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
        });
    </script>
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('admin.farmer.farming_detail.index') }}">{{ __('Plot') }}</a></li>
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
                                <select class="form-control select" name="block_id" id="block_id"
                                    placeholder="Select Block" readonly>
                                    @if ($farming_detail->block)
                                        <option value="{{ $farming_detail->block_id }}">{{ $farming_detail->block->name }}
                                        </option>
                                    @else
                                        @if (@$farming_detail->farming->block)
                                            <option value="{{ @$farming_detail->farming->block_id }}">
                                                {{ @$farming_detail->farming->block->name }}</option>
                                        @else
                                            <option value="">{{ __('Select Block') }}</option>
                                        @endif
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('gram_panchyat_id', __('Gram Panchyat'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="gram_panchyat_id" id="gram_panchyat_id"
                                    placeholder="Select Gram Panchyat" readonly>
                                    @if ($farming_detail->gram_panchyat)
                                        <option value="{{ $farming_detail->gram_panchyat_id }}">
                                            {{ $farming_detail->gram_panchyat->name }}</option>
                                    @else
                                        @if (@$farming_detail->farming->gram_panchyat)
                                            <option value="{{ @$farming_detail->farming->gram_panchyat_id }}">
                                                {{ @$farming_detail->farming->gram_panchyat->name }}</option>
                                        @else
                                            <option value="">{{ __('Select Gram Panchyat') }}</option>
                                        @endif
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('village_id', __('Village'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="village_id" id="village_id"
                                    placeholder="Select Village" readonly>
                                    @if ($farming_detail->village)
                                        <option value="{{ $farming_detail->village_id }}">
                                            {{ $farming_detail->village->name }}</option>
                                    @else
                                        @if (@$farming_detail->farming->village)
                                            <option value="{{ @$farming_detail->farming->village_id }}">
                                                {{ @$farming_detail->farming->village->name }}</option>
                                        @else
                                            <option value="">{{ __('Select Village') }}</option>
                                        @endif
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('zone_id', __('Zone'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="zone_id" id="zone_id" readonly
                                    placeholder="Select Country">
                                    @if ($farming_detail->zone)
                                        <option value="{{ $farming_detail->zone_id }}">{{ $farming_detail->zone->name }}
                                        </option>
                                    @else
                                        @if (@$farming_detail->farming->zone)
                                            <option value="{{ @$farming_detail->farming->zone_id }}">
                                                {{ @$farming_detail->farming->zone->name }}</option>
                                        @else
                                            <option value="">{{ __('Select Zone') }}</option>
                                        @endif
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('center_id', __('Center'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="center_id" id="center_id"
                                    placeholder="Select Center" readonly>
                                    @if ($farming_detail->center)
                                        <option value="{{ $farming_detail->center_id }}">
                                            {{ $farming_detail->center->name }}</option>
                                    @else
                                        @if (@$farming_detail->farming->center)
                                            <option value="{{ @$farming_detail->farming->center_id }}">
                                                {{ @$farming_detail->farming->center->name }}</option>
                                        @else
                                            <option value="">{{ __('Select Center') }}</option>
                                        @endif
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
                                {{ Form::label('block_id', __('Can Field Block'), ['class' => 'form-label']) }}
                                <select class="form-control select" id="can_field_block_id" name="can_field_block_id">
                                    <option value="">{{ __('Select Block') }}</option>
                                    @foreach ($blocks as $block)
                                        <option value="{{ $block->id }}" {{ ($farming_detail->can_field_block_id == $block->id) ? 'selected' : '' }}>{{ $block->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('gram_panchyat_id', __('Can Field Gram Panchyat'), ['class' => 'form-label']) }}
                                <select class="form-control select" id="can_field_gram_panchyat_id" name="can_field_gram_panchyat_id">
                                    <option value="">{{ __('Select Gram Panchyat') }}</option> 
                                    @foreach ($gp as $GP)
                                        <option value="{{ $GP->id }}" {{ ($farming_detail->can_field_gram_panchyat_id == $GP->id) ? 'selected' : '' }}>{{ $GP->name }}</option>
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
                                        <option value="{{ $villagee->id }}" {{ ($farming_detail->can_field_village_id == $villagee->id) ? 'selected' : '' }}>{{ $villagee->name }}</option>
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
                                {{ Form::label('seed_category_id', __('Seed Category'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="seed_category_id" id="seed_category_id" required
                                    placeholder="Select Seed Category">
                                    <option value="">{{ __('Select Seed Category') }}</option>
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
                            <input name="type" type="radio" value="Plant" {{ ($farming_detail->type == "Plant") ? 'checked':'' }}> Plant
                            <input name="type" type="radio" value="Ratun" {{ ($farming_detail->type == "Ratun") ? 'checked':'' }}> Ratun
                        </div>
                        <div class="form-group col-md-6" id="planting_category">
                            {{ Form::label('type', __('Planting Category'), ['class' => 'form-label']) }}
                            @if($farming_detail->type == "Plant")
                            <select class="form-control select" id="plant_type">
                                <option value="">{{ __('Select') }}</option>
                                <option value="Plant" {{ ($farming_detail->planting_category == "Plant") ? 'selected':'' }}>Plant</option>
                                <option value="Seed" {{ ($farming_detail->planting_category == "Seed") ? 'selected':'' }}>Seed</option>
                            </select>
                            @elseif($farming_detail->type == "Ratun")
                            <select class="form-control select" id="ratun_type">
                                <option value="">{{ __('Select') }}</option>
                                <option value="R-1" {{ ($farming_detail->planting_category == "R-1") ? 'selected':'' }}>R-1</option>
                                <option value="R-2" {{ ($farming_detail->planting_category == "R-2") ? 'selected':'' }}>R-2</option>
                                <option value="R-3" {{ ($farming_detail->planting_category == "R-3") ? 'selected':'' }}>R-3</option>
                                <option value="R-4" {{ ($farming_detail->planting_category == "R-4") ? 'selected':'' }}>R-4</option>
                                <option value="R-5" {{ ($farming_detail->planting_category == "R-5") ? 'selected':'' }}>R-5</option>
                            </select>
                            @endif
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
