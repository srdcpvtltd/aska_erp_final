<?php

namespace App\Http\Controllers\Web;

use App\Helpers\SMPush\SMPushHelper;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Repositories\UserRepository;
use App\Requests\Project\ProjectRequest;
use App\Requests\Task\TaskRequest;
use App\Services\Notification\NotificationService;
use App\Services\Project\ProjectService;
use App\Services\Task\TaskService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class  TaskController extends Controller
{
    private $view = 'admin.task.';

    public ProjectService $projectService;
    public TaskService $taskService;
    public UserRepository $userRepo;
    private NotificationService $notificationService;

    public function __construct(
        TaskService $taskService,
        ProjectService $projectService,
        UserRepository $userRepo,
        NotificationService $notificationService
    ) {
        $this->taskService = $taskService;
        $this->projectService = $projectService;
        $this->userRepo = $userRepo;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-task_management')) {
            try {
                $filterParameters = [
                    'task_name' => $request->task_name ?? null,
                    'status' => $request->status ?? null,
                    'priority' => $request->priority ?? null,
                    'members' => $request->members ?? null,
                    'projects' => $request->projects ?? null,
                ];
                $select = ['*'];
                $with = ['assignedMembers.user:id,name', 'project:name,id'];
                $projects = $this->projectService->getAllActiveProjects(['id', 'name']);
                $employees = $this->userRepo->getAllVerifiedEmployeesExceptAdminOfCompany(['id', 'name']);
                $tasks = $this->taskService->getAllFilteredTasksPaginated($filterParameters, $select, $with);
                $allTasks = $this->taskService->getAllTasks(['id', 'name']);
                return view($this->view . 'index', compact(
                    'tasks',
                    'filterParameters',
                    'projects',
                    'employees',
                    'allTasks'
                ));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-task_management')) {
            try {
                $projectSelect = ['name', 'id'];
                $projectLists = $this->projectService->getAllActiveProjects($projectSelect);
                return view($this->view . 'create', compact('projectLists'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function createTaskFromProjectPage($projectId)
    {
        if (\Auth::user()->can('create-task_management')) {
            try {
                $select = ['name', 'id'];
                $with = ['assignedMembers.user:id,name'];
                $project = $this->projectService->findProjectDetailById($projectId, $with, $select);
                $projectMember = $project->assignedMembers;
                return view($this->view . 'create', compact('project', 'projectMember'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        if (\Auth::user()->can('create-task_management')) {
            try {
                $validatedData = $request->validated();
                DB::beginTransaction();
                $task = $this->taskService->saveTaskDetail($validatedData);
                DB::commit();
                if ($task) {
                    $notificationData['title'] = 'Task Assignment Notification';
                    $notificationData['type'] = 'task';
                    $notificationData['user_id'] = $validatedData['assigned_member'];
                    $notificationData['description'] = 'You are assigned to a new task ' . $validatedData['name'] . ' with deadline on ' . $validatedData['end_date'];
                    $notificationData['notification_for_id'] = $task->id;
                    $notification = $this->notificationService->store($notificationData);
                    // if($notification){
                    //     $this->sendNotificationToTaskAssignedMember(
                    //         $notification->title,
                    //         $notification->description,
                    //         $notificationData['user_id'],
                    //         $task->id);
                    // }
                }
                return redirect()
                    ->route('admin.tasks.index')
                    ->with('success', 'New Task Added Successfully');
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with('danger', $e->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show($id)
    {
        if (\Auth::user()->can('show-task_management')) {
            try {
                $taskSelect = ['*'];
                $with = [
                    'project:id,name',
                    'assignedMembers.user:id,post_id,name,avatar',
                    'assignedMembers.user.post:id,post_name',
                    'taskChecklists.taskAssigned:id,name,avatar',
                    'completedTaskChecklist:id,task_id',
                    'taskAttachments',
                    'taskComments.replies',
                    'taskComments.replies.mentionedMember.user:id,name',
                    'taskComments.mentionedMember.user:id,name,avatar',
                    'taskComments.createdBy:id,name,avatar',
                ];
                $taskDetail = $this->taskService->findTaskDetailById($id, $with, $taskSelect);
                $images = [];
                $files = [];
                $taskAttachment = $taskDetail->taskAttachments;
                $comments = $taskDetail->taskComments;

                foreach ($taskAttachment as $key => $value) {
                    if (!in_array($value->attachment_extension, ['pdf', 'doc', 'docx', 'ppt', 'txt', 'xls', 'zip'])) {
                        $images[] = $value;
                    } else {
                        $files[] = $value;
                    }
                }
                return view($this->view . 'show', compact('taskDetail', 'images', 'files', 'comments'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-task_management')) {
            try {
                $images = [];
                $files = [];
                $memberId = [];
                $taskSelect = ['*'];
                $projectSelect = ['name', 'id'];
                $projectWith = ['assignedMembers.user:id,name'];
                $taskWith = ['project.assignedMembers.user:name,id', 'assignedMembers.user:id,name', 'taskAttachments'];
                $projectLists = $this->projectService->getAllActiveProjects($projectSelect, $projectWith);
                $taskDetail = $this->taskService->findTaskDetailById($id, $taskWith, $taskSelect);
                foreach ($taskDetail->assignedMembers as $key => $value) {
                    $memberId[] = $value->user->id;
                }
                $attachments =  $taskDetail->taskAttachments;
                if (count($attachments) > 0) {
                    foreach ($attachments as $key => $value) {
                        if (!in_array($value->attachment_extension, ['pdf', 'doc', 'docx', 'ppt', 'txt', 'xls', 'zip'])) {
                            $images[] = $value;
                        } else {
                            $files[] = $value;
                        }
                    }
                }
                return view(
                    $this->view . 'edit',
                    compact(
                        'taskDetail',
                        'memberId',
                        'projectLists',
                        'images',
                        'files'
                    )
                );
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(TaskRequest $request, $taskId)
    {
        if (\Auth::user()->can('edit-task_management')) {
            try {
                $validatedData = $request->validated();
                $this->taskService->updateTaskDetail($validatedData, $taskId);
                return redirect()
                    ->route('admin.tasks.index')
                    ->with('success', 'Task Detail Updated Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage())->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleStatus($id)
    {
        if (\Auth::user()->can('edit-task_management')) {
            try {
                DB::beginTransaction();
                $this->taskService->toggleStatus($id);
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
        if (\Auth::user()->can('delete-task_management')) {
            try {
                DB::beginTransaction();
                $this->taskService->deleteTaskDetail($id);
                DB::commit();
                return redirect()->back()->with('success', 'Task Detail Deleted  Successfully');
            } catch (Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    private function sendNotificationToTaskAssignedMember($title, $message, $userIds, $id)
    {
        SMPushHelper::sendProjectManagementNotification($title, $message, $userIds, $id);
    }
}
