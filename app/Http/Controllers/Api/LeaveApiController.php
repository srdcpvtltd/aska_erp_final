<?php

namespace App\Http\Controllers\Api;

use App\Enum\LeaveStatusEnum;
use App\Helpers\AppHelper;
use App\Helpers\SMPush\SMPushHelper;
use App\Http\Controllers\Controller;
use App\Models\LeaveRequestMaster;
use App\Requests\Leave\LeaveRequestStoreRequest;
use App\Requests\Leave\TimeLeaveStoreRequest;
use App\Resources\Leave\EmployeeLeaveDetailCollection;
use App\Resources\Leave\EmployeeTimeLeaveDetailCollection;
use App\Resources\Leave\LeaveRequestCollection;
use App\Resources\Leave\TimeLeaveRequestCollection;
use App\Services\Leave\LeaveService;
use App\Services\Leave\TimeLeaveService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Validation\Rule;

class LeaveApiController extends Controller
{

    public function __construct(protected LeaveService $leaveService, protected TimeLeaveService $timeLeaveService)
    {}

    public function getAllLeaveRequestOfEmployee(Request $request): JsonResponse
    {
        try{
            $filterParameter = [
                'leave_type' => $request->leave_type ?? null,
                'status' => $request->status ?? null,
                'year' => $request->year ?? \Carbon\Carbon::now()->year,
                'month' => $request->month ?? null,
                'early_exit' => $request->early_exit ?? null,
                'user_id' => getAuthUserCode()
            ];
            $getAllLeaveRequests =  $this->leaveService->getAllLeaveRequestOfEmployee($filterParameter);
            $timeLeaveRequests = $this->timeLeaveService->getAllTimeLeaveRequestOfEmployee($filterParameter);

            if(isset($request) && ($request->leave_type == '' || $request->leave_type == 0)){
                $mergedCollection = $getAllLeaveRequests->merge($timeLeaveRequests);
            }else{
                $mergedCollection = $getAllLeaveRequests;
            }

            $leaveData = new LeaveRequestCollection($mergedCollection);

            return AppHelper::sendSuccessResponse('Data Found',$leaveData);
        } catch (\Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    public function saveLeaveRequestDetail(LeaveRequestStoreRequest $request): JsonResponse
    {
        try {
            $permissionKeyForNotification = 'employee_leave_request';

            $validatedData = $request->validated();

            $validatedData['requested_by'] = getAuthUserCode();
            DB::beginTransaction();
              $leaveRequestDetail = $this->leaveService->storeLeaveRequest($validatedData);
            DB::commit();

            if($leaveRequestDetail) {
                AppHelper::sendNotificationToAuthorizedUser(
                        'Leave Request Notification',
                    ucfirst(auth()->user()->name). ' has requested ' .$leaveRequestDetail['no_of_days'] .
                        ' day(s) leave from ' .AppHelper::formatDateForView($leaveRequestDetail['leave_from']).
                        ' on ' .AppHelper::convertLeaveDateFormat($leaveRequestDetail['leave_requested_date']) . ' Reason: '.$validatedData['reasons'],
                    $permissionKeyForNotification
                );
            }
            return AppHelper::sendSuccessResponse('Leave request submitted successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    public function getLeaveCountDetailOfEmployeeOfTwoMonth(): JsonResponse
    {
        try {

            $dateWithNumberOfEmployeeOnLeave = $this->leaveService->getLeaveCountDetailOfEmployeeOfTwoMonth();
            $timeLeaveCount = $this->timeLeaveService->getTimeLeaveCountDetailOfEmployeeOfTwoMonth();
            $leaveCalendar = array_merge($dateWithNumberOfEmployeeOnLeave, $timeLeaveCount);
            return AppHelper::sendSuccessResponse('Data Found',$leaveCalendar);
        } catch (\Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    public function getAllEmployeeLeaveDetailBySpecificDay(Request $request): JsonResponse
    {
        try {
            $filterParameter['leave_date'] = $request->leave_date ?? Carbon::now()->format('Y-m-d') ;

            $leaveListDetail = $this->leaveService->getAllEmployeeLeaveDetailBySpecificDay($filterParameter);
            $timeLeaveListDetail = $this->timeLeaveService->getAllEmployeeTimeLeaveDetailBySpecificDay($filterParameter);
            $timeLeaveDetail = new EmployeeTimeLeaveDetailCollection($timeLeaveListDetail);
            $leaveDetail = new EmployeeLeaveDetailCollection($leaveListDetail);
            $leaveData = $timeLeaveDetail->concat($leaveDetail);


            return AppHelper::sendSuccessResponse('Data Found',$leaveData);
        } catch (\Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    public function cancelLeaveRequest($leaveRequestId)
    {
        try {
            $validatedData = [
                'status' => 'cancelled'
            ];
            $leaveRequestDetail = $this->leaveService->findLeaveRequestDetailByIdAndEmployeeId($leaveRequestId,getAuthUserCode());
            if($leaveRequestDetail->status != 'pending'){
                throw new \Exception('Leave request cannot be cancelled once it is updated from pending state.',403);
            }
            $this->leaveService->cancelLeaveRequest($validatedData, $leaveRequestDetail);
            return AppHelper::sendSuccessResponse('Leave request cancelled successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }


    public function saveTimeLeaveRequest(TimeLeaveStoreRequest $request): JsonResponse
    {
        try {
            $permissionKeyForNotification = 'employee_leave_request';

            $validatedData = $request->validated();

            $validatedData['requested_by'] = getAuthUserCode();

            DB::beginTransaction();
            $leaveRequestDetail = $this->timeLeaveService->storeTimeLeaveRequest($validatedData);
            DB::commit();

            if ($leaveRequestDetail) {
                AppHelper::sendNotificationToAuthorizedUser(
                    'Leave Request Notification',
                    ucfirst(auth()->user()?->name) . ' has requested leave from ' .
                    $leaveRequestDetail['start_time']. ' to ' .$leaveRequestDetail['end_time'].
                    ' on ' . AppHelper::convertLeaveDateFormat($leaveRequestDetail['issue_date']) . ' Reason: ' . $validatedData['reasons'],
                    $permissionKeyForNotification
                );
            }
            return AppHelper::sendSuccessResponse('Leave request submitted successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    public function cancelTimeLeaveRequest($leaveRequestId)
    {
        try {
            $validatedData = [
                'status' => LeaveStatusEnum::cancelled->value
            ];
            $leaveRequestDetail = $this->timeLeaveService->findEmployeeTimeLeaveRequestById($leaveRequestId);
            if($leaveRequestDetail->status != LeaveStatusEnum::pending->value){
                throw new \Exception('Leave request cannot be cancelled once it is updated from pending state.',403);
            }
            $this->timeLeaveService->cancelLeaveRequest($validatedData, $leaveRequestDetail);
            return AppHelper::sendSuccessResponse('Leave request cancelled successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

}
