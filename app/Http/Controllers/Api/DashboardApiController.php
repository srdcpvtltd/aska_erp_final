<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AppHelper;
use App\Helpers\AttendanceHelper;
use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Repositories\FeatureRepository;
use App\Repositories\UserRepository;
use App\Resources\Dashboard\CompanyWeekendResource;
use App\Resources\Dashboard\EmployeeTodayAttendance;
use App\Resources\Dashboard\EmployeeWeeklyReport;
use App\Resources\Dashboard\FeatureCollection;
use App\Resources\Dashboard\FeatureResource;
use App\Resources\Dashboard\OfficeTimeResource;
use App\Resources\Dashboard\OverviewResource;
use App\Resources\Dashboard\UserReportResource;
use App\Resources\Holiday\HolidayCollection;
use App\Resources\User\CompanyResource;
use App\Resources\User\HolidayResource;
use App\Resources\User\TeamSheetCollection;
use App\Services\Holiday\HolidayService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DashboardApiController extends Controller
{
    public function __construct(protected UserRepository $userRepo, protected HolidayService $holidayService, protected FeatureRepository $featureRepository)
    {}

    public function userDashboardDetail(Request $request): JsonResponse
    {
        try {
            $fcmToken =  $request->header('fcm_token');
            $nfc_key = 'create_nfc';
            $userId = getAuthUserCode();
            $with = [
                'branch:id,name',
                'company:id,name,weekend',
                'post:id,post_name',
                'department:id,dept_name',
                'role:id,name',
                'officeTime',
                'employeeTodayAttendance:user_id,check_in_at,check_out_at,attendance_date',
                'employeeWeeklyAttendance:user_id,check_in_at,check_out_at,attendance_date'
            ];
            $dashboard = [];
            $select = ['users.*', 'branch_id', 'company_id', 'department_id', 'post_id', 'role_id'];
            $date = AppHelper::yearDetailToFilterData();

            $userDetail = $this->userRepo->findUserDetailById($userId, $select, $with);

            $this->userRepo->updateUserFcmToken($userDetail,$fcmToken);

            $teamMembers = $this->userRepo->getAllActiveEmployeeOfDepartment($userDetail->department_id,['*'],['branch:id,name', 'post:id,post_name', 'department:id,dept_name']);

            $holiday = $this->holidayService->getCurrentActiveHoliday();

            $overview = $this->userRepo->getEmployeeOverviewDetail($userId,$date);
            $shiftDates = $this->getAllDatesForShiftNotification($userDetail);
            $features = $this->featureRepository->getAllFeatures();

            $dashboard['user'] = new UserReportResource($userDetail);
            $dashboard['employee_today_attendance'] = new EmployeeTodayAttendance($userDetail);
            $dashboard['overview'] = new OverviewResource($overview);
            $dashboard['office_time'] = new OfficeTimeResource($userDetail);
            $dashboard['company'] = new CompanyWeekendResource($userDetail);
            $dashboard['employee_weekly_report'] = new EmployeeWeeklyReport($userDetail);
            $dashboard['date_in_ad'] = !AppHelper::ifDateInBsEnabled();
            $dashboard['shift_dates'] = $shiftDates;
            $dashboard['features'] = new FeatureCollection($features);
            $dashboard['teamMembers'] = new TeamSheetCollection($teamMembers);
            $dashboard['add_nfc'] = AppHelper::checkRoleIdWithGivenPermission($userDetail->role_id, $nfc_key);

            if (isset($holiday)) {
                $dashboard['recent_holiday'] = new HolidayResource($holiday);
            } else {
                $dashboard['recent_holiday'] = null;
            }

            return AppHelper::sendSuccessResponse('Data Found', $dashboard);
        } catch (Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), 400);
        }
    }

    private function getAllDatesForShiftNotification($userDetail)
    {
        try{
            $dates = [];
            $numberOfDays = AppHelper::getDaysToFindDatesForShiftNotification();
            $holidaysDetail = $this->holidayService->getAllActiveHolidaysFromNowToGivenNumberOfDays($numberOfDays);
            $weekendWeekDays = $userDetail->company->weekend;
            $nowDate = Carbon::now();
            $endDate = Carbon::now()->addDay($numberOfDays);

            while ($nowDate <= $endDate) {
                $isHoliday = in_array($nowDate->format('Y-m-d'), $holidaysDetail);
                $isWeekend = in_array($nowDate->dayOfWeek, $weekendWeekDays);
                if( !$isHoliday && !$isWeekend){
                  $dates[] = $nowDate->format('Y-m-d');
                }
                $nowDate->addDay();
            }
           return $dates;
        }catch(Exception $exception){
            throw $exception;
        }
    }

}



