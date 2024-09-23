<?php

namespace App\Repositories;

use App\Models\PermissionGroup;
use App\Models\PermissionGroupType;
use App\Models\PermissionRole;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleRepository
{
    public function getAllUserRoles($select=['*'])
    {
        return Role::select($select)->get();
    }

    public function getAllRolesExceptAdmin($select=['*'])
    {
        return Role::select($select)->where('name','!=','admin')->get();
    }

    public function getAllActiveRoles($select=['*'])
    {
        return Role::select($select)->get();
    }

    public function store($validatedData)
    {
        $validatedData['created_by'] = getAuthUserCode();

        return Role::create($validatedData)->fresh();
    }

    public function getRoleById($id,$select=['*'],$with=[])
    {
        return Role::select($select)
            ->with($with)
            ->where('id',$id)
            ->first();
    }

    public function delete($roleDetail)
    {
        return $roleDetail->delete();
    }

    public function update($roleDetail,$validatedData)
    {
        $validatedData['slug'] =  Str::slug($validatedData['name']);
        return $roleDetail->update($validatedData);
    }

    public function toggleStatus($id)
    {
        $roleDetail = Role::where('id',$id)->first();
        if ($roleDetail->name == 'admin') {
            throw new Exception('Sorry, admin role status cannot be changed.', 403);
        }
        return $roleDetail->update([
            'is_active' => !$roleDetail->is_active,
        ]);
    }

}
