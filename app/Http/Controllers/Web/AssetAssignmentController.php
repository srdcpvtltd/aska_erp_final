<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Repositories\AssetAssignmentRepository;
use App\Repositories\UserRepository;
use App\Services\AssetManagement\AssetService;
use App\Services\AssetManagement\AssetTypeService;
use Exception;
use Illuminate\Http\Request;

class AssetAssignmentController extends Controller
{
    private $view = 'admin.assetManagement.assetAssignment.';

    public function __construct(
        private AssetTypeService $assetTypeService,
        private AssetService $assetService,
        private UserRepository $userRepo,
        private AssetAssignmentRepository $assetAsignmentRepo
    ) {}

    public function index()
    {
        if (\Auth::user()->can('manage-asset_management')) {
            try {
                $select = ['*'];
                $with = ['assets', 'users'];
                $assetLists = $this->assetAsignmentRepo->getAllAssetAssignments($select, $with);
                // dd($assetLists);
                return view($this->view . 'index', compact('assetLists'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-asset_management')) {
            try {
                $employeeSelect = ['id', 'name'];
                $typeSelect = ['id', 'name'];

                $assets =  Asset::all(['id', 'name'])->toArray();
                $assetType = $this->assetTypeService->getAllActiveAssetTypes($typeSelect);
                $employees = $this->userRepo->getAllVerifiedEmployeeOfCompany($employeeSelect);
                return view($this->view . 'create', compact('assets', 'assetType', 'employees'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function getAllAssetsByAssetTypeId($id)
    {

        $assets =  Asset::where('type_id', $id)->get(['id', 'name']);

        if ($assets) {
            return response()->json([
                'data' => $assets
            ]);
        } else {
            return response()->json([
                'data' => null
            ]);
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-asset_management')) {
            try {
                AssetAssignment::create([
                    'asset_type_id' => $request->type_id,
                    'asset_id' => $request->asset,
                    'user_id' => $request->assigned_to,
                    'assign_date' => $request->assign_date
                ]);
                return redirect()->route('admin.asset_assignment.index')->with('success', 'Asset Assigned Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
