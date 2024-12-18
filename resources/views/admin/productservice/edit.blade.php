@extends('layouts.master')
@section('title')
    {{ __('Manage Product & Services') }}
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.productservice.index') }}">{{ __('Product & Services') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Edit') }}</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-sm-12">
            <div class=" mt-2 {{ isset($_GET['category']) ? 'show' : '' }}" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::model($productService, ['route' => ['admin.productservice.update', $productService->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        <div class="modal-body">
                            {{-- start for ai module --}}
                            @php
                                $settings = \App\Models\Utility::settings();
                            @endphp
                            @if ($settings['ai_chatgpt_enable'] == 'on')
                                <div class="text-end">
                                    <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm"
                                        data-ajax-popup-over="true" data-url="{{ route('generate', ['productservice']) }}"
                                        data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                                        <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
                                    </a>
                                </div>
                            @endif
                            {{-- end for ai module --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<span
                                            class="text-danger">*</span>
                                        {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>
                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('sku', __('SKU'), ['class' => 'form-label']) }}<span
                                            class="text-danger">*</span>
                                        {{ Form::text('sku', null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>
                                </div> --}}

                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('sale_price', __('Sale Price'), ['class' => 'form-label']) }}<span
                                            class="text-danger">*</span>
                                        {{ Form::number('sale_price', null, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('sale_chartaccount_id', __('Income Account'), ['class' => 'form-label']) }}
                                    {{ Form::select('sale_chartaccount_id', $incomeChartAccounts, null, ['class' => 'form-control select', 'required' => 'required']) }}
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('purchase_price', __('Purchase Price'), ['class' => 'form-label']) }}
                                        {{ Form::number('purchase_price', null, ['class' => 'form-control', 'step' => '0.01']) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('expense_chartaccount_id', __('Expense Account'), ['class' => 'form-label']) }}
                                    {{ Form::select('expense_chartaccount_id', $expenseChartAccounts, null, ['class' => 'form-control select', 'required' => 'required']) }}
                                </div>

                                <div class="form-group  col-md-6">
                                    {{ Form::label('tax_id', __('Tax'), ['class' => 'form-label']) }}
                                    {{ Form::select('tax_id[]', $tax, null, ['class' => 'form-control select2', 'id' => 'choices-multiple1', 'multiple' => '']) }}
                                </div>

                                <div class="form-group  col-md-6">
                                    {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}<span
                                        class="text-danger">*</span>
                                    {{ Form::select('category_id', $category, null, ['class' => 'form-control select', 'required' => 'required']) }}
                                </div>
                                <div class="form-group  col-md-6">
                                    {{ Form::label('unit_id', __('Unit'), ['class' => 'form-label']) }}<span
                                        class="text-danger">*</span>
                                    {{ Form::select('unit_id', $unit, null, ['class' => 'form-control select', 'required' => 'required']) }}
                                </div>

                                <div class="col-md-6 form-group">
                                    {{ Form::label('unit_weight', __('Unit weight'), ['class' => 'form-label']) }}
                                    <div class="choose-file ">
                                        <input type="text" class="form-control" name="unit_weight" value="{{ $productService->unit_weight }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="d-block form-label">{{ __('Type') }}</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input type" id="customRadio5"
                                                        name="type" value="product"
                                                        @if ($productService->type == 'product') checked @endif>
                                                    <label class="form-label"
                                                        for="customRadio5">{{ __('Product') }}</label>
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input type" id="customRadio6"
                                                        name="type" value="service"
                                                        @if ($productService->type == 'service') checked @endif>
                                                    <label class="form-label"
                                                        for="customRadio6">{{ __('Service') }}</label>
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>

                                {{-- <div
                                    class="form-group col-md-6 quantity {{ $productService->type == 'service' ? 'd-none' : '' }}">
                                    {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<span
                                        class="text-danger">*</span>
                                    {{ Form::text('quantity', null, ['class' => 'form-control', 'required' => 'required']) }}
                                </div> --}}
                                <div class="form-group  col-md-12">
                                    {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                                    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '2']) !!}
                                </div>


                            </div>
                            @if (!$customFields->isEmpty())
                                <div class="col-md-6">
                                    <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                        @include('customFields.formBuilder')
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
                        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.getElementById('pro_image').onchange = function() {
            var src = URL.createObjectURL(this.files[0])
            document.getElementById('image').src = src
        }

        //hide & show quantity

        $(document).on('click', '.type', function() {
            var type = $(this).val();
            if (type == 'product') {
                $('.quantity').removeClass('d-none')
                $('.quantity').addClass('d-block');
            } else {
                $('.quantity').addClass('d-none')
                $('.quantity').removeClass('d-block');
            }
        });
    </script>
@endsection
