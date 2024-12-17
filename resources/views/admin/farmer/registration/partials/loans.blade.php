<div class="table-responsive  mt-2">
    <table class="data_table table datatable">
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
                <tr class="font-style">
                    <td>{{ $loan->farming->name }}</td>
                    <td>{{ $loan->farming->old_g_code }}</td>
                    <td>{{ date('d-m-Y', strtotime($loan->updated_at)) }}</td>
                    <td>
                        @php
                            $invoice = explode('.', $loan->invoice);
                        @endphp
                        {{ $invoice[0] }}</td>
                    <td>{{ $loan->category->name }}</td>
                    <td>{{ $loan->round_amount }}/-</td>
                </tr>
            @endforeach

        </tbody>
    </table>
</div>
