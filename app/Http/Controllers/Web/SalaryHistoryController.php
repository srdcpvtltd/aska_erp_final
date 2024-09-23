<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SalaryReviseHistory;
use App\Repositories\EmployeeSalaryRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\UserRepository;
use App\Requests\Payroll\ReviseSalary\ReviseSalaryRequest;
use App\Services\Payroll\SalaryReviseHistoryService;
use Exception;

class SalaryHistoryController extends Controller
{
    public $view = 'admin.payroll.employeeSalary.';

    public function __construct(
        public SalaryReviseHistoryService $salaryHistoryService,
        public UserRepository $userRepo,
        public EmployeeSalaryRepository $employeeSalaryRepository,
    ){}

    public function getEmployeeAllSalaryHistory($employeeId)
    {
        try{
            $select = ['*'];

            $salaryReviseLists = $this->salaryHistoryService->getEmployeeAllSalaryHistory($employeeId,$select);
            $employeeDetail = $this->userRepo->findUserDetailById($employeeId,['id','name']);
            return view($this->view.'salary_history',compact('salaryReviseLists','employeeDetail'));
        }catch (Exception $exception){
            return redirect()->back()->with('danger',$exception->getMessage());
        }
    }

    public function create($employeeId)
    {
        try{
            $employeeSalary = $this->employeeSalaryRepository->getEmployeeSalaryByEmployeeId($employeeId,['annual_salary']);

            $employeeDetail = $this->userRepo->findUserDetailById($employeeId,['id','name']);

            return view($this->view.'increment_salary',compact('employeeDetail','employeeSalary'));
        }catch(Exception $exception){
            return redirect()->back()->with('danger',$exception->getMessage());
        }
    }

    public function store(ReviseSalaryRequest $request)
    {
        try{
            $validatedData = $request->validated();

            $this->salaryHistoryService->store($validatedData);
            return redirect()
                ->route('admin.employee-salaries.index')
                ->with('success','Employee Salary Updated Successfully');
        }catch(Exception $exception){
            return redirect()
                ->back()
                ->with('danger',$exception->getMessage())
                ->withInput();
        }
    }
}
