<?php

namespace App\Services\Payroll;

use App\Repositories\EmployeePayslipRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class EmployeePayslipService
{
    public function __construct(public EmployeePayslipRepository $employeePayslipRepo){}

    public function getAllEmployeePayslipDetailByEmployeeId($employeeId)
    {
        try{

        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function findEmployeePayslipById($id,$select=['*'],$with=[])
    {
        try{
            $employeePayslipDetail =  $this->employeePayslipRepo->findAssetById($id,$select,$with);
            if(!$employeePayslipDetail){
                throw new \Exception('Asset Type Not Found',400);
            }
            return $employeePayslipDetail;
        }catch(Exception $exception){
            throw $exception;
        }
    }

    /**
     * @throws Exception
     */
    public function saveEmployeePayslip($validatedData)
    {
        try {
            DB::beginTransaction();
            $employeePayslipDetail = $this->employeePayslipRepo->store($validatedData);
            DB::commit();
            return $employeePayslipDetail;
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    /**
     * @throws Exception
     */
    public function updateEmployeePayslip($id, $validatedData)
    {
        try {
            $employeePayslipDetail = $this->findEmployeePayslipById($id);
            DB::beginTransaction();
            $updateStatus = $this->employeePayslipRepo->update($employeePayslipDetail, $validatedData);
            DB::commit();
            return $updateStatus;
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    /**
     * @throws Exception
     */
    public function deleteEmployeePayslip($id)
    {
        try {
            $employeePayslipDetail = $this->findEmployeePayslipById($id);
            DB::beginTransaction();
            $status =  $this->employeePayslipRepo->delete($employeePayslipDetail);
            DB::commit();
            return $status;
        } catch (Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();

        }
    }

}
