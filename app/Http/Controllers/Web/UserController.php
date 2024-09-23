<?php

namespace App\Http\Controllers\Web;

use Exception;
use App\Models\User;
use App\Helpers\AppHelper;
use App\Imports\ImportUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\EmployeeFormExport;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Http\Controllers\Controller;
use Laravel\Passport\TokenRepository;
use App\Repositories\BranchRepository;
use Illuminate\Contracts\View\Factory;
use App\Repositories\CompanyRepository;
use App\Requests\User\UserCreateRequest;
use App\Requests\User\UserUpdateRequest;
use App\Requests\User\UserAccountRequest;
use App\Repositories\LeaveTypeRepository;
use App\Repositories\OfficeTimeRepository;
use App\Requests\User\UserLeaveTypeRequest;
use App\Repositories\UserAccountRepository;
use App\Requests\User\ChangePasswordRequest;
use Laravel\Passport\RefreshTokenRepository;
use Illuminate\Contracts\Foundation\Application;
use App\Repositories\EmployeeLeaveTypeRepository;

class UserController extends Controller
{
    private $view = 'admin.users.';

    public function __construct(
        protected UserRepository $userRepo,
        protected CompanyRepository $companyRepo,
        protected RoleRepository $roleRepo,
        protected OfficeTimeRepository $officeTimeRepo,
        protected UserAccountRepository $accountRepo,
        protected CompanyRepository $companyRepository,
        protected BranchRepository $branchRepository,
        protected LeaveTypeRepository $leaveTypeRepository,
        protected EmployeeLeaveTypeRepository $employeeLeaveTypeRepository,

    ) {}

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-employees')) {
            try {
                $filterParameters = [
                    'employee_name' => $request->employee_name ?? null,
                    'email' => $request->email ?? null,
                    'phone' => $request->phone ?? null,
                    'branch_id' => $request->branch_id ?? null,
                    'department_id' => $request->department_id ?? null,
                ];
                $with = ['branch:id,name', 'company:id,name', 'post:id,post_name', 'department:id,dept_name', 'role:id,name'];
                $select = ['users.*', 'branch_id', 'company_id', 'department_id', 'post_id', 'role_id'];
                $users = $this->userRepo->getAllUsers($filterParameters, $select, $with);
                $company = $this->companyRepository->getCompanyDetail(['id']);
                $branches = $this->branchRepository->getLoggedInUserCompanyBranches($company->id, ['id', 'name']);

                return view($this->view . 'index', compact('users', 'filterParameters', 'branches'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function employeeImport(): Factory|View|Application
    {
        return view($this->view . 'imports.importUser');
    }

    //import user excel
    public function importEmployee(Request $request)
    {
        try {
            $file = $request->file('file');
            Excel::import(new ImportUser, $file);
            return redirect()->route('admin.users.index')->with('success', 'Employee Imported Successfully');
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function exportForm()
    {
        return Excel::download(new EmployeeFormExport(), 'crate-form'  .  '-report.xlsx');
    }

    public function create()
    {
        if (\Auth::user()->can('create-employees')) {
            try {
                $with = ['branches:id,name'];
                $select = ['id', 'name'];
                $companyDetail = $this->companyRepo->getCompanyDetail($select, $with);
                $roles = $this->roleRepo->getAllActiveRoles();

                $employeeCode = AppHelper::getEmployeeCode();

                $leaveTypes = $this->leaveTypeRepository->getPaidLeaveTypes();

                return view($this->view . 'create', compact('companyDetail', 'roles', 'leaveTypes', 'employeeCode'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(UserCreateRequest $request, UserAccountRequest $accountRequest, UserLeaveTypeRequest $leaveRequest)
    {
        if (\Auth::user()->can('create-employees')) {
            try {
                $validatedData = $request->validated();

                $accountValidatedData = $accountRequest->validated();
                $leaveTypeData = $leaveRequest->validated();

                $validatedData['password'] = bcrypt($validatedData['password']);
                $validatedData['is_active'] = 1;
                $validatedData['status'] = 'verified';
                $validatedData['company_id'] = AppHelper::getAuthUserCompanyId();


                DB::beginTransaction();
                $user = $this->userRepo->store($validatedData);
                $user->assignRole($user->role->name);
                $accountValidatedData['user_id'] = $user['id'];
                $this->accountRepo->store($accountValidatedData);

                if (!is_null($user['leave_allocated']) && isset($leaveTypeData['leave_type_id'])) {
                    foreach ($leaveTypeData['leave_type_id'] as $key => $value) {
                        $input['days']       = $leaveTypeData['days'][$key] ?? 0;
                        $input['is_active']  = $leaveTypeData['is_active'][$key] ?? 0;
                        $input['employee_id']  = $user['id'];
                        $input['leave_type_id']  = $value;

                        $this->employeeLeaveTypeRepository->store($input);
                    }
                }

                DB::commit();
                return redirect()
                    ->route('admin.users.index')
                    ->with('success', 'New Employee Detail Added Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage())->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show($id)
    {
        if (\Auth::user()->can('show-employees')) {
            try {
                $with = [
                    'branch:id,name',
                    'company:id,name',
                    'post:id,post_name',
                    'department:id,dept_name',
                    'role:id,name',
                    'accountDetail'
                ];
                $select = ['users.*', 'branch_id', 'company_id', 'department_id', 'post_id', 'role_id'];
                $userDetail = $this->userRepo->findUserDetailById($id, $select, $with);
                return view($this->view . 'show2', compact('userDetail'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getFile());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-employees')) {
            try {
                $with = ['branches:id,name'];
                $select = ['id', 'name'];
                $companyDetail = $this->companyRepo->getCompanyDetail($select, $with);
                $roles = $this->roleRepo->getAllActiveRoles();

                $userSelect = ['*'];
                $userWith = ['accountDetail'];
                $userDetail = $this->userRepo->findUserDetailById($id, $userSelect, $userWith);
                $leaveTypes = $this->leaveTypeRepository->getPaidLeaveTypes();
                $employeeLeaveTypes = $this->employeeLeaveTypeRepository->getAll(['id', 'leave_type_id', 'days', 'is_active'], $id);

                return view($this->view . 'edit', compact('companyDetail', 'roles', 'userDetail', 'leaveTypes', 'employeeLeaveTypes'));
            } catch (Exception $exception) {

                return redirect()->back()->with('danger', $exception->getFile());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(UserUpdateRequest $request, UserAccountRequest $accountRequest, UserLeaveTypeRequest $leaveRequest, $id)
    {
        if (\Auth::user()->can('edit-employees')) {
            try {
                $validatedData = $request->validated();
                $accountValidatedData = $accountRequest->validated();

                $leaveTypeData = $leaveRequest->validated();

                $userDetail = $this->userRepo->findUserDetailById($id);
                if (in_array($userDetail->username, User::DEMO_USERS_USERNAME)) {
                    throw new Exception('This is a demo version. Please buy the application to use the full feature', 400);
                }
                if (!$userDetail) {
                    throw new Exception('User Detail Not Found', 404);
                }
                DB::beginTransaction();
                $this->userRepo->update($userDetail, $validatedData);
                $this->accountRepo->createOrUpdate($userDetail, $accountValidatedData);

                if (!is_null($validatedData['leave_allocated']) && isset($leaveTypeData['leave_type_id'])) {
                    foreach ($leaveTypeData['leave_type_id'] as $key => $value) {
                        $input['days']       = $leaveTypeData['days'][$key];
                        $input['is_active']  = $leaveTypeData['is_active'][$key] ?? 0;

                        $employeeLeaveTypeData = $this->employeeLeaveTypeRepository->findByLeaveType($id, $value);
                        if ($employeeLeaveTypeData) {

                            $this->employeeLeaveTypeRepository->update($employeeLeaveTypeData, $input);
                        } else {
                            $input['employee_id']  = $id;
                            $input['leave_type_id']  = $value;


                            $this->employeeLeaveTypeRepository->store($input);
                        }
                    }
                } else {
                    $this->employeeLeaveTypeRepository->deleteByEmployee($id);
                }

                DB::commit();
                return redirect()
                    ->route('admin.users.index')
                    ->with('success', 'User Detail Updated Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleStatus($id)
    {
        if (\Auth::user()->can('edit-employees')) {
            try {
                DB::beginTransaction();
                $this->userRepo->toggleIsActiveStatus($id);
                DB::commit();
                return redirect()->back()->with('success', 'Users Is Active Status Changed  Successfully');
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
        if (\Auth::user()->can('edit-employees')) {
            try {
                $usersDetail = $this->userRepo->findUserDetailById($id);
                if (in_array($usersDetail->username, User::DEMO_USERS_USERNAME)) {
                    throw new Exception('This is a demo version. Please buy the application to use the full feature', 400);
                }
                if (!$usersDetail) {
                    throw new Exception('Users Detail Not Found', 404);
                }
                if ($usersDetail->id == auth()->user()->id) {
                    throw new Exception('cannot delete own records', 402);
                }
                DB::beginTransaction();
                $this->userRepo->delete($usersDetail);
                DB::commit();
                return redirect()->back()->with('success', 'User Detail Removed Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function changeWorkSpace($id)
    {
        try {
            $select = ['id', 'workspace_type'];
            $userDetail = $this->userRepo->findUserDetailById($id, $select);
            if (!$userDetail) {
                throw new Exception('Users Detail Not Found', 404);
            }
            DB::beginTransaction();
            $this->userRepo->changeWorkSpace($userDetail);
            DB::commit();
            return redirect()->back()->with('success', 'User Workspace Changed Successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function getAllCompanyEmployeeDetail($branchId)
    {
        try {
            $branch = $this->branchRepository->findBranchDetailById($branchId);

            $selectEmployee = ['id', 'name'];
            $selectOfficeTime = ['id', 'opening_time', 'closing_time'];
            $employees = $this->userRepo->getAllVerifiedEmployeeOfCompany($selectEmployee);
            $officeTime = $this->officeTimeRepo->getALlActiveOfficeTimeByCompanyId($branch->company_id, $selectOfficeTime);

            return response()->json([
                'employee' => $employees,
                'officeTime' => $officeTime
            ]);
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function changePassword(ChangePasswordRequest $request, $userId)
    {
        try {
            $validatedData = $request->validated();
            $userDetail = $this->userRepo->findUserDetailById($userId);
            if (in_array($userDetail->username, User::DEMO_USERS_USERNAME)) {
                throw new Exception('This is a demo version. Please buy the application to use the full feature', 400);
            }
            if (!$userDetail) {
                throw new Exception('Users Detail Not Found', 404);
            }
            DB::beginTransaction();
            $this->userRepo->changePassword($userDetail, $validatedData['new_password']);
            DB::commit();
            return redirect()->back()->with('success', 'User Password Changed Successfully');
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function forceLogOutEmployee($employeeId)
    {
        try {
            $tokenRepository = app(TokenRepository::class);
            $refreshTokenRepository = app(RefreshTokenRepository::class);

            $userDetail = $this->userRepo->findUserDetailById($employeeId);
            if (!$userDetail) {
                throw new Exception('User Detail Not found', 404);
            }
            $accessToken = $userDetail->tokens;
            DB::beginTransaction();
            foreach ($accessToken as $token) {
                $tokenRepository->revokeAccessToken($token->id);
                $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
            }
            $validatedData['uuid'] = null;
            $validatedData['logout_status'] = 0;
            $validatedData['remember_token'] = null;
            $validatedData['fcm_token'] = null;
            $this->userRepo->update($userDetail, $validatedData);
            DB::commit();
            return redirect()->back()->with('success', 'Force log out successFull');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function deleteEmployeeLeaveType($id)
    {
        try {
            $employeeLeaveType = $this->employeeLeaveTypeRepository->find($id);

            if (!$employeeLeaveType) {
                throw new Exception('Employee Leave Type Detail Not Found', 404);
            }

            DB::beginTransaction();
            $this->employeeLeaveTypeRepository->delete($employeeLeaveType);
            DB::commit();
            return redirect()->back()->with('success', 'User Leave Type Removed Successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }
}
