@extends('layouts.master')
@section('title')
    {{ __('Plot Create') }}
@endsection
@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#plant_type').hide();
            $('#ratun_type').hide();
            $('#planting_category').hide();

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
            $("input[name=type]").on('click', function(){
                var type = $(this).val();
                console.log(type);
                if(type == "Plant"){
                    $("#ratun_type").removeAttr("name");
                    $('#planting_category').show();
                    $('#plant_type').show();
                    $('#plant_type').attr('name', "planting_category");
                    $('#ratun_type').hide();
                } else if (type == "Ratun"){
                    $("#plant_type").removeAttr("name");
                    $('#planting_category').show();
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
            <li class="breadcrumb-item"><a href="{{ route('admin.farmer.farming_detail.index') }}">{{ __('Plot') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Create') }}</li>
        </ol>
    </nav>

    <div class="row">
        {{ Form::open(['url' => 'admin/farmer/farming_detail', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="created_by" id="created_by" value="{{ Auth::user()->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Select Farmer'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required
                                    placeholder="Select Farmer">
                                    <option value="">{{ __('Select Farmer') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option value="{{ $farming->id }}">{{ $farming->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('block_id', __('Block'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="block_id" id="block_id"
                                    placeholder="Select Block" readonly>
                                    <option value="">{{ __('Select Block') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('gram_panchyat_id', __('Gram Panchyat'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="gram_panchyat_id" id="gram_panchyat_id"
                                    placeholder="Select Gram Panchyat" readonly>
                                    <option value="">{{ __('Select Gram Panchyat') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('village_id', __('Village'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="village_id" id="village_id"
                                    placeholder="Select Village" readonly>
                                    <option value="">{{ __('Select Village') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('zone_id', __('Zone'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="zone_id" id="zone_id" readonly
                                    placeholder="Select Country">
                                    <option value="">{{ __('Select Zone') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('center_id', __('Center'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="center_id" id="center_id"
                                    placeholder="Select Center" readonly>
                                    <option value="">{{ __('Select Center') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('plot_number', __('Plot Number'), ['class' => 'form-label']) }}
                            {{ Form::text('plot_number', $plot_number, ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('block_id', __('Can Field Block'), ['class' => 'form-label']) }}
                                <select class="form-control select" id="can_field_block_id" name="can_field_block_id">
                                    <option value="">{{ __('Select Block') }}</option>
                                    @foreach ($blocks as $block)
                                        <option value="{{ $block->id }}">{{ $block->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('gram_panchyat_id', __('Can Field Gram Panchyat'), ['class' => 'form-label']) }}
                                <select class="form-control select" id="can_field_gram_panchyat_id" name="can_field_gram_panchyat_id">
                                    <option value="">{{ __('Select Gram Panchyat') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('village_id', __('Can Field Village'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="can_field_village_id" id="can_field_village_id"
                                    placeholder="Select Village">
                                    <option value="">{{ __('Select Village') }}</option>
                                </select>
                            </div>
                        </div>
                        <!-- <div class="form-group col-md-6">
                                        {{ Form::label('kata_number', __('Khata Number'), ['class' => 'form-label']) }}
                                        {{ Form::text('kata_number', '', ['class' => 'form-control', 'required' => 'required']) }}
                                    </div> -->
                        <div class="form-group col-md-6">
                            {{ Form::label('area_in_acar', __('Area in acar'), ['class' => 'form-label']) }}
                            {{ Form::text('area_in_acar', '', ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('date_of_harvesting', __('Date of Planting'), ['class' => 'form-label']) }}
                            {{ Form::date('date_of_harvesting', '', ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <!-- <div class="form-group col-md-6">
                                        {{ Form::label('quantity', __('Quantity (In K.G)'), ['class' => 'form-label']) }}
                                        {{ Form::number('quantity', '', ['class' => 'form-control', 'required' => 'required']) }}
                                    </div> -->
                        {{-- <div class="form-group col-md-6">
                            {{ Form::label('tentative_harvest_quantity', __('Tentative Plant Quantity (In Ton)'), ['class' => 'form-label']) }}
                            {{ Form::number('tentative_harvest_quantity', '', ['class' => 'form-control', 'required' => 'required']) }}
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('seed_category_id', __('Seed Category'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="seed_category_id" id="seed_category_id" required
                                    placeholder="Select Seed Category">
                                    <option value="">{{ __('Select Seed Category') }}</option>
                                    @foreach ($seed_categories as $seed_category)
                                        <option value="{{ $seed_category->id }}">{{ $seed_category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('type', __('Planting Type'), ['class' => 'form-label']) }} <br>
                            <input name="type" type="radio" value="Plant"> Plant
                            <input name="type" type="radio" value="Ratun"> Ratun
                        </div>
                        <div class="form-group col-md-6" id="planting_category">
                            {{ Form::label('type', __('Planting Category'), ['class' => 'form-label']) }}
                            <select class="form-control select" id="plant_type">
                                <option value="">{{ __('Select') }}</option>
                                <option value="Plant">Plant</option>
                                <option value="Seed">Seed</option>
                            </select>
                            <select class="form-control select" id="ratun_type">
                                <option value="">{{ __('Select') }}</option>
                                <option value="R-1">R-1</option>
                                <option value="R-2">R-2</option>
                                <option value="R-3">R-3</option>
                                <option value="R-4">R-4</option>
                                <option value="R-5">R-5</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.farming_detail.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
