<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Requests\Role\RoleRequest;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class  RoleController extends Controller
{
    private $view = 'admin.role.';

    private RoleRepository $roleRepo;
    private UserRepository $userRepo;

    public function __construct(RoleRepository $roleRepo, UserRepository $userRepo)
    {
        $this->roleRepo = $roleRepo;
        $this->userRepo = $userRepo;
    }

    public function index()
    {
        if (\Auth::user()->can('manage-role')) {
            try {
                $roles = $this->roleRepo->getAllUserRoles();

                return view($this->view . 'index', compact('roles'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-role')) {
            try {
                return view($this->view . 'create');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(RoleRequest $request)
    {
        if (\Auth::user()->can('create-role')) {
            try {
                $validatedData = $request->validated();
                DB::beginTransaction();
                $this->roleRepo->store($validatedData);
                DB::commit();
                Artisan::call('cache:clear');
                return redirect()->back()->with('success', 'New Role Added Successfully');
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

    public function edit($id)
    {
        if (\Auth::user()->can('edit-role')) {
            try {
                $roleDetail = $this->roleRepo->getRoleById($id);
                if (!$roleDetail) {
                    throw new Exception('Role Detail Not Found', 204);
                }
                if ($roleDetail->slug == 'admin') {
                    throw new Exception('Cannot Edit Admin Role', 402);
                }
                return view($this->view . 'edit', compact('roleDetail'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(RoleRequest $request, $id)
    {
        if (\Auth::user()->can('edit-role')) {
            try {
                $validatedData = $request->validated();
                $roleDetail = $this->roleRepo->getRoleById($id);
                if (!$roleDetail) {
                    throw new Exception('Role Detail Not Found', 404);
                }
                DB::beginTransaction();
                $this->roleRepo->update($roleDetail, $validatedData);
                DB::commit();
                Artisan::call('cache:clear');
                return redirect()->back()->with('success', 'Role Detail Updated Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleStatus($id)
    {
        try {
            DB::beginTransaction();
            $this->roleRepo->toggleStatus($id);
            DB::commit();
            return redirect()->back()->with('success', 'Status changed  Successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function delete($id)
    {
        if (\Auth::user()->can('delete-role')) {
            try {
                $roleDetail = $this->roleRepo->getRoleById($id);
                if (!$roleDetail) {
                    throw new Exception('Role Detail Not Found', 404);
                }
                if ($roleDetail->slug == 'admin') {
                    throw new Exception('Cannot Delete Admin Role', 402);
                }
                $user = $this->userRepo->findUserDetailByRole($id); {
                    if ($user) {
                        throw new Exception('Cannot Delete Assigned Role', 402);
                    }
                }
                DB::beginTransaction();
                $this->roleRepo->delete($roleDetail);
                DB::commit();
                Artisan::call('cache:clear');
                return redirect()->back()->with('success', 'Role Detail Deleted  Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function createPermission($roleId): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('show-role')) {
            try {
                $role = Role::find($roleId);
                if (!$role) {
                    throw new Exception('Role Detail Not Found', 404);
                }
                if ($role->name == 'admin') {
                    throw new Exception('Admin Role Is Always Assigned With All Permission', 404);
                }
                $permissions = $role->permissions->pluck('name', 'id')->toArray();
                $allpermissions = Permission::all()->pluck('name', 'id')->toArray();
                $allmodules = Module::all()->pluck('name', 'id')->toArray();
                return view($this->view . 'permission')
                    ->with('role', $role)
                    ->with('permissions', $permissions)
                    ->with('allpermissions', $allpermissions)
                    ->with('moduals', $allmodules);
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function assignPermissionToRole(Request $request, $roleId): RedirectResponse
    {
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $role = Role::find($roleId);
            $permissions = $role->permissions()->get();
            DB::beginTransaction();
            $role->revokePermissionTo($permissions);
            $role->givePermissionTo($request->permissions);
            DB::commit();

            return redirect()->back()->with('success', 'Permission Updated To Role Successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }
}
