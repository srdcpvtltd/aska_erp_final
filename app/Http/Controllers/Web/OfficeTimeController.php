<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\OfficeTime;
use App\Repositories\CompanyRepository;
use App\Repositories\OfficeTimeRepository;
use App\Repositories\UserRepository;
use App\Requests\OfficeTime\OfficeTimeRequest;
use App\Requests\OfficeTime\OverTimeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfficeTimeController extends Controller
{
    private $view = 'admin.officeTime.';

    public function __construct(protected OfficeTimeRepository $officeTimeRepo, protected CompanyRepository $companyRepo, protected UserRepository $userRepository) {}

    public function index()
    {
        if (\Auth::user()->can('manage-office_time')) {
            try {
                $with = [];
                $officeTimes = $this->officeTimeRepo->getAllCompanyOfficeTime($with);
                return view($this->view . 'index', compact('officeTimes'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-office_time')) {
            try {
                $select = ['id', 'name'];

                $shift = OfficeTime::SHIFT;
                $category = OfficeTime::CATEGORY;
                return view(
                    $this->view . 'create',
                    compact('shift', 'category')
                );
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(OfficeTimeRequest $request)
    {
        if (\Auth::user()->can('create-office_time')) {
            try {
                $validatedData = $request->validated();

                $validatedData['company_id'] = AppHelper::getAuthUserCompanyId();
                DB::beginTransaction();
                $this->officeTimeRepo->store($validatedData);
                DB::commit();
                return redirect()->route('admin.office-times.index')
                    ->with('success', 'New Office Schedule Added Successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()
                    ->with('danger', $e->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show($id)
    {
        if (\Auth::user()->can('show-office_time')) {
            try {
                $select = ['opening_time', 'closing_time', 'shift', 'checkin_before', 'checkout_before', 'checkin_after', 'checkout_after'];
                $officeTimes = $this->officeTimeRepo->findCompanyOfficeTimeById($id, $select);
                return response()->json([
                    'data' => $officeTimes,
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function edit($id)
    {
        if (\Auth::user()->can('edit-office_time')) {
            try {
                $officeTime = $this->officeTimeRepo->findCompanyOfficeTimeById($id);
                $select = ['id', 'name'];
                $companyDetail = $this->companyRepo->getCompanyDetail($select);
                $shift = OfficeTime::SHIFT;
                $category = OfficeTime::CATEGORY;
                return view($this->view . 'edit', compact('officeTime', 'companyDetail', 'shift', 'category'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function update(OfficeTimeRequest $request, $id)
    {
        if (\Auth::user()->can('edit-office_time')) {
            try {
                $validatedData = $request->validated();
                $officeTime = $this->officeTimeRepo->findCompanyOfficeTimeById($id);
                if (!$officeTime) {
                    throw new \Exception('Office Time Detail Not Found', 404);
                }

                $validatedData['is_early_check_in'] = $validatedData['is_early_check_in'] ?? 0;
                $validatedData['is_early_check_out'] = $validatedData['is_early_check_out'] ?? 0;
                $validatedData['is_late_check_in'] = $validatedData['is_late_check_in'] ?? 0;
                $validatedData['is_late_check_out'] = $validatedData['is_late_check_out'] ?? 0;

                DB::beginTransaction();
                $this->officeTimeRepo->update($officeTime, $validatedData);
                DB::commit();
                return redirect()->route('admin.office-times.index')
                    ->with('success', 'Office Time Detail Updated Successfully');
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleStatus($id)
    {
        if (\Auth::user()->can('edit-office_time')) {
            try {
                $checkUserOfficeTime = $this->userRepository->checkOfficeTime($id);
                if ($checkUserOfficeTime > 0) {
                    return redirect()->back()->with('danger', 'Office time status cannot be changed. It is in use.');
                }
                DB::beginTransaction();
                $this->officeTimeRepo->toggleStatus($id);
                DB::commit();
                return redirect()->back()->with('success', 'Status changed  Successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function delete($id)
    {
        if (\Auth::user()->can('delete-office_time')) {
            try {
                $officeTime = $this->officeTimeRepo->findCompanyOfficeTimeById($id);
                $checkUserOfficeTime = $this->userRepository->checkOfficeTime($id);
                if ($checkUserOfficeTime > 0) {
                    return redirect()->back()->with('danger', 'Office time cannot be deleted. It is in use.');
                }
                if (!$officeTime) {
                    throw new \Exception('Company Office Time Detail Not Found', 404);
                }
                DB::beginTransaction();
                $this->officeTimeRepo->delete($officeTime);
                DB::commit();
                return redirect()->back()->with('success', 'Office schedule Deleted  Successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
