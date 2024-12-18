<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NfcApiController;
use App\Http\Controllers\Api\FarmerController;
use App\Http\Controllers\Api\TadaApiController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\LeaveApiController;
use App\Http\Controllers\Api\NoticeApiController;
use App\Http\Controllers\Api\HolidayApiController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\SupportApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\LeaveTypeApiController;
use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\TaskCommentApiController;
use App\Http\Controllers\Api\TeamMeetingApiController;
use App\Http\Controllers\Api\UserProfileApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\PushNotificationController;
use App\Http\Controllers\Api\AdvanceSalaryApiController;
use App\Http\Controllers\Api\AllotmentController;
use App\Http\Controllers\Api\BankDetailsController;
use App\Http\Controllers\Api\TaskChecklistApiController;
use App\Http\Controllers\Api\EmployeePayrollApiController;
use App\Http\Controllers\Api\FarmingDetailsController;
use App\Http\Controllers\Api\GuarantorController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\StaticPageContentApiController;
use App\Http\Controllers\Api\ProjectManagementDashboardApiController;
use App\Http\Controllers\Api\SecurityDepositeController;
use App\Http\Controllers\Api\SeedCatagoryController;
use App\Http\Controllers\Farming\FarmingController;
use App\Http\Requests\Api\AllotmentRequest;
use App\Http\Requests\Api\FarmingDetailsRequest;

/**   user login **/
Route::post('login', [AuthApiController::class, 'login']);

Route::get('team-meetings/{id}', [TeamMeetingApiController::class, 'findTeamMeetingDetail']);
Route::group([
    'middleware' => ['auth:api', 'permission']
], function () {

    /**   user logout **/
    Route::get('logout', [AuthApiController::class, 'logout'])->name('user.logout');

    /** Users Routes **/
    Route::get('users/profile', [UserProfileApiController::class, 'userProfileDetail'])->name('users.profile');
    Route::post('users/change-password', [UserProfileApiController::class, 'changePassword'])->name('users.change-password');
    Route::post('users/update-profile', [UserProfileApiController::class, 'updateUserProfile'])->name('users.update-profile');
    Route::get('users/profile-detail/{userId}', [UserProfileApiController::class, 'findEmployeeDetailById']);
    Route::get('users/company/team-sheet', [UserProfileApiController::class, 'getTeamSheetOfCompany'])->name('users.company.team-sheet');

    /** content management Routes **/
    Route::get('static-page-content/{contentType}', [StaticPageContentApiController::class, 'getStaticPageContentByContentType']);
    Route::get('company-rules', [StaticPageContentApiController::class, 'getCompanyRulesDetail']);
    Route::get('static-page-content/{contentType}/{titleSlug}', [StaticPageContentApiController::class, 'getStaticPageContentByContentTypeAndTitleSlug']);

    /** notifications Routes **/
    Route::get('notifications', [NotificationApiController::class, 'getAllRecentPublishedNotification']);

    /** notice Routes **/
    Route::get('notices', [NoticeApiController::class, 'getAllRecentlyReceivedNotice']);

    /** Dashboard Routes **/
    Route::get('dashboard', [DashboardApiController::class, 'userDashboardDetail']);

    /** Attendance Routes **/
    /**
     * @Deprecated Don't use this now
     */
    Route::post('employees/check-in', [AttendanceApiController::class, 'employeeCheckIn']);
    /**
     * @Deprecated Don't use this now
     */
    Route::post('employees/check-out', [AttendanceApiController::class, 'employeeCheckOut']);
    Route::get('employees/attendance-detail', [AttendanceApiController::class, 'getEmployeeAllAttendanceDetailOfTheMonth']);
    Route::post('employees/attendance', [AttendanceApiController::class, 'employeeAttendance']);

    /** Leave Request Routes **/
    Route::get('leave-types', [LeaveTypeApiController::class, 'getAllLeaveTypeWithEmployeeLeaveRecord']);
    Route::post('leave-requests/store', [LeaveApiController::class, 'saveLeaveRequestDetail']);
    Route::get('leave-requests/employee-leave-requests', [LeaveApiController::class, 'getAllLeaveRequestOfEmployee']);
    Route::get('leave-requests/employee-leave-calendar', [LeaveApiController::class, 'getLeaveCountDetailOfEmployeeOfTwoMonth']);
    Route::get('leave-requests/employee-leave-list', [LeaveApiController::class, 'getAllEmployeeLeaveDetailBySpecificDay']);
    Route::get('leave-requests/cancel/{leaveRequestId}', [LeaveApiController::class, 'cancelLeaveRequest']);
    /** Time Leave Route */
    Route::post('time-leave-requests/store', [LeaveApiController::class, 'saveTimeLeaveRequest']);
    Route::get('time-leave-requests/cancel/{timeLeaveRequestId}', [LeaveApiController::class, 'cancelTimeLeaveRequest']);



    /** Team Meeting Routes **/
    Route::get('team-meetings', [TeamMeetingApiController::class, 'getAllAssignedTeamMeetingDetail']);

    /** Holiday route */
    Route::get('holidays', [HolidayApiController::class, 'getAllActiveHoliday']);

    /** Project Management Dashboard route */
    Route::get('project-management-dashboard', [ProjectManagementDashboardApiController::class, 'getUserProjectManagementDashboardDetail']);

    /** Project route */
    Route::get('assigned-projects-list', [ProjectApiController::class, 'getUserAssignedAllProjects']);
    Route::get('assigned-projects-detail/{projectId}', [ProjectApiController::class, 'getProjectDetailById']);

    /** Tasks route */
    Route::get('assigned-task-list', [TaskApiController::class, 'getUserAssignedAllTasks']);
    Route::get('assigned-task-detail/{taskId}', [TaskApiController::class, 'getTaskDetailById']);
    Route::get('assigned-task-detail/change-status/{taskId}', [TaskApiController::class, 'changeTaskStatus']);
    Route::get('assigned-task-comments', [TaskApiController::class, 'getTaskComments']);

    /** Task checklist route */
    Route::get('assigned-task-checklist/toggle-status/{checklistId}', [TaskChecklistApiController::class, 'toggleCheckListIsCompletedStatus']);

    /** Task Comment route */
    Route::post('assigned-task/comments/store', [TaskCommentApiController::class, 'saveComment']);
    Route::get('assigned-task/comment/delete/{commentId}', [TaskCommentApiController::class, 'deleteComment']);
    Route::get('assigned-task/reply/delete/{replyId}', [TaskCommentApiController::class, 'deleteReply']);

    /** Support route */
    Route::post('support/query-store', [SupportApiController::class, 'store']);
    Route::get('support/department-lists', [SupportApiController::class, 'getAuthUserBranchDepartmentLists']);
    Route::get('support/get-user-query-lists', [SupportApiController::class, 'getAllAuthUserSupportQueryList']);

    /** Tada route */
    Route::get('employee/tada-lists', [TadaApiController::class, 'getEmployeesTadaLists']);
    Route::get('employee/tada-details/{tadaId}', [TadaApiController::class, 'getEmployeesTadaDetail']);
    Route::post('employee/tada/store', [TadaApiController::class, 'storeTadaDetail']);
    Route::post('employee/tada/update', [TadaApiController::class, 'updateTadaDetail']);
    Route::get('employee/tada/delete-attachment/{attachmentId}', [TadaApiController::class, 'deleteTadaAttachment']);

    /** Advance Salary */
    Route::get('employee/advance-salaries-lists', [AdvanceSalaryApiController::class, 'getEmployeesAdvanceSalaryDetailLists']);
    Route::post('employee/advance-salaries/store', [AdvanceSalaryApiController::class, 'store']);
    Route::get('employee/advance-salaries-detail/{id}', [AdvanceSalaryApiController::class, 'getEmployeeAdvanceSalaryDetailById']);
    Route::post('employee/advance-salaries-detail/update', [AdvanceSalaryApiController::class, 'updateDetail']);

    /** Nfc  */
    Route::post('nfc/store', [NfcApiController::class, 'save']);

    /** Push Notification */
    Route::post('employee/push', [PushNotificationController::class, 'sendPushNotification']);

    /** Payslip */
    Route::post('employee/payslip', [EmployeePayrollApiController::class, 'getPayrollList']);
    Route::get('employee/payslip/{id}', [EmployeePayrollApiController::class, 'getEmployeePayslipDetailById']);

    // Famers APIs
    Route::group([
        'prefix' => 'farmer',
        'as' => 'farmer.',
    ], function () {
        //Farmer CRUD
        Route::post('register', [FarmerController::class, 'register']);
        Route::post('delete', [FarmerController::class, 'delete_farmer']);
        Route::post('update', [FarmerController::class, 'update_farmer']);
        Route::post('retrive', [FarmerController::class, 'retrive_farmers']);

        //Farmer CRUD for guarantor
        Route::group(([
            'prefix' => 'guarantor',
            'as' => 'guarantor.'
        ]), function () {
            Route::post('create', [GuarantorController::class, 'create_guarantor']);
            Route::post('delete', [GuarantorController::class, 'delete_guarantor']);
            Route::get('retrive', [GuarantorController::class, 'retrive_guarantor']);
            Route::post('update', [GuarantorController::class, 'update_guarentor']);
        });

        //Farmer CRUD for farming-details
        Route::group([
            'prefix' => 'farming-details',
            'as' => 'farming-details.'
        ], function () {
            Route::post('create', [FarmingDetailsController::class, 'store_farmingDetails']);
            Route::post('delete', [FarmingDetailsController::class, 'delete_farmingDetails']);
            Route::post('update', [FarmingDetailsController::class, 'update_farmingDetails']);
            Route::get('retrive', [FarmingDetailsController::class, 'retrive_farmingDetails']);
        });

        //Farmer CRUD for bank-details
        Route::group([
            'prefix' => 'bank-details',
            'as' => 'bank-details.'
        ], function () {
            Route::post('store', [BankDetailsController::class, 'update_bankDetails']);
            Route::post('delete', [BankDetailsController::class, 'delete_bankDetails']);
            Route::post('update', [BankDetailsController::class, 'update_bankDetails']);
            Route::get('retrive', [BankDetailsController::class, 'retriveFarmerBankDetails']);
        });

        //Farmer Security deposit CRUD for Security Deposite
        Route::group([
            'prefix' => 'security-deposite',
            'as' => 'security-deposite.'
        ], function () {
            Route::post('store', [SecurityDepositeController::class, 'store_deposites']);
            Route::post('delete', [SecurityDepositeController::class, 'delete_deposites']);
            Route::post('update', [SecurityDepositeController::class, 'update_deposites']);
            Route::get('retrive', [SecurityDepositeController::class, 'retriveFarmerBankDetails']);
        });
        //Farmer Allotment CRUD for Security Deposite
        Route::group([
            'prefix' => 'loan-allotment',
            'as' => 'loan-allotment.'
        ], function () {
            Route::post('store', [AllotmentController::class, 'store_loanAllotment']);
            Route::post('delete', [AllotmentController::class, 'delete_loanAllotment']);
            Route::post('update', [AllotmentController::class, 'update_loanAllotment']);
            Route::get('retrive', [AllotmentController::class, 'retrive_loanAllotments']);
        });

        Route::group([
            'prefix' => 'seed-category',
            'as' => 'seed-category.'
        ], function () {
            Route::post('store', [SeedCatagoryController::class, 'store_seedCategory']);
            Route::post('delete', [SeedCatagoryController::class, 'delete_seedCategory']);
            Route::post('update', [SeedCatagoryController::class, 'update_seedCategory']);
            Route::get('retrive', [SeedCatagoryController::class, 'retrive_seedCategory']);
        });
    });

    // Location Apis
    Route::get('countries', [LocationController::class, 'getCountries']);
    Route::post('get_states', [LocationController::class, 'getStates']);
    Route::post('get_districts', [LocationController::class, 'getDistricts']);
    Route::post('get_blocks', [LocationController::class, 'getBlocks']);
    Route::post('get_gram_panchyats', [LocationController::class, 'getGramPanchyats']);
    Route::post('get_villages', [LocationController::class, 'getVillages']);
    Route::post('get-center-get-zones', [LocationController::class, 'get_center_and_zones']);
    Route::post('get-irrigation-mode', [LocationController::class, 'get_irrigations']);
});
