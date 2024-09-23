@php
    $settings = App\Models\Utility::settings();
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $settings == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">

    <title>Allotment Invoice</title>
    @if (isset($settings['SITE_RTL']) && $settings['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
    @endif
    <style>
        .heading span {
            text-decoration: underline;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div id="bot" class="mt-5">
        <p style="text-align: center;"><b><span style="float: left;">Form No. 22</span> <span>ASKA CO-OP. SUGAR
                    INDUSTRIES LTD., ASKA.</span></b></p><br>
        <div class="heading">
            <p>Zone No. <span>{{ $farming->zone_id }}</span> Center No. <span>{{ $farming->center_id }}</span> Receipt
                No. <span>3574</span> G. Code No. <span>{{ $data['agreement_number'] }}</span>
                Plot Area <span>{{ $farming->offered_area }}</span> Date <span>{{ $data['date'] }}</span> Farmer Name
                <span>{{ $farming->name }}</span> Father's Name <span>{{ $farming->father_name }}</span> Village
                <span>{{ $farming->village->name }}</span> Farmer's Nominee <span></span>.
            </p><br>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sl No.</th>
                        <th>Items</th>
                        <th>Quantity</th>
                        <th>Price/KG</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $loan_category_id = json_decode($data->loan_category_id);
                        $loan_type_id = json_decode($data->loan_type_id);
                        $price_kg = json_decode($data->price_kg);
                        $quantity = json_decode($data->quantity);
                        $total_amount = json_decode($data->total_amount);
                        $count = count($loan_category_id);
                        $grandtotal = 0;
                    @endphp
                    @for($i = 0; $i < $count; $i++)
                    @php
                        $product = App\Models\ProductService::where('id',$loan_type_id[$i])->first();
                        $grandtotal += $total_amount[$i];
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $quantity[$i] }}</td>
                        <td>{{ $price_kg[$i] }}</td>
                        <td>{{ $total_amount[$i] }}</td>
                    </tr>
                    @endfor
                    <tr>
                        <td colspan="3"></td>
                        <td><b>Grand Total Amount</b></td>
                        <td><b>{{ $grandtotal }}/-</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        window.print();
        window.onafterprint = function(event) {
            window.location.href = "{{ route('admin.farmer.loan.create') }}";
        };

        function back() {
            window.close();
            window.location.href = "{{ route('admin.farmer.loan.create') }}";
        }
    </script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/js/jquery-barcode.min.js') }}"></script>
    <script src="{{ asset('public/js/jquery-barcode.js') }}"></script>

</body>

</html>
