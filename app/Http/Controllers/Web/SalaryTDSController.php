<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Helpers\PayrollHelper;
use App\Http\Controllers\Controller;
use App\Requests\Payroll\SalaryTDS\SalaryTDSUpdateRequest;
use App\Requests\Payroll\SalaryTDS\SalaryTDSStoreRequest;
use App\Services\Payroll\SalaryTDSService;
use Exception;

class SalaryTDSController extends Controller
{
    private $view = 'admin.payrollSetting.salaryTDS.';

    public function __construct(public SalaryTDSService $salaryTDSService) {}

    public function index()
    {
        if (\Auth::user()->can('manage-salary_tds')) {
            try {
                $select = ['*'];
                $salaryTDSList = $this->salaryTDSService->getAllSalaryTDSListGroupByMaritalStatus($select);
                $singleSalaryTDS = $salaryTDSList->get('single', collect());
                $marriedSalaryTDS = $salaryTDSList->get('married', collect());

                return view($this->view . 'index', compact('singleSalaryTDS', 'marriedSalaryTDS'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create-salary_tds')) {
            try {
                return view($this->view . 'create');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function store(SalaryTDSStoreRequest $request)
    {
        if (\Auth::user()->can('create-salary_tds')) {
            try {
                $validatedData = $request->validated();
                $this->salaryTDSService->store($validatedData);
                return AppHelper::sendSuccessResponse('Salary TDS Detail Added Successfully');
            } catch (Exception $e) {
                return AppHelper::sendErrorResponse($e->getMessage(), $e->getCode());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function edit($id)
    {
        if (\Auth::user()->can('edit-salary_tds')) {
            try {
                $salaryTDSDetail = $this->salaryTDSService->findSalaryTDSById($id);
                return view($this->view . 'edit', compact('salaryTDSDetail'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function update(SalaryTDSUpdateRequest $request, $id)
    {
        if (\Auth::user()->can('edit-salary_tds')) {
            try {
                $validatedData = $request->validated();
                $salaryTDSDetail = $this->salaryTDSService->findSalaryTDSById($id);
                $this->salaryTDSService->updateDetail($salaryTDSDetail, $validatedData);
                return redirect()
                    ->route('admin.salary-tds.index')
                    ->with('success', 'TDS Detail Updated Successfully');
            } catch (Exception $exception) {
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function deleteSalaryTDS($id)
    {
        if (\Auth::user()->can('delete-salary_tds')) {
            try {
                $select = ['*'];
                $salaryTDSDetail = $this->salaryTDSService->findSalaryTDSById($id, $select);
                $this->salaryTDSService->deleteSalaryTDSDetail($salaryTDSDetail);
                return redirect()
                    ->back()
                    ->with('success', 'Salary TDS Detail Deleted Successfully');
            } catch (Exception $exception) {
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleSalaryTDSStatus($id)
    {
        if (\Auth::user()->can('edit-salary_tds')) {
            try {
                $select = ['*'];
                $salaryTDSDetail = $this->salaryTDSService->findSalaryTDSById($id, $select);
                $this->salaryTDSService->changeSalaryTDSStatus($salaryTDSDetail);
                return redirect()
                    ->back()
                    ->with('success', 'Status changed Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
