<?php

namespace App\Helpers;

use App\Exports\DatabaseData\LeaveMaster;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\EmployeeSalary;
use App\Models\Holiday;
use App\Models\LeaveRequestMaster;
use App\Models\OfficeTime;
use App\Models\OverTimeEmployee;
use App\Models\UnderTimeSetting;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class AttendanceHelper
{
    const WEEK_DAY_IN_NEPALI = [
        '0' => array(
            'en' => 'Sunday',
            'en_short' => 'Sun',
            'np' => 'आइतबार',
            'np_short' => 'आइत',
        ),
        '1' => array(
            'en' => 'Monday',
            'en_short' => 'Mon',
            'np' => 'सोमबार',
            'np_short' => 'सोम',
        ),
        '2' => array(
            'en' => 'Tuesday',
            'en_short' => 'Tue',
            'np' => 'मंगलबार',
            'np_short' => 'मंगल',
        ),
        '3' => array(
            'en' => 'Wednesday',
            'en_short' => 'Wed',
            'np' => 'बुधबार',
            'np_short' => 'बुध',
        ),
        '4' => array(
            'en' => 'Thursday',
            'en_short' => 'Thur',
            'np' => 'बिहिबार',
            'np_short' => 'बिहि',
        ),
        '5' => array(
            'en' => 'Friday',
            'en_short' => 'Fri',
            'np' => 'शुक्रबार',
            'np_short' => 'शुक्र',
        ),
        '6' => array(
            'en' => 'Saturday',
            'en_short' => 'Sat',
            'np' => 'शनिबार',
            'np_short' => 'शनि',
        ),
    ];

    const WEEK_DAY = [
        'sunday' => 0,
        'saturday' => 6,
        'monday' => 2,
    ];


    public static function getStartOfWeekDate($currentDate): mixed
    {
        return $currentDate->startOfWeek(self::WEEK_DAY['sunday'])->format('Y-m-d');
    }

    public static function getEndOfWeekDate($currentDate): mixed
    {
        return $currentDate->endOfWeek(self::WEEK_DAY['saturday'])->format('Y-m-d');
    }

    public static function getWeekDayFromDate($date): string
    {
        $week = date('w', strtotime($date));
        return self::WEEK_DAY_IN_NEPALI[$week]['en'];
    }

    public static function getWeekDayInShortForm($date): string
    {
        $week = date('w', strtotime($date));
        return self::WEEK_DAY_IN_NEPALI[$week]['en_short'];
    }

    public static function changeTimeFormatForAttendanceView($time): string
    {
        $appTimeSetting = AppHelper::check24HoursTimeAppSetting();
        if ($appTimeSetting) {
            return date('H:i:s', strtotime($time));
        }
        return date('h:i A', strtotime($time));
    }

    public static function changeTimeFormatForAttendanceAdminView($appTimeSetting, $time): string
    {
        if ($appTimeSetting) {
            return date('H:i:s', strtotime($time));
        }
        return date('h:i:s A', strtotime($time));
    }

    public static function getWorkedHourInHourAndMinute($checkInTime, $checkOutTime): string
    {
        $workedTimeInMinute = Carbon::createFromFormat('H:i:s', $checkInTime)->diffInMinutes($checkOutTime);
        return intdiv($workedTimeInMinute, 60) . ' hr ' . ($workedTimeInMinute) % 60 . ' min';
    }

    public static function isHolidayOrWeekendOnCurrentDate(): bool
    {
        $date = Carbon::today()->format('Y-m-d');
        $holiday = Holiday::whereDate('event_date', $date)->count();
        if ($holiday == 0) {
            $weekDay = self::getWeekDayInNumber($date);
            $weekend = Company::whereJsonContains('weekend', $weekDay)->count();
            if ($weekend > 0) {
                return false;
            }
        }
        return true;
    }

    public static function getWeekDayInNumber($date): string
    {
        return date('w', strtotime($date));
    }

    public static function getTotalNumberOfDaysInSpecificMonth($month, $year): int
    {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    public static function getEmployeeWorkedTimeInHourAndMinute($attendanceDetail): string
    {
        $productiveTimeInMin = Carbon::createFromFormat('H:i:s', $attendanceDetail['check_out_at'])->diffInMinutes($attendanceDetail['check_in_at']);
        return floor($productiveTimeInMin / 60) . ' hrs and ' . ($productiveTimeInMin - floor($productiveTimeInMin / 60) * 60) . ' min(s)';
    }

    public static function formattedAttendanceDate($isBsEnabled, $date, $changeEngToNep = true): string
    {
        if ($isBsEnabled && $changeEngToNep) {
            return  AppHelper::dateInNepaliFormatEngToNep($date);
        }
        return date('d M Y (l)', strtotime($date));
    }

    public static function getHolidayOrLeaveDetail($date, $userId)
    {

        $leaveStatus = [
            'pending' => 'P',
            'rejected' => 'R',
            'approved' => 'A',
        ];

        if (Carbon::parse($date) < Carbon::today()) {



            $holidayDetail = Holiday::whereDate('event_date', $date)->first();

            if ($holidayDetail) {
                return 'Holiday (' . (ucfirst($holidayDetail->event)) . ')';
            }

            $weekday = date('w', strtotime($date));

            $companyWeekend = Company::whereJsonContains('weekend', $weekday)->exists();

            if ($companyWeekend) {
                return 'Weekend';
            }

            $leaveDetail = LeaveRequestMaster::where('requested_by', $userId)
                ->where('status', '=', 'approved')
                ->where(function ($query) use ($date) {
                    $query->whereRaw('DATE(leave_from) <= ?', [$date])
                        ->whereRaw('DATE(leave_to) >= ?', [$date]);
                })
                ->where('early_exit', 0)
                ->first();

            if ($leaveDetail) {
                return 'Leave (' . ($leaveStatus[$leaveDetail['status']]) . ')';
            } else {
                return 'Absent';
            }
        }
    }

    public static function getMonthlyDetail($userId, $date_in_bs, $year, $month): mixed
    {
        $today = date('Y-m-d');
        $isCurrentMonth = false;
        $presentCount  = 0;
        $holidayCount  = 0;
        $absentCount  = 0;
        $leaveCount = 0;
        $weekendCount = 0;
        $totalWorkedHours = '';
        $totalWorkingHours = '';


        if ($date_in_bs) {
            $dateInAD = AppHelper::findAdDatesFromNepaliMonthAndYear($year, $month);
            $startDate = date('Y-m-d', strtotime($dateInAD['start_date'])) ?? null;
            $endDate = date('Y-m-d', strtotime($dateInAD['end_date'])) ?? null;
            $days = AppHelper::getTotalDaysInNepaliMonth($year, $month);

            $currentMonthYear = AppHelper::getCurrentNepaliYearMonth();
            if ($currentMonthYear['year'] == $year && $currentMonthYear['month'] == $month) {
                $isCurrentMonth = true;
            }
        } else {

            $days = AttendanceHelper::getTotalNumberOfDaysInSpecificMonth($month, $year);
            $firstDayOfMonth  = Carbon::create($year, $month, 1)->startOfDay();;
            $startDate = date('Y-m-d', strtotime($firstDayOfMonth));
            $endDate = date('Y-m-d', strtotime($firstDayOfMonth->endOfMonth()));

            if (date('Y') == $year && date('m' == $month)) {
                $isCurrentMonth = true;
            }
        }


        if ($isCurrentMonth) {
            $endDate = $today;
        }

        if ($startDate <= $endDate) {
            // get weekend counts
            $weekendCount = self::countWeekdaysInMonth($startDate, $endDate);

            $leaveDays = LeaveRequestMaster::where('status', '=', 'approved')
                ->where('requested_by', $userId)
                ->whereBetween('leave_from', [$startDate, $endDate])
                ->whereBetween('leave_to', [$startDate, $endDate])
                ->where('early_exit', 0)
                ->sum('no_of_days');

            $leaveCount = intval($leaveDays);

            $worked_time = Attendance::select(
                DB::raw("SUM(" .
                    "IF(worked_hour IS NOT NULL, worked_hour, " .
                    "TIME_TO_SEC(TIMEDIFF(check_out_at, check_in_at))/60" .
                    ") " .
                    ") AS total_minutes"),
                DB::raw("Count(attendance_date) as present_days"),
                DB::raw("SUM(overtime) as total_overtime"),
                DB::raw("SUM(undertime) as total_undertime"),
            )
                ->where('attendance_status', 1)
                ->whereNotNull('check_in_at')
                ->whereNotNull('check_out_at')
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('user_id', $userId)
                ->get();

            $WorkedHours = $worked_time[0]->total_minutes;
            $presentCount = $worked_time[0]->present_days;

            //            $holidayCount = Holiday::whereBetween('event_date',[$startDate, $endDate])->count();

            $holidayCounts = Holiday::whereBetween('event_date', [$startDate, $endDate])
                ->selectRaw('COUNT(*) AS total_count, SUM(DAYOFWEEK(event_date) = 7) AS weekend_count')
                ->first();

            $holidayCount = $holidayCounts->total_count ?? 0;
            $weekendHolidayCount = $holidayCounts->weekend_count ?? 0;

            $weekendCount = $weekendCount - $weekendHolidayCount;

            $shift =  User::select(
                DB::raw("TIME_FORMAT(TIMEDIFF(office_times.closing_time, office_times.opening_time), '%l') as hours")
            )
                ->leftJoin('office_times', function ($join) {
                    $join->on('users.office_time_id', '=', 'office_times.id')
                        ->where('office_times.is_active', '=', 1);
                })
                ->where('users.id', $userId)->first();

            $workingHours = $shift['hours'];
            $totalWorkingHours = $workingHours * ($days - ($weekendCount + $holidayCount)) . 'h';

            $totalWorkedHours = floor($WorkedHours / 60) . 'h ' . round(($WorkedHours - floor($WorkedHours / 60) * 60)) . 'm';


            //get days diff for current month
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $diffInDays = $start->diffInDays($end) + 1;

            $absentCount = $diffInDays - ($presentCount + $weekendCount + $leaveCount + $holidayCount);
        }

        return array(
            'totalDays' => $days,
            'totalWeekend' => $weekendCount,
            'totalPresent' => $presentCount,
            'totalHoliday' => $holidayCount,
            'totalAbsent' => $absentCount,
            'totalLeave' => $leaveCount,
            'totalWorkedHours' => $totalWorkedHours,
            'totalWorkingHours' => $totalWorkingHours,
            'totalOverTime' => isset($worked_time[0]) ? $worked_time[0]->total_overtime : 0,
            'totalUnderTime' =>  isset($worked_time[0]) ? $worked_time[0]->total_undertime : 0,
        );
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $desiredWeekday
     * @return int
     */
    public static function countWeekdaysInMonth($startDate, $endDate): int
    {
        $companyWeekend = Company::pluck('weekend')->first();
        $weekendValue = str_replace(['[', ']', '"'], '', $companyWeekend);
        $desiredWeekday = intval($weekendValue);

        $start = date('Y-m-d', strtotime($startDate));
        $end = date('Y-m-d', strtotime($endDate));
        $count = 0;

        while ($start <= $end) {
            $weekday = date('w', strtotime($start));

            if ($weekday == $desiredWeekday) {
                $count++;
            }

            $start = date('Y-m-d', strtotime("+1 day", strtotime($start)));
        }
        return $count;
    }

    /**
     * @param $employeeId
     * @return mixed
     */
    public static function checkEmployeeSalary($employeeId): mixed
    {
        return EmployeeSalary::where('employee_id', $employeeId)->count();
    }


    public static function getWeeklyDetail($userId, $date_in_bs, $start_date, $end_date): mixed
    {
        $days = 7;


        if ($date_in_bs) {

            $startDate = date('Y-m-d', strtotime($start_date)) ?? null;
            $endDate = date('Y-m-d', strtotime($end_date)) ?? null;
        } else {


            $startDate = $start_date;
            $endDate = $end_date;
        }

        // get weekend counts
        $companyWeekend = Company::pluck('weekend')->first();
        $weekendValue = str_replace(['[', ']', '"'], '', $companyWeekend);
        $desiredWeekday = intval($weekendValue);
        $weekendCount = self::countWeekdaysInMonth($startDate, $endDate, $desiredWeekday);

        $leaveDays = LeaveRequestMaster::where('status', '=', 'approved')
            ->where('requested_by', $userId)
            ->whereBetween('leave_from', [$startDate, $endDate])
            ->whereBetween('leave_to', [$startDate, $endDate])
            ->where('early_exit', 0)
            ->sum('no_of_days');

        $leaveCount = intval($leaveDays);

        $worked_time = Attendance::select(
            DB::raw("Count(attendance_date) as present_days"),
            DB::raw("SUM(overtime) as total_overtime"),
            DB::raw("SUM(undertime) as total_undertime"),
            DB::raw("SUM(" .
                "IF(worked_hour IS NOT NULL, worked_hour, " .
                "TIME_TO_SEC(TIMEDIFF(check_out_at, check_in_at))/60" .
                ") " .
                ") AS total_minutes"),
        )
            ->where('attendance_status', 1)
            ->whereNotNull('check_in_at')
            ->whereNotNull('check_out_at')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where('user_id', $userId)
            ->get();

        $WorkedHours = $worked_time[0]->total_minutes;
        $presentCount = $worked_time[0]->present_days;

        $holidayCounts = Holiday::whereBetween('event_date', [$startDate, $endDate])
            ->selectRaw('COUNT(*) AS total_count, SUM(DAYOFWEEK(event_date) = 7) AS weekend_count')
            ->first();

        $holidayCount = $holidayCounts->total_count ?? 0;
        $weekendHolidayCount = $holidayCounts->weekend_count ?? 0;

        $weekendCount = $weekendCount - $weekendHolidayCount;

        $totalWorkedHours = floor($WorkedHours / 60) . 'h ' . round(($WorkedHours - floor($WorkedHours / 60) * 60)) . 'm';

        //get days diff for current month
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $diffInDays = $start->diffInDays($end) + 1;

        $absentCount = $diffInDays - ($presentCount + $weekendCount + $leaveCount + $holidayCount);

        return array(
            'totalDays' => $days,
            'totalWeekend' => $weekendCount,
            'totalPresent' => $presentCount,
            'totalHoliday' => $holidayCount,
            'totalAbsent' => $absentCount,
            'totalLeave' => $leaveCount,
            'totalWorkedHours' => $totalWorkedHours,
            'totalOverTime' =>  $worked_time[0]->total_overtime,
            'totalUnderTime' =>  $worked_time[0]->total_undertime,
        );
    }

    /**
     * @throws \Exception
     */
    public static function calculateWorkedHour($checkOutTime, $checkInTime, $employeeId): array
    {
        $shift =  User::select(
            'office_times.closing_time',
            'office_times.opening_time'
        )
            ->leftJoin('office_times', function ($join) {
                $join->on('users.office_time_id', '=', 'office_times.id')
                    ->where('office_times.is_active', '=', 1);
            })
            ->where('users.id', $employeeId)->first();

        $workingTime = self::calculateTime($shift['closing_time'], $shift['opening_time']);

        $workedTime = self::calculateTime($checkOutTime, $checkInTime);

        // calculate overTime
        $overTime = OverTimeEmployee::select('over_time_settings.*')
            ->leftJoin('over_time_settings', function ($join) {
                $join->on('over_time_employees.over_time_setting_id', '=', 'over_time_settings.id')
                    ->where('over_time_settings.is_active', 1);
            })
            ->where('over_time_employees.employee_id', $employeeId)
            ->first();
        $extraWorkedTime = 0;
        if (isset($overTime)) {
            if ($workedTime > $workingTime) {

                $extraTime = $workedTime - $workingTime;
                if ($extraTime >= ($overTime->valid_after_hour * 60)) {

                    if ($extraWorkedTime > ($overTime->max_daily_ot_hours * 60)) {
                        $extraWorkedTime = ($overTime->max_daily_ot_hours * 60);
                    }
                } else {
                    $extraWorkedTime = 0;
                }
            }
        }

        // calculate underTime
        $underTime = UnderTimeSetting::where('is_active', 1)->first();


        if (isset($underTime)) {

            $timeToWork = $workingTime - $underTime->applied_after_minutes;

            if ($workedTime < $timeToWork) {
                $workTimeDeficiency = $timeToWork - $workedTime;
            }
        }

        return [
            'workedHours' => round($workedTime, 2),
            'overtime' => isset($extraWorkedTime) ? round($extraWorkedTime, 2) : 0,
            'undertime' => isset($workTimeDeficiency) ? round($workTimeDeficiency, 2) : 0,
        ];
    }

    /**
     * @throws \Exception
     */
    public static function calculateTime($outTime, $inTime): float|int
    {
        $dateTime1 = new DateTime($outTime);
        $dateTime2 = new DateTime($inTime);

        // Calculate the difference between the two DateTime objects
        $interval = $dateTime1->diff($dateTime2);

        // Convert the difference to hours and minutes
        $hours = $interval->h;
        $minutes = $interval->i;
        return ($interval->days * 24 * 60) + ($hours * 60) + $minutes;
    }

    public static function payslipDate($date): string
    {

        $isBsEnabled = AppHelper::ifDateInBsEnabled();

        if ($isBsEnabled) {
            return  AppHelper::dateInYmdFormatEngToNep($date);
        }
        return date('Y-m-d', strtotime($date));
    }

    public static function payslipDuration($startDate, $endDate): string
    {
        $isBsEnabled = AppHelper::ifDateInBsEnabled();


        if ($isBsEnabled) {
            $fromMonth =   AppHelper::getMonthYear($startDate);
            $toMonth  = AppHelper::getMonthYear($endDate);

            $sMonth = explode(' ', $fromMonth);
            $eMonth = explode(' ', $fromMonth);
            if ($fromMonth != $toMonth) {
                $duration = $sMonth[0] . '/' . $eMonth[0] . ' ' . $eMonth[1];
            } else {
                $duration = $fromMonth;
            }
        } else {
            $fromMonth = date('F', strtotime($startDate));
            $toMonth = date('F', strtotime($endDate));

            if ($fromMonth != $toMonth) {
                $duration = $fromMonth . '/' . $toMonth . ' ' . date('Y', strtotime($endDate));
            } else {
                $duration = $fromMonth . ' ' . date('Y', strtotime($startDate));
            }
        }
        return $duration;
    }

    public static function paidDate($date): string
    {
        $isBsEnabled = AppHelper::ifDateInBsEnabled();

        if ($isBsEnabled) {
            return  AppHelper::formatDateForView($date);
        }
        return date('d/m/Y', strtotime($date));
    }
}
