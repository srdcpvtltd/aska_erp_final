@extends('layouts.master')
@section('title')
    {{ __('Farmer Seed Category Edit') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('admin.farmer.seed_category.index') }}">{{ __('Seed Category') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Seed Category Edit') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::model($seedCategory, ['route' => ['admin.farmer.seed_category.update', $seedCategory->id], 'method' => 'PUT']) }}
        <div class="modal-body">
            {{-- start for ai module --}}
            @php
                $settings = \App\Models\Utility::settings();
            @endphp
            @if ($settings['ai_chatgpt_enable'] == 'on')
                <div class="text-end">
                    <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true"
                        data-url="{{ route('generate', ['country']) }}" data-bs-placement="top"
                        data-title="{{ __('Generate content with AI') }}">
                        <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
                    </a>
                </div>
            @endif

            <div class="row">
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
                    {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}
                    {{ Form::text('category', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
                <div class="form-group col-md-6">
                    {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
                    <select name="type" id="type" class="form-control" required>
                        <option value="">Select</option>
                        <option value="EARLY" {{ ($seedCategory->type === "EARLY") ? 'selected':'' }}>EARLY</option>
                        <option value="MID" {{ ($seedCategory->type === "MID") ? 'selected':'' }}>MID</option>
                        <option value="LATE" {{ ($seedCategory->type === "LATE") ? 'selected':'' }}>LATE</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="{{ route('admin.farmer.seed_category.index') }}">
                <input type="button" value="{{ __('Back') }}" class="btn  btn-light">
            </a>
            <input type="submit" value="{{ __('Save') }}" class="btn  btn-primary">
        </div>
        {{ Form::close() }}
    </div>
@endsection
