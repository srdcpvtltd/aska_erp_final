@extends('layouts.master')
@section('title')
    {{ __('Farmer Seed Category Create') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('admin.farmer.seed_category.index') }}">{{ __('Farmer Seed Category') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Farmer Seed Category Create') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::open(['url' => 'admin/farmer/seed_category']) }}
        <div class="modal-body">
            {{-- start for ai module --}}
            @php
                $settings = \App\Models\Utility::settings();
            @endphp
            @if ($settings['ai_chatgpt_enable'] == 'on')
                <div class="text-end">
                    <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true"
                        data-url="{{ route('generate', ['warehouse']) }}" data-bs-placement="top"
                        data-title="{{ __('Generate content with AI') }}">
                        <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
                    </a>
                </div>
            @endif
            {{-- end for ai module --}}
            <div class="row">
                <div class="form-group col-md-6">
                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                    {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required']) }}
                </div>
                <div class="form-group col-md-6">
                    {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}
                    {{ Form::text('category', '', ['class' => 'form-control', 'required' => 'required']) }}
                </div>
                <div class="form-group col-md-6">
                    {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
                    <select name="type" id="type" class="form-control" required>
                        <option value="">Select</option>
                        <option value="EARLY">EARLY</option>
                        <option value="MID">MID</option>
                        <option value="LATE">LATE</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="{{ route('admin.farmer.seed_category.index') }}">
                <input type="button" value="{{ __('Back') }}" class="btn  btn-light">
            </a>
            <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
        </div>
        {{ Form::close() }}
    </div>
@endsection
