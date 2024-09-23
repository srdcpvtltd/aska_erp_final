<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Requests\AssetManagement\AssetDetailRequest;
use App\Services\AssetManagement\AssetService;
use App\Services\AssetManagement\AssetTypeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    private $view = 'admin.assetManagement.assetDetail.';

    public function __construct(
        private AssetService $assetService,
        private AssetTypeService $assetTypeService,
        private UserRepository $userRepo
    ) {}

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-assets')) {
            try {
                $filterParameters = [
                    'name' => $request->name ?? null,
                    'purchased_from' => $request->purchased_from ?? null,
                    'purchased_to' => $request->purchased_to ?? null,
                    'is_working' => $request->is_working ?? null,
                    'is_available' => $request->is_available ?? null,
                    'type' => $request->type ?? null,
                ];
                $select = ['*'];
                $with = ['type:id,name', 'assignedTo:id,name'];
                $assetType = $this->assetTypeService->getAllAssetTypes(['id', 'name']);
                $assetLists = $this->assetService->getAllAssetsPaginated($filterParameters, $select, $with);
                return view($this->view . 'index', compact('assetLists', 'assetType', 'filterParameters'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-assets')) {
            try {
                $employeeSelect = ['id', 'name'];
                $typeSelect = ['id', 'name'];
                $assetType = $this->assetTypeService->getAllActiveAssetTypes($typeSelect);
                $employees = $this->userRepo->getAllVerifiedEmployeeOfCompany($employeeSelect);
                return view($this->view . 'create', compact('assetType', 'employees'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(AssetDetailRequest $request)
    {
        if (\Auth::user()->can('create-assets')) {
            try {
                $validatedData = $request->validated();
                $this->assetService->saveAssetDetail($validatedData);
                return redirect()->route('admin.assets.index')->with('success', 'Asset record saved successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show($id)
    {
        if (\Auth::user()->can('show-assets')) {
            try {
                $select = ['*'];
                $with = ['type:id,name', 'assignedTo:id,name'];
                $assetDetail = $this->assetService->findAssetById($id, $select, $with,);
                return view($this->view . 'show', compact('assetDetail'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-assets')) {
            try {
                $employeeSelect = ['id', 'name'];
                $typeSelect = ['id', 'name'];
                $assetType = $this->assetTypeService->getAllActiveAssetTypes($typeSelect);
                $employees = $this->userRepo->getAllVerifiedEmployeeOfCompany($employeeSelect);
                $assetDetail = $this->assetService->findAssetById($id);
                return view($this->view . 'edit', compact('assetDetail', 'assetType', 'employees'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(AssetDetailRequest $request, $id)
    {
        if (\Auth::user()->can('edit-assets')) {
            try {
                $validatedData = $request->validated();
                $this->assetService->updateAssetDetail($id, $validatedData);
                return redirect()->route('admin.assets.index')
                    ->with('success', 'Asset Detail Updated Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function delete($id)
    {
        if (\Auth::user()->can('delete-assets')) {
            try {
                DB::beginTransaction();
                $this->assetService->deleteAsset($id);
                DB::commit();
                return redirect()->back()->with('success', 'Asset Detail Deleted Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function changeAvailabilityStatus($id)
    {
        if (\Auth::user()->can('edit-assets')) {
            try {
                $this->assetService->toggleAvailabilityStatus($id);
                return redirect()->back()->with('success', 'Status Changed  Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
