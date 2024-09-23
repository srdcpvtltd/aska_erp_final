<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Repositories\LeaveRepository;
use App\Repositories\LeaveTypeRepository;
use App\Requests\Leave\LeaveTypeRequest;
use Exception;

class LeaveTypeController extends Controller
{
    private $view = 'admin.leaveType.';

    private LeaveTypeRepository $leaveTypeRepo;
    private LeaveRepository $leaveRepo;


    public function __construct(
        LeaveTypeRepository $leaveTypeRepo,
        LeaveRepository     $leaveRepo
    ) {
        $this->leaveTypeRepo = $leaveTypeRepo;
        $this->leaveRepo = $leaveRepo;
    }

    public function index()
    {
        if (\Auth::user()->can('manage-leave_types')) {
            try {
                $leaveTypes = $this->leaveTypeRepo->getAllLeaveTypes();
                return view($this->view . 'index', compact('leaveTypes'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-leave_types')) {
            try {
                return view($this->view . 'create');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(LeaveTypeRequest $request)
    {
        if (\Auth::user()->can('create-leave_types')) {
            try {
                $validatedData = $request->validated();
                $validatedData['company_id'] = AppHelper::getAuthUserCompanyId();
                $this->leaveTypeRepo->store($validatedData);
                return redirect()
                    ->route('admin.leaves.index')
                    ->with('success', 'New Leave Type Added Successfully');
            } catch (Exception $e) {
                return redirect()->back()
                    ->with('danger', $e->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-leave_types')) {
            try {
                $leaveDetail = $this->leaveTypeRepo->findLeaveTypeDetailById($id);
                return view($this->view . 'edit', compact('leaveDetail'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(LeaveTypeRequest $request, $id)
    {
        if (\Auth::user()->can('edit-leave_types')) {
            try {
                $validatedData = $request->validated();
                $validatedData['company_id'] = AppHelper::getAuthUserCompanyId();
                $leaveDetail = $this->leaveTypeRepo->findLeaveTypeDetailById($id);
                if (!$leaveDetail) {
                    throw new Exception('Leave Type  Not Found', 404);
                }
                $this->leaveTypeRepo->update($leaveDetail, $validatedData);
                return redirect()
                    ->route('admin.leaves.index')
                    ->with('success', 'Leave Type Updated Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleStatus($id)
    {
        if (\Auth::user()->can('edit-leave_types')) {
            try {
                $this->leaveTypeRepo->toggleStatus($id);
                return redirect()->back()->with('success', 'Status changed  Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleEarlyExit($id)
    {
        if (\Auth::user()->can('edit-leave_types')) {
            try {
                $this->leaveTypeRepo->toggleEarlyExitStatus($id);
                return redirect()->back()->with('success', 'Early exit status  changed  Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function delete($id)
    {
        if (\Auth::user()->can('delete-leave_types')) {
            try {
                $leaveType = $this->leaveTypeRepo->findLeaveTypeDetailById($id);
                if (!$leaveType) {
                    throw new Exception('Leave Type Not Found', 404);
                }
                $checkLeaveTypeIfUsed = $this->leaveRepo->findLeaveRequestCountByLeaveTypeId($leaveType->id);
                if ($checkLeaveTypeIfUsed > 0) {
                    throw new Exception('Cannot delete ' . ucfirst($leaveType->name) . ' is in use', 402);
                }
                $this->leaveTypeRepo->delete($leaveType);
                return redirect()->back()->with('success', 'Leave Type Deleted  Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function getLeaveTypesBasedOnEarlyExitStatus($status)
    {
        try {
            $leaveType = $this->leaveTypeRepo->getAllLeaveTypesBasedOnEarlyExitStatus($status);
            return AppHelper::sendSuccessResponse('Data Found', $leaveType);
        } catch (Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }
}
