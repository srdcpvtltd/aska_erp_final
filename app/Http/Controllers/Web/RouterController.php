<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Repositories\CompanyRepository;
use App\Repositories\RouterRepository;
use App\Requests\Router\RouterRequest;

use Exception;
use Illuminate\Support\Facades\DB;

class RouterController extends Controller
{
    private $view = 'admin.router.';

    public function __construct(
        public RouterRepository $routerRepo,
        public CompanyRepository $companyRepo
    ) {}

    public function index()
    {
        if (\Auth::user()->can('manage-routers')) {
            try {
                $with = ['branch:id,name', 'company:id,name'];
                $select = ['id', 'branch_id', 'company_id', 'router_ssid', 'is_active'];
                $routers = $this->routerRepo->getAllRouters($select, $with);
                return view($this->view . 'index', compact('routers'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-routers')) {
            try {
                $with = ['branches:company_id,id,name'];
                $select = ['id', 'name'];
                $companyDetail = $this->companyRepo->getCompanyDetail($select, $with);
                return view($this->view . 'create', compact('companyDetail'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(RouterRequest $request)
    {
        if (\Auth::user()->can('create-routers')) {
            try {
                $validatedData = $request->validated();
                $validatedData['company_id'] = AppHelper::getAuthUserCompanyId();
                $validatedData['is_active'] = 1;
                DB::beginTransaction();
                $this->routerRepo->store($validatedData);
                DB::commit();
                return redirect()->route('admin.routers.index')
                    ->with('success', 'New Router Detail Added Successfully');
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

    public function show(Router $router)
    {
        //
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-routers')) {
            try {
                $with = ['branches:company_id,id,name'];
                $select = ['id', 'name'];
                $companyDetail = $this->companyRepo->getCompanyDetail($select, $with);
                $routerDetail = $this->routerRepo->findRouterDetailById($id);
                return view($this->view . 'edit', compact('routerDetail', 'companyDetail'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(RouterRequest $request, $id)
    {
        if (\Auth::user()->can('edit-routers')) {
            try {
                $validatedData = $request->validated();
                $routerDetail = $this->routerRepo->findRouterDetailById($id);
                if (!$routerDetail) {
                    throw new Exception('Router Detail Not Found', 404);
                }
                DB::beginTransaction();
                $this->routerRepo->update($routerDetail, $validatedData);
                DB::commit();
                return redirect()
                    ->route('admin.routers.index')
                    ->with('success', 'Router Detail Updated Successfully');
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
        if (\Auth::user()->can('edit-routers')) {
            try {
                DB::beginTransaction();
                $this->routerRepo->toggleStatus($id);
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
        if (\Auth::user()->can('delete-routers')) {
            try {
                $routerDetail = $this->routerRepo->findRouterDetailById($id);
                if (!$routerDetail) {
                    throw new Exception('RouterDetail Not Found', 404);
                }
                DB::beginTransaction();
                $this->routerRepo->delete($routerDetail);
                DB::commit();
                return redirect()->back()->with('success', 'Router Detail Trashed Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
