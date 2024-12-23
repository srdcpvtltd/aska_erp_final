@extends('layouts.master')
@section('title')
    {{ __('Seed Stock Create') }}
@endsection
@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#g_code_from').keyup(function() {
                let g_code = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.get_detail') }}",
                    method: 'post',
                    data: {
                        g_code: g_code,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#farming_id_from').empty();
                        if (response.farmerHtml) {
                            $('#farming_id_from').append(response.farmerHtml);
                        } else {
                            $('#farming_id_from').append('<option value="">Select Farmer</option>');
                        }
                        if (response.villageHtml) {
                            $('#village_id_from').append(response.villageHtml);
                        } else {
                            $('#village_id_from').append('<option value="">Select Village</option>');
                        }
                        $('#father_name_from').val(response.farming.father_name);
                    }
                });
            });
            $('#g_code_to').keyup(function() {
                let g_code = $(this).val();
                $.ajax({
                    url: "{{ route('admin.farmer.get_detail') }}",
                    method: 'post',
                    data: {
                        g_code: g_code,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#farming_id_to').empty();
                        if (response.farmerHtml) {
                            $('#farming_id_to').append(response.farmerHtml);
                        } else {
                            $('#farming_id_to').append('<option value="">Select Farmer</option>');
                        }
                        if (response.villageHtml) {
                            $('#village_id_to').append(response.villageHtml);
                        } else {
                            $('#village_id_to').append('<option value="">Select Village</option>');
                        }
                        $('#father_name_to').val(response.farming.father_name);
                    }
                });
            });
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
                        <label for=""><b>Seed From</b></label>
                        <hr>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('g_code', __('G.Code'), ['class' => 'form-label']) }}
                                {{ Form::text('g_code_from', '', ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farmer_id', __('Farmer'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farmer_id_from" id="farming_id_from">
                                    <option value="">{{ __('Select Farmer') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('father_name', __('Father Name'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="father_name_from" id="father_name_from" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('village', __('Village'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="village_id_from" id="village_id_from">
                                    <option value="">{{ __('Select Village') }}</option>
                                </select>
                            </div>
                        </div>
                        <label for=""><b>Seed TO</b></label>
                        <hr>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('g_code', __('G.Code'), ['class' => 'form-label']) }}
                                {{ Form::text('g_code_to', '', ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farmer_id', __('Farmer'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farmer_id_to" id="farming_id_to">
                                    <option value="">{{ __('Select Farmer') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('father_name', __('Father Name'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control" name="father_name_to" id="father_name_to" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('village', __('Village'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="village_id_to" id="village_id_to">
                                    <option value="">{{ __('Select Village') }}</option>
                                </select>
                            </div>
                        </div>
                        <hr>
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
