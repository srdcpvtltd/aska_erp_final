<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Repositories\BranchRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\UserRepository;
use App\Requests\Department\DepartmentStoreRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    private $view = 'admin.department.';

    private DepartmentRepository $departmentRepo;
    private CompanyRepository $companyRepo;
    private UserRepository $userRepo;
    private BranchRepository $branchRepo;

    public function __construct(
        DepartmentRepository $departmentRepo,
        CompanyRepository    $companyRepo,
        UserRepository $userRepo,
        BranchRepository $branchRepo
    ) {
        $this->departmentRepo = $departmentRepo;
        $this->companyRepo = $companyRepo;
        $this->userRepo = $userRepo;
        $this->branchRepo = $branchRepo;
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-department')) {
            try {
                $filterParameters = [
                    'company_id' => AppHelper::getAuthUserCompanyId(),
                    'name' =>  $request->name ?? null,
                    'branch' =>  $request->branch ?? null,
                    'per_page' => $request->per_page ?? Department::RECORDS_PER_PAGE,
                ];
                $selectBranch = ['id', 'name'];
                $with = ['branch:id,name', 'departmentHead:id,name'];
                $branch = $this->branchRepo->getLoggedInUserCompanyBranches($filterParameters['company_id'], $selectBranch);
                $departments = $this->departmentRepo->getAllPaginatedDepartments($filterParameters, $with);
                return view($this->view . 'index', compact('departments', 'filterParameters', 'branch'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-department')) {
            try {
                $select = ['name', 'id'];
                $users = $this->userRepo->getAllUsers($select);
                $with = ['branches:company_id,id,name'];
                $select = ['id', 'name'];
                $companyDetail = $this->companyRepo->getCompanyDetail($select, $with);
                return view(
                    $this->view . 'create',
                    compact('companyDetail', 'users')
                );
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function getAllDepartmentsByBranchId($branchId): JsonResponse|RedirectResponse
    {
        if (\Auth::user()->can('create-department')) {
            try {

                $with = [];
                $select = ['dept_name', 'id'];
                $departments = $this->departmentRepo->getAllActiveDepartmentsByBranchId($branchId, $with, $select);
                return response()->json([
                    'data' => $departments
                ]);
            } catch (Exception $exception) {
                return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(DepartmentStoreRequest $request)
    {
        if (\Auth::user()->can('create-department')) {
            try {
                $validatedData = $request->validated();
                DB::beginTransaction();
                $this->departmentRepo->store($validatedData);
                DB::commit();
                return redirect()
                    ->route('admin.departments.index')
                    ->with('success', 'New Department Added Successfully');
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()
                    ->route('admin.departments.index')
                    ->with('danger', $e->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-department')) {
            try {
                $departmentsDetail = $this->departmentRepo->findDepartmentById($id);
                $users = User::select('name', 'id')->get();
                $with = ['branches:company_id,id,name'];
                $select = ['id', 'name'];
                $companyDetail = $this->companyRepo->getCompanyDetail($select, $with);
                return view(
                    $this->view . 'edit',
                    compact('companyDetail', 'users', 'departmentsDetail')
                );
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(DepartmentStoreRequest $request, $id)
    {
        if (\Auth::user()->can('edit-department')) {
            try {
                $validatedData = $request->validated();
                $departmentDetail = $this->departmentRepo->findDepartmentById($id);
                if (!$departmentDetail) {
                    throw new Exception('Department Detail Not Found', 404);
                }
                DB::beginTransaction();
                $department = $this->departmentRepo->update($departmentDetail, $validatedData);
                DB::commit();
                return redirect()
                    ->route('admin.departments.index')
                    ->with('success', 'Department Detail Updated Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage())->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleStatus($id)
    {
        if (\Auth::user()->can('edit-department')) {
            try {
                DB::beginTransaction();
                $this->departmentRepo->toggleStatus($id);
                DB::commit();
                return redirect()->back()->with('success', 'Status changed  Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function delete($id)
    {
        if (\Auth::user()->can('delete-department')) {
            try {
                $select = ['*'];
                $with = ['posts'];
                $departmentDetail = $this->departmentRepo->findDepartmentById($id, $select, $with);
                if (!$departmentDetail) {
                    throw new Exception('Department Record Not Found', 404);
                }
                if (count($departmentDetail->posts) > 0) {
                    throw new Exception('Cannot Delete Department With Posts', 403);
                }

                DB::beginTransaction();
                $this->departmentRepo->delete($departmentDetail);
                DB::commit();
                return redirect()->back()->with('success', 'Department Record Deleted  Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
