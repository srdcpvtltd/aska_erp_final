<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Requests\Payroll\SalaryComponent\SalaryComponentRequest;
use App\Services\Payroll\SalaryComponentService;
use Exception;

class SalaryComponentController extends Controller
{
    private $view = 'admin.payrollSetting.salaryComponent.';

    public function __construct(public SalaryComponentService $salaryComponentService) {}

    public function index()
    {
        if (\Auth::user()->can('manage-payroll_setting')) {
            try {
                $select = ['*'];
                $salaryComponentLists = $this->salaryComponentService->getAllSalaryComponentList($select);
                return view($this->view . 'index', compact('salaryComponentLists'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-payroll_setting')) {
            try {
                return view($this->view . 'create');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(SalaryComponentRequest $request)
    {
        if (\Auth::user()->can('create-payroll_setting')) {
            try {
                $validatedData = $request->validated();
                $this->salaryComponentService->store($validatedData);
                return redirect()
                    ->route('admin.salary-components.index')
                    ->with('success', 'Salary Component Added Successfully');
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
        if (\Auth::user()->can('edit-payroll_setting')) {
            try {
                $select = ['*'];
                $salaryComponentDetail = $this->salaryComponentService->findSalaryComponentById($id, $select);
                return view($this->view . 'edit', compact('salaryComponentDetail'));
            } catch (Exception $exception) {
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(SalaryComponentRequest $request, $id)
    {
        if (\Auth::user()->can('edit-payroll_setting')) {
            try {
                $select = ['*'];
                $salaryComponentDetail = $this->salaryComponentService->findSalaryComponentById($id, $select);
                $validatedData = $request->validated();
                $this->salaryComponentService->updateDetail($salaryComponentDetail, $validatedData);
                return redirect()
                    ->route('admin.salary-components.index')
                    ->with('success', 'Salary Component Detail Updated Successfully');
            } catch (Exception $e) {
                return redirect()->back()
                    ->with('danger', $e->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function delete($id)
    {
        if (\Auth::user()->can('delete-payroll_setting')) {
            try {
                $select = ['*'];
                $salaryComponentDetail = $this->salaryComponentService->findSalaryComponentById($id, $select);
                $this->salaryComponentService->deleteSalaryComponentDetail($salaryComponentDetail);
                return redirect()
                    ->back()
                    ->with('success', 'Salary Component Detail Deleted Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleSalaryComponentStatus($id)
    {
        try {
            $select = ['*'];
            $salaryComponentDetail = $this->salaryComponentService->findSalaryComponentById($id, $select);
            $this->salaryComponentService->changeSalaryComponentStatus($salaryComponentDetail);
            return redirect()
                ->back()
                ->with('success', 'Status changed Successfully');
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }
}
