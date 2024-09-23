@extends('layouts.master')
@section('title')
    {{ __('Create Village') }}
@endsection
@section('scripts')
<script>
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
            <li class="breadcrumb-item">{{ __('Village') }}</li>
            <li class="breadcrumb-item">{{ __('Create') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.location.village.create') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                {{ Form::open(['route' => 'admin.location.village.store', 'class' => 'w-100']) }}
                <div class="card-body">
                    {{-- start for ai module --}}
                    @php
                        $settings = \App\Models\Utility::settings();
                    @endphp
                    @if ($settings['ai_chatgpt_enable'] == 'on')
                        <div class="text-end">
                            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm"
                                data-ajax-popup-over="true" data-url="{{ route('generate', ['village']) }}"
                                data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                                <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
                            </a>
                        </div>
                    @endif
                    {{-- end for ai module --}}
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('gram_panchyat_id', __('Gram Panchyat'), ['class' => 'form-label']) }}<span
                                class="text-danger">*</span>
                            {{ Form::select('gram_panchyat_id', $gram_panchyats, null, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                            {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required']) }}
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
                    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
