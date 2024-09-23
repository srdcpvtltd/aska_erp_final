@extends('layouts.master')
@section('title')
    {{__('POS Product Barcode')}}
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/datatable/buttons.dataTables.min.css') }}">
@endsection

@section('main-content')
@include('admin.section.flash_message')
<nav class="page-breadcrumb d-flex align-items-center justify-content-between">
    <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('POS Product Barcode')}}</li>
    </ol>
    <div class="float-end">
        @can('create-print_barcode')
            <a href="{{ route('admin.pos.print') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Print Barcode')}}">
                <i class="link-icon" data-feather="printer"></i>
            </a>
            <a data-url="{{ route('admin.pos.setting') }}" data-ajax-popup="true" data-bs-toggle="tooltip" data-title="{{__('Barcode Setting')}}" title="{{__('Barcode Setting')}}" class="btn btn-sm btn-primary">
                <i class="link-icon" data-feather="settings"></i>
            </a>
        @endcan
    </div>
</nav>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive ">
                        <table class="table datatable-barcode" >
                            <thead>
                                <tr>
                                    <th>{{__('Product')}}</th>
                                    <th>{{ __('SKU') }}</th>
                                    <th>{{ __('Barcode') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($productServices as $productService)
                                    <tr>
                                        <td>{{$productService->name}}</td>
                                        <td>{{$productService->sku}}</td>
                                        <td>
                                            <div id="{{ $productService->id }}" class="product_barcode product_barcode_hight_de" data-skucode="{{ $productService->sku }}"></div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-dark"><p>{{__('No Data Found')}}</p></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
{{--    <script src="{{ asset('public/js/jquery-barcode.min.js') }}"></script>--}}
    <script src="{{ asset('public/js/jquery-barcode.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".product_barcode").each(function() {
                var id = $(this).attr("id");
                var sku = $(this).data('skucode');
                generateBarcode(sku, id);
            });
        });
        function generateBarcode(val, id) {

            var value = val;
            var btype = '{{ $barcode['barcodeType'] }}';
            var renderer = '{{ $barcode['barcodeFormat'] }}';
            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: '1',
                barHeight: '50',
                moduleSize: '5',
                posX: '10',
                posY: '20',
                addQuietZone: '1'
            };
            $('#' + id).html("").show().barcode(value, btype, settings);

        }

        setTimeout(myGreeting, 1000);
        function myGreeting() {
            if ($(".datatable-barcode").length > 0) {
                const dataTable =  new simpleDatatables.DataTable(".datatable-barcode");
            }
        }
        // });
    </script>

@endsection
