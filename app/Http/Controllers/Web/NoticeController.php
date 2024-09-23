<?php

namespace App\Http\Controllers\Web;

use App\Helpers\SMPush\SMPushHelper;
use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;
use App\Requests\Notice\NoticeRequest;
use App\Services\Notice\NoticeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoticeController extends Controller
{
    private $view = 'admin.notice.';

    private CompanyRepository $companyRepo;
    private UserRepository $userRepo;
    private NoticeService $noticeService;


    public function __construct(
        CompanyRepository $companyRepo,
        UserRepository    $userRepo,
        NoticeService     $noticeService
    ) {
        $this->companyRepo = $companyRepo;
        $this->userRepo = $userRepo;
        $this->noticeService = $noticeService;
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-notice')) {
            try {
                $filterParameters = [
                    'notice_receiver' => $request->notice_receiver ?? null,
                    'publish_date_from' => $request->publish_date_from ?? null,
                    'publish_date_to' => $request->publish_date_to ?? null,
                ];

                $select = ['*'];
                $with = ['noticeReceiversDetail'];
                $notices = $this->noticeService->getAllCompanyNotices($filterParameters, $select, $with);
                return view($this->view . 'index', compact('notices', 'filterParameters'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-notice')) {
            try {
                $selectCompany = ['id', 'name'];
                $selectUser = ['id', 'name'];
                $companyDetail = $this->companyRepo->getCompanyDetail($selectCompany);
                $userDetail = $this->userRepo->getAllVerifiedEmployeeOfCompany($selectUser);
                return view(
                    $this->view . 'create',
                    compact('companyDetail', 'userDetail')
                );
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function store(NoticeRequest $request)
    {
        if (\Auth::user()->can('create-notice')) {
            try {
                $validatedData = $request->validated();
                DB::beginTransaction();
                $notice = $this->noticeService->store($validatedData);
                DB::commit();
                if ($notice) {
                    $userIds = $this->getUserIdsForNoticeNotification($validatedData['receiver']);
                    // $this->sendNoticeNotification(ucfirst($validatedData['title']), removeHtmlTags($notice['description']), $userIds);
                }
                return redirect()
                    ->back()
                    ->with('success', 'Notice created and sent Successfully');
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('danger', $e->getMessage())->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    private function getUserIdsForNoticeNotification($validatedData)
    {
        try {
            $userIds = [];
            foreach ($validatedData as $key => $value) {
                $userIds[] = $value['notice_receiver_id'];
            }
            return $userIds;
        } catch (Exception $ex) {
            return redirect()->back()->with('danger', $ex->getMessage());
        }
    }

    private function sendNoticeNotification($title, $description, $userIds)
    {
        SMPushHelper::sendNoticeNotification($title, $description, $userIds);
    }

    public function show($id)
    {
        if (\Auth::user()->can('show-notice')) {
            try {
                $select = ['description', 'title'];
                $notice = $this->noticeService->findOrFailNoticeDetailById($id, $select);
                $notice->description = removeHtmlTags($notice->description);
                $notice->title = ucfirst($notice->title);
                return response()->json([
                    'data' => $notice,
                ]);
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-notice')) {
            try {
                $with = ['noticeReceiversDetail'];
                $selectNotice = ['*'];
                $selectCompany = ['id', 'name'];
                $selectUser = ['id', 'name'];
                $noticeDetail = $this->noticeService->findOrFailNoticeDetailById($id, $selectNotice, $with);
                $receiverUserIds = [];
                foreach ($noticeDetail->noticeReceiversDetail as $key => $value) {
                    $receiverUserIds[] = $value->notice_receiver_id;
                }
                $companyDetail = $this->companyRepo->getCompanyDetail($selectCompany);
                $userDetail = $this->userRepo->getAllVerifiedEmployeeOfCompany($selectUser);
                return view($this->view . 'edit', compact('noticeDetail', 'companyDetail', 'userDetail', 'receiverUserIds'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(NoticeRequest $request, $id)
    {
        if (\Auth::user()->can('edit-notice')) {
            try {
                $validatedData = $request->validated();
                $noticeDetail = $this->noticeService->findOrFailNoticeDetailById($id);
                DB::beginTransaction();
                $updateNotice = $this->noticeService->update($noticeDetail, $validatedData);
                DB::commit();
                if ($updateNotice) {
                    $userIds = $this->getUserIdsForNoticeNotification($validatedData['receiver']);
                    // $this->sendNoticeNotification(ucfirst($validatedData['title']), removeHtmlTags($validatedData['description']), $userIds);
                }
                return redirect()->back()->with('success', 'Notice Updated and Sent Successfully');
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
        if (\Auth::user()->can('edit-notice')) {
            try {
                DB::beginTransaction();
                $this->noticeService->changeNoticeStatus($id);
                DB::commit();
                return redirect()->back()->with('success', 'Notice Status changed Successfully');
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
        if (\Auth::user()->can('delete-notice')) {
            try {
                DB::beginTransaction();
                $this->noticeService->deleteNotice($id);
                DB::commit();
                return redirect()->back()->with('success', 'Notice Deleted  Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function sendNotice($noticeId)
    {
        try {
            $with = ['noticeReceiversDetail'];
            $select = ['*'];
            $noticeDetail = $this->noticeService->findOrFailNoticeDetailById($noticeId, $select, $with);
            $userIds = $this->getUserIdsForNoticeNotification($noticeDetail->noticeReceiversDetail);
            $this->sendNoticeNotification(ucfirst($noticeDetail->title), removeHtmlTags($noticeDetail->description), $userIds);
            DB::beginTransaction();
            $validatedData['is_active'] = 1;
            $validatedData['notice_publish_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $this->noticeService->updatePublishDateAndStatus($noticeDetail, $validatedData);
            DB::commit();
            return redirect()->back()->with('success', 'Notice Sent Successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('danger', $exception->getFile());
        }
    }
}
