@extends('layouts.master')
@section('title')
    {{ __('Edit Farmer Registration') }}
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#country_id').change(function() {
                let country_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.location.get_states') }}",
                    method: 'post',
                    data: {
                        country_id: country_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        states = response.states;
                        $('#state_id').empty();
                        $('#state_id').append('<option value="">Select State</option>');
                        for (i = 0; i < states.length; i++) {
                            $('#state_id').append('<option value="' + states[i].id + '">' +
                                states[i].name + '</option>');
                        }
                    }
                });
            });
            $('#state_id').change(function() {
                let state_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.location.get_districts') }}",
                    method: 'post',
                    data: {
                        state_id: state_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        districts = response.districts;
                        $('#district_id').empty();
                        $('#district_id').append('<option  value="">Select District</option>');
                        for (i = 0; i < districts.length; i++) {
                            $('#district_id').append('<option value="' + districts[i].id +
                                '">' + districts[i].name + '</option>');
                        }
                    }
                });
            });
            $('#district_id').change(function() {
                let district_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.location.get_blocks') }}",
                    method: 'post',
                    data: {
                        district_id: district_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        blocks = response.blocks;
                        $('#block_id').empty();
                        $('#block_id').append('<option  value="">Select Blocks</option>');
                        for (i = 0; i < blocks.length; i++) {
                            $('#block_id').append('<option value="' + blocks[i].id + '">' +
                                blocks[i].name + '</option>');
                        }
                    }
                });
            });
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
                        $('#gram_panchyat_id').empty();
                        $('#gram_panchyat_id').append(
                            '<option  value="">Select Gram Panchyat</option>');
                        for (i = 0; i < gram_panchyats.length; i++) {
                            $('#gram_panchyat_id').append('<option value="' + gram_panchyats[i]
                                .id + '">' + gram_panchyats[i].name + '</option>');
                        }
                    }
                });
            });
            $('#gram_panchyat_id').change(function() {
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
            $('#village_id').change(function() {
                let village_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.location.get_zone_center') }}",
                    method: 'post',
                    data: {
                        village_id: village_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        zone = response.zone;
                        center = response.center;
                        $('#zone_id').val(zone.id);
                        $('#zone_name').val(zone.name);
                        $('#center_id').val(center.id);
                        $('#center_name').val(center.name);
                    }
                });
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
            $('input[type=radio][name="land_type"]').on('change', function(event) {
                var value = $(this).val();
                if (value == "Leased Land") {
                    $('#land_holding_fields').hide();
                } else {
                    $('#land_holding_fields').show();
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
                    href="{{ route('admin.farmer.farming_registration.index') }}">{{ __('Farmer Registration') }}</a></li>
            <li class="breadcrumb-item">{{ __('Edit Farmer Registration') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::model($farming, ['route' => ['admin.farmer.farming_registration.update', $farming->id], 'method' => 'PUT', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                            {{ Form::text('name', $farming->name, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('father_name', __('Father / Husband Name'), ['class' => 'form-label']) }}
                            {{ Form::text('father_name', $farming->father_name, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('mobile', __('Mobile'), ['class' => 'form-label']) }}
                            {{ Form::number('mobile', $farming->mobile, ['class' => 'form-control']) }}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('country_id', __('Country'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="country_id" id="country_id" required
                                    placeholder="Select Country">
                                    <option value="">{{ __('Select Country') }}</option>
                                    @foreach ($countries as $country)
                                        <option {{ $farming->country_id == $country->id ? 'selected' : '' }}
                                            value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('state_id', __('State'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="state_id" id="state_id"
                                    placeholder="Select State" required>
                                    <option value="">{{ __('Select State') }}</option>
                                    @foreach ($states as $state)
                                        <option {{ $farming->state_id == $state->id ? 'selected' : '' }}
                                            value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('district_id', __('District'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="district_id" id="district_id"
                                    placeholder="Select District" required>
                                    <option value="">{{ __('Select District') }}</option>
                                    @foreach ($districts as $district)
                                        <option {{ $farming->district_id == $district->id ? 'selected' : '' }}
                                            value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('block_id', __('Block'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="block_id" id="block_id"
                                    placeholder="Select Block" required>
                                    <option value="">{{ __('Select Block') }}</option>
                                    @foreach ($blocks as $block)
                                        <option {{ $farming->block_id == $block->id ? 'selected' : '' }}
                                            value="{{ $block->id }}">{{ $block->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('gram_panchyat_id', __('Gram Panchyat'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="gram_panchyat_id" id="gram_panchyat_id"
                                    placeholder="Select Gram Panchyat" required>
                                    <option value="">{{ __('Select Gram Panchyat') }}</option>
                                    @foreach ($gram_panchyats as $gram_panchyat)
                                        <option {{ $farming->gram_panchyat_id == $gram_panchyat->id ? 'selected' : '' }}
                                            value="{{ $gram_panchyat->id }}">{{ $gram_panchyat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('village_id', __('Village'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="village_id" id="village_id"
                                    placeholder="Select Village" required>
                                    <option value="">{{ __('Select Village') }}</option>
                                    @foreach ($villages as $village)
                                        <option {{ $farming->village_id == $village->id ? 'selected' : '' }}
                                            value="{{ $village->id }}">{{ $village->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('post_office', __('Post Office'), ['class' => 'form-label']) }}
                            {{ Form::text('post_office', $farming->post_office, ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('police_station', __('Police Station'), ['class' => 'form-label']) }}
                            {{ Form::text('police_station', $farming->police_station, ['class' => 'form-control']) }}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('zone_id', __('Zone'), ['class' => 'form-label']) }}
                                {{ Form::hidden('zone_id', $farming->zone_id, ['class' => 'form-control', 'required' => 'required']) }}
                                {{ Form::text('', $farming->zone->name, ['class' => 'form-control', 'required' => 'required', 'id' => 'zone_name', 'readonly']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('center_id', __('Center'), ['class' => 'form-label']) }}
                                {{ Form::hidden('center_id', $farming->center_id, ['class' => 'form-control', 'required' => 'required']) }}
                                {{ Form::text('', $farming->center->name, ['class' => 'form-control', 'required' => 'required', 'id' => 'center_name', 'readonly']) }}
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('age', __('Age'), ['class' => 'form-label']) }}
                            {{ Form::number('age', $farming->age, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('gender', __('Gender'), ['class' => 'form-label']) }}
                            <select class="form-control select" name="gender" id="gender" placeholder="Select Gender">
                                <option value="">{{ __('Select Gender') }}</option>
                                <option {{ $farming->gender == 'Male' ? 'selected' : '' }} value="Male">
                                    {{ __('Male') }}</option>
                                <option {{ $farming->gender == 'Female' ? 'selected' : '' }} value="Female">
                                    {{ __('Female') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('qualification', __('Qualification'), ['class' => 'form-label']) }}
                            <select class="form-control select" name="qualification" id="qualification"
                                placeholder="Select Qualification">
                                <option value="">{{ __('Select Qualification') }}</option>
                                <option {{ $farming->qualification == '10th Standard' ? 'selected' : '' }}
                                    value="10th Standard">{{ __('10th Standard') }}</option>
                                <option {{ $farming->qualification == '12th Standard' ? 'selected' : '' }}
                                    value="12th Standard">{{ __('12th Standard') }}</option>
                                <option {{ $farming->qualification == 'Bachelor Degree' ? 'selected' : '' }}
                                    value="Bachelor Degree">{{ __('Bachelor Degree') }}</option>
                                <option {{ $farming->qualification == 'Master Degree' ? 'selected' : '' }}
                                    value="Master Degree">{{ __('Master Degree') }}</option>
                                <option {{ $farming->qualification == 'PHD' ? 'selected' : '' }} value="PHD">
                                    {{ __('PHD') }}</option>
                                 <option {{ $farming->qualification == 'N/A' ? 'selected' : '' }} value="N/A">
                                    {{ __('N/A') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('farmer id', __('Farmer ID'), ['class' => 'form-label']) }}
                            <input type="text" name="farmer_id_2" class="form-control"
                                value="{{ $farming->farmer_id_2 }}">
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('farmer category', __('Farmer Category'), ['class' => 'form-label']) }}
                            <select class="form-control select" name="farmer_category" id="farmer_category"
                                placeholder="Select Qualification">
                                <option value="">{{ __('Select Category') }}</option>
                                <option {{ $farming->farmer_category == 'NORMAL' ? 'selected' : '' }} value="NORMAL">
                                    NORMAL</option>
                                <option {{ $farming->farmer_category == 'SCP' ? 'selected' : '' }} value="SCP">
                                    SCP</option>
                                <option {{ $farming->farmer_category == 'TASP' ? 'selected' : '' }} value="TASP">TASP
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('registration_year', __('Registration Year'), ['class' => 'form-label']) }}
                            {{ Form::text('registration_year', null, ['class' => 'form-control', 'minlength' => '4', 'maxlength' => '4']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('land_type', __('Land Type'), ['class' => 'form-label']) }}
                            <br>
                            <label><input type="radio" name="land_type" value="Leased Land"
                                    {{ $farming->land_type == 'Leased Land' ? 'checked' : '' }}> Leased Land</label>
                            <label><input type="radio" name="land_type" value="Owned Land"
                                    {{ $farming->land_type == 'Owned Land' ? 'checked' : '' }}> Owned Land</label>
                        </div>
                        @if ($farming->land_type == 'Owned Land')
                            <div class="form-group col-md-6" id="land_holding_fields">
                                {{ Form::label('land_holding', __('Land Holding (In Acre)'), ['class' => 'form-label']) }}
                                {{ Form::number('land_holding', $farming->land_holding, ['class' => 'form-control', 'step' => '0.01']) }}
                            </div>
                        @endif
                        <div class="form-group col-md-6">
                            {{ Form::label('offered_area', __('Offered Area'), ['class' => 'form-label']) }}
                            {{ Form::text('offered_area', $farming->offered_area, ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6 irregation_fields">
                            {{ Form::label('irregation', __('Mode of Irregation'), ['class' => 'form-label']) }}
                            <select class="form-control select" name="irregation_mode" id="irregation_mode"
                                placeholder="Select Seed Category">
                                <option value="">{{ __('Select Irregation') }}</option>
                                <option value="Major Irrigation"
                                    {{ $farming->irregation_mode == 'Major Irrigation' ? 'selected' : '' }}>Major
                                    Irrigation</option>
                                <option value="Medium Irrigation"
                                    {{ $farming->irregation_mode == 'Medium Irrigation' ? 'selected' : '' }}>Medium
                                    Irrigation</option>
                                <option value="Minor Irrigation"
                                    {{ $farming->irregation_mode == 'Minor Irrigation' ? 'selected' : '' }}>Minor
                                    Irrigation</option>
                                    <option value="Bore Well"
                                    {{ $farming->irregation_mode == 'Bore Well' ? 'selected' : '' }}>Bore Well</option>
                                <option value="Other Irrigation"
                                    {{ $farming->irregation_mode == 'Other Irrigation' ? 'selected' : '' }}>Other
                                    Irrigation</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 irregation_fields">
                            {{ Form::label('irregation', __('Irregation'), ['class' => 'form-label']) }}
                            <select class="form-control select" name="irregation" id="irregation"
                                placeholder="Select Seed Category">
                                <option value="">{{ __('Select Irregation') }}</option>
                                @foreach ($irrigations as $irrigation)
                                    <option value="{{ $irrigation->id }}"
                                        {{ $farming->irregation == $irrigation->id ? 'selected' : '' }}>
                                        {{ $irrigation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            {{ Form::label('language', __('Language'), ['class' => 'form-label']) }}
                            <br>
                            <label><input type="radio" name="language" value="Hindi"
                                    {{ $farming->language == 'Hindi' ? 'checked' : '' }}> Hindi</label>
                            <label><input type="radio" name="language" value="English"
                                    {{ $farming->language == 'English' ? 'checked' : '' }}> English</label>
                        </div>
                        <div class="form-group col-md-2">
                            {{ Form::label('sms_mode', __('Sms Mode'), ['class' => 'form-label']) }}
                            <br>
                            <label><input type="radio" name="sms_mode" value="Text"
                                    {{ $farming->sms_mode == 'Text' ? 'checked' : '' }}> Text</label>
                            <label><input type="radio" name="sms_mode" value="Voice"
                                    {{ $farming->sms_mode == 'Voice' ? 'checked' : '' }}> Voice</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.farming_registration.index') }}';"
                    class="btn btn-light">
                <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
