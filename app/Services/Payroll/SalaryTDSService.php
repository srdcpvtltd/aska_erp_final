<?php

namespace App\Services\Payroll;

use App\Repositories\SalaryTDSRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalaryTDSService
{

    public function __construct(private SalaryTDSRepository $salaryTDSRepo)
    {
    }

    public function getAllSalaryTDSListGroupByMaritalStatus($select=['*'])
    {
        try{
            return $this->salaryTDSRepo->getAllSalaryTDSDetail($select)->groupBy('marital_status');;
        }catch(\Exception $exception){
            throw $exception;
        }
    }

    public function store($validatedData)
    {
        try{
            $createdBy = getAuthUserCode();
            $salaryTDs = [];
            $count = count($validatedData['annual_salary_to']);
            for($i= 0 ; $i < $count; $i++){
                $salaryTDs[] = [
                    'annual_salary_from' => $validatedData['annual_salary_from'][$i],
                    'annual_salary_to' => $validatedData['annual_salary_to'][$i],
                    'tds_in_percent' => $validatedData['tds_in_percent'][$i],
                    'marital_status' => $validatedData['marital_status'],
                    'created_by' => $createdBy,
                ];
            }
            DB::beginTransaction();
                $salaryTDS = $this->salaryTDSRepo->store($salaryTDs);
            DB::commit();
            return $salaryTDS;
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function findSalaryTDSById($id,$select=['*'])
    {
        try{
            $salaryTDSDetail = $this->salaryTDSRepo->findSalaryTDSDetailById($id,$select);
            if(!$salaryTDSDetail){
                throw new \Exception('Salary TDS Detail Not Found',404);
            }
            return $salaryTDSDetail;
        }catch(\Exception $exception){
            throw $exception;
        }
    }

    public function updateDetail($salaryTDSDetail,$validatedData)
    {
        try{
            DB::beginTransaction();
            $update = $this->salaryTDSRepo->update($salaryTDSDetail,$validatedData);
            DB::commit();;
            return $update;
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteSalaryTDSDetail($salaryTDSDetail)
    {
        try{
            DB::beginTransaction();
            $delete = $this->salaryTDSRepo->delete($salaryTDSDetail);
            DB::commit();;
            return $delete;
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function changeSalaryTDSStatus($salaryTDSDetail)
    {
        try{
            DB::beginTransaction();
            $status = $this->salaryTDSRepo->toggleSalaryTDSDetail($salaryTDSDetail);
            DB::commit();;
            return $status;
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
}
