@extends('layouts.master')
@section('title')
    {{ __('Purchase Edit') }}
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#vender-box').hide();
            $('#vender_detail').hide();
        })
        $(document).on('change', '#vender', function() {
            $('#vender_detail').removeClass('d-none');
            $('#vender_detail').addClass('d-block');
            $('#vender-box').removeClass('d-block');
            $('#vender-box').addClass('d-none');
            var id = $(this).val();
            var url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                cache: false,
                success: function(data) {
                    if (data != '') {
                        $('#vender_detail').html(data);
                    } else {
                        $('#vender-box').removeClass('d-none');
                        $('#vender-box').addClass('d-block');
                        $('#vender_detail').removeClass('d-block');
                        $('#vender_detail').addClass('d-none');
                    }
                },
            });
        });
        $(document).on('click', '#remove', function() {
            $('#vender-box').removeClass('d-none');
            $('#vender-box').addClass('d-block');
            $('#vender_detail').removeClass('d-block');
            $('#vender_detail').addClass('d-none');
            $('#vender_bill_ship_address').hide();
        });

        var purchase_id = '{{ $purchase->id }}';

        $(document).on('change', '.item', function() {
            var iteams_id = $(this).val();
            var url = $(this).data('url');
            var el = $(this).closest('.ui-sortable');
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'product_id': iteams_id
                },
                cache: false,
                success: function(data) {
                    var item = JSON.parse(data);

                    $(el.find('.quantity')).val(1);
                    $(el.find('.price')).val(item.product.purchase_price);
                    $(el.find('.pro_description')).val(item.product
                        .description);

                    var taxes = '';
                    var tax = [];

                    var totalItemTaxRate = 0;
                    if (item.taxes == 0) {
                        taxes += '-';
                    } else {
                        for (var i = 0; i < item.taxes.length; i++) {

                            taxes += '<span class="badge bg-primary mt-1 mr-2">' + item.taxes[i].name +
                                ' ' + '(' + item.taxes[i].rate + '%)' + '</span>';
                            tax.push(item.taxes[i].id);
                            totalItemTaxRate += parseFloat(item.taxes[i].rate);

                        }
                    }
                    var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (item.product
                        .purchase_price * 1));

                    $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
                    $(el.find('.itemTaxRate')).val(totalItemTaxRate.toFixed(2));
                    $(el.find('.taxes')).html(taxes);
                    $(el.find('.tax')).val(tax);
                    $(el.find('.unit')).html(item.unit);
                    $(el.find('.discount')).val(0);
                    $(el.find('.amount')).html(item.totalAmount + itemTaxPrice);


                    var inputs = $(el.find(".amount"));
                    var subTotal = 0;
                    console.log(inputs.length);
                    
                    for (var i = 0; i < inputs.length; i++) {
                        subTotal += parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                    }

                    var totalItemPrice = 0;
                    var priceInput = $('.price');
                    for (var j = 0; j < priceInput.length; j++) {
                        totalItemPrice += parseFloat(priceInput[j].value);
                    }

                    var totalItemTaxPrice = 0;
                    var itemTaxPriceInput = $('.itemTaxPrice');
                    for (var j = 0; j < itemTaxPriceInput.length; j++) {
                        totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                    }

                    $('.subTotal').html(subTotal.toFixed(2));
                    $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                    $('.totalAmount').html((parseFloat(subTotal) + parseFloat(totalItemTaxPrice))
                        .toFixed(2));
                    $('input[name=total_amount]').val((parseFloat(subTotal) + parseFloat(
                            totalItemTaxPrice))
                        .toFixed(2))
                },
            });
        });

        $(document).on('keyup', '.quantity', function() {
            var quntityTotalTaxPrice = 0;

            var el = $(this).closest('.ui-sortable');

            var quantity = $(this).val();
            var price = $(el.find('.price')).val();
            var discount = $(el.find('.discount')).val();
            if (discount.length <= 0) {
                discount = 0;
            }

            var totalItemPrice = (quantity * price) - discount;

            var amount = (totalItemPrice);


            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice) + parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }

            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");

            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));

        })

        $(document).on('keyup change', '.price', function() {

            var el = $(this).parent().parent().parent().parent();
            var price = $(this).val();
            var quantity = $(el.find('.quantity')).val();
            var discount = $(el.find('.discount')).val();
            if (discount.length <= 0) {
                discount = 0;
            }


            var totalItemPrice = (quantity * price) - discount;

            var amount = (totalItemPrice);

            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice) + parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");

            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));

        })

        $(document).on('keyup change', '.discount', function() {
            var el = $(this).parent().parent().parent();
            var discount = $(this).val();
            if (discount.length <= 0) {
                discount = 0;
            }
            var price = $(el.find('.price')).val();

            var quantity = $(el.find('.quantity')).val();
            var totalItemPrice = (quantity * price) - discount;

            var amount = (totalItemPrice);

            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice) + parseFloat(amount));


            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");

            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }


            var totalItemDiscountPrice = 0;
            var itemDiscountPriceInput = $('.discount');

            for (var k = 0; k < itemDiscountPriceInput.length; k++) {

                totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
            $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
        })

        $(document).on('click', '.add_more_btn', function() {
            $('#table').append('<tbody class="ui-sortable">' +
                '<tr>' +
                '<td width="25%" class="form-group">' +
                '{{ Form::select('item[]', $product_services, '', ['class' => 'form-control select2 item', 'data-url' => route('admin.purchase.product'), 'required' => 'required']) }}' +
                '</td>' +
                '<td>' +
                '<div class="form-group price-input input-group search-form">' +
                '{{ Form::text('quantity[]', '', ['class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Qty'), 'required' => 'required']) }}' +
                '<span class="unit input-group-text bg-transparent"></span>' +
                '</div>' +
                '</td>' +
                '<td>' +
                '<div class="form-group price-input input-group search-form">' +
                '{{ Form::text('price[]', '', ['class' => 'form-control price', 'required' => 'required', 'placeholder' => __('Price'), 'required' => 'required']) }}' +
                '<span class="input-group-text bg-transparent">{{ \Auth::user()->currencySymbol() }}</span>' +
                '</div>' +
                '</td>' +
                '<td>' +
                '<div class="form-group price-input input-group search-form">' +
                '{{ Form::text('discount[]', '', ['class' => 'form-control discount', 'required' => 'required', 'placeholder' => __('Discount')]) }}' +
                '<span class="input-group-text bg-transparent">{{ \Auth::user()->currencySymbol() }}</span>' +
                '</div>' +
                '</td>' +
                '<td>' +
                '<div class="form-group">' +
                '<div class="input-group">' +
                '<div class="taxes"></div>' +
                '{{ Form::hidden('tax[]', '', ['class' => 'form-control tax']) }}' +
                '{{ Form::hidden('itemTaxPrice[]', '', ['class' => 'form-control itemTaxPrice']) }}' +
                '{{ Form::hidden('itemTaxRate[]', '', ['class' => 'form-control itemTaxRate']) }}' +
                '</div>' +
                '</div>' +
                '</td>' +
                '<td class="text-end amount">' +
                '0.00' +
                '</td>' +
                '<td>' +
                '<a href="#" class="delete_append_data">' +
                '<i class="link-icon" data-feather="trash"></i>' +
                '</a>' +
                '</td>' +
                '</tr>' +
                '<tr>' +
                '<td colspan="2">' +
                '<div class="form-group">' +
                '{{ Form::textarea('description[]', null, ['class' => 'form-control pro_description', 'rows' => '2', 'placeholder' => __('Description')]) }}' +
                '</div>' +
                '</td>' +
                '<td colspan="5"></td>' +
                '</tr>' +
                '</tbody>');
            feather.replace();
        });

        $(document).on('click', '.delete_append_data', function(e) {
            e.preventDefault();
            var el = $(this).closest('.ui-sortable');

            var quantity = $(el).find('.quantity').val();
            var price = $(el).find('.price').val();
            var discount = $(el).find('.discount').val();
            var itemTaxPrice = parseFloat($(el).find('.itemTaxPrice').val()) || 0;
            var subTotal = parseFloat($('.Sub_Total').val()) || 0;
            var totalDiscount = parseFloat($('.Total_discount').val()) || 0;
            var totalTax = parseFloat($('.Total_tax').val()) || 0;
            var totalAmount = parseFloat($('.Total_Amount').val()) || 0;

            if (!discount) {
                discount = 0;
            }

            quantity = parseFloat(quantity) || 0;
            price = parseFloat(price) || 0;
            discount = parseFloat(discount) || 0;

            var total_Discount = totalDiscount - discount;
            var totalPrice = (quantity * price) - discount;
            var totalItemPrice = subTotal - totalPrice;
            var totalItemTaxPrice = totalTax - itemTaxPrice;

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalDiscount').html(total_Discount.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(totalItemPrice) + parseFloat(totalItemTaxPrice))
                .toFixed(2));
            $('input[name=total_amount]').val((parseFloat(totalItemPrice) + parseFloat(
                    totalItemTaxPrice))
                .toFixed(2));
            $(this).closest('tbody').remove();
        });
    </script>
@endsection

@section('main-content')
    @include('admin.section.flash_message')
    <nav class="page-breadcrumb d-flex align-items-center justify-content-between">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.purchase.index') }}">{{ __('Purchase') }}</a></li>
            <li class="breadcrumb-item">{{ __('Purchase Edit') }}</li>
        </ol>
    </nav>
    <div class="row">
        {{ Form::model($purchase, ['route' => ['admin.purchase.update', $purchase->id], 'method' => 'PUT', 'class' => 'w-100']) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="vender-box">
                                {{ Form::label('vender_id', __('Vendor'), ['class' => 'form-label']) }}
                                {{ Form::select('vender_id', $venders, null, ['class' => 'form-control select', 'id' => 'vender', 'data-url' => route('admin.purchase.vender'), 'required' => 'required']) }}
                            </div>
                            <div id="vender_detail" class="d-none">
                            </div>
                            <div id="vender_bill_ship_address">
                                @include('admin.purchase.vender_detail')
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('warehouse_id', __('Warehouse'), ['class' => 'form-label']) }}
                                        {{ Form::select('warehouse_id', $warehouse, null, ['class' => 'form-control select', 'required' => 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
                                        {{ Form::select('category_id', $category, null, ['class' => 'form-control select', 'required' => 'required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('purchase_date', __('Purchase Date'), ['class' => 'form-label']) }}
                                        {{ Form::date('purchase_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('purchase_number', __('Purchase Number'), ['class' => 'form-label']) }}
                                        <input type="text" class="form-control" value="{{ $purchase_number }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5 class="d-inline-block mb-4">{{ __('Product & Services') }}</h5>
            <div class="card repeater" data-value='{!! json_encode($purchase->items) !!}'>
                <div class="item-section py-2">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                            <div class="all-button-box me-2">
                                <a class="btn btn-primary add_more_btn text-white">
                                    {{ __('Add item') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style ">
                    <div class="table-responsive">
                        <table class="table mb-0" id="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Items') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Price') }} </th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Tax') }} (%)</th>
                                    <th class="text-end">{{ __('Amount') }} <br><small
                                            class="text-danger font-weight-bold">{{ __('after tax & discount') }}</small>
                                    </th>
                                </tr>
                            </thead>
                            @php
                                $totalQuantity = 0;
                                $totalRate = 0;
                                $totalDiscount = 0;
                                $taxesData = [];
                                $totalTaxPrice = 0;
                                $totalTaxRate = 0;
                                $totalTax = 0;
                            @endphp
                            @foreach ($purchase->items as $key => $iteam)
                                <tbody class="ui-sortable">
                                    @if (!empty($iteam->tax))
                                        @php
                                            $taxArr = explode(',', $iteam->tax);
                                            $taxes = [];
                                            foreach ($taxArr as $tax) {
                                                $taxes[] = App\Models\Tax::find($tax);
                                            }
                                            $totalQuantity += $iteam->quantity;
                                            $totalRate = $iteam->price * $iteam->quantity;
                                            $totalDiscount += $iteam->discount;
                                        @endphp
                                    @endif
                                    <tr>
                                        {{ Form::hidden('id[]', $iteam->id, ['class' => 'form-control id']) }}
                                        <td width="25%">
                                            <div class="form-group">
                                                {{ Form::select('item[]', $product_services, $iteam->product_id, ['class' => 'form-control select item', 'data-url' => route('admin.purchase.product')]) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group price-input input-group search-form">
                                                {{ Form::text('quantity[]', $iteam->quantity, ['class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Qty'), 'required' => 'required']) }}
                                                <span class="unit input-group-text bg-transparent"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group price-input input-group search-form">
                                                {{ Form::text('price[]', $iteam->price, ['class' => 'form-control price', 'required' => 'required', 'placeholder' => __('Price'), 'required' => 'required']) }}
                                                <span
                                                    class="input-group-text bg-transparent">{{ \Auth::user()->currencySymbol() }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group price-input input-group search-form">
                                                {{ Form::text('discount[]', $iteam->discount, ['class' => 'form-control discount', 'required' => 'required', 'placeholder' => __('Discount')]) }}
                                                <span
                                                    class="input-group-text bg-transparent">{{ \Auth::user()->currencySymbol() }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if (!empty($iteam->tax))
                                                @php
                                                    foreach ($taxes as $taxe) {
                                                        $taxDataPrice = App\Models\Utility::taxRate(
                                                            $taxe->rate,
                                                            $iteam->price,
                                                            $iteam->quantity,
                                                            $iteam->discount,
                                                        );
                                                        $totalTaxRate += $taxe->rate;
                                                        $totalTaxPrice += $taxDataPrice;
                                                        if (array_key_exists($taxe->name, $taxesData)) {
                                                            $taxesData[$taxe->name] =
                                                                $taxesData[$taxe->name] + $taxDataPrice;
                                                        } else {
                                                            $taxesData[$taxe->name] = $taxDataPrice;
                                                        }
                                                    }
                                                @endphp
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="taxes">
                                                            @foreach ($taxes as $taxe)
                                                                <span
                                                                    class="badge bg-primary mt-1 mr-2">{{ $taxe->name . ' (' . $taxe->rate . '%)' }}</span>
                                                            @endforeach
                                                        </div>
                                                        {{ Form::hidden('tax[]', $taxe->id, ['class' => 'form-control tax']) }}
                                                        {{ Form::hidden('itemTaxPrice[]', $totalTaxPrice, ['class' => 'form-control itemTaxPrice']) }}
                                                        {{ Form::hidden('itemTaxRate[]', $totalTaxRate, ['class' => 'form-control itemTaxRate']) }}
                                                    </div>
                                                </div>
                                            @else
                                                @php
                                                    $totalTaxPrice = 0;
                                                @endphp
                                                {{ Form::hidden('itemTaxPrice[]', 0, ['class' => 'form-control itemTaxPrice']) }}
                                                {{ Form::hidden('itemTaxRate[]', 0, ['class' => 'form-control itemTaxRate']) }}
                                            @endif
                                        </td>
                                        <td class="text-end amount">
                                            {{ \Auth::user()->priceFormat($iteam->price * $iteam->quantity - $iteam->discount + $totalTaxPrice) }}
                                        </td>
                                        <td>
                                            @if ($key != 0)
                                                <a href="#" class="delete_append_data">
                                                    <i class="link-icon" data-feather="trash"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div class="form-group">
                                                {{ Form::textarea('description', !empty($iteam->description) ? $iteam->description : '-', ['class' => 'form-control pro_description', 'rows' => '2', 'placeholder' => __('Description')]) }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            @endforeach
                            <tfoot>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td><strong>{{ __('Sub Total') }} ({{ \Auth::user()->currencySymbol() }})</strong>
                                    </td>
                                    <td class="text-end subTotal">
                                        {{ \Auth::user()->priceFormat($purchase->getSubTotal()) }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td><strong>{{ __('Discount') }} ({{ \Auth::user()->currencySymbol() }})</strong></td>
                                    <td class="text-end totalDiscount">
                                        {{ \Auth::user()->priceFormat($purchase->getTotalDiscount()) }}</td>
                                    <td></td>
                                </tr>
                                @if (!empty($taxesData))
                                    @foreach ($taxesData as $taxName => $taxPrice)
                                        @php
                                            $totalTax += $taxPrice;
                                        @endphp
                                    @endforeach
                                @endif
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td><strong>{{ __('Tax') }} ({{ \Auth::user()->currencySymbol() }})</strong></td>
                                    <td class="text-end totalTax">
                                        {{ \Auth::user()->priceFormat($totalTax) }}
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="blue-text"><strong>{{ __('Total Amount') }}
                                            ({{ \Auth::user()->currencySymbol() }})</strong></td>
                                    <td class="blue-text text-end totalAmount">
                                        {{ \Auth::user()->priceFormat($purchase->total_price) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="total_amount" value="{{ $purchase->total_price }}">
                        <input type="hidden" class="Total_discount" value="{{ $purchase->getTotalDiscount() }}">
                        <input type="hidden" class="Total_tax" value="{{ $totalTax }}">
                        <input type="hidden" class="Sub_Total" value="{{ $purchase->getSubTotal() }}">
                        <input type="hidden" class="Total_Amount" value="{{ $purchase->total_price }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <input type="button" value="{{ __('Cancel') }}"
                onclick="location.href = '{{ route('admin.purchase.index') }}';" class="btn btn-light">
            <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
        </div>
        {{ Form::close() }}
    </div>
@endsection
