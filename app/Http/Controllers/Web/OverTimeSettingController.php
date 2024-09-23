<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\OverTimeSetting;
use App\Repositories\OverTimeSettingRepository;
use App\Repositories\UserRepository;
use App\Requests\Payroll\OverTime\OverTimeRequest;
use App\Services\Payroll\OverTimeSettingService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OverTimeSettingController extends Controller
{
    private $view = 'admin.payrollSetting.overtime.';

    public function __construct(protected OverTimeSettingService $otService, protected UserRepository $userRepository) {}


    public function index(): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('manage-overtime')) {
            try {
                $select = ['*'];
                $overTimeData = $this->otService->getAllOTList($select);

                $currency = AppHelper::getCompanyPaymentCurrencySymbol();
                return view($this->view . 'index', compact('overTimeData', 'currency'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(): View|Factory|RedirectResponse|Application
    {
        if (\Auth::user()->can('create-overtime')) {
            try {
                $employees = $this->userRepository->pluckIdAndNameOfAllVerifiedEmployee();
                return view($this->view . 'create', compact('employees'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    /**
     * @param OverTimeRequest $request
     * @return View|Factory|RedirectResponse|Application
     */
    public function store(OverTimeRequest $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        if (\Auth::user()->can('create-overtime')) {
            try {
                $validatedData = $request->all();
                $this->otService->store($validatedData);

                return redirect()->route('admin.overtime.index')->with('success', 'OverTime Added Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id): View|Factory|RedirectResponse|Application
    {
        if (\Auth::user()->can('edit-overtime')) {
            try {
                $with = ['otEmployees:over_time_setting_id,employee_id'];
                $overtime = $this->otService->findOTById($id, $with);
                $employees = $this->userRepository->pluckIdAndNameOfAllVerifiedEmployee();

                $overTimeEmployeeId = $overtime?->otEmployees?->pluck('employee_id')->toArray() ?? [];

                return view($this->view . 'edit', compact('overtime', 'employees', 'overTimeEmployeeId'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function update(OverTimeRequest $request, $id): RedirectResponse
    {
        if (\Auth::user()->can('edit-overtime')) {
            try {
                $validatedData = $request->all();

                $this->otService->updateOverTime($id, $validatedData);

                return redirect()->route('admin.overtime.index')->with('success', 'OverTime updated Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function delete($id): RedirectResponse
    {
        if (\Auth::user()->can('delete-overtime')) {
            try {
                DB::beginTransaction();
                $this->otService->deleteOTSetting($id);
                DB::commit();
                return redirect()->back()->with('success', 'OverTime Deleted Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleOTStatus($id): RedirectResponse
    {
        if (\Auth::user()->can('edit-overtime')) {
            try {
                $this->otService->changeOTStatus($id);
                return redirect()
                    ->back()
                    ->with('success', 'Status changed Successfully');
            } catch (Exception $exception) {
                return redirect()
                    ->back()
                    ->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
