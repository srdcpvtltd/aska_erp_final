<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\Farming;
use App\Models\FarmingDetail;
use App\Repositories\DashboardRepository;
use App\Services\Client\ClientService;
use App\Services\Project\ProjectService;
use App\Services\Task\TaskService;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private DashboardRepository $dashboardRepo;
    private ClientService $clientService;
    private TaskService $taskService;
    private ProjectService $projectService;

    public function __construct(
        DashboardRepository $dashboardRepo,
        ClientService       $clientService,
        TaskService         $taskService,
        ProjectService      $projectService
    ) {
        $this->dashboardRepo = $dashboardRepo;
        $this->clientService = $clientService;
        $this->projectService = $projectService;
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        try {
            $appTimeSetting = AppHelper::check24HoursTimeAppSetting();

            $projectSelect = ['id', 'name', 'start_date', 'deadline', 'status', 'priority'];
            $withProject = [
                'projectLeaders.user:id,name,avatar',
                'tasks:id,project_id',
                'completedTask:id,project_id'
            ];
            $companyId = AppHelper::getAuthUserCompanyId();

            if (!$companyId) {
                throw new Exception('Company Detail Not Found');
            }
            $date = AppHelper::yearDetailToFilterData();
            $dashboardDetail = $this->dashboardRepo->getCompanyDashboardDetail($companyId, $date);
            $topClients = $this->clientService->getTopClientsOfCompany();
            $taskPieChartData = $this->taskService->getTaskDataForPieChart();
            $projectCardDetail = $this->projectService->getProjectCardData();
            $recentProjects = $this->projectService->getRecentProjectListsForDashboard($projectSelect, $withProject);
            return view(
                'admin.dashboard',
                compact(
                    'dashboardDetail',
                    'topClients',
                    'taskPieChartData',
                    'projectCardDetail',
                    'recentProjects',
                    'appTimeSetting'
                )
            );
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->with('danger', $exception->getMessage());
        }
    }

    public function hr_index()
    {
        try {
            $appTimeSetting = AppHelper::check24HoursTimeAppSetting();

            $projectSelect = ['id', 'name', 'start_date', 'deadline', 'status', 'priority'];
            $withProject = [
                'projectLeaders.user:id,name,avatar',
                'tasks:id,project_id',
                'completedTask:id,project_id'
            ];
            $companyId = AppHelper::getAuthUserCompanyId();

            if (!$companyId) {
                throw new Exception('Company Detail Not Found');
            }
            $date = AppHelper::yearDetailToFilterData();
            $dashboardDetail = $this->dashboardRepo->getCompanyDashboardDetail($companyId, $date);
            $topClients = $this->clientService->getTopClientsOfCompany();
            $taskPieChartData = $this->taskService->getTaskDataForPieChart();
            $projectCardDetail = $this->projectService->getProjectCardData();
            $recentProjects = $this->projectService->getRecentProjectListsForDashboard($projectSelect, $withProject);
            return view(
                'admin.hr_dashboard',
                compact(
                    'dashboardDetail',
                    'topClients',
                    'taskPieChartData',
                    'projectCardDetail',
                    'recentProjects',
                    'appTimeSetting'
                )
            );
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->with('danger', $exception->getMessage());
        }
    }

    public function account_index() 
    {
        return redirect()->back();
    }

    public function farmer_index()
    {
        $companyId = AppHelper::getAuthUserCompanyId();

        if (!$companyId) {
            throw new Exception('Company Detail Not Found');
        }
        $date = AppHelper::yearDetailToFilterData();
        $dashboardDetail = $this->dashboardRepo->getCompanyDashboardDetail($companyId, $date);
        $farmings = Farming::where('is_validate', 1)->get();
        $plots = FarmingDetail::get();
        $area = FarmingDetail::sum('area_in_acar');
        $tentative_planting = FarmingDetail::sum('tentative_harvest_quantity');
        $plant = FarmingDetail::where('planting_category', 'Plant')->count();
        $seed = FarmingDetail::where('planting_category', 'Seed')->count();
        $r1 = FarmingDetail::where('planting_category', 'R-1')->count();
        $r2 = FarmingDetail::where('planting_category', 'R-2')->count();
        $r3 = FarmingDetail::where('planting_category', 'R-3')->count();
        $r4 = FarmingDetail::where('planting_category', 'R-4')->count();
        $r5 = FarmingDetail::where('planting_category', 'R-5')->count();

        return view('admin.farmer_dashboard', compact('farmings', 'plots', 'area', 'tentative_planting', 'dashboardDetail', 'plant', 'seed', 'r1', 'r2', 'r3', 'r4', 'r5'));
    }
}
