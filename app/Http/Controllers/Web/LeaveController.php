<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Helpers\SMPush\SMPushHelper;
use App\Http\Controllers\Controller;
use App\Models\LeaveRequestMaster;
use App\Repositories\LeaveTypeRepository;
use App\Repositories\UserRepository;
use App\Requests\Leave\LeaveRequestAdd;
use App\Requests\Leave\LeaveRequestStoreFromWeb;
use App\Requests\Leave\LeaveRequestStoreRequest;
use App\Services\Leave\LeaveService;
use App\Services\Notification\NotificationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LeaveController extends Controller
{
    private $view = 'admin.leaveRequest.';

    private LeaveService $leaveService;
    private LeaveTypeRepository $leaveTypeRepo;
    private NotificationService $notificationService;
    private UserRepository $userRepository;

    public function __construct(LeaveService $leaveService, LeaveTypeRepository $leaveTypeRepo, NotificationService $notificationService, UserRepository $userRepository)
    {
        $this->leaveService = $leaveService;
        $this->leaveTypeRepo = $leaveTypeRepo;
        $this->notificationService = $notificationService;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-leave_request')) {
            try {
                $filterParameters = [
                    'leave_type' => $request->leave_type ?? null,
                    'requested_by' => $request->requested_by ?? null,
                    'month' => $request->month ?? null,
                    'year' => $request->year ?? Carbon::now()->format('Y'),
                    'status' => $request->status ?? null
                ];
                if (AppHelper::ifDateInBsEnabled()) {
                    $nepaliDate = AppHelper::getCurrentNepaliYearMonth();
                    $filterParameters['year'] = $request->year ?? $nepaliDate['year'];
                }
                $leaveTypes = $this->leaveTypeRepo->getAllLeaveTypes(['id', 'name']);
                $months = AppHelper::MONTHS;
                $with = ['leaveType:id,name', 'leaveRequestedBy:id,name'];
                $select = ['leave_requests_master.*'];
                $leaveDetails = $this->leaveService->getAllEmployeeLeaveRequests($filterParameters, $select, $with);
                return view(
                    $this->view . 'index',
                    compact('leaveDetails', 'filterParameters',  'leaveTypes', 'months')
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
        if (\Auth::user()->can('show-leave_request')) {
            try {
                $select = ['reasons', 'admin_remark'];
                $with = ['referredBy:id,name'];
                $leaveRequest = $this->leaveService->findEmployeeLeaveRequestById($leaveId, $select, $with);

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
        if (\Auth::user()->can('edit-leave_request')) {

            $validatedData = $request->validate([
                'status' => ['required', 'string', Rule::in(LeaveRequestMaster::STATUS)],
                'admin_remark' => ['nullable', 'required_if:status,rejected', 'string', 'min:10'],
            ]);

            try {
                DB::beginTransaction();

                $leaveRequestDetail = $this->leaveService->updateLeaveRequestStatus($validatedData, $leaveRequestId);

                if ($leaveRequestDetail) {
                    $notificationData = [
                        'title' => 'Leave Status Update',
                        'type' => 'leave',
                        'user_id' => [$leaveRequestDetail->requested_by],
                        'description' => 'Your ' . $leaveRequestDetail->no_of_days . ' day leave request requested on ' . date('M d Y h:i A', strtotime($leaveRequestDetail->leave_requested_date)) . ' has been ' . ucfirst($validatedData['status']),
                        'notification_for_id' => $leaveRequestId,
                    ];

                    $notification = $this->notificationService->store($notificationData);

                    if ($notification) {
                        // $this->sendLeaveStatusNotification($notification,$leaveRequestDetail->requested_by);
                    }
                }
                DB::commit();
                return redirect()
                    ->route('admin.leave-request.index')
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
        if (\Auth::user()->can('create-leave_request')) {
            try {
                $leavetypes = $this->leaveTypeRepo->getAllActiveLeaveTypes(['id', 'name']);
                return view($this->view . 'create', compact('leavetypes'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function storeLeaveRequest(LeaveRequestStoreFromWeb $request)
    {
        if (\Auth::user()->can('create-leave_request')) {
            try {
                $validatedData = $request->validated();
                $permissionKeyForNotification = 'employee_leave_request';

                $validatedData['requested_by'] = auth()->user()->id;
                DB::beginTransaction();
                $leaveRequest = $this->leaveService->storeLeaveRequest($validatedData);
                DB::commit();

                return redirect()
                    ->back()
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

    public function addLeaveRequest()
    {
        if (\Auth::user()->can('create-leave_request')) {
            try {
                $leavetypes = $this->leaveTypeRepo->getAllActiveLeaveTypes(['id', 'name']);

                $employees = $this->userRepository->getAllVerifiedEmployeesExceptAdminOfCompany(['id', 'name']);

                return view($this->view . 'add', compact('employees', 'leavetypes'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function saveLeaveRequest(LeaveRequestAdd $request)
    {
        if (\Auth::user()->can('create-leave_request')) {
            try {
                $validatedData = $request->validated();
                $permissionKeyForNotification = 'employee_leave_request';

                $validatedData['referred_by'] = auth()->user()->id;

                $employee = $this->userRepository->findUserDetailById($validatedData['requested_by'], ['name']);

                DB::beginTransaction();
                $leaveRequest = $this->leaveService->storeLeaveRequest($validatedData);
                DB::commit();

                return redirect()
                    ->route('admin.leave-request.index')
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
