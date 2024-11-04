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

        function changeItem(element) {
            var iteams_id = element.val();
            var url = element.data('url');
            var el = element;
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
                    $.ajax({
                        url: '{{ route('admin.purchase.items') }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': jQuery('#token').val()
                        },
                        data: {
                            'purchase_id': purchase_id,
                            'product_id': iteams_id,
                        },
                        cache: false,
                        success: function(data) {
                            var purchaseItems = JSON.parse(data);
                            console.log(purchaseItems);

                            if (purchaseItems != null) {
                                var amount = (purchaseItems.price * purchaseItems.quantity);
                                $(el.parent().parent().parent().find('.quantity')).val(purchaseItems
                                    .quantity);
                                $(el.parent().parent().parent().find('.price')).val(purchaseItems
                                    .price);
                                $(el.parent().parent().parent().find('.discount')).val(purchaseItems
                                    .discount);
                                $(el.parent().parent().parent().parent().find('.pro_description'))
                                    .val(purchaseItems.description);

                                // $('.pro_description').text(purchaseItems.description);
                            } else {

                                $(el.parent().parent().parent().find('.quantity')).val(1);
                                $(el.parent().parent().parent().find('.price')).val(item.product
                                    .purchase_price);
                                $(el.parent().parent().parent().find('.discount')).val(0);
                                $(el.parent().parent().parent().find('.pro_description')).val(item
                                    .product.purchase_price);
                                // $('.pro_description').text(item.product.purchase_price);
                                $(el.parent().parent().parent().parent().find('.pro_description'))
                                    .val(item.product.description);

                            }

                            var taxes = '';
                            var tax = [];

                            var totalItemTaxRate = 0;
                            for (var i = 0; i < item.taxes.length; i++) {

                                taxes +=
                                    '<span class="badge bg-primary p-2 px-3 rounded mt-1 mr-1">' +
                                    item.taxes[i].name + ' ' + '(' + item.taxes[i].rate + '%)' +
                                    '</span>';
                                tax.push(item.taxes[i].id);
                                totalItemTaxRate += parseFloat(item.taxes[i].rate);

                            }

                            var discount = $(el.parent().parent().parent().find('.discount')).val();

                            if (purchaseItems != null) {
                                var itemTaxPrice = parseFloat((totalItemTaxRate / 100)) *
                                    parseFloat((purchaseItems.price * purchaseItems.quantity) -
                                        discount);
                            } else {
                                var itemTaxPrice = parseFloat((totalItemTaxRate / 100)) *
                                    parseFloat((item.product.purchase_price * 1) - discount);
                            }

                            $(el.parent().parent().parent().find('.itemTaxPrice')).val(itemTaxPrice
                                .toFixed(2));
                            $(el.parent().parent().parent().find('.itemTaxRate')).val(
                                totalItemTaxRate.toFixed(2));
                            $(el.parent().parent().parent().find('.taxes')).html(taxes);
                            $(el.parent().parent().parent().find('.tax')).val(tax);
                            $(el.parent().parent().parent().find('.unit')).html(item.unit);


                            var inputs = $(".amount");
                            var subTotal = 0;
                            for (var i = 0; i < inputs.length; i++) {
                                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                            }

                            var totalItemPrice = 0;
                            var inputs_quantity = $(".quantity");
                            var priceInput = $('.price');
                            for (var j = 0; j < priceInput.length; j++) {
                                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(
                                    inputs_quantity[j].value));
                            }



                            var totalItemTaxPrice = 0;
                            var itemTaxPriceInput = $('.itemTaxPrice');
                            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                                if (purchaseItems != null) {
                                    $(el.parent().parent().parent().find('.amount')).html(
                                        parseFloat(amount) + parseFloat(itemTaxPrice) -
                                        parseFloat(discount));
                                } else {
                                    $(el.parent().parent().parent().find('.amount')).html(
                                        parseFloat(item.totalAmount) + parseFloat(itemTaxPrice));
                                }

                            }


                            var totalItemDiscountPrice = 0;
                            var itemDiscountPriceInput = $('.discount');

                            for (var k = 0; k < itemDiscountPriceInput.length; k++) {
                                totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k]
                                    .value);
                            }


                            $('.subTotal').html(totalItemPrice.toFixed(2));
                            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                            $('.totalAmount').html((parseFloat(totalItemPrice) - parseFloat(
                                    totalItemDiscountPrice) + parseFloat(totalItemTaxPrice))
                                .toFixed(2));
                            $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));

                        }
                    });


                },
            });
        }
        $(document).on('change', '.item', function() {
            changeItem($(this));
        });

        $(document).on('keyup', '.quantity', function() {
            var quntityTotalTaxPrice = 0;

            var el = $(this).parent().parent().parent().parent();

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

        $(document).on('click', '[data-repeater-delete]', function() {
            // $('.delete_item').click(function () {
            if (confirm('Are you sure you want to delete this element?')) {
                var el = $(this).parent().parent();
                var id = $(el.find('.id')).val();

                $.ajax({
                    url: '{{ route('admin.purchase.product.destroy') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': jQuery('#token').val()
                    },
                    data: {
                        'id': id
                    },
                    cache: false,
                    success: function(data) {

                    },
                });

            }
        });

        $(document).on('click', '.delete_append_data', function(e) {
            e.preventDefault();
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
                                <a href="#" data-repeater-create="" class="btn btn-primary" data-bs-toggle="modal"
                                    data-target="#add-bank">
                                    <i class="ti ti-plus"></i> {{ __('Add item') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style ">
                    <div class="table-responsive">
                        <table class="table  mb-0" data-repeater-list="items" id="sortable-table">
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
                                        {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
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
                                                    $totalTaxPrice = 0;
                                                    foreach ($taxes as $taxe) {
                                                        $taxDataPrice = App\Models\Utility::taxRate(
                                                            $taxe->rate,
                                                            $iteam->price,
                                                            $iteam->quantity,
                                                            $iteam->discount,
                                                        );
                                                        $totalTaxPrice += $taxDataPrice;
                                                        if (array_key_exists($taxe->name, $taxesData)) {
                                                            $taxesData[$taxe->name] =
                                                                $taxesData[$taxe->name] + $taxDataPrice;
                                                        } else {
                                                            $taxesData[$taxe->name] = $taxDataPrice;
                                                        }
                                                    }
                                                @endphp
                                                @foreach ($taxes as $taxe)
                                                    <div>
                                                        <div class="input-group">
                                                            <div class="taxes">
                                                                <span
                                                                    class="badge bg-primary mt-1 mr-2">{{ $taxe->name . ' (' . $taxe->rate . '%)' }}</span>
                                                            </div>
                                                            {{ Form::hidden('tax[]', $taxe->id, ['class' => 'form-control tax']) }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-end amount">
                                            {{ \Auth::user()->priceFormat($totalRate - $iteam->discount + $totalTaxPrice) }}
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
                                        <tr>
                                            <td colspan="4"></td>
                                            <td class="text-end"><b>{{ $taxName }}</b></td>
                                            <td class="text-end">
                                                {{ \Auth::user()->priceFormat($taxPrice) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
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
