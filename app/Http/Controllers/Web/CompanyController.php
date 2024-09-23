<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepository;
use App\Requests\Company\CompanyRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    private $view = 'admin.company.';

    private CompanyRepository $companyRepo;

    public function __construct(CompanyRepository $companyRepo)
    {
        $this->companyRepo = $companyRepo;
    }

    public function index()
    {
        if (\Auth::user()->can('manage-company')) {
            try {
                $companyDetail = $this->companyRepo->getCompanyDetail();
                return view($this->view . 'index', compact('companyDetail'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(CompanyRequest $request)
    {
        if (\Auth::user()->can('create-company')) {
            try {
                $validatedData = $request->validated();
                DB::beginTransaction();
                $this->companyRepo->store($validatedData);
                DB::commit();
                return redirect()->route('admin.company.index')->with('success', 'Company Detail Added Successfully');
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()
                    ->route('admin.company.index')
                    ->with('danger', $e->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function update(CompanyRequest $request, $id)
    {
        if (\Auth::user()->can('edit-company')) {
            try {
                if (env('APP_ENV') == 'test') {
                    throw new Exception('This is a demo version. Please buy the application to use the full feature', 400);
                }
                $validatedData = $request->validated();
                $validatedData['weekend'] = $validatedData['weekend'] ?? [];
                $companyDetail = $this->companyRepo->findOrFailCompanyDetailById($id);
                if (!$companyDetail) {
                    throw new Exception('Company Detail Not Found', 404);
                }
                DB::beginTransaction();
                $this->companyRepo->update($companyDetail, $validatedData);
                DB::commit();
                return redirect()->route('admin.company.index')
                    ->with('success', 'Company Detail Updated Successfully');
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()
                    ->route('admin.company.index')
                    ->with('danger', $e->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
