<?php

namespace App\Services\Attendance;

use App\Enum\EmployeeAttendanceTypeEnum;
use App\Helpers\AppHelper;
use App\Helpers\AttendanceHelper;
use App\Helpers\DateConverter;
use App\Models\Attendance;
use App\Models\User;
use App\Repositories\AppSettingRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\BranchRepository;
use App\Repositories\LeaveRepository;
use App\Repositories\RouterRepository;
use App\Repositories\TimeLeaveRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceService
{

    public function __construct(protected AttendanceRepository $attendanceRepo,
                                protected UserRepository       $userRepo,
                                protected RouterRepository     $routerRepo,
                                protected AppSettingRepository $appSettingRepo,
                                protected LeaveRepository $leaveRepo,
                                protected TimeLeaveRepository $timeLeaveRepository,
                                protected BranchRepository $branchRepository,
    )
    {}

    /**
     * @param $filterParameter
     * @return mixed
     * @throws Exception
     */
    public function getAllCompanyEmployeeAttendanceDetailOfTheDay($filterParameter): mixed
    {
        
        if($filterParameter['date_in_bs']){
            $filterParameter['attendance_date'] = AppHelper::dateInYmdFormatNepToEng($filterParameter['attendance_date']);
        }
        // dd($filterParameter);
        return $this->attendanceRepo->getAllCompanyEmployeeAttendanceDetailOfTheDay($filterParameter);

    }

    public function getAllCompanyEmployeeRegularizationDetailOfTheDay($filterParameter): mixed
    {

        if($filterParameter['date_in_bs']){
            $filterParameter['regularization_date'] = $filterParameter['regularization_date'] != null?AppHelper::dateInYmdFormatNepToEng($filterParameter['regularization_date']):$filterParameter['regularization_date'];
        }
        return $this->attendanceRepo->getAllCompanyEmployeeregularizationDetailOfTheDay($filterParameter);

    }

    /**
     * @param $filterParameter
     * @param array $select
     * @param array $with
     * @return Builder[]|Collection
     * @throws Exception
     */
    public function getEmployeeAttendanceDetailOfTheMonth($filterParameter, array $select = ['*'], array $with = []): Collection|array
    {
        try {
//            $days = $filterParameter['date_in_bs']
//                ? AppHelper::getTotalDaysInNepaliMonth($filterParameter['year'], $filterParameter['month'])
//                : AttendanceHelper::getTotalNumberOfDaysInSpecificMonth($filterParameter['month'], $filterParameter['year']);

            if($filterParameter['date_in_bs']){
                $dateInAD = AppHelper::findAdDatesFromNepaliMonthAndYear($filterParameter['year'], $filterParameter['month']);
                $filterParameter['start_date'] = date('Y-m-d',strtotime($dateInAD['start_date'])) ?? null;

                $filterParameter['end_date'] = date('Y-m-d',strtotime($dateInAD['end_date'])) ?? null;
            }else{
                $fiirstDay = $filterParameter['year'].'-'.$filterParameter['month'].'-'.'01';
                $filterParameter['start_date'] = date('Y-m-d',strtotime($fiirstDay));
                $filterParameter['end_date'] = date('Y-m-t',strtotime($fiirstDay));
            }

            $today = date('Y-m-d');
            if($filterParameter['end_date'] > $today){
                $filterParameter['end_date'] = $today;
            }



            $employeeMonthlyAttendance = [];
//            for ($i = 1; $i <= $days; ++$i) {
//                $attendance_date = $filterParameter['date_in_bs']
//                    ? $filterParameter['year'] . '-' . $filterParameter['month'] . '-' . $i
//                    : Carbon::createFromDate($filterParameter['year'], $filterParameter['month'], $i)->format('Y-m-d');
//
//                $employeeMonthlyAttendance[] = ['attendance_date' => $attendance_date];
//            }
            $attendanceDetail = $this->attendanceRepo->getEmployeeAttendanceDetailOfTheMonth($filterParameter, $select);

            if(($filterParameter['start_date'] <= $today) && $attendanceDetail->isNotEmpty()){
                do{
                    $employeeMonthlyAttendance[] = [
                        'attendance_date' => $filterParameter['start_date'],
                    ];

                    $filterParameter['start_date'] = date('Y-m-d', strtotime("+1 day", strtotime($filterParameter['start_date'] )));

                }while($filterParameter['start_date'] <= $filterParameter['end_date']);
            }

            foreach ($attendanceDetail as $key => $value)
            {

                if($filterParameter['date_in_bs']){
                    $getDay = AppHelper::getNepaliDay($value->attendance_date);

                }else{
                    $getDay = date('d',strtotime($value->attendance_date));
                }

                $employeeMonthlyAttendance[$getDay-1] = [
                    'id' => $value->id,
                    'user_id' => $value->user_id,
                    'attendance_date' => $value->attendance_date,
                    'check_in_at' => $value->check_in_at,
                    'check_out_at' => $value->check_out_at,
                    'check_in_latitude' => $value->check_in_latitude,
                    'check_out_latitude' => $value->check_out_latitude,
                    'check_in_longitude' => $value->check_in_longitude,
                    'check_out_longitude' => $value->check_out_longitude,
                    'attendance_status' => $value->attendance_status,
                    'note' => $value->note,
                    'edit_remark' => $value->edit_remark,
                    'created_by' => $value->created_by,
                    'created_at' => $value->created_at,
                    'updated_at' => $value->updated_at,
                    'check_in_type' => $value->check_in_type,
                    'check_out_type' => $value->check_out_type,
                ];
            }

            return $employeeMonthlyAttendance;
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * @throws Exception
     */
    public function getEmployeeAttendanceDetailOfTheMonthFromUserRepo($filterParameter, $select = ['*'], $with = [])
    {
        if(AppHelper::ifDateInBsEnabled()){
            $nepaliDate = AppHelper::getCurrentNepaliYearMonth();
            $filterParameter['year'] = $nepaliDate['year'];
            $filterParameter['month'] = $filterParameter['month'] ?? $nepaliDate['month'];
            $dateInAD = AppHelper::findAdDatesFromNepaliMonthAndYear($filterParameter['year'], $filterParameter['month']);
            $filterParameter['start_date'] = $dateInAD['start_date'] ?? null;
            $filterParameter['end_date'] = $dateInAD['end_date'] ?? null;
        }else{
            $filterParameter['year'] = AppHelper::getCurrentYear();
            $filterParameter['month'] =  $filterParameter['month'] ?? now()->month;
        }
        return $this->userRepo->getEmployeeAttendanceDetailOfTheMonth($filterParameter, $select, $with);

    }

    public function findEmployeeTodayAttendanceDetail($userId, $select=['*'])
    {
        return $this->attendanceRepo->findEmployeeTodayCheckInDetail($userId, $select);
    }


    /**
     * @throws Exception
     */
    public function newCheckIn($validatedData)
    {
        $employeeLeaveDetail = $this->leaveRepo->findEmployeeApprovedLeaveForCurrentDate($validatedData,['id']);
        if($employeeLeaveDetail){
            throw new Exception('Cannot check in when leave request is Approved/Pending.',400);
        }

        $checkHolidayAndWeekend = AttendanceHelper::isHolidayOrWeekendOnCurrentDate();
        if (!$checkHolidayAndWeekend) {
            throw new Exception('Check In not allowed on holidays or on office Off Days', 403);
        }

        $shift  = AppHelper::getUserShift();
        $checkInAt = Carbon::now()->toTimeString();

        if(isset($shift->opening_time)){
            $checkInAt = Carbon::createFromTimeString(now()->toTimeString());
            $openingTime = Carbon::createFromFormat('H:i:s', $shift->opening_time);
            $timeLeave = $this->timeLeaveRepository->getEmployeeApprovedTimeLeave(date('Y-m-d'));

            if (!isset($timeLeave) && isset($shift->checkin_before)) {
                $checkInTimeAllowed = $openingTime->copy()->subMinutes($shift->checkin_before);

                if ($checkInAt->lt($checkInTimeAllowed)) {
                    throw new Exception('CheckIn is earlier than allowed time!', 400);
                }
            }

            if (!isset($timeLeave) && isset($shift->checkin_after)) {

                $checkInTimeAllowed = $openingTime->copy()->addMinutes($shift->checkin_after);

                if ($checkInAt->greaterThan($checkInTimeAllowed)) {

                    throw new Exception('CheckIn is late than allowed time contact Admin!', 400);
                }
            }

            if(isset($timeLeave) &&  strtotime($timeLeave->end_time) > strtotime($checkInAt)){
                $checkInAt = Carbon::parse($timeLeave->end_time)->toTimeString();
            }

        }

        $validatedData['attendance_date'] = Carbon::now()->format('Y-m-d');
        $validatedData['check_in_at'] = $checkInAt;
        $validatedData['check_in_latitude'] = $validatedData['check_in_latitude'] ?? '';
        $validatedData['check_in_longitude'] = $validatedData['check_in_longitude'] ?? '';

        $attendance = $this->attendanceRepo->storeAttendanceDetail($validatedData);
        if ($attendance) {

            $this->updateUserOnlineStatus($attendance->user_id,User::ONLINE);
        }
        return $attendance;

    }

    public function newRgularization($reason,$validatedData,$date,$checkin_at,$checkout_at){
        
        $employeeLeaveDetail = $this->leaveRepo->findEmployeeApprovedLeaveForCurrentDate($validatedData,['id']);
        // dd($employeeLeaveDetail);
        if($employeeLeaveDetail){
            throw new Exception('Cannot check in when leave request is Approved/Pending.',400);
        }

        // $checkHolidayAndWeekend = AttendanceHelper::isHolidayOrWeekendOnCurrentDate();
        // if (!$checkHolidayAndWeekend) {
        //     throw new Exception('Check In not allowed on holidays or on office Off Days', 403);
        // }

        $validatedData['attendance_date'] = $date;
        $validatedData['check_in_at'] = $checkin_at;
        $validatedData['check_out_at'] = $checkout_at;
        $validatedData['check_in_latitude'] = $validatedData['check_in_latitude'] ?? '';
        $validatedData['check_in_longitude'] = $validatedData['check_in_longitude'] ?? '';
        $validatedData['reason'] = $reason;

        //storeRegularizationDetail

        // dd($validatedData);  
        $regularize = $this->attendanceRepo->storeRegularizationDetail($validatedData);
        if ($regularize) {

            $this->updateUserOnlineStatus($regularize->user_id,User::ONLINE);
        }
        return $regularize;
    }

    /**
     * @throws Exception
     */
    public function newCheckOut($attendanceData, $validatedData)
    {
        $checkOut = Carbon::now()->toTimeString();
        $timeLeaveInMinutes = 0;
        $shift  = AppHelper::getUserShift();

        if(isset($shift->closing_time)){
            $openingTime = Carbon::createFromFormat('H:i:s', $shift->closing_time);
            $checkOutAt = Carbon::createFromTimeString(now()->toTimeString());

            $timeLeave = $this->timeLeaveRepository->getEmployeeApprovedTimeLeave(date('Y-m-d'));

            if (!isset($timeLeave) && isset($shift->checkout_before)) {

                $checkOutTimeAllowed = $openingTime->copy()->subMinutes($shift->checkout_before);

                if ($checkOutAt->lt($checkOutTimeAllowed)) {
                    throw new Exception('You cannot check-out early!', 400);
                }
            }

            if (!isset($timeLeave) &&  isset($shift->checkout_after)) {

                $checkOutTimeAllowed = $openingTime->copy()->addMinutes($shift->checkout_after);

                if ($checkOutAt->greaterThan($checkOutTimeAllowed)) {
                    throw new Exception('Check-in is late than allowed time. contact Admin', 400);
                }
            }


            if(isset($timeLeave) && (strtotime($timeLeave->start_time) < strtotime($checkOut) &&  strtotime($timeLeave->end_time) > strtotime($checkOut))){
                $checkOut = Carbon::parse($timeLeave->start_time)->toTimeString();
            }

            if(isset($timeLeave) && (strtotime($timeLeave->end_time) == strtotime($shift->closing_time))){
                $checkOut = Carbon::parse($timeLeave->start_time)->toTimeString();
            }

            if(isset($timeLeave) && (strtotime($timeLeave->end_time) < strtotime($checkOut))){
                $timeLeaveInMinutes = Carbon::parse($timeLeave->end_time)->diffInMinutes(Carbon::parse($timeLeave->start_time));
            }

        }

        $validatedData['check_out_latitude'] = $validatedData['check_out_latitude'] ?? '';
        $validatedData['check_out_longitude'] = $validatedData['check_out_longitude'] ?? '';

        //calculate worked_hours
        $workedData = AttendanceHelper::calculateWorkedHour($checkOut, $attendanceData->check_in_at,$attendanceData->user_id );

        $validatedData['check_out_at'] = $checkOut;
        $validatedData['worked_hour'] = $workedData['workedHours'] - $timeLeaveInMinutes;
        $validatedData['overtime'] = $workedData['overtime'];
        $validatedData['undertime'] = $workedData['undertime'];

        DB::beginTransaction();
            $attendanceCheckOut = $this->attendanceRepo->updateAttendanceDetail($attendanceData,$validatedData);
            $this->updateUserOnlineStatus($validatedData['user_id'],User::OFFLINE);
        DB::commit();
        return $attendanceCheckOut;

    }

    /**
     * @Deprecated Don't use this now
     */
    public function employeeCheckIn($validatedData)
    {
        try {
            $select = ['id', 'check_out_at'];
            $userTodayCheckInDetail = $this->attendanceRepo->findEmployeeTodayCheckInDetail($validatedData['user_id'],$select);
            if ($userTodayCheckInDetail) {
                throw new Exception('Sorry ! employee cannot check in twice a day.', 400);
            }

            $employeeLeaveDetail = $this->leaveRepo->findEmployeeApprovedLeaveForCurrentDate($validatedData,['id']);
            if($employeeLeaveDetail){
                throw new Exception('Cannot check in when leave request is Approved/Pending.',400);
            }

            $checkHolidayAndWeekend = AttendanceHelper::isHolidayOrWeekendOnCurrentDate();
            if (!$checkHolidayAndWeekend) {
                throw new Exception('Check In not allowed on holidays or on office Off Days', 403);
            }

            $validatedData['attendance_date'] = Carbon::now()->format('Y-m-d');
            $validatedData['check_in_at'] = Carbon::now()->toTimeString();


            DB::beginTransaction();
            $attendance = $this->attendanceRepo->storeAttendanceDetail($validatedData);
            if ($attendance) {
                $this->updateUserOnlineStatus($attendance->user_id,User::ONLINE);
            }
            DB::commit();
            return $attendance;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * @Deprecated Don't use this now
     */
    public function employeeCheckOut($validatedData)
    {
        try {

            $this->authorizeAttendance($validatedData['router_bssid'], $validatedData['user_id']);

            $select = ['id', 'check_out_at', 'check_in_at','user_id'];
            $userTodayCheckInDetail = $this->attendanceRepo->findEmployeeTodayCheckInDetail($validatedData['user_id'], $select);
            if (!$userTodayCheckInDetail) {
                throw new Exception('Not checked in yet', 400);
            }
            if ($userTodayCheckInDetail->check_out_at) {
                throw new Exception('Employee already checked out for today', 400);
            }

            $checkOut = Carbon::now()->toTimeString();

            $workedData = AttendanceHelper::calculateWorkedHour($checkOut, $userTodayCheckInDetail->check_in_at,$userTodayCheckInDetail->user_id );

            $validatedData['check_out_at'] = $checkOut;
            $validatedData['worked_hour'] = $workedData['workedHours'];
            $validatedData['overtime'] = $workedData['overtime'];
            $validatedData['undertime'] = $workedData['undertime'];
            DB::beginTransaction();
            $attendanceCheckOut = $this->attendanceRepo->updateAttendanceDetail($userTodayCheckInDetail,$validatedData);
            $this->updateUserOnlineStatus($validatedData['user_id'],User::OFFLINE);
            DB::commit();
            return $attendanceCheckOut;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateUserOnlineStatus($userId,$loginStatus)
    {
        try {
            $userDetail = $this->findUserDetailById($userId);
            if($userDetail->online_status == $loginStatus){
                return ;
            }
            DB::beginTransaction();
              $this->userRepo->updateUserOnlineStatus($userDetail,$loginStatus);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function updateUserOnlineStatusToOffline($userId)
    {
        try {
            $userDetail = $this->findUserDetailById($userId);
            DB::beginTransaction();
                $this->userRepo->updateUserOnlineStatus($userDetail,User::OFFLINE);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function findUserDetailById($userId,$select=['*'])
    {
        try {
            $employeeDetail = $this->userRepo->findUserDetailById($userId, $select);
            if (!$employeeDetail) {
                throw new Exception('User Detail Not found', 403);
            }
           return $employeeDetail;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function newAuthorizeAttendance($routerBSSID, $userId)
    {
        try {
            $slug = 'override-bssid';
            $overrideBSSID = $this->appSettingRepo->findAppSettingDetailBySlug($slug);
            if($overrideBSSID && $overrideBSSID->status == 1){
                $select= ['workspace_type'];
                $employeeWorkSpace = $this->findUserDetailById($userId, $select);
                if ($employeeWorkSpace->workspace_type == User::OFFICE) {
                    $checkEmployeeRouter = $this->routerRepo->findRouterDetailBSSID($routerBSSID);
                    if (!$checkEmployeeRouter) {
                        throw new Exception('Cannot take Attendance outside of workspace area');
                    }
                    $branch = $this->branchRepository->findBranchDetailById($checkEmployeeRouter->branch_id);

                    return ['latitude'=>$branch->branch_location_latitude, 'longitude'=>$branch->branch_location_longitude];
                }

            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * @Deprecated Don't use this now
     */
    public function authorizeAttendance($routerBSSID, $userId): void
    {
        try {
            $slug = 'override-bssid';
            $overrideBSSID = $this->appSettingRepo->findAppSettingDetailBySlug($slug);
            if($overrideBSSID && $overrideBSSID->status == 1){
                $select= ['workspace_type'];
                $employeeWorkSpace = $this->findUserDetailById($userId, $select);
                if ($employeeWorkSpace->workspace_type == User::OFFICE) {
                    $checkEmployeeRouter = $this->routerRepo->findRouterDetailBSSID($routerBSSID);
                    if (!$checkEmployeeRouter) {
                        throw new Exception('Cannot take Attendance outside of workspace area');
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function changeAttendanceStatus($id)
    {
        try {
            $attendanceDetail = $this->attendanceRepo->findAttendanceDetailById($id);
            if (!$attendanceDetail) {
                throw new Exception('Attendance Detail Not Found', 403);
            }
            DB::beginTransaction();
            $this->attendanceRepo->updateAttendanceStatus($attendanceDetail);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function findAttendanceDetailById($id,$select=['*'])
    {
        try{
            $attendanceDetail = $this->attendanceRepo->findAttendanceDetailById($id);
            if(!$attendanceDetail){
                throw new Exception("Attendance Detail Not Found",404);
            }
            return $attendanceDetail;
        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function update($attendanceDetail,$validatedData)
    {
        try{
            return $this->attendanceRepo->updateAttendanceDetail($attendanceDetail,$validatedData);
        }catch(Exception $exception){
            return $exception;
        }
    }

    public function delete($id)
    {
        try{
            $attendanceDetail = $this->findAttendanceDetailById($id);
            DB::beginTransaction();
                 $this->attendanceRepo->delete($attendanceDetail);;
            DB::commit();
            return ;
        }catch(Exception $exception){
            DB::rollBack();
            throw $exception;
        }
    }

    public function addAttendance($validatedData)
    {
        try{

            return $this->attendanceRepo->storeAttendanceDetail($validatedData);
        }catch(Exception $exception){
            return $exception;
        }
    }

//    public function getEmployeeAttendanceSummaryOfTheMonth($filterParameter, array $select = ['*'], array $with = []): Collection|array
//    {
//        try {
//
//            if($filterParameter['date_in_bs']){
//                $dateInAD = AppHelper::findAdDatesFromNepaliMonthAndYear($filterParameter['year'], $filterParameter['month']);
//                $filterParameter['start_date'] = $dateInAD['start_date'] ?? null;
//                $filterParameter['end_date'] = $dateInAD['end_date'] ?? null;
//            }
//
//            $employeeMonthlyAttendance = [];
//
//
////            $select = ['id','attendance_date','user_id','check_in_at','check_out_at'];
////            $attendanceDetail = $this->attendanceRepo->getEmployeeAttendanceDetailOfTheMonth($filterParameter, $select);
////            foreach ($attendanceDetail as $key => $value){
////                $attendanceDate = $filterParameter['date_in_bs']
////                    ? AppHelper::dateInYmdFormatEngToNep($value->attendance_date)
////                    : $value->attendance_date;
////
////                $getDay = (int) explode('-', $attendanceDate)[2];
////                $employeeMonthlyAttendance[$getDay-1] = [
////                    'id' => $value->id,
////                    'user_id' => $value->user_id,
////                    'attendance_date' => $attendanceDate,
////                    'check_in_at' => $value->check_in_at,
////                    'check_out_at' => $value->check_out_at,
////                ];
////            }
//            $select = ['id','attendance_date','user_id','check_in_at','check_out_at'];
//            $attendanceDetail = $this->attendanceRepo->getEmployeeAttendanceDetailOfTheMonth($filterParameter, $select);
//
//            return AttendanceHelper::getMonthlyDetail($attendanceDetail, $filterParameter);
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }


}
