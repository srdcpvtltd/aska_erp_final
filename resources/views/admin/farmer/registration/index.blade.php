@extends('layouts.master')
@section('title')
    {{ __('Farmer Registration') }}
@endsection
@section('styles')
    @include('layouts.datatables_css')
@endsection
@section('scripts')
    @include('layouts.datatables_js')
    {{ $dataTable->scripts() }}
    <script>
        const table = $('#farmings-table');

        table.on('preXhr.dt', function(e, settings, data) {
            data.block_id = $('#block_id').val();
            data.grampanchyat_id = $('#grampanchyat_id').val();
            data.village_id = $('#village_id').val();
            data.zone_id = $('#zone_id').val();
            data.center_id = $('#center_id').val();
        });

        $('#filter').on('click', function() {
            table.DataTable().ajax.reload();
            return false;
        });
        $('#reset').on('click', function() {
            table.on('preXhr.dt', function(e, settings, data) {
                data.block_id = '';
                data.grampanchyat_id = '';
                data.village_id = '';
                data.zone_id = '';
                data.center_id = '';
            });

            table.DataTable().ajax.reload();
            return false;
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
                    $('#grampanchyat_id').empty();
                    $('#grampanchyat_id').append(
                        '<option  value="">Select Gram Panchyat</option>');
                    for (i = 0; i < gram_panchyats.length; i++) {
                        $('#grampanchyat_id').append('<option value="' + gram_panchyats[i]
                            .id + '">' + gram_panchyats[i].name + '</option>');
                    }
                }
            });
        });
        $('#grampanchyat_id').change(function() {
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
            <li class="breadcrumb-item">{{ __('Farmer Registration') }}</li>
        </ol>
        <div class="float-end">
            @can('create-farmer_registration')
                <a href="{{ route('admin.farmer.farming_registration.create') }}" class="btn btn-primary">
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
                                <div class="form-group col-md-2">
                                    {{ Form::label('block_id', __('Block'), ['class' => 'form-label']) }}
                                    {{ Form::select('block_id', $blocks, null, ['class' => 'form-control select']) }}
                                </div>
                                <div class="form-group col-md-2">
                                    {{ Form::label('grampanchyat_id', __('Grampanchyat'), ['class' => 'form-label']) }}
                                    {{ Form::select('grampanchyat_id', ['' => 'Select Gram Panchyat'], null, ['class' => 'form-control select']) }}
                                </div>
                                <div class="form-group col-md-2">
                                    {{ Form::label('village_id', __('Village'), ['class' => 'form-label']) }}
                                    {{ Form::select('village_id', ['' => 'Select Village'], null, ['class' => 'form-control select']) }}
                                </div>
                                <div class="form-group col-md-2">
                                    {{ Form::label('zone_id', __('Zone'), ['class' => 'form-label']) }}
                                    {{ Form::select('zone_id', $zones, null, ['class' => 'form-control select']) }}
                                </div>
                                <div class="form-group col-md-2">
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
@endsection
