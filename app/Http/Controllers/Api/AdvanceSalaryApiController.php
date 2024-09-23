<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\AdvanceSalaryAttachment;
use App\Requests\Payroll\AdvanceSalary\AdvanceSalaryRequest;
use App\Resources\Payroll\AdvanceSalary\AdvanceSalaryCollection;
use App\Resources\Payroll\AdvanceSalary\AdvanceSalaryResource;
use App\Services\Payroll\AdvanceSalaryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvanceSalaryApiController extends Controller
{
    public function __construct(
        public AdvanceSalaryService $advanceSalaryService,
        public AdvanceSalaryAttachment $advanceSalaryAttachment
    ){}

    public function getEmployeesAdvanceSalaryDetailLists(Request $request)
    {
        try {
            $select = ['*'];
            $with = [];
            $advanceSalaryLists = $this->advanceSalaryService->getAllEmployeeAdvanceSalaryListDetail(getAuthUserCode(),$select,$with);

            $data = new AdvanceSalaryCollection($advanceSalaryLists);
            return AppHelper::sendSuccessResponse('Data Found',$data);
        } catch (Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    public function getEmployeeAdvanceSalaryDetailById($id)
    {
        try {
            $select= ['*'];
            $with = ['attachments'];
            $detail = $this->advanceSalaryService->findEmployeeAdvanceSalaryDetailByIdAndEmployeeId($id,$with,$select);
            $data = new AdvanceSalaryResource($detail);
            return AppHelper::sendSuccessResponse('Data Found',$data);
        } catch (Exception $exception) {
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    public function store(AdvanceSalaryRequest $request)
    {
        try {
            $permissionKeyForNotification = 'advance_salary_alert';
            $checkEmployeePendingRequests = $this->advanceSalaryService->checkIfEmployeeUnsettledAdvanceSalaryRequestExists(getAuthUserCode());
            if($checkEmployeePendingRequests){
                throw new Exception('Another advance salary cannot be requested until the previous advance request is settled.',400);
            }
            $validatedData = $request->validated();
            DB::beginTransaction();
                $advanceDetail = $this->advanceSalaryService->store($validatedData);
                $data = new AdvanceSalaryResource($advanceDetail);
            DB::commit();
            AppHelper::sendNotificationToAuthorizedUser(
                'Advance Salary Request Alert',
                auth()->user()->name.' has submitted a advance salary request of amount '.$validatedData['requested_amount'],
                $permissionKeyForNotification
            );
            return AppHelper::sendSuccessResponse('Data created successfully',$data);
        }catch(Exception $e) {
            DB::rollBack();
            return AppHelper::sendErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function updateDetail(AdvanceSalaryRequest $request)
    {
        try {
            $permissionKeyForNotification = 'advance_salary_alert';
            $detail = $this->advanceSalaryService->findEmployeeAdvanceSalaryDetailByIdAndEmployeeId($request->advance_salary_id);
            $validatedData = $request->validated();
            DB::beginTransaction();
              $updateDetail = $this->advanceSalaryService->update($detail,$validatedData);
            DB::commit();
            AppHelper::sendNotificationToAuthorizedUser(
                'Advance Salary Request Alert',
                auth()->user()->name.' has updated a advance salary request detail',
                $permissionKeyForNotification
            );
            return AppHelper::sendSuccessResponse('Data updated successfully',
                new AdvanceSalaryResource($updateDetail)
            );
        }catch (Exception $e) {
            DB::rollBack();
            return AppHelper::sendErrorResponse($e->getMessage(), $e->getCode());
        }
    }

}
