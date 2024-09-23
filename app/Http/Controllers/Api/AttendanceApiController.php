<?php

namespace App\Http\Controllers\Api;

use App\Enum\EmployeeAttendanceTypeEnum;
use App\Helpers\AppHelper;
use App\Helpers\AttendanceHelper;
use App\Helpers\SMPush\SMPushHelper;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Requests\Attendance\AttendanceCheckInRequest;
use App\Requests\Attendance\AttendanceCheckOutRequest;
use App\Resources\Attendance\EmployeeAttendanceDetailCollection;
use App\Resources\Attendance\MonthlyEmployeeAttendanceResource;
use App\Resources\Attendance\TodayAttendanceResource;
use App\Services\Attendance\AttendanceService;
use App\Services\Nfc\NfcService;
use App\Services\Qr\QrCodeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class AttendanceApiController extends Controller
{
    public function __construct(protected AttendanceService $attendanceService, protected QrCodeService $qrCodeService, protected NfcService $nfcService)
    {}

    public function getEmployeeAllAttendanceDetailOfTheMonth(Request $request): JsonResponse
    {
        try{
            $isBsEnabled = AppHelper::ifDateInBsEnabled();

            $filterParameter['month'] = $request->month ?? null;
            $filterParameter['user_id'] = getAuthUserCode();
            $with = ['employeeTodayAttendance:user_id,check_in_at,check_out_at,attendance_date'];
            $select = [
                'users.id',
                'users.name',
                'users.email'
            ];
            $attendanceDetail = $this->attendanceService->getEmployeeAttendanceDetailOfTheMonthFromUserRepo($filterParameter, $select, $with);

            if ($isBsEnabled) {
                $yearMonth = AppHelper::getCurrentNepaliYearMonth();
                $year = $yearMonth['year'];
                $month = $filterParameter['month'] ?? $yearMonth['month'];
            } else {
                $year = date('Y');
                $month = $filterParameter['month'] ?? date('m');
            }



            $attendanceSummary = AttendanceHelper::getMonthlyDetail($filterParameter['user_id'], $isBsEnabled, $year, $month);



            $returnData['user_detail'] = [
                'user_id' => $attendanceDetail->id,
                'name' => $attendanceDetail->name,
                'email' => $attendanceDetail->email,
            ];
            if ($attendanceDetail->employeeTodayAttendance) {
                $returnData['employee_today_attendance'] = [
                    'check_in_at' => $attendanceDetail->employeeTodayAttendance->check_in_at ? AttendanceHelper::changeTimeFormatForAttendanceView($attendanceDetail->employeeTodayAttendance->check_in_at) : '-',
                    'check_out_at' => $attendanceDetail->employeeTodayAttendance->check_out_at ? AttendanceHelper::changeTimeFormatForAttendanceView($attendanceDetail->employeeTodayAttendance->check_out_at) : '-',
                ];
            } else {
                $returnData['employee_today_attendance'] = [
                    'check_in_at' => '-',
                    'check_out_at' => '-',
                ];
            }
            if ($attendanceDetail->employeeAttendance->count() > 0) {
                $returnData['employee_attendance'] = new EmployeeAttendanceDetailCollection($attendanceDetail->employeeAttendance);
            } else {
                $returnData['employee_attendance'] = [];
            }

            $returnData['attendance_summary'] = [
                'totalDays' => $attendanceSummary['totalDays'],
                'totalWeekend' => $attendanceSummary['totalWeekend'],
                'totalPresent' => $attendanceSummary['totalPresent'],
                'totalHoliday' => $attendanceSummary['totalHoliday'],
                'totalAbsent' => $attendanceSummary['totalAbsent'],
                'totalLeave' => $attendanceSummary['totalLeave'],
                'totalWorkedHours' => $attendanceSummary['totalWorkedHours'],
                'totalWorkingHours' => $attendanceSummary['totalWorkingHours'],
            ];

            return AppHelper::sendSuccessResponse('Data Found', $returnData);
        } catch (Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function employeeAttendance(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'attendance_type' => [new Enum(EmployeeAttendanceTypeEnum::class)],
                'latitude' => ['nullable'],
                'longitude' => ['nullable'],
                'router_bssid' => ['nullable'],
                'identifier' => ['nullable', 'required_if:attendance_type,' . EmployeeAttendanceTypeEnum::qr->value, 'required_if:attendance_type,' . EmployeeAttendanceTypeEnum::nfc->value,],
                'attendance_status_type' => ['nullable', 'required_if:attendance_type,' . EmployeeAttendanceTypeEnum::wifi->value],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()->toArray()
                ],422);
            }

            $validatedData = $validator->validated();
            $validatedData['attendance_status_type'] = $validatedData['attendance_status_type'] ?? '';
            $userDetail = auth()->user();

            $validatedData['user_id'] = $userDetail['id'];
            $validatedData['company_id'] = $userDetail['company_id'];
            $userLock = Cache::get('user_lock_' . $userDetail->id);
            if($userLock){
                return response()->json([
                    'success' => false,
                    'message' => 'Another attendance request is already in progress.',
                ], 409);
            }

            Cache::put('user_lock_' . $userDetail->id, true,now()->addMinutes(5));
            $message = '';
            $data = [];

            if ($validatedData['attendance_type'] == EmployeeAttendanceTypeEnum::nfc->value)
            {
                $nfcData = $this->nfcService->verifyNfc($validatedData['identifier']);

                if (!$nfcData) {
                    throw new Exception('Invalid NFC token or NFC is not available', 400);
                }
            } elseif ($validatedData['attendance_type'] == EmployeeAttendanceTypeEnum::qr->value)
            {
                $attendanceQr = $this->qrCodeService->verifyQr($validatedData['identifier']);

                if (!$attendanceQr) {

                    throw new Exception('Invalid QR or QR is not available', 400);
                }

            } elseif ($validatedData['attendance_type'] == EmployeeAttendanceTypeEnum::wifi->value)
            {
                $coordinate = $this->attendanceService->newAuthorizeAttendance($validatedData['router_bssid'], $validatedData['user_id']);

                if(!is_null($coordinate)){
                    $validatedData['latitude'] = $coordinate['latitude'];
                    $validatedData['longitude'] = $coordinate['longitude'];
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid attendance attendance_type.']);
            }

            // check today's attendance data
            $select = ['id', 'user_id','check_out_at', 'check_in_at'];
            $userTodayCheckInDetail = $this->attendanceService->findEmployeeTodayAttendanceDetail($validatedData['user_id'], $select);


            if ($userTodayCheckInDetail) {

                if (($userTodayCheckInDetail->check_in_at) && ($validatedData['attendance_status_type'] == 'checkIn')) {
                    throw new Exception('Sorry ! employee cannot check in twice a day.', 400);
                }

                if ($userTodayCheckInDetail->check_out_at) {
                    throw new Exception('Employee already checked out for today', 400);
                }

                $validatedData['check_out_type'] = $validatedData['attendance_type'];
                $attendanceData = $this->attendanceService->newCheckOut($userTodayCheckInDetail, $validatedData);
                $workedTime = AttendanceHelper::getEmployeeWorkedTimeInHourAndMinute($attendanceData);
                $title = 'Check Out Notification';
                $permissionKeyForNotification = 'employee_check_out';

                $message = ucfirst(auth()->user()->name) . ' has checked out at ' . AttendanceHelper::changeTimeFormatForAttendanceView($attendanceData->check_out_at) . ' and has worked for '
                    . $workedTime;

                $displayMessage = 'Check out successful';
            } else {

                if (($validatedData['attendance_type'] == EmployeeAttendanceTypeEnum::wifi->value) && ($validatedData['attendance_status_type'] == 'checkOut')) {
                    throw new Exception('Not checked in yet', 400);
                }

                $validatedData['check_in_type'] = $validatedData['attendance_type'];
                $attendanceData = $this->attendanceService->newCheckIn($validatedData);

                $title = 'Check In Notification';

                $message = ucfirst(auth()->user()->name) . ' checked in at ' . AttendanceHelper::changeTimeFormatForAttendanceView($attendanceData->check_in_at);

                $permissionKeyForNotification = 'employee_check_in';
                $displayMessage = 'Check in successful';

            }

            $data = new TodayAttendanceResource($attendanceData);


            AppHelper::sendNotificationToAuthorizedUser(
                $title,
                $message,
                $permissionKeyForNotification
            );
            DB::commit();
            Cache::forget('user_lock_' . $userDetail->id);
            return AppHelper::sendSuccessResponse($displayMessage, $data);



        } catch (Exception $exception) {
            DB::rollBack();
            Cache::forget('user_lock_' . $userDetail->id);
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @Deprecated Don't use this now
    */
    public function employeeCheckIn(AttendanceCheckInRequest $request): JsonResponse
    {
        try {
            $permissionKeyForNotification = 'employee_check_in';
            $userDetail = auth()->user();

            $validatedData = $request->validated();

            $validatedData['user_id'] = $userDetail->id;
            $validatedData['company_id'] = $userDetail->company_id;


            $this->attendanceService->authorizeAttendance($validatedData['router_bssid'], $validatedData['user_id']);

            $checkIn = $this->attendanceService->employeeCheckIn($validatedData);
            $data = new TodayAttendanceResource($checkIn);

            AppHelper::sendNotificationToAuthorizedUser(
                'Check In Notification',
                ucfirst(auth()->user()->name) . ' checked in at ' . AttendanceHelper::changeTimeFormatForAttendanceView($checkIn->check_in_at),
                $permissionKeyForNotification
            );
            return AppHelper::sendSuccessResponse('Check In Successful', $data);
        } catch (Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }
    /**
     * @Deprecated Don't use this now
     */
    public function employeeCheckOut(AttendanceCheckOutRequest $request): JsonResponse
    {
        try {
            $userDetail = auth()->user();
            $permissionKeyForNotification = 'employee_check_out';

            $validatedData = $request->validated();
            $validatedData['user_id'] = $userDetail->id;
            $validatedData['company_id'] = $userDetail->company_id;

            $checkOut = $this->attendanceService->employeeCheckOut($validatedData);
            $data = new TodayAttendanceResource($checkOut);
            $workedTime = AttendanceHelper::getEmployeeWorkedTimeInHourAndMinute($checkOut);

            AppHelper::sendNotificationToAuthorizedUser(
                'Check Out Notification',
                ucfirst(auth()->user()->name) . ' has checked out at ' . AttendanceHelper::changeTimeFormatForAttendanceView($checkOut->check_out_at) . ' and has worked for '
                . $workedTime,
                $permissionKeyForNotification

            );
            return AppHelper::sendSuccessResponse('Check out Successful', $data);
        } catch (Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }



}
