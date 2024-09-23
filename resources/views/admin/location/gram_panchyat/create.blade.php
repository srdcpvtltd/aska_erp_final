@extends('layouts.master')
@section('title')
    {{ __('Create GP (Gram Panchyat)') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('GP (Gram Panchyat)') }}</li>
            <li class="breadcrumb-item">{{ __('Create') }}</li>
        </ol>
        <div class="float-end">
            <a href="{{ route('admin.location.gram_panchyat.index') }}" class="btn btn-primary">
                Back
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                {{ Form::open(['route' => 'admin.location.gram_panchyat.store', 'class' => 'w-100']) }}
                <div class="card-body">
                    {{-- start for ai module --}}
                    @php
                        $settings = \App\Models\Utility::settings();
                    @endphp
                    @if ($settings['ai_chatgpt_enable'] == 'on')
                        <div class="text-end">
                            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm"
                                data-ajax-popup-over="true" data-url="{{ route('generate', ['warehouse']) }}"
                                data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
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
                            {{ Form::label('block_id', __('Block'), ['class' => 'form-label']) }}<span
                                class="text-danger">*</span>
                            {{ Form::select('block_id', $blocks, null, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.location.gram_panchyat.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
