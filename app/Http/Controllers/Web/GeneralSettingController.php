<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\GeneralSettingRepository;
use App\Requests\GeneralSetting\GeneralSettingRequest;
use Database\Seeders\EmployeeCodeSeeder;
use Exception;

class GeneralSettingController extends Controller
{
    private $view = 'admin.generalSetting.';

    private GeneralSettingRepository $generalSettingRepo;

    public function __construct(GeneralSettingRepository $generalSettingRepo)
    {
        $this->generalSettingRepo = $generalSettingRepo;
    }

    public function index()
    {
        if (\Auth::user()->can('manage-general_settings')) {
            try {
                $select = ['*'];
                $generalSettings = $this->generalSettingRepo->getAllGeneralSettingDetails($select);
                return view($this->view . 'index', compact('generalSettings'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-general_settings')) {
            try {
                return view($this->view . 'create');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(GeneralSettingRequest $request)
    {
        if (\Auth::user()->can('create-general_settings')) {
            try {
                $validatedData = $request->validated();
                $this->generalSettingRepo->store($validatedData);
                return redirect()->back()->with('success', 'New Detail In General Setting Added');
            } catch (Exception $e) {
                return redirect()->back()->with('danger', $e->getMessage())->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function edit($id)
    {
        if (\Auth::user()->can('edit-general_settings')) {
            try {
                $generalSettingDetail = $this->generalSettingRepo->findOrFailGeneralSettingDetailById($id);
                return view($this->view . 'edit', compact('generalSettingDetail'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(GeneralSettingRequest $request, $id)
    {
        if (\Auth::user()->can('edit-general_settings')) {
            try {
                $validatedData = $request->validated();
                $generalSettingDetail = $this->generalSettingRepo->findOrFailGeneralSettingDetailById($id);

                $this->generalSettingRepo->update($generalSettingDetail, $validatedData);

                if ($generalSettingDetail->key == 'employee_code_prefix') {
                    $employeeCodeSeeder = new EmployeeCodeSeeder();

                    $employeeCodeSeeder->run();
                }
                return redirect()->back()->with('success', 'General Setting Detail Updated Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function delete($id)
    {
        if (\Auth::user()->can('delete-general_settings')) {
            try {
                $this->generalSettingRepo->delete($id);
                return redirect()->back()->with('success', 'General Setting Detail Deleted  Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
