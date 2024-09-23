<?php

namespace App\Http\Controllers\Web;

use App\Enum\EmployeeAttendanceTypeEnum;
use App\Enum\LeaveStatusEnum;
use App\Helpers\AppHelper;
use App\Helpers\AttendanceHelper;
use App\Helpers\SMPush\SMPushHelper;
use App\Http\Controllers\Controller;
use App\Requests\Leave\TimeLeaveStoreRequest;
use App\Services\Attendance\AttendanceService;
use App\Services\Leave\TimeLeaveService;
use App\Services\Notification\NotificationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TimeLeaveController extends Controller
{
    private $view = 'admin.timeLeaveRequest.';

    public function __construct(protected TimeLeaveService $timeLeaveService, protected NotificationService $notificationService, protected AttendanceService $attendanceService) {}

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-time_leave_request')) {
            try {
                $filterParameters = [
                    'requested_by' => $request->requested_by ?? null,
                    'month' => $request->month ?? null,
                    'year' => $request->year ?? Carbon::now()->format('Y'),
                    'status' => $request->status ?? null
                ];
                if (AppHelper::ifDateInBsEnabled()) {
                    $nepaliDate = AppHelper::getCurrentNepaliYearMonth();
                    $filterParameters['year'] = $request->year ?? $nepaliDate['year'];
                }
                $months = AppHelper::MONTHS;
                $with = ['leaveRequestedBy:id,name'];
                $select = ['time_leaves.*'];
                $timeLeaves = $this->timeLeaveService->getAllEmployeeLeaveRequests($filterParameters, $select, $with);


                return view(
                    $this->view . 'index',
                    compact('timeLeaves', 'filterParameters', 'months')
                );
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show($leaveId)
    {
        if (\Auth::user()->can('show-time_leave_request')) {
            try {
                $select = ['reasons', 'admin_remark'];
                $leaveRequest = $this->timeLeaveService->findEmployeeTimeLeaveRequestById($leaveId, $select);

                $leaveRequest->reasons = strip_tags($leaveRequest->reasons);
                return response()->json([
                    'data' => $leaveRequest,
                ]);
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function updateLeaveRequestStatus(Request $request, $leaveRequestId)
    {
        if (\Auth::user()->can('edit-time_leave_request')) {

            try {
                $validatedData = $request->validate([
                    'status' => ['required', 'string', Rule::in(array_column(LeaveStatusEnum::cases(), 'value'))],
                    'admin_remark' => ['nullable', 'required_if:status,' . LeaveStatusEnum::rejected->value, 'string', 'min:10'],
                ]);

                DB::beginTransaction();

                $leaveRequestDetail = $this->timeLeaveService->updateLeaveRequestStatus($validatedData, $leaveRequestId);

                if ($leaveRequestDetail) {
                    $notificationData = [
                        'title' => 'Time Leave Status Update',
                        'type' => 'leave',
                        'user_id' => [$leaveRequestDetail->requested_by],
                        'description' => 'Your Time Leave request requested on ' . date('M d Y', strtotime($leaveRequestDetail->issue_date)) . ' has been ' . ucfirst($validatedData['status']),
                        'notification_for_id' => $leaveRequestId,
                    ];

                    $notification = $this->notificationService->store($notificationData);

                    if ($notification) {
                        $this->sendLeaveStatusNotification($notification, $leaveRequestDetail->requested_by);
                    }

                    // make checkout
                    $attendanceData = $this->attendanceService->findEmployeeTodayAttendanceDetail($leaveRequestDetail->requested_by);

                    if ($attendanceData) {
                        if ((strtotime($attendanceData->attendance_date) == strtotime($leaveRequestDetail->issue_date)) && ($validatedData['status'] == LeaveStatusEnum::approved->value)) {

                            $updateData = [
                                'check_out_at' => $leaveRequestDetail->start_time,
                                'check_out_type' => EmployeeAttendanceTypeEnum::wifi->value
                            ];

                            $workedData = AttendanceHelper::calculateWorkedHour($leaveRequestDetail->start_time, $attendanceData->check_in_at, $attendanceData->user_id);

                            $updateData['worked_hour'] = $workedData['workedHours'];
                            $updateData['overtime'] = $workedData['overtime'];
                            $updateData['undertime'] = $workedData['undertime'];

                            $this->attendanceService->update($attendanceData, $updateData);
                        }
                    }
                }
                DB::commit();
                return redirect()
                    ->route('admin.time-leave-request.index')
                    ->with('success', 'Status Updated Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    private function sendLeaveStatusNotification($notificationData, $userId)
    {
        SMPushHelper::sendLeaveStatusNotification($notificationData->title, $notificationData->description, $userId);
    }

    public function createLeaveRequest()
    {
        if (\Auth::user()->can('create-time_leave_request')) {
            try {
                return view($this->view . 'add');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function storeLeaveRequest(TimeLeaveStoreRequest $request)
    {
        if (\Auth::user()->can('create-time_leave_request')) {
            try {
                $validatedData = $request->validated();

                $permissionKeyForNotification = 'employee_time_leave_request';

                $validatedData['requested_by'] = auth()->user()->id;

                DB::beginTransaction();
                $leaveRequest = $this->timeLeaveService->storeTimeLeaveRequest($validatedData);
                DB::commit();
                AppHelper::sendNotificationToAuthorizedUser(
                    'Leave Request Notification',
                    ucfirst(auth()->user()?->name) . ' has requested leave from ' .
                        $leaveRequest['start_time'] . ' to ' . $leaveRequest['end_time'] .
                        ' on ' . AppHelper::convertLeaveDateFormat($leaveRequest['issue_date']) . ' Reason: ' . $validatedData['reasons'],
                    $permissionKeyForNotification
                );
                return redirect()
                    ->route('admin.time-leave-request.index')
                    ->with('success', 'Leave request submitted Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()
                    ->with('danger', $exception->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
