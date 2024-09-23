<?php

namespace App\Http\Controllers\Web;

use App\Exports\AttendanceDayWiseExport;
use App\Exports\AttendanceExport;
use App\Helpers\AppHelper;
use App\Helpers\AttendanceHelper;
use App\Helpers\NepaliDate;
use App\Helpers\SMPush\SMPushHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Repositories\BranchRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\RouterRepository;
use App\Repositories\UserRepository;
use App\Requests\Attendance\AttendanceTimeAddRequest;
use App\Requests\Attendance\AttendanceTimeEditRequest;
use App\Services\Attendance\AttendanceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

class AttendanceController extends Controller
{
    private $view = 'admin.attendance.';

    private CompanyRepository $companyRepo;
    private AttendanceService $attendanceService;
    private RouterRepository $routerRepo;
    private UserRepository $userRepository;
    private BranchRepository $branchRepo;


    public function __construct(
        CompanyRepository $companyRepo,
        AttendanceService $attendanceService,
        RouterRepository  $routerRepo,
        UserRepository $userRepository,
        BranchRepository $branchRepo,
    ) {
        $this->attendanceService = $attendanceService;
        $this->companyRepo = $companyRepo;
        $this->routerRepo = $routerRepo;
        $this->userRepository =  $userRepository;
        $this->branchRepo =  $branchRepo;
    }

    public function index(Request $request)
    {
        Log::info($request->all());

        if (\Auth::user()->can('manage-attendance')) {
            try {

                $data = $request->all();
                $start_date = '';
                $end_date = '';
                if (isset($data['datetimes'])) {
                    $datetimes = explode(' - ', $request->datetimes);
                    $start_date = date('Y-m-d', strtotime($datetimes[0]));
                    $end_date = date('Y-m-d', strtotime($datetimes[1]));
                } else {
                    $start_date = AppHelper::getCurrentDateInYmdFormat();
                    $end_date = AppHelper::getCurrentDateInYmdFormat();
                }
                $appTimeSetting = AppHelper::check24HoursTimeAppSetting();
                $isBsEnabled = AppHelper::ifDateInBsEnabled();
                $selectBranch = ['id', 'name'];
                $companyId = AppHelper::getAuthUserCompanyId();
                $filterParameter = [
                    // 'attendance_date' =>  AppHelper::getCurrentDateInYmdFormat(),
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'company_id' => $companyId,
                    'branch_id' => $request->branch_id ?? null,
                    'department_id' => $request->department_id ?? null,
                    'download_excel' => $request->download_excel,
                    'date_in_bs' => false,
                ];

                // if  (AppHelper::ifDateInBsEnabled())  {
                //     $filterParameter['attendance_date'] =  AppHelper::getCurrentDateInBS();
                //     $filterParameter['date_in_bs'] = true;
                // }
                $attendanceDetail = $this->attendanceService->getAllCompanyEmployeeAttendanceDetailOfTheDay($filterParameter);
                // dd($attendanceDetail,$filterParameter);
                Log::info($filterParameter);
                $branch = $this->branchRepo->getLoggedInUserCompanyBranches($companyId, $selectBranch);
                if ($filterParameter['download_excel']) {
                    return FacadesExcel::download(new AttendanceDayWiseExport($attendanceDetail, $filterParameter), 'attendance- from' . $filterParameter['start_date'] . 'to' . $filterParameter['end_date'] .  '-report.xlsx');
                }
                return view($this->view . 'index', compact('attendanceDetail', 'filterParameter', 'branch', 'isBsEnabled', 'appTimeSetting'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function checkInEmployee($companyId, $userId): RedirectResponse
    {
        try {
            $this->checkIn($userId, $companyId);
            return redirect()->back()->with('success', 'Employee Check In Successful');
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }


    public function checkOutEmployee($companyId, $userId): RedirectResponse
    {
        try {
            $this->checkOut($userId, $companyId);
            return redirect()->back()->with('success', 'Employee Check Out Successful');
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }


    public function changeAttendanceStatus($id): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $this->attendanceService->changeAttendanceStatus($id);
            DB::commit();
            return redirect()->back()->with('success', 'Attendance status changed successful');
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function update(AttendanceTimeEditRequest $request, $id)
    {
        if (\Auth::user()->can('edit-attendance')) {
            try {
                $validatedData = $request->validated();
                $attendanceDetail = $this->attendanceService->findAttendanceDetailById($id);
                $validatedData['is_active'] = 1;
                $with = ['branch:id,branch_location_latitude,branch_location_longitude'];
                $select = ['routers.*'];
                $userDetail = $this->userRepository->findUserDetailById($attendanceDetail->user_id);

                $routerDetail = $this->routerRepo->findRouterDetailByBranchId($userDetail->branch_id, $with, $select);

                if ($validatedData['check_out_at']) {

                    $validatedData['check_out_latitude'] = $routerDetail->branch->branch_location_latitude;
                    $validatedData['check_out_longitude'] = $routerDetail->branch->branch_location_longitude;

                    $workedData = AttendanceHelper::calculateWorkedHour($validatedData['check_out_at'], $validatedData['check_in_at'], $attendanceDetail->user_id);

                    $validatedData['worked_hour'] = $workedData['workedHours'];
                    $validatedData['overtime'] = $workedData['overtime'];
                    $validatedData['undertime'] = $workedData['undertime'];
                }
                DB::beginTransaction();
                $this->attendanceService->update($attendanceDetail, $validatedData);
                $this->userRepository->updateUserOnlineStatus($userDetail, 1);

                DB::commit();
                return redirect()->back()->with('success', 'Employee Attendance Edited Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show(Request $request, $employeeId)
    {

        if (\Auth::user()->can('show-attendance')) {
            try {
                $appTimeSetting = AppHelper::check24HoursTimeAppSetting();
                $isBsEnabled = AppHelper::ifDateInBsEnabled();
                $filterParameter = [
                    'year' => $request->year ?? now()->format('Y'),
                    'month' => $request->month ?? now()->month,
                    'user_id' => $employeeId,
                    'download_excel' => $request->get('download_excel') ? true : false,
                    'date_in_bs' => false,
                ];
                $engDate = strtotime($filterParameter['year'] . '-' . $filterParameter['month'] . '-01');
                $monthName  = date("F", $engDate);

                if ($isBsEnabled) {
                    $nepaliDate = AppHelper::getCurrentNepaliYearMonth();
                    $filterParameter['year'] = $request->year ?? $nepaliDate['year'];
                    $filterParameter['month'] = $request->month ?? $nepaliDate['month'];
                    $filterParameter['date_in_bs'] = true;
                    $monthName = AppHelper::getNepaliMonthName($filterParameter['month']);
                }


                $months = AppHelper::MONTHS;
                $userDetail = $this->userRepository->findUserDetailById($employeeId, ['id', 'name']);
                $attendanceDetail = $this->attendanceService->getEmployeeAttendanceDetailOfTheMonth($filterParameter);

                $attendanceSummary = AttendanceHelper::getMonthlyDetail($employeeId, $filterParameter['date_in_bs'], $filterParameter['year'], $filterParameter['month']);

                if ($filterParameter['download_excel']) {
                    if ($filterParameter['date_in_bs']) {
                        $month = \App\Helpers\AppHelper::MONTHS[date("n", strtotime($attendanceDetail[0]['attendance_date']))]['np'];
                    } else {
                        $month = date("F", strtotime($attendanceDetail[0]['attendance_date']));
                    }
                    return \Maatwebsite\Excel\Facades\Excel::download(new AttendanceExport($attendanceDetail, $userDetail), 'attendance-' . $userDetail->name . '-' . $filterParameter['year'] . '-' . $month . '-report.xlsx');
                }


                return view(
                    $this->view . 'show',
                    compact(
                        'attendanceDetail',
                        'filterParameter',
                        'months',
                        'userDetail',
                        'attendanceSummary',
                        'appTimeSetting',
                        'isBsEnabled',
                        'monthName'
                    )
                );
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function delete($id)
    {
        if (\Auth::user()->can('delete-attendance')) {
            $this->attendanceService->delete($id);
            return redirect()->back()->with('success', 'Attendance Deleted Successful');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function dashboardAttendance(Request $request, $attendanceType): JsonResponse
    {
        try {
            $appTimeSetting = AppHelper::check24HoursTimeAppSetting();
            $locationDetail = [
                'lat' => $request->get('lat'),
                'long' => $request->get('long')
            ];
            $userId = getAuthUserCode();
            $companyId = AppHelper::getAuthUserCompanyId();
            $attendance = ($attendanceType == 'checkIn') ?
                $this->checkIn($userId, $companyId, true, $locationDetail) :
                $this->checkOut($userId, $companyId, true, $locationDetail);
            $message = ($attendanceType == 'checkIn') ?
                'Check In SuccessFull' :
                'Check Out SuccessFull';
            $data = [
                'check_in_at' => $attendance->check_in_at ?
                    AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $attendance->check_in_at) : '',
                'check_out_at' => $attendance->check_out_at ?
                    AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $attendance->check_out_at) : '',
            ];

            return AppHelper::sendSuccessResponse($message, $data);
        } catch (Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    private function checkIn($userId, $companyId, $dashboardAttendance = false, $locationData = [])
    {
        try {
            $select = ['name'];
            $permissionKeyForNotification = 'employee_check_in';
            $userDetail = $this->userRepository->findUserDetailById($userId);
            if (!$userDetail) {
                throw new Exception('Employee Detail Not Found', 404);
            }
            $validatedData = $this->prepareDataForAttendance($companyId, $userId, 'checkIn');
            if ($dashboardAttendance) {
                $validatedData['check_in_latitude'] = $locationData['lat'];
                $validatedData['check_in_longitude'] = $locationData['long'];
            }
            DB::beginTransaction();
            $checkInAttendance =  $this->attendanceService->newCheckIn($validatedData);
            $this->userRepository->updateUserOnlineStatus($userDetail, 1);

            DB::commit();
            AppHelper::sendNotificationToAuthorizedUser(
                'Check In Notification',
                ucfirst($userDetail->name) . ' checked in at  ' . AttendanceHelper::changeTimeFormatForAttendanceView($checkInAttendance->check_in_at),
                $permissionKeyForNotification
            );
            return $checkInAttendance;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function checkOut($userId, $companyId, $dashboardAttendance = false, $locationData = [])
    {
        try {
            $select = ['name'];
            $permissionKeyForNotification = 'employee_check_out';
            $userDetail = $this->userRepository->findUserDetailById($userId);
            $validatedData = $this->prepareDataForAttendance($companyId, $userId,  'checkout');
            if ($dashboardAttendance) {
                $validatedData['check_out_latitude'] = $locationData['lat'];
                $validatedData['check_out_longitude'] = $locationData['long'];
            }

            $attendanceData = Attendance::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->where('attendance_date', date('Y-m-d'))
                ->first();

            DB::beginTransaction();
            $attendanceCheckOut = $this->attendanceService->newCheckOut($validatedData);

            $this->userRepository->updateUserOnlineStatus($userDetail, 0);
            DB::commit();
            AppHelper::sendNotificationToAuthorizedUser(
                'Check Out Notification',
                ucfirst($userDetail->name) . ' checked out at ' . AttendanceHelper::changeTimeFormatForAttendanceView($attendanceCheckOut->check_out_at),
                $permissionKeyForNotification
            );
            return $attendanceCheckOut;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function prepareDataForAttendance($companyId, $userId, $checkStatus): array|RedirectResponse
    {
        try {
            $with = ['branch:id,branch_location_latitude,branch_location_longitude'];
            $select = ['routers.*'];
            $userBranchId = AppHelper::getAuthUserBranchId();

            $routerDetail = $this->routerRepo->findRouterDetailByBranchId($userBranchId, $with, $select);
            if (!$routerDetail) {
                throw new Exception('Branch Routers Detail Not Found.', 400);
            }
            if ($checkStatus == 'checkIn') {
                $validatedData['check_in_latitude'] = $routerDetail->branch->branch_location_latitude;
                $validatedData['check_in_longitude'] = $routerDetail->branch->branch_location_longitude;
            } else {
                $validatedData['check_out_latitude'] = $routerDetail->branch->branch_location_latitude;
                $validatedData['check_out_longitude'] = $routerDetail->branch->branch_location_longitude;
            }
            $validatedData['user_id'] = $userId;
            $validatedData['company_id'] = $companyId;
            $validatedData['router_bssid'] = $routerDetail->router_ssid;
            return $validatedData;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function prepareDataForRegularization($companyId, $userId, $checkStatus): array|RedirectResponse
    {
        try {
            $with = ['branch:id,branch_location_latitude,branch_location_longitude'];
            $select = ['routers.*'];
            $userBranchId = AppHelper::getAuthUserBranchId();

            $routerDetail = $this->routerRepo->findRouterDetailByBranchId($userBranchId, $with, $select);
            if (!$routerDetail) {
                throw new Exception('Branch Routers Detail Not Found.', 400);
            }
            if ($checkStatus == 'checkIn') {
                $validatedData['check_in_latitude'] = $routerDetail->branch->branch_location_latitude;
                $validatedData['check_in_longitude'] = $routerDetail->branch->branch_location_longitude;
            } else {
                $validatedData['check_out_latitude'] = $routerDetail->branch->branch_location_latitude;
                $validatedData['check_out_longitude'] = $routerDetail->branch->branch_location_longitude;
            }
            $validatedData['user_id'] = $userId;
            $validatedData['company_id'] = $companyId;
            $validatedData['router_bssid'] = $routerDetail->router_ssid;
            return $validatedData;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function store(AttendanceTimeAddRequest $request)
    {
        if (\Auth::user()->can('create-attendance')) {
            try {

                $validatedData = $request->validated();

                $validatedData['company_id'] = AppHelper::getAuthUserCompanyId();


                $with = ['branch:id,branch_location_latitude,branch_location_longitude'];
                $select = ['routers.*'];
                $routerDetail = $this->routerRepo->findRouterDetailByBranchId(AppHelper::getAuthUserBranchId(), $with, $select);
                if ($validatedData['check_out_at']) {

                    $validatedData['check_out_latitude'] = $routerDetail->branch->branch_location_latitude;
                    $validatedData['check_out_longitude'] = $routerDetail->branch->branch_location_longitude;

                    $workedData = AttendanceHelper::calculateWorkedHour($validatedData['check_out_at'], $validatedData['check_in_at'], $validatedData['user_id']);

                    $validatedData['worked_hour'] = $workedData['workedHours'];
                    $validatedData['overtime'] = $workedData['overtime'];
                    $validatedData['undertime'] = $workedData['undertime'];
                }
                DB::beginTransaction();
                $this->attendanceService->addAttendance($validatedData);
                DB::commit();
                return redirect()->back()->with('success', 'Employee Attendance Added Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
