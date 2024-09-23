<?php

namespace App\Helpers;

use App\Helpers\SMPush\SMPushHelper;
use App\Models\AppSetting;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Department;
use App\Models\GeneralSetting;
use App\Models\PaymentCurrency;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppHelper
{
    const IS_ACTIVE = 1;

    const MONTHS = [
        '1' => array(
            'en' => 'Jan',
            'np' => 'Baishakh',
        ),
        '2' => array(
            'en' => 'Feb',
            'np' => 'Jestha',
        ),
        '3' => array(
            'en' => 'Mar',
            'np' => 'Asar',
        ),
        '4' => array(
            'en' => 'Apr',
            'np' => 'Shrawan',
        ),
        '5' => array(
            'en' => 'May',
            'np' => 'Bhadra',
        ),
        '6' => array(
            'en' => 'Jun',
            'np' => 'Ashwin',
        ),
        '7' => array(
            'en' => 'Jul',
            'np' => 'kartik',
        ),
        '8' => array(
            'en' => 'Aug',
            'np' => 'Mangsir',
        ),
        '9' => array(
            'en' => 'Sept',
            'np' => 'Poush',
        ),
        '10' => array(
            'en' => 'Oct',
            'np' => 'Magh',
        ),
        '11' => array(
            'en' => 'Nov',
            'np' => 'Falgun',
        ),
        '12' => array(
            'en' => 'Dec',
            'np' => 'Chaitra',
        ),

    ];

    public static function getAuthUserCompanyId(): int
    {
        $user = auth()->user();
        if (!$user) {
            throw new Exception('unauthenticated', 401);
        }
        $companyId = optional($user)->company_id;
        if (!$companyId) {
            throw new Exception('User Company Id not found', 401);
        }
        return $companyId;
    }

    public static function getCompanyLogo()
    {
        $company = Company::select('logo')->first();
        return optional($company)->logo;
    }

    public static function getAuthUserRole()
    {
        $user = auth()->user();
        if (!$user) {
            throw new Exception('unauthenticated', 401);
        }
        return $user->role->name;
    }

    public static function findAdminUserAuthId()
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->first();
        if (!$user) {
            throw new Exception('Admin User Not Found', 400);
        }
        return $user->id;
    }

    public static function getAuthUserBranchId()
    {
        $user = auth()->user();
        if(!$user){
            throw new Exception('unauthenticated',401);
        }
        $branchId = optional($user)->branch_id;
        if (!$branchId) {
            throw new Exception('User Branch Id Not Found',400);
        }
        return $branchId;
    }

    public static function getFirebaseServerKey(): mixed
    {
        return GeneralSetting::where('key', 'firebase_key')->value('value') ?: '';
    }

    public static function sendErrorResponse($message, $code = 500, array $errorFields = null): JsonResponse
    {
        $response = [
            'status' => false,
            'message' => $message,
            'status_code' => $code,
        ];
        if (!is_null($errorFields)) {
            $response['data'] = $errorFields;
        }
        if ($code < 200 || !is_numeric($code) || $code > 599) {
            $code = 500;
            $response['code'] = $code;
        }
        return response()->json($response, $code);
    }

    public static function sendSuccessResponse($message, $data = null, $headers = [], $options = 0): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message,
            'status_code' => 200,

        ];
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, 200, $headers, $options);
    }

    public static function getProgressBarStyle($progressPercent): string
    {
        $width = 'width: ' . $progressPercent . '%;';

        if ($progressPercent >= 0 && $progressPercent < 26) {
            $color = 'background-color:#C1E1C1';
        } elseif ($progressPercent >= 26 && $progressPercent < 51) {
            $color = 'background-color:#C9CC3F';
        } elseif ($progressPercent >= 51 && $progressPercent < 76) {
            $color = 'background-color: #93C572';
        } else {
            $color = 'background-color:#3cb116';
        }
        return $width . $color;
    }

    public static function convertLeaveDateFormat($dateTime, $changeEngToNep = true): string
    {

        if (self::check24HoursTimeAppSetting()) {
            if (self::ifDateInBsEnabled() && $changeEngToNep) {
                $date = self::getDayMonthYearFromDate($dateTime);
                $dateInBs = (new DateConverter())->engToNep($date['year'], $date['month'], $date['day']);
                $time = date('H:i A', strtotime($dateTime));
                return $dateInBs['date'] . ' ' . $dateInBs['nmonth'] . ' ' . $time;
            }
            return date('M d H:i A', strtotime($dateTime));
        } else {
            if (self::ifDateInBsEnabled() && $changeEngToNep) {
                $date = self::getDayMonthYearFromDate($dateTime);
                $dateInBs = (new DateConverter())->engToNep($date['year'], $date['month'], $date['day']);
                $time = date('h:i A', strtotime($dateTime));
                return $dateInBs['date'] . ' ' . $dateInBs['nmonth'] . ' ' . $time;
            }
            return date('M d h:i A', strtotime($dateTime));
        }
    }

    public static function check24HoursTimeAppSetting(): bool
    {
        $slug = '24-hour-format';
        return AppSetting::where('slug', $slug)->where('status', 1)->exists();
    }

    public static function ifDateInBsEnabled(): bool
    {
        $slug = 'bs';
        return AppSetting::where('slug', $slug)->where('status', 1)->exists();
    }

    public static function isAuthorizeLogin(): bool
    {
        $slug = 'authorize-login';
        return AppSetting::where('slug', $slug)->where('status', 1)->exists();
    }


    public static function getDayMonthYearFromDate($date): array
    {
        return [
            'year' => date('Y', strtotime($date)),
            'month' => date('n', strtotime($date)),
            'day' => date('d', strtotime($date)),
        ];
    }

    public static function getCurrentDateInYmdFormat(): string
    {
        return Carbon::now()->format('Y-m-d');
    }

    public static function getCurrentYear(): string
    {
        return Carbon::now()->format('Y');
    }

    public static function getFormattedNepaliDate($date): string
    {
        $explodedData = explode('-', $date);
        $data = [
            'year' => $explodedData[0],
            'month' => (int)($explodedData[1]),
            'day' => $explodedData[2]
        ];
        return $data['day'] . ' '.AppHelper::MONTHS[$data['month']]['np']. ' ' . $data['year'];
    }


    public static function dateInYmdFormatEngToNep($date): string
    {
        $date = self::getDayMonthYearFromDate($date);
        $dateInAd = (new NepaliDate())->convertAdToBs($date['year'], $date['month'], $date['day']);

        return $dateInAd['year'] . '-' . $dateInAd['month'] . '-' . $dateInAd['day'];
    }

    public static function dateInNepaliFormatEngToNep($date): string
    {
        $_nepaliDate = new NepaliDate();
        $date = self::getDayMonthYearFromDate($date);
        $dateInAd = $_nepaliDate->convertAdToBs($date['year'], $date['month'], $date['day']);
        $monthName = $_nepaliDate->getNepaliMonth($dateInAd['month']);
        $weekDayName = $_nepaliDate->getDayOfTheWeek($dateInAd['weekday']);

        return  $dateInAd['day']. ' ' . $monthName . ' '. $dateInAd['year'] . ' ('.$weekDayName.')';
    }

    public static function getNepaliDay($date): string
    {
        $_nepaliDate = new NepaliDate();
        $date = self::getDayMonthYearFromDate($date);
        $dateInAd = $_nepaliDate->convertAdToBs($date['year'], $date['month'], $date['day']);

        return  $dateInAd['day'];
    }

    public static function dateInDDMMFormat($date, $dateEngToNep = true): string
    {
        if ($dateEngToNep) {
            $date = explode(' ', self::formatDateForView($date));
            return $date[0];
        }
        return date('d', strtotime($date));
    }

    public static function formatDateForView($date, $changeEngToNep = true): string
    {
        if (self::ifDateInBsEnabled() && $changeEngToNep) {
            $date = self::getDayMonthYearFromDate($date);
            $dateInBs = (new DateConverter())->engToNep($date['year'], $date['month'], $date['day']);
            return $dateInBs['date'] . ' ' . $dateInBs['nmonth'] . ' ' . $dateInBs['year'];
        }
        return date('d M Y', strtotime($date));
    }

    public static function getTotalDaysInNepaliMonth($year, $month): int
    {
        return (new DateConverter())->getTotalDaysInMonth($year, $month);
    }

    public static function yearDetailToFilterData()
    {
        $dateArray = [
            'start_date' => null,
            'end_date' => null,
            'year' => Carbon::now()->format('Y-m-d'),
        ];
        if (self::ifDateInBsEnabled()) {
            $nepaliDate = self::getCurrentNepaliYearMonth();
            $dateInAD = self::findAdDatesFromNepaliMonthAndYear($nepaliDate['year']);
            $dateArray['start_date'] = $dateInAD['start_date'];
            $dateArray['end_date'] = $dateInAD['end_date'];
        }
        return $dateArray;
    }

    public static function getCurrentNepaliYearMonth(): array
    {
        return (new DateConverter())->getCurrentMonthAndYearInNepali();
    }

    public static function findAdDatesFromNepaliMonthAndYear($year, $month = ''): array
    {
        if (!empty($month)) {
            return (new DateConverter())->getStartAndEndDateFromGivenNepaliMonth($year, $month);
        }
        return (new DateConverter())->getStartAndEndDateOfYearFromGivenNepaliYear($year);
    }

    public static function getCurrentDateInBS(): string
    {
        return (new DateConverter())->getTodayDateInBS();
    }

    public static function weekDay($date): string
    {
        if (self::ifDateInBsEnabled()) {
            $date = self::dateInYmdFormatNepToEng($date);
        }
        return date('D', strtotime($date));
    }

    public static function dateInYmdFormatNepToEng($date): string
    {
        $date = self::getDayMonthYearFromDate($date);
        $dateInAd = (new DateConverter())->nepToEng($date['year'], $date['month'], $date['day']);
        return $dateInAd['year'] . '-' . $dateInAd['month'] . '-' . $dateInAd['date'];
    }

    public static function dateInYmdFormatNepToEngForProject($date): string
    {
        $explodedData = explode('-', $date);
        $date = [
                'year' => $explodedData[0],
                'month' => $explodedData[1],
                'day' => $explodedData[2]
            ];
        $dateInAd = (new DateConverter())->nepToEng($date['year'], $date['month'], $date['day']);
        return $dateInAd['year'] . '-' . $dateInAd['month'] . '-' . $dateInAd['date'];
    }

    public static function nepToEngDateInYmdFormat($date): string
    {
        $explodedData = explode('-', $date);
        $date = [
            'year' => $explodedData[0],
            'month' => $explodedData[1],
            'day' => $explodedData[2]
        ];
        $dateInAd = (new DateConverter())->nepToEng($date['year'], $date['month'], $date['day']);
        return $dateInAd['year'] . '-' . $dateInAd['month'] . '-' . $dateInAd['date'];
    }

    public static function getFormattedAdDateToBs($englishDate): string
    {
        $date = self::getDayMonthYearFromDate($englishDate);
        $dateInBs = (new DateConverter())->engToNep($date['year'], $date['month'], $date['day']);
        return $dateInBs['date'] . ' ' . $dateInBs['nmonth'] . ' ' . $dateInBs['year'];
    }

    public static function getBsNxtYearEndDateInAd()
    {
        $addYear = 1;
        $nepaliDate = self::getCurrentNepaliYearMonth();
        $dateInAD = self::findAdDatesFromNepaliMonthAndYear($nepaliDate['year'] + $addYear);
        return $dateInAD['end_date'];
    }

    public static function getBackendLoginAuthorizedRole()
    {
        if (Cache::has('role')) {
            return Cache::get('role');
        } else {
            $roles = [];
            $backendAuthorizedLoginRole = Role::select('name')->get();
            foreach ($backendAuthorizedLoginRole as $key => $value) {
                $roles[] = $value->name;
            }
            Cache::forever('role', $roles);
        }
        return $roles;
    }


    public static function getTheme()
    {
//        if (Cache::has('theme')){
//            return Cache::get('theme');
//        } else {
//            $getTheme = AppSetting::select('status')->where('slug','dark-theme')->first();
//            $theme = $getTheme->status ? 'light' : 'dark' ;
//            Cache::forever('theme', $theme);
//        }
        return $theme = 'light';
    }

    public static function employeeTodayAttendanceDetail()
    {
        $today = Carbon::today();
        $userId = auth()->id();
        return Attendance::select(['attendance_date', 'check_in_at', 'check_out_at'])
            ->where('user_id', $userId)
            ->whereDate('attendance_date', $today)
            ->first();
    }

    public static function getDaysToFindDatesForShiftNotification()
    {
        $key = 'attendance_notify';
        return GeneralSetting::where('key',$key)->value('value') ?? 0;
    }

    public static function getAllRoleIdsWithGivenPermission($permissionKey)
    {
        return DB::table('permission_roles')
            ->leftJoin('permissions', function ($query) {
                $query->on('permission_roles.permission_id', '=', 'permissions.id');
            })
            ->Join('roles', function ($query) {
                $query->on('roles.id', '=', 'permission_roles.role_id')
                    ->where('roles.is_active',self::IS_ACTIVE);
            })
            ->where('permissions.permission_key', $permissionKey)
            ->pluck('permission_roles.role_id')
            ->toArray();
    }

    public static function sendNotificationToAuthorizedUser($title, $message, $permissionKey): void
    {
        $roleIds =  AppHelper::getAllRoleIdsWithGivenPermission($permissionKey);
        if(!empty($roleIds)){
            SMPushHelper::sendNotificationToAuthorizedUsers($title, $message,$roleIds);
        }
    }

    public static function sendNotificationToDepartmentHead($title, $message,$departmentId): void
    {
        $department = Department::where('id',$departmentId)->first();

        if (!$department) {
            throw new Exception('Department not found', 404);
        }

        $departmentHeadId = $department->dept_head_id;

        if(isset($departmentHeadId)){
            SMPushHelper::sendNotificationToDepartmentHead($title, $message,$departmentHeadId);
        }
    }


    public static function getCompanyPaymentCurrencySymbol()
    {
        return Cache::remember('payment_currency_symbol', now()->addYear(), function () {
            $paymentCurrency = PaymentCurrency::first();
            if (!$paymentCurrency) {
                $paymentCurrency = PaymentCurrency::create([
                    'name' => 'Nepalese Rupee',
                    'code' => 'NPR',
                    'symbol' => 'Rs'
                ]);
            }
            return $paymentCurrency->symbol;
        });
    }

    public static function getMaxAllowedAdvanceSalaryLimit()
    {
        $key = 'advance_salary_limit';
        return GeneralSetting::where('key',$key)->value('value') ?? 0;
    }


    public static function getNepaliMonthName($month){
        $_nepaliDate = new NepaliDate();

        return $_nepaliDate->getNepaliMonth($month);
    }

    public static function getMonthYear($date){

        if (self::ifDateInBsEnabled()) {
            $_nepaliDate = new NepaliDate();
            $date = self::getDayMonthYearFromDate($date);
            $dateInAd = $_nepaliDate->convertAdToBs($date['year'], $date['month'], $date['day']);

            $month = $_nepaliDate->getNepaliMonth($dateInAd['month']);
            $year = $dateInAd['year'];

        }else{
            $date = date('Y-m-d',strtotime($date));
            // Extract day, month, and year
            $day = date('d', strtotime($date));
            $month = date('F', strtotime($date));
            $year = date('Y', strtotime($date));
        }

        return $month .' '. $year;
    }


    public static function getEmployeeCodePrefix()
    {
        $key = 'employee_code_prefix';
        return GeneralSetting::where('key',$key)->value('value') ?? '';
    }

    public static function getEmployeeCode(){
        $user = User::orderBy('created_at', 'desc')->first('employee_code');

        $prefix = self::getEmployeeCodePrefix();

        $code = $prefix.'-'.str_pad( 1, 5, '0', STR_PAD_LEFT);

        if(isset($user) && $user->employee_code){

            $codeNumber = explode("-",$user->employee_code);

            $codeId = (int)$codeNumber[1] +1;
            $code = $prefix.'-'.str_pad( $codeId, 5, '0', STR_PAD_LEFT);

        }
        return $code;

    }
    public static function getUserShift()
    {
        $user = auth()->user();
        if (!$user) {
            throw new Exception('unauthenticated', 401);
        }

        $shift = User::select('office_times.opening_time','office_times.closing_time','office_times.checkin_before','office_times.checkout_before','office_times.checkin_after','office_times.checkout_after')
            ->leftJoin('office_times','users.office_time_id','office_times.id')
            ->where('users.id',$user->id)->first();

        return $shift;
    }

    public static function timeLeaverequestDate($date): string
    {

        if (self::ifDateInBsEnabled()) {
            $date = self::getDayMonthYearFromDate($date);
            $dateInBs = (new DateConverter())->engToNep($date['year'], $date['month'], $date['day']);
            return  $dateInBs['month'] .'/'.$dateInBs['date'] . '/' . $dateInBs['year'];
        }
        return date('Y-m-d', strtotime($date));
    }

    public static function getEnglishDate($date): string
    {
        if (self::ifDateInBsEnabled()) {
          return self::nepToEngDateInYmdFormat($date);
        }
        return $date;
    }

    public static function getMonthsList()
    {
        if (self::ifDateInBsEnabled()) {
            $months = [
                '1' => 'Baishakh',
                '2' => 'Jestha',
                '3' => 'Asar',
                '4' => 'Shrawan',
                '5' => 'Bhadra',
                '6' => 'Ashwin',
                '7' => 'Kartik',
                '8' => 'Mangsir',
                '9' => 'Poush',
                '10' => 'Magh',
                '11' => 'Falgun',
                '12' => 'Chaitra'
            ];
        }else{
            $months =  [
                '1' => 'January',
                '2' => 'February',
                '3' => 'March',
                '4' => 'April',
                '5' => 'May',
                '6' => 'June',
                '7' => 'July',
                '8' => 'August',
                '9' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December'
            ];
        }


        return $months;
    }

    public static function getCurrentYearMonth()
    {
        $converter = new DateConverter();
        return $converter->getCurrentMonthAndYearInNepali();

    }

    public static function getweeksList($year)
    {
        $weeks = [];
        date_default_timezone_set('UTC');
        if (self::ifDateInBsEnabled()) {
            $date = self::findAdDatesFromNepaliMonthAndYear($year);
            // Define start and end dates
            $startDateStr = $date['start_date'];
            $endDateStr = $date['end_date'];

            // Convert start and end dates to DateTime objects
            $startDate = new DateTime($startDateStr);
            $endDate = new DateTime($endDateStr);

            // Adjust start date to the first day of the week (usually Sunday or Monday)
            $startDate->modify('last sunday');


            // Loop through each week between the start and end dates
            $i = 1;
            while ($startDate->format('Y-m-d') <= $endDate->format('Y-m-d')) {
                $weekStartDate = clone $startDate;
                $weekEndDate = clone $weekStartDate;

                // Set end date of the week to the following Saturday
                $weekEndDate->modify('next saturday');

                // Add week details to the array
                $weeks[] = [
                    'week_value' => $weekStartDate->format('Y-m-d') .' to '. $weekEndDate->format('Y-m-d'),
                    'week' => self::timeLeaverequestDate($weekStartDate->format('Y-m-d')) .' to '.self::timeLeaverequestDate($weekEndDate->format('Y-m-d')),
                ];

                $i++;

                // Move to the next Sunday to start the next week
                $startDate->modify('next sunday');
            }

        } else {


            // Get the first day of the year
            $startDate = new DateTime($year . '-01-01');
            $startDate->modify('midnight');

            // Get the last day of the year
            $endDate = new DateTime($year . '-12-31');
            $endDate->modify('midnight');

            // Adjust start date to the first day of the week (usually Sunday or Monday)
            $startDate->modify('last sunday');

            // Loop through each week of the year

            for ($i = 0; $i < 52; $i++) {
                $weekStartDate = clone $startDate;
                $weekEndDate = clone $weekStartDate;
                $weekEndDate->modify('+6 days'); // Set end date of the week to 6 days after the start

                $weeks[] = [
                    'week_value' => $weekStartDate->format('Y-m-d') .' to '. $weekEndDate->format('Y-m-d'),
                    'week' => $weekStartDate->format('Y-m-d').' to '.$weekEndDate->format('Y-m-d'),
                ];

                $startDate->modify('+1 week'); // Move to the next week
            }


        }
        return $weeks;

    }

    public static function getStartEndDateForLeaveCalendar()
    {
        $date = [];
        if (self::ifDateInBsEnabled()) {
            $nepaliDate = self::getCurrentYearMonth();

            $startMonth = self::findAdDatesFromNepaliMonthAndYear($nepaliDate['year'], $nepaliDate['month']);
            if($nepaliDate['month'] == 12){
                $year = $nepaliDate['year'] +1;
                $month = 1;
            }else{
                $year = $nepaliDate['year'];
                $month = $nepaliDate['month'] + 1;
            }
            $endMonth = self::findAdDatesFromNepaliMonthAndYear($year, $month);

            $date['start_date'] = $startMonth['start_date'];
            $date['end_date'] =$endMonth['end_date'];
        }else{
            $startMonth = \Illuminate\Support\Carbon::now()->startOfMonth();
            $date['start_date'] = $startMonth->firstOfMonth()->format('Y-m-d');
            $endMonth = Carbon::now()->startOfMonth()->addMonth(1);
            $date['end_date'] = $endMonth->endOfMonth()->format('Y-m-d');
        }
        return $date;
    }

    public static function convertLeaveTimeFormat($time): string
    {
        if (self::check24HoursTimeAppSetting()) {
            return date('H:i', strtotime($time));
        } else {
           return date('h:i A', strtotime($time));
        }
    }

    public static function checkRoleIdWithGivenPermission($roleId, $permissionKey)
    {
        $hasPermission =  DB::table('permission_roles')
            ->join('permissions', 'permission_roles.permission_id', '=', 'permissions.id')
            ->join('roles', function ($join) use ($roleId) {
                $join->on('roles.id', '=', 'permission_roles.role_id')
                    ->where('roles.id', $roleId)
                    ->where('roles.is_active', self::IS_ACTIVE);
            })
            ->where('permissions.permission_key', $permissionKey)
            ->first();

        return isset($hasPermission);
    }

}
