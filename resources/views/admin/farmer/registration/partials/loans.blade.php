<div class="table-responsive  mt-2">
    <table class="table datatable">
        <thead>
            <tr>
                <th>{{ __('Farmer Name') }}</th>
                <th>{{ __('G-Code No.') }}</th>
                <th>{{ __('Invoice Date') }}</th>
                <th>{{ __('Invoice No') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $loan)
                @php
                    $loan_category_id = json_decode($loan->loan_category_id);
                    $loan_type_id = json_decode($loan->loan_type_id);
                    $price_kg = json_decode($loan->price_kg);
                    $quantity = json_decode($loan->quantity);
                    $total_amount = json_decode($loan->total_amount);
                    $count = count($loan_category_id);
                @endphp
                <tr class="font-style">
                    <td>{{ $loan->farming->name }}</td>
                    <td>{{ $loan->farming->g_code }}</td>
                    <td>{{ date('d-m-Y',strtotime($loan->updated_at)) }}</td>
                    <td>
                        @php
                            $invoice = explode('.',$loan->invoice);
                        @endphp
                        {{ $invoice[0] }}</td>
                    <td>
                        @for ($i = 0; $i < $count; $i++)
                            @php
                                $productcategory = App\Models\ProductServiceCategory::where('id',$loan_category_id[$i])->first();
                            @endphp
                            {{ $productcategory->name }}
                            @if($i < $count - 1)/@endif
                        @endfor
                    </td>
                    <td>
                        @for ($i = 0; $i < $count; $i++)
                            {{ $total_amount[$i] }}
                            @if($i < $count - 1)/@endif
                        @endfor
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
</div>
