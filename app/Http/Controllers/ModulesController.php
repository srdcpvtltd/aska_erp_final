<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Permission;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage-modules')) {
            $modules = Module::all();
            return view('admin.modules.index', compact('modules'));
        } else {
            return redirect()->back()->with('danger', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::user()->can('create-modules')) {
            return view('admin.modules.create');
        } else {
            return redirect()->back()->with('danger', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create-modules')) {
            try {
                $modual = new Module();
                $modual->name = $request->name;
                $modual->save();
                $data = [];
                if (!empty($request['permissions'])) {
                    foreach ($request['permissions'] as $check) {
                        if ($check == 'M') {
                            $data[] = ['name' => 'manage-' . $request->name, 'guard_name' => 'web'];
                        } else if ($check == 'C') {
                            $data[] = ['name' => 'create-' . $request->name, 'guard_name' => 'web'];
                        } else if ($check == 'E') {
                            $data[] = ['name' => 'edit-' . $request->name, 'guard_name' => 'web'];
                        } else if ($check == 'D') {
                            $data[] = ['name' => 'delete-' . $request->name, 'guard_name' => 'web'];
                        } else if ($check == 'S') {
                            $data[] = ['name' => 'show-' . $request->name, 'guard_name' => 'web'];
                        }
                    }
                }
                Permission::insert($data);
                return redirect()->route('admin.modules.index')
                    ->with('success', __('module Created Successfully'));
            } catch (Exception $e) {
                return redirect()->back()->with('danger', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('danger', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-modules')) {
            $module = Module::find($id);
            return view('admin.modules.edit', compact('module'));
        } else {
            return redirect()->back()->with('danger', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-modules')) {
            try {
                $modules = Module::find($id);

                $this->validate($request, [
                    'name' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|min:4|unique:modules,name,' . $modules->id,
                ], [
                    'regex' => 'Invalid Entry! Only letters,underscores,hypens and numbers are allowed',
                ]);

                $permissions = DB::table('permissions')
                    ->where('name', 'like', '%' . $modules->name . '%')
                    ->get();

                $module_name  = str_replace(' ', '-', strtolower($request->name));
                foreach ($permissions as $permission) {
                    $update_permission = Permission::find($permission->id);
                    if ($permission->name == 'manage-' . $modules->name) {
                        $update_permission->name = 'manage-' . $module_name;
                    }
                    if ($permission->name == 'create-' . $modules->name) {
                        $update_permission->name = 'create-' . $module_name;
                    }
                    if ($permission->name == 'edit-' . $modules->name) {
                        $update_permission->name = 'edit-' . $module_name;
                    }
                    if ($permission->name == 'delete-' . $modules->name) {
                        $update_permission->name = 'delete-' . $module_name;
                    }
                    if ($permission->name == 'show-' . $modules->name) {
                        $update_permission->name = 'show-' . $module_name;
                    }
                    $update_permission->save();
                }

                $modules->name = str_replace(' ', '-', strtolower($request->name));
                $modules->save();

                return redirect()->route('admin.modules.index')->with('success', 'Module Updated Sucessfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('danger', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('danger', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-modules')) {
            $module = Module::findorfail($id);

            $permissions = DB::table('permissions')
                ->where('name', 'like', '%' . $module->name . '%');

            $permissions->delete();
            $module->delete();

            return redirect()->route('admin.modules.index')->with('success', __('Module Deleted successfully.'));
        } else {
            return redirect()->back()->with('danger', __('Permission denied.'));
        }
    }
}
