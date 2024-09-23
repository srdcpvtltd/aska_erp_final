
@extends('layouts.master')

@section('title','Create')

@section('sub_page','Create')
@section('main-content')

    <section class="content">

        @include('admin.section.flash_message')

        @include('admin.payrollSetting.common.breadcrumb')
        <div class="row">
            <div class="col-lg-2">
                @include('admin.payrollSetting.common.setting_menu')
            </div>
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <form class="forms-sample" action="{{route('admin.general-settings.update',$advanceSalarySetting->id)}}"  method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="col-lg-6 col-md-6 mb-3">
                                    <label for="title" class="form-label"> {{ $advanceSalarySetting->name }} <span style="color: red">*</span></label>
                                    <input type="number"
                                           class="form-control"
                                           id="title" step="0.1" min="0" name="value" required
                                           value="{{ isset($advanceSalarySetting) ? $advanceSalarySetting->value: old('title') }}"
                                           autocomplete="off"
                                           placeholder="Advance Salary Limit(%)">
                                    @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 col-md-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="link-icon" data-feather="{{ isset($underTime) ? 'edit-2':'plus'}}"></i>
                                      Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </section>
@endsection

@section('scripts')
    @include('admin.payrollSetting.under_time.common.scripts')
@endsection
