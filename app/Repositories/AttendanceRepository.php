<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\Regularization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isType;

class AttendanceRepository
{

    public function getAllCompanyEmployeeAttendanceDetailOfTheDay($filterParameter)
    {

        return User::select(
            'attendances.id AS attendance_id',
            'users.id AS user_id',
            'users.name AS user_name',
            'users.company_id AS company_id',
            'users.branch_id AS branch_id',
            'companies.name AS company_name',
            'attendances.attendance_date',
            'attendances.attendance_status',
            'attendances.check_in_at',
            'attendances.check_out_at',
            'attendances.check_in_latitude',
            'attendances.check_out_latitude',
            'attendances.check_in_longitude',
            'attendances.check_out_longitude',
            'attendances.edit_remark',
            'attendances.check_in_type',
            'attendances.check_out_type',
            'attendances.created_by',
            'attendances.updated_by',
        )->leftJoin('attendances', function ($join) use ($filterParameter) {
            $join->on('users.id', '=', 'attendances.user_id');
            if (!empty($filterParameter['start_date']) && !empty($filterParameter['end_date'])) {

                $join->whereBetween('attendances.attendance_date',[$filterParameter['start_date'],$filterParameter['end_date']]);
            }
        })
            ->join('companies', 'users.company_id', '=', 'companies.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->when(isset($filterParameter['branch_id']), function ($query) use ($filterParameter) {
                $query->where('users.branch_id', $filterParameter['branch_id']);
            })
            ->when(isset($filterParameter['department_id']), function ($query) use ($filterParameter) {
                $query->where('users.department_id', $filterParameter['department_id']);
            })
            ->get();
            // dd($result);
    }

    public function getAllCompanyEmployeeregularizationDetailOfTheDay($filterParameter)
    {
        return User::select(
            'regularizations.id AS regularizations_id',
            'users.id AS user_id',
            'users.name AS user_name',
            'users.company_id AS company_id',
            'users.branch_id AS branch_id',
            'companies.name AS company_name',
            'regularizations.regularization_date',
            'regularizations.regularization_status',
            'regularizations.check_in_at',
            'regularizations.check_out_at',
            'regularizations.reason',
            'regularizations.check_in_latitude',
            'regularizations.check_out_latitude',
            'regularizations.check_in_longitude',
            'regularizations.check_out_longitude',
            'regularizations.edit_remark',
            // 'regularizations.check_in_type',
            // 'regularizations.check_out_type',
            'regularizations.created_by',
            'regularizations.updated_by',
        )->leftJoin('regularizations', function ($join) use ($filterParameter) {
            
            // dd($filterParameter);
            if ($filterParameter['regularization_date'] != null) {
                $join->on('users.id', '=', 'regularizations.user_id');
                $join->where('regularizations.regularization_date', '=', $filterParameter['regularization_date']);
            }else{
                $join->on('users.id', '=', 'regularizations.user_id');
            }
            
            if($filterParameter['regularization_status'] != null){
                $join->where('regularizations.regularization_status', $filterParameter['regularization_status']);
            }   
        })
            ->join('companies', 'users.company_id', '=', 'companies.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->when(isset($filterParameter['branch_id']), function ($query) use ($filterParameter) {
                $query->where('users.branch_id', $filterParameter['branch_id']);
            })
            ->when(isset($filterParameter['department_id']), function ($query) use ($filterParameter) {
                $query->where('users.department_id', $filterParameter['department_id']);
            })
            // ->when(isset($filterParameter['regularization_status']), function($query) use ($filterParameter){
            //     $query->where('regularizations.regularization_status', $filterParameter['regularization_status']);
            // })
            ->get();
    }

    public function getEmployeeAttendanceDetailOfTheMonth($filterParameters, $select = ['*'], $with = [])
    {
        $attendanceList = Attendance::with($with)
            ->select($select)
            ->where('user_id', $filterParameters['user_id']);
        if (isset($filterParameters['start_date'])) {
            $attendanceList->whereBetween('attendance_date', [$filterParameters['start_date'], $filterParameters['end_date']]);
        } else {
            $attendanceList
                ->whereMonth('attendance_date', '=', $filterParameters['month'])
                ->whereYear('attendance_date', '=', $filterParameters['year']);
        }
        return $attendanceList->get();
    }

    public function findEmployeeTodayCheckInDetail($userId, $select = ['*'])
    {
        return Attendance::select($select)
            ->where('user_id', $userId)
            ->where('attendance_date', Carbon::now()->format('Y-m-d'))
            ->first();
    }

    public function findAttendanceDetailById($id, $select = ['*'])
    {
        return Attendance::where('id', $id)->first();
    }

    public function updateAttendanceStatus($attendanceDetail)
    {
        return $attendanceDetail->update([
            'attendance_status' => !$attendanceDetail->attendance_status
        ]);
    }

    public function delete(Attendance $attendanceDetail)
    {
        return $attendanceDetail->delete();
    }

    public function storeAttendanceDetail($validatedData)
    {
        return Attendance::create($validatedData)->fresh();
    }

    public function storeRegularizationDetail($validatedData)
    {
        try {
            return Regularization::create([
                'user_id' => $validatedData['user_id'],
                'company_id' => $validatedData['company_id'],
                'regularization_date' => date('Y-m-d', strtotime($validatedData['attendance_date'])),
                'check_in_at' => Carbon::createFromFormat('H:i', $validatedData['check_in_at'])->format('H:i:s'),
                'check_out_at' => Carbon::createFromFormat('H:i', $validatedData['check_out_at'])->format('H:i:s'),
                'reason' =>  $validatedData['reason'],
                'check_in_latitude' => $validatedData['check_in_latitude'],
                'check_out_latitude' => $validatedData['check_in_latitude'],
                'check_in_longitude' => $validatedData['check_in_longitude'],
                'check_out_longitude' => $validatedData['check_in_longitude'], 
                'regularization_status' => 0
            ])->fresh();
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error saving regularization: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save regularization'], 500);
        }
    }
    public function updateAttendanceDetail($attendanceDetail, $validatedData)
    {
        $attendanceDetail->update($validatedData);
        return $attendanceDetail;
    }
}
