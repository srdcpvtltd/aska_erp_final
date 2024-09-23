<?php

namespace App\Services\Payroll;

use App\Helpers\AppHelper;
use App\Repositories\EmployeeSalaryRepository;
use App\Repositories\SalaryReviseHistoryRepository;
use App\Repositories\UserAccountRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class SalaryReviseHistoryService
{
    public function __construct(
        public SalaryReviseHistoryRepository $salaryHistoryRepo,
        public EmployeeSalaryRepository $employeeSalaryRepository,
        public SalaryGroupService $salaryGroupService,
    ){}

    public function getEmployeeAllSalaryHistory($employeeId,$select=['*'],$with=[])
    {
        return $this->salaryHistoryRepo->getAllEmployeeSalaryHistoryList($employeeId,$select,$with);
    }

    /**
     * @param $validatedData
     * @return void
     * @throws Exception
     */
    public function store($validatedData): void
    {
        try{

            $employeeSalary = $this->employeeSalaryRepository->getEmployeeSalaryByEmployeeId($validatedData['employee_id']);
            if(!$employeeSalary)
            {
                throw new Exception('Employee Salary Not found',404);
            }

            $validatedData['base_salary'] = $employeeSalary->annual_salary;
            $validatedData['salary_revised_on'] = AppHelper::getCurrentDateInYmdFormat();

            DB::beginTransaction();
            $salaryReviseHistory = $this->salaryHistoryRepo->store($validatedData);
            if($salaryReviseHistory)
            {
                $monthlyBasic = $employeeSalary->basic_salary_value;

                $monthSalary = ($salaryReviseHistory->revised_salary /12);


                if($employeeSalary->basic_salary_type == 'percent'){
                    $monthlyBasic =  ($employeeSalary->basic_salary_value /100) * $monthSalary;

                }

                $salaryGroup = $this->salaryGroupService->findOrFailSalaryGroupDetailById($employeeSalary->salary_group_id, ['*'], ['salaryComponents']);

                $incomeComponent = 0;
                foreach ($salaryGroup->salaryComponents as $component) {

                    if($component->component_type == 'earning'){
                        if($component->value_type == 'basic'){
                            $incomeComponent +=($component->component_value_monthly / 100) *  $monthlyBasic;
                        }elseif($component->value_type == 'ctc'){
                            $incomeComponent +=($component->component_value_monthly / 100) *  $salaryReviseHistory->revised_salary;
                        }else{
                            $incomeComponent +=$component->component_value_monthly;
                        }
                    }

                }
                $monthlyFixedAllowance = (($monthSalary - $monthlyBasic) - $incomeComponent);

                $employeeSalaryData = [
                    'annual_salary'=> $salaryReviseHistory->revised_salary,
                    'monthly_basic_salary'=>$monthlyBasic,
                    'annual_basic_salary'=>$monthlyBasic * 12,
                    'monthly_fixed_allowance'=> $monthlyFixedAllowance,
                    'annual_fixed_allowance'=>$monthlyFixedAllowance * 12,
                ];
                $this->employeeSalaryRepository->update($employeeSalary, $employeeSalaryData);
            }
            DB::commit();
            return ;
        }catch (Exception $exception){
            DB::rollBack();
        }
    }
}
