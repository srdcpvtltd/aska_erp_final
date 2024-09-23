<?php

namespace App\Exports;

use App\Helpers\AppHelper;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceDayWiseExport implements FromView, ShouldAutoSize
{
    protected $attendanceDayWiseRecord;
    protected $filterParameter;

    function __construct($attendanceDayWiseRecord, $filterParameter)
    {
        $this->attendanceDayWiseRecord = $attendanceDayWiseRecord;
        $this->filterParameter = $filterParameter;
    }

    public function view(): View
    {
        $appTimeSetting = AppHelper::check24HoursTimeAppSetting();
        $startDate = $this->filterParameter['start_date'];
        $endDate = $this->filterParameter['end_date'];
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $period = CarbonPeriod::create($startDate, $endDate);
        $holidays = Holiday::get('event_date')->toArray();

        // Convert the period to an array of dates
        $dates = [];
        foreach ($period as $date) {
            Log::info($date);
            $dates[] = $date->format('d-m-Y');
        }

        // Initialize arrays for weekends and holidays
        $weekends = [];
        $holidayDates = [];

        // Iterate through the period and check for weekends and holidays
        foreach ($period as $date) {
            // Check if the date is a weekend
            if ($date->isWeekend()) {
                $weekends[] = $date->format('d-m-Y');
            }

            // Check if the date is a holiday
            foreach ($holidays as $holiday) {
                if ($date->format('Y-m-d') == $holiday['event_date']) {

                    $holidayDates[] = $date->format('d-m-Y');
                }
            }
        }
        return view('admin.attendance.export.attendance-day-wise-export', [
            'attendanceDayWiseRecord' => $this->attendanceDayWiseRecord,
            'dayDetail' => $this->filterParameter,
            'appTimeSetting' => $appTimeSetting,
            'dates' => $dates,
            'weekends' => $weekends,
            'holidayDates' => $holidayDates
        ]);
    }
}
