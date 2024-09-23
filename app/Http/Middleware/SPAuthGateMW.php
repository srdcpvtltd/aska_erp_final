<?php

namespace App\Http\Middleware;

use App\Models\PermissionRole;
use App\Models\Role_has_permission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SPAuthGateMW
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::user()?->role->name == 'admin') {
            Gate::before(function () {
                return true;
            });
        } else if (Auth::user()) {
            $role_id = Auth::user()?->role_id ?? null;
            $allAllocatedPermissions = Role_has_permission::select([
                    DB::raw('role_has_permissions.permission_id as permission_id'),
                    DB::raw('permissions.name as name'),
                    DB::raw('role_has_permissions.role_id as role_id'),
                ])
                ->leftJoin('permissions', function ($query) {
                    $query->on('role_has_permissions.permission_id', '=', 'permissions.id');
                })
                ->where('role_has_permissions.role_id', $role_id)
                ->get();
            foreach ($allAllocatedPermissions as $permission) {
                Gate::define($permission->name, function () {
                    return true;
                });
            }
        }
        return $next($request);
    }
}
