<?php

namespace App\Http\Controllers\Web;

use App\Exports\AttendanceDayWiseExport;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Repositories\BranchRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\RouterRepository;
use App\Repositories\UserRepository;
use App\Services\Attendance\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\AppHelper;
use App\Models\Regularization;

class RegularizationController extends Controller
{

    private $view = 'admin.attendance.';

    private CompanyRepository $companyRepo;
    private AttendanceService $attendanceService;
    private RouterRepository $routerRepo;
    private UserRepository $userRepository;
    private BranchRepository $branchRepo;
    private AttendanceController $attendanceController;


    public function __construct(
        CompanyRepository $companyRepo,
        AttendanceService $attendanceService,
        RouterRepository  $routerRepo,
        UserRepository $userRepository,
        BranchRepository $branchRepo,
        AttendanceController $attendanceController
    ) {
        $this->attendanceService = $attendanceService;
        $this->companyRepo = $companyRepo;
        $this->routerRepo = $routerRepo;
        $this->userRepository =  $userRepository;
        $this->branchRepo =  $branchRepo;
        $this->attendanceController = $attendanceController;
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-regularization')) {
            try {
                $appTimeSetting = AppHelper::check24HoursTimeAppSetting();
                $isBsEnabled = AppHelper::ifDateInBsEnabled();
                $selectBranch = ['id', 'name'];
                $companyId = AppHelper::getAuthUserCompanyId();

                $filterParameter = [
                    'regularization_date' => $request->attendance_date ?? null,
                    'company_id' => $companyId,
                    'branch_id' => $request->branch_id ?? null,
                    'department_id' => $request->department_id ?? null,
                    'download_excel' => $request->download_excel,
                    'regularization_status' => $request->status,
                    'date_in_bs' => false,
                ];

                $regularizationDetails = $this->attendanceService->getAllCompanyEmployeeRegularizationDetailOfTheDay($filterParameter);
                $branch = $this->branchRepo->getLoggedInUserCompanyBranches($companyId, $selectBranch);
                if ($filterParameter['download_excel']) {
                    return \Maatwebsite\Excel\Facades\Excel::download(new AttendanceDayWiseExport($regularizationDetails, $filterParameter), 'attendance-' . $filterParameter['attendance_date'] . '-report.xlsx');
                }
                return view('admin.regularization.index', compact('regularizationDetails', 'filterParameter', 'branch', 'isBsEnabled', 'appTimeSetting'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function approveRegularization($id)
    {
        $regularization_data = Regularization::find($id);
        $regularization_data->regularization_status = 1;
        $result = $regularization_data->save();
        if ($result) {
            $attendance_data = Attendance::where('user_id', $regularization_data->user_id)->where('attendance_date', $regularization_data->regularization_date)->first();
            $data = [
                'user_id' => $regularization_data->user_id,
                'company_id' => $regularization_data->company_id,
                'attendance_date' => $regularization_data->regularization_date,
                'check_in_at' => $regularization_data->check_in_at,
                'check_out_at' => $regularization_data->check_out_at,
                'check_in_latitude' => $regularization_data->check_in_latitude,
                'check_out_latitude' => $regularization_data->check_out_latitude,
                'check_in_longitude' => $regularization_data->check_in_longitude,
                'check_out_longitude' => $regularization_data->check_out_longitude,
                'created_by' => $regularization_data->created_by,
                'updated_by' => $regularization_data->updated_by
            ];

            if ($attendance_data) {
                $attendance_data->update($data);
                return response()->json([
                    'message' => "The Request has been Approved"
                ]);
            } else {
                Attendance::create($data);
                return response()->json([
                    'message' => "The Request has been Approved"
                ]);
            }
        }
    }

    public function rejectRegularization($id)
    {
        $regularization_data = Regularization::find($id);
        $regularization_data->regularization_status = 2;
        $result = $regularization_data->save();

        if ($result) {
            return response()->json([
                'message' => "The Request has been Rejected"
            ]);
        } else {
            return response()->json([
                'message' => "Request Failed"
            ]);
        }
    }

    public function checkAttendance(Request $request)
    {
        $date = $request->date;
        $user_id = auth()->user()->id;

        $attendance_data = Attendance::where('user_id', $user_id)->where('attendance_date', $date)->first();

        if ($attendance_data && $attendance_data->check_in_at != null  && $attendance_data->check_out_at != null) {
            return response()->json([
                'check_in' => $attendance_data ? $attendance_data->check_in_at : null,
                'check_out' => $attendance_data ? $attendance_data->check_out_at : null,
                'message' => "Already Checkdin"
            ]);
        } elseif ($attendance_data && $attendance_data->check_in_at != null && $attendance_data->check_out_at == null) {
            Log::info($attendance_data ? $attendance_data->check_in_at : null);

            return response()->json([
                'check_in' => $attendance_data ? $attendance_data->check_in_at : null,
                'check_out' =>  null,
                'message' => "Enter Checkout",
            ]);
        } else {
            Log::info($attendance_data);

            return response()->json([
                'check_in' => null,
                'message' => "You were absent",
            ]);
        }
    }

    public function createRegularization(Request $request)
    {
        $date = $request->date;
        $reason = $request->reason;
        $checkin_at = $request->checkin;
        $checkout_at = $request->checkout ? $request->checkout : null;
        $user_id = auth()->user()->id;
        $companyId = auth()->user()->company_id;

        try {
            $result = $this->regularization($reason, $user_id, $companyId, $date, $checkin_at, $checkout_at);
            if ($result) {
                return response()->json([
                    'message' => "Regularization Successfull"
                ]);
            } else {
                return response()->json([
                    'message' => null
                ]);
            }
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function regularization($reason, $userId, $companyId, $date, $checkin_at, $checkout_at, $dashboardAttendance = false, $locationData = [])
    {
        try {
            $select = ['name'];
            $permissionKeyForNotification = 'employee_check_in';
            $userDetail = $this->userRepository->findUserDetailById($userId);

            if (!$userDetail) {
                throw new Exception('Employee Detail Not Found', 404);
            }

            $validatedData = $this->attendanceController->prepareDataForRegularization($companyId, $userId, 'checkIn');

            if ($dashboardAttendance) {
                $validatedData['check_in_latitude'] = $locationData['lat'];
                $validatedData['check_in_longitude'] = $locationData['long'];
            }
            DB::beginTransaction();

            $regularization_data =  $this->attendanceService->newRgularization($reason, $validatedData, $date, $checkin_at, $checkout_at);
            $this->userRepository->updateUserOnlineStatus($userDetail, 1);
            DB::commit();

            return $regularization_data;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
