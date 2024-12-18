@extends('layouts.master')
@section('title')
    {{ __('Seed Stock Create') }}
@endsection
@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#quantity').on('keyup', function() {
                var quantity = $(this).val();
                var product_id = $('#product_id').val();

                $.ajax({
                    url: "{{ route('admin.seedstock.getSeedAmount') }}",
                    method: 'post',
                    data: {
                        product_id: product_id,
                        quantity: quantity,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#amount').val(response.total_price);
                    }
                });
            })
        });
    </script>
@endsection
@section('main-content')
    @include('admin.section.flash_message')

    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.seedstock.index') }}">{{ __('Seed Stock') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Create') }}</li>
        </ol>
    </nav>

    <div class="row">
        {{ Form::open(['url' => 'admin/seedstock', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="created_by" id="created_by" value="{{ Auth::user()->id }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farmer_id', __('Farmer'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farmer_id" id="farmer_id">
                                    <option value="">{{ __('Select Farmer') }}</option>
                                    @foreach ($farmers as $farmer)
                                        <option value="{{ $farmer->id }}">{{ $farmer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('product_id', __('Product'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="product_id" id="product_id">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('receive_date', __('Receive Date'), ['class' => 'form-label']) }}
                                <input type="date" class="form-control" name="receive_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                                <input type="number" class="form-control" name="quantity" id="quantity">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="amount" id="amount" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.seedstock.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
