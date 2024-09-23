<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\Tada;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Requests\Tada\TadaRequest;
use App\Requests\Tada\TadaUpdateStatusRequest;
use App\Services\Tada\TadaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TadaController extends Controller
{
    private $view = 'admin.tada.';

    public TadaService $tadaService;
    public UserRepository $userRepo;

    public function __construct(TadaService $tadaService, UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
        $this->tadaService = $tadaService;
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-tada')) {
            try {
                $filterParameters = [
                    'employee' => $request->employee ?? null,
                    'status' => $request->status ?? null
                ];
                $select = ['*'];
                $with = ['employeeDetail:id,name'];
                $tadaLists = $this->tadaService->getAllTadaDetailPaginated($filterParameters, $select, $with);
                return view($this->view . 'index', compact('tadaLists', 'filterParameters'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-tada')) {
            try {
                $select = ['name', 'id'];
                $employee = $this->userRepo->getAllVerifiedEmployeeOfCompany($select);
                $attachments = [];
                return view($this->view . 'create', compact('employee', 'attachments'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(TadaRequest $request)
    {
        if (\Auth::user()->can('create-tada')) {
            try {
                $validatedData = $request->validated();
                DB::beginTransaction();
                $this->tadaService->store($validatedData);
                DB::commit();
                return redirect()
                    ->route('admin.tadas.index')
                    ->with('success', 'Tada Detail Added Successfully');
            } catch (Exception $e) {
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
        if (\Auth::user()->can('show-tada')) {
            try {
                $select = ['*'];
                $with = ['employeeDetail:id,name', 'attachments', 'verifiedBy:id,name'];
                $tadaDetail = $this->tadaService->findTadaDetailById($id, $with, $select);
                $attachments = $tadaDetail->attachments;
                return view($this->view . 'show', compact('tadaDetail', 'attachments'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-tada')) {
            try {
                $select = ['*'];
                $with = ['employeeDetail:id,name', 'attachments'];
                $tadaDetail = $this->tadaService->findTadaDetailById($id, $with, $select);
                $employeeSelect = ['name', 'id'];
                $employee = $this->userRepo->getAllVerifiedEmployeeOfCompany($employeeSelect);
                $attachments = $tadaDetail->attachments;
                return view($this->view . 'edit', compact('tadaDetail', 'employee', 'attachments'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(TadaRequest $request, $id)
    {
        if (\Auth::user()->can('edit-tada')) {
            try {
                $validatedData = $request->validated();
                $with = ['attachments'];
                $tadaDetail = $this->tadaService->findTadaDetailById($id, $with);
                DB::beginTransaction();
                $this->tadaService->update($tadaDetail, $validatedData);
                DB::commit();
                return redirect()->route('admin.tadas.index')
                    ->with('success', 'Tada Detail Added Successfully');
            } catch (Exception $e) {
                DB::rollBack();
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
        if (\Auth::user()->can('delete-tada')) {
            try {
                DB::beginTransaction();
                $this->tadaService->delete($id);
                DB::commit();
                return redirect()->back()->with('success', 'Tada Detail Deleted Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleTadaIsActive($id)
    {
        if (\Auth::user()->can('edit-tada')) {
            try {
                DB::beginTransaction();
                $this->tadaService->toggleStatus($id);
                DB::commit();
                return redirect()->back()->with('success', 'Tada Settlement Status changed Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function changeTadaStatus(TadaUpdateStatusRequest $request, $id)
    {
        if (\Auth::user()->can('edit-tada')) {
            try {
                $validatedData = $request->validated();
                $tadaDetail = $this->tadaService->findTadaDetailById($id);
                $this->tadaService->changeTadaStatus($tadaDetail, $validatedData);
                return redirect()->back()->with('success', 'Tada Detail Status Changed Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
