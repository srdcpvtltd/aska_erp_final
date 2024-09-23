<div class="table-responsive mt-2">
    <table class="table datatable">
        <thead>
        <tr>
            <th>{{__('Payment Type')}}</th>
            <th>{{__('Farmer Name')}}</th>
            <th>{{__('G-Code No.')}}</th>
            <th>{{__('Receipt/GL No.')}}</th>
            <th>{{__('Amount')}}</th>
            <th>{{__('Date')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($security_deposits as $security_deposit)
            <tr class="font-style">
                <td>{{ $security_deposit->type}}</td>
                <td>{{ $security_deposit->farming->name}}</td>
                <td>{{ $security_deposit->farming->g_code}}</td>
                <td>{{ $security_deposit->receipt_type}}</td>
                <td>{{ $security_deposit->amount }}</td>
                <td>{{ $security_deposit->date }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>