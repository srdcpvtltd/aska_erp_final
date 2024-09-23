<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AssetType;
use App\Requests\AssetManagement\AssetTypeRequest;
use App\Services\AssetManagement\AssetTypeService;
use Illuminate\Support\Facades\DB;

class AssetTypeController extends Controller
{
    private $view = 'admin.assetManagement.types.';

    public function __construct(
        private AssetTypeService $assetTypeService
    ) {}

    public function index()
    {
        if (\Auth::user()->can('manage-asset_types')) {
            try {
                $select = ['*'];
                $with = ['assets'];
                $assetTypeLists = $this->assetTypeService->getAllAssetTypes($select, $with);
                return view($this->view . 'index', compact('assetTypeLists'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create-asset_types')) {
            try {
                return view($this->view . 'create');
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function store(AssetTypeRequest $request)
    {
        if (\Auth::user()->can('create-asset_types')) {
            try {
                $validatedData = $request->validated();
                $this->assetTypeService->store($validatedData);
                return redirect()->route('admin.asset-types.index')->with('success', 'Asset Type Created Successfully');
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show($id)
    {
        if (\Auth::user()->can('show-asset_types')) {
            try {
                $select = ['*'];
                $with = ['assets:id,type_id,name,purchased_date,is_working,is_available'];
                $assetTypeDetail = $this->assetTypeService->findAssetTypeById($id, $select, $with);
                return view($this->view . 'show', compact('assetTypeDetail'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function edit($id)
    {
        if (\Auth::user()->can('edit-asset_types')) {
            try {
                $assetTypeDetail = $this->assetTypeService->findAssetTypeById($id);
                return view($this->view . 'edit', compact('assetTypeDetail'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function update(AssetTypeRequest $request, $id)
    {
        if (\Auth::user()->can('edit-asset_types')) {
            try {
                $validatedData = $request->validated();
                $this->assetTypeService->updateAssetType($id, $validatedData);
                return redirect()->route('admin.asset-types.index')
                    ->with('success', 'Asset Type Detail Updated Successfully');
            } catch (\Exception $exception) {
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
        if (\Auth::user()->can('delete-asset_types')) {
            try {
                DB::beginTransaction();
                $this->assetTypeService->deleteAssetType($id);
                DB::commit();
                return redirect()->back()->with('success', 'Asset Type Deleted Successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleIsActiveStatus($id)
    {
        if (\Auth::user()->can('edit-asset_types')) {
            try {
                $this->assetTypeService->toggleIsActiveStatus($id);
                return redirect()->back()->with('success', 'Status changed Successfully');
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
