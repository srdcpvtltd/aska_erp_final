<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepository;
use App\Repositories\ContentManagementRepository;
use App\Requests\ContentManagement\ContentManagementRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StaticPageContentController extends Controller
{
    private $view = 'admin.contentManagement.';

    private ContentManagementRepository $contentMgmtRepo;
    private CompanyRepository $companyRepo;


    public function __construct(ContentManagementRepository $contentMgmtRepo, CompanyRepository $companyRepo)
    {
        $this->contentMgmtRepo = $contentMgmtRepo;
        $this->companyRepo = $companyRepo;
    }

    public function index()
    {
        if (\Auth::user()->can('manage-content_management')) {
            try {
                $staticPageContents = $this->contentMgmtRepo->getAllCompanyContentManagementDetail();
                return view($this->view . 'index', compact('staticPageContents'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-content_management')) {
            try {
                $select = ['id', 'name'];
                $companyDetail = $this->companyRepo->getCompanyDetail($select);
                return view(
                    $this->view . 'create',
                    compact('companyDetail')
                );
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show($id)
    {
        if (\Auth::user()->can('show-content_management')) {
            try {
                $select = ['description', 'title'];
                $contentDescription = $this->contentMgmtRepo->findCompanyContentById($id, $select);
                $contentDescription->description = removeHtmlTags($contentDescription->description);
                return response()->json([
                    'data' => $contentDescription,
                ]);
            } catch (\Exception $exception) {
                return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(ContentManagementRequest $request)
    {
        if (\Auth::user()->can('create-content_management')) {
            try {
                $validatedData = $request->validated();
                DB::beginTransaction();
                $validatedData['title_slug'] = Str::slug($validatedData['title']);
                $this->contentMgmtRepo->store($validatedData);
                DB::commit();
                return redirect()->back()->with('success', 'New Company Static Page Content Added Successfully');
            } catch (\Exception $e) {
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
        if (\Auth::user()->can('edit-content_management')) {
            try {
                $companyContentDetail = $this->contentMgmtRepo->findCompanyContentById($id);
                $select = ['id', 'name'];
                $companyDetail = $this->companyRepo->getCompanyDetail($select);
                return view($this->view . 'edit', compact('companyContentDetail', 'companyDetail'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function update(ContentManagementRequest $request, $id)
    {
        if (\Auth::user()->can('edit-content_management')) {
            try {
                $validatedData = $request->validated();
                $companyContentDetail = $this->contentMgmtRepo->findCompanyContentById($id);
                if (!$companyContentDetail) {
                    throw new \Exception('Office Time Detail Not Found', 404);
                }
                DB::beginTransaction();
                $validatedData['title_slug'] = Str::slug($validatedData['title']);
                $this->contentMgmtRepo->update($companyContentDetail, $validatedData);
                DB::commit();
                return redirect()->back()->with('success', 'Company Static Page Content Updated Successfully');
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleStatus($id)
    {
        if (\Auth::user()->can('edit-content_management')) {
            try {
                DB::beginTransaction();
                $this->contentMgmtRepo->toggleStatus($id);
                DB::commit();
                return redirect()->back()->with('success', 'Company Static Page Content Status changed  Successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function delete($id)
    {
        if (\Auth::user()->can('delete-content_management')) {
            try {
                $companyContentDetail = $this->contentMgmtRepo->findCompanyContentById($id);
                if (!$companyContentDetail) {
                    throw new \Exception('Company Static Page Content Detail Not Found', 404);
                }
                DB::beginTransaction();
                $this->contentMgmtRepo->delete($companyContentDetail);
                DB::commit();
                return redirect()->back()->with('success', 'Company Static Page Content Deleted  Successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
