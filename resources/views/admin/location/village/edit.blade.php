@extends('layouts.master')
@section('title')
    {{ __('Edit village') }}
@endsection
@section('scripts')
    <script>
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
            <li class="breadcrumb-item">{{ __('village') }}</li>
            <li class="breadcrumb-item">{{ __('Edit') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.location.village.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                {{ Form::model($village, ['route' => ['admin.location.village.update', $village->id], 'method' => 'PUT']) }}
                <div class="card-body">
                    {{-- start for ai module --}}
                    @php
                        $settings = \App\Models\Utility::settings();
                    @endphp
                    @if ($settings['ai_chatgpt_enable'] == 'on')
                        <div class="text-end">
                            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm"
                                data-ajax-popup-over="true" data-url="{{ route('generate', ['gram_panchyat']) }}"
                                data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                                <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
                            </a>
                        </div>
                    @endif
                    {{-- end for ai module --}}
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('district_id', __('District'), ['class' => 'form-label']) }}<span
                                class="text-danger">*</span>
                            {{ Form::select('district_id', $districts, $dstct->district_id, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('block_id', __('Block'), ['class' => 'form-label']) }}<span
                                class="text-danger">*</span>
                            {{ Form::select('block_id', $blocks, $blk->block_id, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('gram_panchyat_id', __('Gram Panchyat'), ['class' => 'form-label']) }}<span
                                class="text-danger">*</span>
                            {{ Form::select('gram_panchyat_id', $grampanchyats, null, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
                            @error('name')
                                <small class="invalid-name" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                </small>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('zone_id', __('Zone'), ['class' => 'form-label']) }}<span
                                class="text-danger">*</span>
                            {{ Form::select('zone_id', $zones, null, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('center_id', __('Center'), ['class' => 'form-label']) }}<span
                                class="text-danger">*</span>
                            {{ Form::select('center_id', $centers, null, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.location.village.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
