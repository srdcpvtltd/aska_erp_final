@extends('layouts.master')
@section('title')
    {{ __('Edit Farmer Guarantor') }}
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#farming_id').change(function() {
                let farmer_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.registration_id') }}",
                    method: 'post',
                    data: {
                        farmer_id: farmer_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        registration_number = response.registration_id;
                        $('#registration_number').empty();
                        $('#registration_number').val(registration_number);
                    }
                });
            });
            $('#name').change(function() {
                let farming_id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.loan.get_farming_detail') }}",
                    method: 'post',
                    data: {
                        farming_id: farming_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log(response);
                        var country_id = response.farming.country_id;
                        var state_id = response.farming.state_id;
                        var district_id = response.farming.district_id;
                        var block_id = response.farming.block_id;
                        var gram_panchyat_id = response.farming.gram_panchyat_id;
                        var village_id = response.farming.village_id;
                        $.ajax({
                            url: "{{ route('admin.farmer.location.get_country_state') }}",
                            method: 'post',
                            data: {
                                country_id: country_id,
                                state_id: state_id,
                                district_id: district_id,
                                block_id: block_id,
                                gram_panchyat_id: gram_panchyat_id,
                                village_id: village_id
                            },
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                country = response.country;
                                state = response.state;
                                district = response.district;
                                block = response.block;
                                gram_panchyat = response.gram_panchyat;
                                village = response.village;

                                $('#country_id').empty();
                                $('#country_id').append('<option value="' +
                                    country_id + '">' + country + '</option>');

                                $('#state_id').empty();
                                $('#state_id').append('<option value="' + state_id +
                                    '">' + state + '</option>');

                                $('#district_id').empty();
                                $('#district_id').append('<option value="' +
                                    district_id + '">' + district + '</option>');

                                $('#block_id').empty();
                                $('#block_id').append('<option value="' + block_id +
                                    '">' + block + '</option>');

                                $('#gram_panchyat_id').empty();
                                $('#gram_panchyat_id').append('<option value="' +
                                    gram_panchyat_id + '">' + gram_panchyat +
                                    '</option>');

                                $('#village_id').empty();
                                $('#village_id').append('<option value="' +
                                    village_id + '">' + village + '</option>');

                            }
                        });
                        $('#father_name').val(response.farming.father_name);
                        $('#post_office').val(response.farming.post_office);
                        $('#police_station').val(response.farming.police_station);
                        $('#age').val(response.farming.age);
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
            <li class="breadcrumb-item"><a
                    href="{{ route('admin.farmer.guarantor.index') }}">{{ __('Farmer Guarantor') }}</a></li>
            <li class="breadcrumb-item">{{ __('Edit Farmer Guarantor') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::model($guarantor, ['route' => ['admin.farmer.guarantor.update', $guarantor->id], 'method' => 'PUT', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Farmer Registration'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required>
                                    <option value="">{{ __('Select Farmer Registration') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option {{ $farming->id == $guarantor->farming_id ? 'selected' : '' }}
                                            value="{{ $farming->id }}">{{ $farming->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                            <select class="form-control select" name="name" id="name" required>
                                <option value="">{{ __('Select Farmer Registration') }}</option>
                                @foreach ($farmings as $farming)
                                    <option {{ $farming->id == $guarantor->name ? 'selected' : '' }}
                                        value="{{ $farming->id }}">{{ $farming->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('father_name', __('Father Name'), ['class' => 'form-label']) }}
                            {{ Form::text('father_name', $guarantor->father_name, ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('country_id', __('Country'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="country_id" id="country_id" required>
                                    <option value="">{{ __('Select Country') }}</option>
                                    @foreach ($countries as $country)
                                        <option {{ $guarantor->country_id == $country->id ? 'selected' : '' }}
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
                                        <option {{ $guarantor->state_id == $state->id ? 'selected' : '' }}
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
                                        <option {{ $guarantor->district_id == $district->id ? 'selected' : '' }}
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
                                        <option {{ $guarantor->block_id == $block->id ? 'selected' : '' }}
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
                                        <option {{ $guarantor->gram_panchyat_id == $gram_panchyat->id ? 'selected' : '' }}
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
                                        <option {{ $guarantor->village_id == $village->id ? 'selected' : '' }}
                                            value="{{ $village->id }}">{{ $village->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('post_office', __('Post Office'), ['class' => 'form-label']) }}
                            {{ Form::text('post_office', $guarantor->post_office, ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('police_station', __('Police Station'), ['class' => 'form-label']) }}
                            {{ Form::text('police_station', $guarantor->police_station, ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('registration_number', __('Registration No.'), ['class' => 'form-label']) }}
                            {{ Form::text('registration_number', $guarantor->registration_number, ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('age', __('Age'), ['class' => 'form-label']) }}
                            {{ Form::number('age', $guarantor->age, ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.guarantor.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
