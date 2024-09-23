<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Helpers\SMPush\SMPushHelper;
use App\Http\Controllers\Controller;
use App\Repositories\GeneralSettingRepository;
use App\Requests\Payroll\AdvanceSalary\AdvanceSalaryUpdateRequest;
use App\Services\Payroll\AdvanceSalaryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvanceSalaryController extends Controller
{
    private $view = 'admin.payroll.advanceSalary.';

    public function __construct(public AdvanceSalaryService $advanceSalaryService, public GeneralSettingRepository $generalSettingRepository) {}

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-advance_salary')) {
            try {
                $filterParameters = [
                    'employee' => $request->employee ?? null,
                    'status' => $request->status ?? null,
                    'month' => $request->month ?? null
                ];
                $select = ['*'];
                $with = [];
                $advanceSalaryRequestLists = $this->advanceSalaryService->getAllAdvanceSalaryDetailPaginated($filterParameters, $select, $with);
                $months = AppHelper::getMonthsList();

                return view($this->view . 'index', compact('advanceSalaryRequestLists', 'filterParameters', 'months'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show($id)
    {
        if (\Auth::user()->can('show-advance_salary')) {
            try {
                $select = ['*'];
                $with = [
                    'verifiedBy:id,name',
                    'requestedBy:id,name',
                    'attachments'
                ];
                $advanceSalaryDetail = $this->advanceSalaryService->findAdvanceSalaryDetailById($id, $with, $select);
                $attachments = $advanceSalaryDetail->attachments;
                return view($this->view . 'show', compact('advanceSalaryDetail', 'attachments'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(AdvanceSalaryUpdateRequest $request, $id)
    {
        if (\Auth::user()->can('edit-advance_salary')) {
            try {
                $validatedData = $request->validated();
                $advanceSalaryRequestDetail = $this->advanceSalaryService->findAdvanceSalaryDetailById($id);
                $this->advanceSalaryService->advanceSalaryUpdateByAdmin($advanceSalaryRequestDetail, $validatedData);

                $notificationData = [
                    'title' => 'Advance Salary ' . ucfirst($validatedData['status']),
                    'type' => 'Advance Salary',
                    'user_id' => [$advanceSalaryRequestDetail->employee_id],
                    'description' => 'Your advance salary requested on ' . date('M d Y', strtotime($advanceSalaryRequestDetail->advance_requested_date)) . ' has been ' . ucfirst($validatedData['status']),
                    'notification_for_id' => $id,
                ];
                $this->sendAdvanceSalaryStatusNotification($notificationData, $advanceSalaryRequestDetail->employee_id);
                return redirect()->back()->with('success', 'Status Changed Successfully');
            } catch (Exception $exception) {
                return redirect()->back()
                    ->with('danger', $exception->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    private function sendAdvanceSalaryStatusNotification($notificationData, $userId)
    {
        SMPushHelper::sendAdvanceSalaryNotification($notificationData->title, $notificationData->description, $userId);
    }

    public function delete($id)
    {
        if (\Auth::user()->can('delete-advance_salary')) {
            try {
                DB::beginTransaction();
                $this->advanceSalaryService->delete($id);
                DB::commit();
                return redirect()->back()->with('success', 'Advance Salary Detail Deleted Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function setting()
    {
        try {
            $key = 'advance_salary_limit';
            $advanceSalarySetting = $this->generalSettingRepository->getGeneralSettingByKey($key);
            return view('admin.payrollSetting.advanceSalary.create', compact('advanceSalarySetting'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }
}
