@extends('layouts.master')
@section('title')
    {{ __('Edit Farmer Security Deposit') }}
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            $('#g_code').keyup(function() {
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
                        $('#farming_id').empty();
                        if (response.farmerHtml) {
                            $('#farming_id').append(response.farmerHtml);
                        } else {
                            $('#farming_id').append('<option value="">Select Farmer</option>');
                        }
                    }
                });
            });
        })
    </script>
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('admin.farmer.payment.index') }}">{{ __('Farmer Security Deposit') }}</a>
            </li>
            <li class="breadcrumb-item">{{ __('Edit Farmer Security Deposit') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::model($payment, ['route' => ['admin.farmer.payment.update', $payment->id], 'method' => 'PUT', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('g_code', __('G.Code'), ['class' => 'form-label']) }}
                            {{ Form::text('g_code', null, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('farming_id', __('Farmer Registration'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="farming_id" id="farming_id" required
                                    placeholder="Select Country">
                                    <option value="">{{ __('Select Farmer Registration') }}</option>
                                    @foreach ($farmings as $farming)
                                        <option {{ $farming->id == $payment->farming_id ? 'selected' : '' }}
                                            value="{{ $farming->id }}">{{ $farming->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('receipt_no', __('GL No./ Receipt No.'), ['class' => 'form-label']) }}
                            {{ Form::text('receipt_no', $payment->receipt_no, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('date', __('Date of Deposit'), ['class' => 'form-label']) }}
                            {{ Form::date('date', $payment->date, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
                            {{ Form::number('amount', $payment->amount, ['class' => 'form-control', 'step' => '0.01', 'required' => 'required']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('admin.farmer.payment.index') }}';" class="btn btn-light">
                <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
