<div class="table-responsive">
    <table class="table datatable">
        <thead>
        <tr>
            <th>{{__('G-Code No')}}</th>
            <th>{{__('Guarantor Name')}}</th>
            <th>{{__('Father Name')}}</th>
            <th>{{__('District')}}</th>
            <th>{{__('Block')}}</th>
            <th>{{__('Village')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($guarantors as $guarantor)
            <tr class="font-style">
                <td>{{ $guarantor->farming->g_code}}</td>
                <td>{{ $guarantor->naming->name}}</td>
                <td>{{ $guarantor->father_name}}</td>
                <td>{{ @$guarantor->district->name }}</td>
                <td>{{ @$guarantor->block->name }}</td>
                <td>{{ @$guarantor->village->name }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>