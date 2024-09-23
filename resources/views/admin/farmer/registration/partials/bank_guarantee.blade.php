<div class="table-responsive mt-2">
    <table class="table datatable">
        <thead>
        <tr>
            <th>{{__('Type')}}</th>
            <th>{{__('Farmer Name')}}</th>
            <th>{{__('Farmer ID#')}}</th>
            <th>{{__('Bank')}}</th>
            <th>{{__('Loan Account No.')}}</th>
            <th>{{__('IFSC')}}</th>
            <th>{{__('Branch')}}</th>
            <th>{{__('Loan Disbursement Date')}}</th>
            <th>{{__('Loan Amount')}}</th>
            <th>{{__('Action')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($bank_guarantees as $bank_guarantee)
            <tr class="font-style">
                <td>{{ $bank_guarantee->type}}</td>
                <td>{{ $bank_guarantee->farming->name}}</td>
                <td>{{ $bank_guarantee->farming->farmer_id}}</td>
                <td>{{ $bank_guarantee->bank}}</td>
                <td>{{ $bank_guarantee->loan_account_number}}</td>
                <td>{{ $bank_guarantee->ifsc}}</td>
                <td>{{ $bank_guarantee->branch}}</td>
                <td>{{ $bank_guarantee->date }}</td>
                <td>{{ $bank_guarantee->amount }}</td>
                <td class="Action">
                    <ul class="d-flex list-unstyled mb-0 justify-content-center">
                        <li class="me-2">
                            <a href="{{route('admin.farmer.bank_guarantee.edit',$bank_guarantee->id)}}">
                                <i class="link-icon" data-feather="edit"></i>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('admin.farmer.bank_guarantee.pdf',$bank_guarantee->id)}}">
                                <i class="ti ti-download text-white"></i>
                                <i class="link-icon" data-feather="download"></i>
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>