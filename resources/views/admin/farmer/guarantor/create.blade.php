@extends('layouts.master')
@section('title')
    {{ __('Farmer Guarantor Create') }}
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
                        guarentor = response.guarentor;
                        $('#registration_number').empty();
                        $('#registration_number').val(registration_number);
                        $('#name').empty();
                        $('#name').append('<option  value="">Select Guarantor Name</option>');
                        for (i = 0; i < guarentor.length; i++) {
                            $('#name').append('<option value="' + guarentor[i].id + '">' +
                                guarentor[i].name + '</option>');
                        }
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
            <li class="breadcrumb-item">{{ __('Farmer Guarantor Create') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::open(['url' => 'admin/farmer/guarantor', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="created_by" id="created_by" value="{{ Auth::user()->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Farmer Registration'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required>
                                    <option value="">{{ __('Select Farmer Registration') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option value="{{ $farming->id }}">{{ $farming->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                            <select class="form-control select" name="name" id="name" required>
                                <option value="">{{ __('Select Guarantor Name') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('father_name', __('Father Name'), ['class' => 'form-label']) }}
                            {{ Form::text('father_name', '', ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('country_id', __('Country'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="country_id" id="country_id" required>
                                    <option value="">{{ __('Select Country') }}</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('state_id', __('State'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="state_id" id="state_id" required>
                                    <option value="">{{ __('Select State') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('district_id', __('District'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="district_id" id="district_id" required>
                                    <option value="">{{ __('Select District') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('block_id', __('Block'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="block_id" id="block_id" required>
                                    <option value="">{{ __('Select Block') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('gram_panchyat_id', __('Gram Panchyat'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="gram_panchyat_id" id="gram_panchyat_id" required>
                                    <option value="">{{ __('Select Gram Panchyat') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('village_id', __('Village'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="village_id" id="village_id" required>
                                    <option value="">{{ __('Select Village') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('post_office', __('Post Office'), ['class' => 'form-label']) }}
                            {{ Form::text('post_office', '', ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('police_station', __('Police Station'), ['class' => 'form-label']) }}
                            {{ Form::text('police_station', '', ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('registration_number', __('Registration No.'), ['class' => 'form-label']) }}
                            {{ Form::text('registration_number', '', ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('age', __('Age'), ['class' => 'form-label']) }}
                            {{ Form::number('age', '', ['class' => 'form-control', 'required' => 'required', 'readonly']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.guarantor.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
