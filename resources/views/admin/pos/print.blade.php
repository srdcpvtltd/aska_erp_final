@extends('layouts.master')
@section('title')
    {{ __('POS Barcode Print') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.pos.barcode') }}">{{ __('POS Product Barcode') }}</a></li>
    <li class="breadcrumb-item">{{ __('POS Barcode Print') }}</li>
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/datatable/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
@endsection

@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        $('#product_id').selectpicker();

        $(document).ready(function() {
            var b_id = $('#warehouse_id').val();
            getProduct(b_id);
        });
        $(document).on('change', 'select[name=warehouse_id]', function() {

            var warehouse_id = $(this).val();
            getProduct(warehouse_id);
        });

        function getProduct(bid) {

            $.ajax({
                url: '{{ route('admin.pos.getproduct') }}',
                type: 'POST',
                data: {
                    "warehouse_id": bid,
                    "_token": "{{ csrf_token() }}",
                },

                success: function(data) {
                    console.log(data);
                    $('#product_id').empty();

                    $("#product_div").html('');
                    $('#product_div').append(
                        '<label for="product_id" class="form-label">{{ __('Product') }}</label>');
                    $('#product_div').append(
                        '<select class="form-control selectpicker" id="product_id" name="product_id[]" multiple data-live-search="true" required></select>'
                    );
                    $('#product_id').append('<option value="">{{ __('Select Product') }}</option>');

                    $.each(data, function(key, value) {
                        console.log(key, value);
                        $('#product_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                    var multipleCancelButton = new Choices('#product_id', {
                        removeItemButton: true,
                    });


                }

            });
        }
    </script>
    <script>
        function copyToClipboard(element) {
            var copyText = element.id;
            navigator.clipboard.writeText(copyText);
            // document.addEventListener('copy', function (e) {
            //     e.clipboardData.setData('text/plain', copyText);
            //     e.preventDefault();
            // }, true);
            // document.execCommand('copy');
            show_toastr('success', 'Url copied to clipboard', 'success');
        }
    </script>
    <script>
        var filename = $('#filesname').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 4,
                    dpi: 72,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A2'
                }
            };
            html2pdf().set(opt).from(element).save();

        }
    </script>
@endsection


@section('action-btn')
    <div class="float-end">
        <a href="{{ route('admin.pos.barcode') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
            title="{{ __('Back') }}">
            <i class="ti ti-arrow-left text-white"></i>
        </a>

    </div>
@endsection


@section('main-content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => 'admin.pos.receipt', 'method' => 'post']) }}
                    <div class="row" id="printableArea">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('warehouse_id', __('Warehouse'), ['class' => 'form-label']) }}
                                {{ Form::select('warehouse_id', $warehouses, '', ['class' => 'form-control select', 'id' => 'warehouse_id', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" id="product_div">
                                {{ Form::label('product_id', __('Product'), ['class' => 'form-label']) }}
                                <select class="form-control select" name="product_id[]" id="product_id" required>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<span
                                class="text-danger">*</span>
                            {{ Form::text('quantity', null, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                    </div>

                    <div class="col-md-6 pt-4">

                        <button class="btn btn-sm btn-primary btn-icon" type="submit">{{ __('Print') }}</button>


                    </div>

                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>
@endsection
