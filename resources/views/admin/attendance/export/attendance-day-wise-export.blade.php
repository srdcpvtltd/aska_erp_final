<table>
    <thead>
        <tr>
            <th align="center" colspan="5" style="text-align: center"><strong>
            Attendance Report From 
                    @if(\App\Helpers\AppHelper::ifDateInBsEnabled())
                    {{\App\Helpers\AppHelper::getFormattedNepaliDate($dayDetail['start_date'])}} to {{\App\Helpers\AppHelper::getFormattedNepaliDate($dayDetail['end_date'])}}
                    
                    @else
                    {{ date('M d Y',strtotime($dayDetail['start_date']))}}
                    @endif
                    </strong></th>
        </tr>
        <tr>
            <th><b>Employee Name</b></th>
            <!--<th style="text-align: center;"><b>Check In At</b></th>
        <th style="text-align: center;"><b>Check Out At</b></th>
        <th style="text-align: center;"><b>Total Worked Hours</b></th>
        <th style="text-align: center;"><b>Attendance Status</b></th> -->
            @foreach($dates as $date)
                <th style="text-align: center;"><b>{{$date}}</b></th>
            @endforeach
                <!-- <th style="text-align: center;"><b>Total Worked Hours</b></th> -->
                <th style="text-align: center;"><b>Total Holidays</b></th>
                <th style="text-align: center;"><b>Total WeekEnds</b></th>
        </tr>
    </thead>
    <tbody>
        @forelse($attendanceDayWiseRecord as $key => $value)
        <tr>
            <td>{{ ucfirst($value->user_name) }}</td>
            @php
                $TotalHoliday = 0;
                $TotalWeekend = 0;
            @endphp
                @foreach($dates as $all_date)
                    @php
                    $isHoliday = in_array($all_date, $holidayDates);
                    $isWeekend = in_array($all_date, $weekends);
                    @endphp

                    @if ($isHoliday)
                        @php
                        $TotalHoliday ++;
                        @endphp
                        <td align="center">Holiday</td>
                    @elseif ($isWeekend)
                    @php
                    $TotalWeekend ++;
                    @endphp
                        <td align="center">Week Off</td>
                    @elseif (date('Y-m-d', strtotime($all_date)) == $value['attendance_date'] && $value['check_in_at'] != null)
                        <td align="center">P</td>
                    @else
                        <td align="center">A</td>
                    @endif
                @endforeach
            <!-- <td align="center">
                @if($value['check_out_at'])
                {{ \App\Helpers\AttendanceHelper::getWorkedHourInHourAndMinute($value['check_in_at'], $value['check_out_at']) }}
                @else
                    0
                @endif
            </td> -->

            <td align="center">
                {{$TotalHoliday}}
            </td>

            <td align="center">
                {{$TotalWeekend}}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="{{ count($dates) + 2 }}">
                <p class="text-center"><b>No records found!</b></p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>