<?php

namespace App\Http\Controllers\Web;

use App\Enum\EmployeeBasicSalaryTypeEnum;
use App\Enum\PayslipStatusEnum;
use App\Helpers\AppHelper;
use App\Helpers\AttendanceHelper;
use App\Helpers\DateConverter;
use App\Helpers\NepaliDate;
use App\Helpers\PayrollHelper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EmployeeAccount;
use App\Models\EmployeePayslipDetail;
use App\Models\UnderTimeSetting;
use App\Models\User;
use App\Repositories\BranchRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\EmployeePayslipDetailRepository;
use App\Repositories\EmployeePayslipRepository;
use App\Repositories\EmployeeSalaryRepository;
use App\Repositories\SalaryGroupEmployeeRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\UserRepository;
use App\Rules\ImageDimensionRule;
use App\Services\Payroll\AdvanceSalaryService;
use App\Services\Payroll\GeneratePayrollService;
use App\Services\Payroll\PaymentMethodService;
use App\Services\Payroll\SalaryGroupService;
use App\Services\Payroll\UnderTimeSettingService;
use App\Services\Tada\TadaService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MilanTarami\NumberToWordsConverter\Services\NumberToWords;

class EmployeeSalaryController extends Controller
{
    public $view = 'admin.payroll.employeeSalary.';

    public function __construct(
        public UserRepository $userRepository,
        public UserAccountRepository $userAccountRepo,
        public GeneratePayrollService $generatePayrollService,
        public EmployeeSalaryRepository $employeeSalaryRepository,
        public SalaryGroupEmployeeRepository $salaryGroupEmployeeRepository,
        public SalaryGroupService $salaryGroupService,
        public EmployeePayslipRepository $payslipRepository,
        public EmployeePayslipDetailRepository $payslipDetailRepository,
        public DepartmentRepository $departmentRepository,
        public TadaService $tadaService,
        public PaymentMethodService $paymentMethodService,
        public AdvanceSalaryService $advanceSalaryService,
        public BranchRepository $branchRepo,
        public UnderTimeSettingService $utSettingService,
    ) {}

    public function index(Request $request): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('manage-payroll')) {
            try {

                $filterParameters = [
                    'employee_name' => $request->employee_name ?? null,
                    'department_id' => $request->department_id ?? null
                ];
                $employeeLists = $this->userRepository->getAllVerifiedActiveEmployeeWithSalaryGroup($filterParameters);

                $departments = $this->departmentRepository->getAllActiveDepartments([], ['id', 'dept_name']);
                return view($this->view . 'index', compact('employeeLists', 'filterParameters', 'departments'));
            } catch (Exception $exception) {
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function changeSalaryCycle($employeeId, $salaryCycle)
    {
        try {

            $employeeAccountDetail = $this->userAccountRepo->findAccountDetailByEmployeeId($employeeId);
            if (!$employeeAccountDetail) {
                throw new Exception('Employee Account Detail Not Found', 404);
            }
            if (!in_array($salaryCycle, EmployeeAccount::SALARY_CYCLE)) {
                throw new Exception('Invalid Cycle Data', 400);
            }
            $updateCycle = $this->userAccountRepo->updateEmployeeSalaryCycle($employeeAccountDetail, $salaryCycle);
            return redirect()->back()->with('success', 'Salary Cycle Updated to ' . ucfirst($updateCycle->salary_cycle) . ' Successfully');
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->with('danger', $exception->getMessage());
        }
    }

    public function payrollCreate(Request $request)
    {
        if (\Auth::user()->can('create-payroll')) {
            try {
                $payrollData = $this->generatePayrollService->getEmployeeSalariesToCreatePayslip();

                return view($this->view . 'generate_payslip', compact('payrollData'));
            } catch (Exception $exception) {
                return response()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function payroll(Request $request): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('show-payroll')) {
            try {
                $filterData = [];

                if ($request->all()) {
                    $validator = Validator::make($request->all(), [
                        'salary_cycle' => ['required'],
                        'year' => ['required'],
                        'month' => ['nullable'],
                        'week' => ['nullable'],
                        'include_tada' => ['nullable'],
                        'include_advance_salary' => ['nullable'],
                        'attendance' => ['required'],
                        'absent_paid' => ['nullable'],
                        'department_id' => ['nullable'],
                        'branch_id' => ['nullable'],
                        'approved_paid_leaves' => ['nullable'],

                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator);
                    }

                    $filterData = $validator->validated();

                    $filterData['include_tada'] =  $filterData['include_tada'] ?? 0;
                    $filterData['include_advance_salary'] =  $filterData['include_advance_salary'] ?? 0;

                    $payrolls = $this->generatePayrollService->getEmployeeSalariesToCreatePayslip($filterData);
                } else {
                    $payrolls = $this->generatePayrollService->getCurrentEmployeeSalaries();
                }

                $employees = $this->userRepository->getAllEmployeesForPayroll();
                $paymentMethods = $this->paymentMethodService->pluckAllActivePaymentMethod(['id', 'name']);
                $currency = AppHelper::getCompanyPaymentCurrencySymbol();

                $companyId = AppHelper::getAuthUserCompanyId();

                $payslipStatus = EmployeePayslipDetail::PAYSLIP_STATUS;
                $salaryCycles = EmployeeAccount::SALARY_CYCLE;
                $branches = $this->branchRepo->getLoggedInUserCompanyBranches($companyId, ['id', 'name']);

                $isBSDate = AppHelper::ifDateInBsEnabled();
                $months = AppHelper::getMonthsList();
                $currentNepaliYearMonth = AppHelper::getCurrentYearMonth();

                return view($this->view . 'payroll', compact('payslipStatus', 'salaryCycles', 'payrolls', 'filterData', 'paymentMethods', 'currency', 'employees', 'branches', 'months', 'isBSDate', 'currentNepaliYearMonth'));
            } catch (Exception $exception) {
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function viewPayroll($employeePayslipId): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('show-payroll')) {
            try {
                $imagePath = User::AVATAR_UPLOAD_PATH;
                $payrollData = $this->generatePayrollService->getEmployeeAccountDetailToCreatePayslip($employeePayslipId);
                $currency = AppHelper::getCompanyPaymentCurrencySymbol();
                $underTimeSetting = $this->utSettingService->getAllUTList(['is_active'], 1);

                return view($this->view . 'payslip', compact('payrollData', 'imagePath', 'currency', 'underTimeSetting'));
            } catch (Exception $exception) {
                return redirect()->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function editPayroll($employeePayslipId): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('edit-payroll')) {
            try {
                $payrollData = $this->generatePayrollService->getEmployeeAccountDetailToCreatePayslip($employeePayslipId);
                $currency = AppHelper::getCompanyPaymentCurrencySymbol();
                $underTimeSetting = $this->utSettingService->getAllUTList(['is_active'], 1);

                $paymentMethods = $this->paymentMethodService->pluckAllActivePaymentMethod(['id', 'name']);
                $paidStatus = PayslipStatusEnum::paid->value;

                return view($this->view . 'edit_payslip', compact('payrollData', 'currency', 'underTimeSetting', 'paidStatus', 'paymentMethods'));
            } catch (Exception $exception) {
                return redirect()->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function updatePayroll(Request $request, $employeePayslipId): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('edit-payroll')) {
            try {
                $validatedData = $request->all();

                $employeePayslipData = [
                    "paid_on" => ($validatedData['status'] == PayslipStatusEnum::paid->value) ? $validatedData['paid_on'] : null,
                    "status" => $validatedData['status'],
                    "monthly_basic_salary" => $validatedData['monthly_basic_salary'],
                    "monthly_fixed_allowance" => $validatedData['monthly_fixed_allowance'],
                    "tds" => $validatedData['tds'],
                    "advance_salary" => $validatedData['advance_salary'] ?? 0,
                    "tada" => $validatedData['tada'] ?? 0,
                    "net_salary" => $validatedData['net_salary'],
                    "absent_deduction" => $validatedData['absent_deduction'],
                    "overtime" => $validatedData['overtime'] ?? 0,
                    "undertime" => $validatedData['undertime'] ?? 0,
                    "payment_method_id" => $validatedData['payment_method_id'] ?? null,
                ];


                $employeePaySlipDetail = $this->payslipRepository->find($employeePayslipId);

                DB::beginTransaction();

                if ($validatedData['status'] == PayslipStatusEnum::paid->value) {
                    $updateData = [
                        'status' => 'accepted',
                        'is_settled' => 1,
                        'remark' => 'included in salary.',
                        'verified_by' => auth()->user()?->id,
                    ];
                    $this->tadaService->makeSettlement($updateData, $employeePaySlipDetail->employee_id);

                    // make advance salary settlement
                    $advanceData = [
                        'is_settled' => true,
                        'remark' => 'settled in payroll'
                    ];
                    $this->advanceSalaryService->advanceSalarySettlement($employeePaySlipDetail->employee_id, $advanceData);
                }
                $this->payslipRepository->update($employeePaySlipDetail, $employeePayslipData);


                if (isset($validatedData['amount'])) {
                    foreach ($validatedData['amount'] as $key => $value) {
                        $payslipDetail = $this->payslipDetailRepository->find($employeePayslipId, $key);

                        $this->payslipDetailRepository->update($payslipDetail, ['amount' => $value]);
                    }
                }

                DB::commit();
                return redirect()->route('admin.employee-employee_salary.payroll-detail', $employeePayslipId)->with('success', 'Payroll updated successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function deletePayroll(Request $request, $employeePayslipId): RedirectResponse
    {
        if (\Auth::user()->can('delete-payroll')) {
            try {
                $employeePaySlipDetail = $this->payslipRepository->find($employeePayslipId);

                if ($employeePaySlipDetail->status == PayslipStatusEnum::paid->value  || $employeePaySlipDetail->status == PayslipStatusEnum::locked->value) {
                    return redirect()->back()->with('danger', 'Payslip cannot be deleted once paid or locked.');
                }

                DB::beginTransaction();
                $this->payslipDetailRepository->deleteByPayslipId($employeePayslipId);
                $this->payslipRepository->delete($employeePaySlipDetail);
                DB::commit();

                return redirect()->route('admin.employee-employee_salary.payroll')->with('success', 'Payroll deleted successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    private function filterComponentsByType($components, $type): array
    {
        return array_filter($components, function ($value) use ($type) {
            return $value['component_type'] === $type;
        });
    }

    /**
     * @return Factory|View|RedirectResponse|Application
     */
    public function createSalary($employeeId): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('create-employee_salary')) {
            try {
                $salaryComponents = [];
                $employee = $this->userRepository->findUserDetailById($employeeId, ['id', 'name']);
                $percentType = EmployeeBasicSalaryTypeEnum::percent->value;
                $fixedType = EmployeeBasicSalaryTypeEnum::fixed->value;

                $employeeSalaryGroup = $this->salaryGroupEmployeeRepository->getSalaryGroupFromEmployeeId($employeeId);

                if ($employeeSalaryGroup) {
                    $salaryGroup = $this->salaryGroupService->findOrFailSalaryGroupDetailById($employeeSalaryGroup->salary_group_id, ['*'], ['salaryComponents']);

                    if ($salaryGroup) {
                        $salaryComponents = $salaryGroup->salaryComponents->toArray();
                    }
                }


                return view($this->view . 'add_salary', compact('employee', 'percentType', 'fixedType', 'employeeSalaryGroup', 'salaryComponents'));
            } catch (Exception $exception) {
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * @param Request $request
     * @param $employeeId
     * @return RedirectResponse
     */
    public function saveSalary(Request $request, $employeeId): RedirectResponse
    {
        if (\Auth::user()->can('create-employee_salary')) {
            try {
                $validatedData = $request->all();

                $employeeSalaryGroup = $this->salaryGroupEmployeeRepository->getSalaryGroupFromEmployeeId($employeeId);

                if ($employeeSalaryGroup) {
                    $validatedData['salary_group_id'] = $employeeSalaryGroup->salary_group_id;
                }

                $validatedData['weekly_basic_salary'] = round(($validatedData['annual_basic_salary'] / 52), 2);
                $validatedData['weekly_fixed_allowance'] = round(($validatedData['annual_fixed_allowance'] / 52), 2);

                DB::beginTransaction();
                $this->employeeSalaryRepository->store($validatedData);
                DB::commit();

                return redirect()->route('admin.employee-salaries.index')->with('success', 'Employee Salary added successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    /**
     * @return Factory|View|RedirectResponse|Application
     */
    public function editSalary($employeeId): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('edit-employee_salary')) {
            try {
                $salaryComponents = [];
                $employee = $this->userRepository->findUserDetailById($employeeId, ['id', 'name']);
                $percentType = EmployeeBasicSalaryTypeEnum::percent->value;
                $fixedType = EmployeeBasicSalaryTypeEnum::fixed->value;
                $employeeSalary = $this->employeeSalaryRepository->getEmployeeSalaryByEmployeeId($employeeId);


                $employeeSalaryGroup = $this->salaryGroupEmployeeRepository->getSalaryGroupFromEmployeeId($employeeId);

                if ($employeeSalaryGroup) {
                    $salaryGroup = $this->salaryGroupService->findOrFailSalaryGroupDetailById($employeeSalaryGroup->salary_group_id, ['*'], ['salaryComponents']);

                    if ($salaryGroup) {
                        $salaryComponents = $salaryGroup->salaryComponents->toArray();
                    }
                }

                return view($this->view . 'edit_salary', compact('employee', 'employeeSalary', 'percentType', 'fixedType', 'salaryComponents'));
            } catch (Exception $exception) {
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * @param Request $request
     * @param $employeeId
     * @return RedirectResponse
     */
    public function updateSalary(Request $request, $employeeId): RedirectResponse
    {
        if (\Auth::user()->can('edit-employee_salary')) {
            try {
                $validatedData = $request->all();

                $employeeSalary = $this->employeeSalaryRepository->getEmployeeSalaryByEmployeeId($employeeId);

                $validatedData['weekly_basic_salary'] = round(($validatedData['annual_basic_salary'] / 52), 2);
                $validatedData['weekly_fixed_allowance'] = round(($validatedData['annual_fixed_allowance'] / 52), 2);
                DB::beginTransaction();
                $this->employeeSalaryRepository->update($employeeSalary, $validatedData);
                DB::commit();

                return redirect()->route('admin.employee-salaries.index')->with('success', 'Employee Salary Updated successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function printPayslip($employeePayslipId): Factory|View|RedirectResponse|Application
    {
        try {
            $companyLogoPath = Company::UPLOAD_PATH;
            $payrollData = $this->generatePayrollService->getEmployeeAccountDetailToCreatePayslip($employeePayslipId);
            $currency = AppHelper::getCompanyPaymentCurrencySymbol();
            $underTimeSetting = $this->utSettingService->getAllUTList(['is_active'], 1);
            $numberToWords = new NumberToWords();

            return view($this->view . 'print_payslip', compact('payrollData', 'companyLogoPath', 'currency', 'underTimeSetting', 'numberToWords'));
        } catch (Exception $exception) {
            return redirect()->back()
                ->with('danger', $exception->getMessage());
        }
    }

    public function makePayment($payslipId, Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'paid_on' => ['required'],
                    'payment_method_id' => ['required'],
                ],
                [
                    'paid_on.required' => 'Please select the paid on date while making payment.',
                    'payment_method_id.required' => 'Please select a payment method while making payment.',
                ]
            );
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()]);
            }

            $validatedData = $validator->validated();
            $employeePayslipData = [
                "paid_on" => $validatedData['paid_on'],
                "status" => PayslipStatusEnum::paid->value,
                "payment_method_id" => $validatedData['payment_method_id'],
            ];

            $employeePaySlipDetail = $this->payslipRepository->find($payslipId);

            DB::beginTransaction();

            $updateData = [
                'status' => 'accepted',
                'is_settled' => 1,
                'remark' => 'included in salary.',
                'verified_by' => auth()->user()?->id,
            ];
            $this->tadaService->makeSettlement($updateData, $employeePaySlipDetail->employee_id);

            // make advance salary settlement
            $advanceData = [
                'is_settled' => true,
                'remark' => 'settled in payroll'
            ];
            $this->advanceSalaryService->advanceSalarySettlement($employeePaySlipDetail->employee_id, $advanceData);


            $this->payslipRepository->update($employeePaySlipDetail, $employeePayslipData);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $employeeId
     * @return RedirectResponse
     */
    public function deleteSalary($employeeId): RedirectResponse
    {
        if (\Auth::user()->can('delete-employee_salary')) {
            try {
                $employeeSalary = $this->employeeSalaryRepository->getEmployeeSalaryByEmployeeId($employeeId);

                DB::beginTransaction();
                $this->employeeSalaryRepository->delete($employeeSalary);
                DB::commit();

                return redirect()->route('admin.employee-salaries.index')->with('success', 'Employee Salary deleted successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function getWeeks($year)
    {
        try {
            $weeks = AppHelper::getweeksList($year);

            return response()->json(['success' => true, 'data' => $weeks]);
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('danger', $exception->getMessage());
        }
    }
}
