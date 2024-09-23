<?php

namespace App\Imports;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Helpers\AppHelper;
use App\Models\Department;
use App\Models\OfficeTime;
use App\Models\EmployeeAccount;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportUser implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // dd(count($row)-3);
        try {
            // Ensure all columns are available
            // if (!isset($row['0'], $row['1'], $row['2'], $row['3'],$row['4'], $row['5'],$row['6'],$row['7'],$row['8'], $row['9'], $row['10'], $row['11'], $row['12'], $row['13'], $row['14'], $row['15'], $row['16'], $row['17'], $row['18'], $row['19'], $row['20'], $row['21'], $row['22'], $row['23'], $row['24'], $row['25'], $row['26'],)) {
            //     throw new \Exception('Missing data in some columns');
            // }

            // for($i=1;$i<=count($row)-3;$i++){

            //     if(!isset($row[$i])){
            //         throw new \Exception('Missing data in some columns');
            //     }
            // }

            // Parse date fields
            $dob = $this->parseExcelDate($row['dob']);
            $joiningDate = $this->parseExcelDate($row['joining_date']);
            $office_time = $this->parseExcelTime($row['office_time']);

            // Fetch related model IDs
            $roleId = Role::where('name', $row['role'])->value('id');
            $branchId = Branch::where('name', $row['branch'])->value('id');
            $departmentId = Department::where('dept_name', $row['department'])->value('id');
            $postId = Post::where('post_name', $row['post'])->value('id');
            $supervisorId = User::where('name', $row['supervisor'])->value('id');

            $result =  User::create([
                "name" => $row['name'],
                "email" => $row['email'],
                "username" => $row['username'],
                "password" => bcrypt($row['password']),
                "address" => $row['address'],
                "avatar" => $row['avatar'],
                "dob" => $dob,
                "gender" => $row['gender'],
                "phone" => $row['phone'],
                "role_id" => $roleId,
                "leave_allocated" => $row['leave_allocated'],
                "employment_type" => $row['employment_type'],
                "joining_date" => $joiningDate,
                "workspace_type" => $row['workspace_type'],
                "company_id" => AppHelper::getAuthUserCompanyId(),
                "branch_id" => $branchId,
                "department_id" => $departmentId,
                "post_id" => $postId,
                "supervisor_id" => $supervisorId,
                "office_time_id" =>  OfficeTime::where('opening_time', $office_time)->value('id'),
                "marital_status" => $row['marital_status'],
                "employee_code" => AppHelper::getEmployeeCode()
            ]);

            EmployeeAccount::create([
                'user_id' => $result->id,
                'bank_name' => $row['bank_name'],
                'bank_account_no' => $row['bank_account_number'],
                'bank_account_type' => strtolower($row['bank_account_type']),
                'account_holder' => $row['account_holder_name'],
            ]);
        } catch (\Exception $e) {
            // Handle exceptions, possibly logging them or returning a default value
            Log::error("Error processing row: " . json_encode($row) . " with error: " . $e->getMessage());
            return null; // Return null to skip this row
        }
    }

    private function parseExcelDate($value)
    {
        // Check if the value is numeric (Excel date format)
        if (is_numeric($value)) {
            Log::info("comming to integer");
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        // Otherwise, attempt to parse it as a normal date string
        $date = \DateTime::createFromFormat('m/d/Y', $value);
        if ($date) {
            return $date->format('Y-m-d');
        }

        // Fallback: Attempt to parse it as a different date format
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if ($date) {
            return $date->format('Y-m-d');
        }

        // Log and return null if no valid date is found
        Log::warning("Invalid date format: " . $value);
        return null;
    }

    private function parseExcelTime($value)
    {
        // Check if the value is numeric (Excel time format)
        if (is_numeric($value)) {
            // Calculate total seconds in a day and convert
            $totalSeconds = $value * 24 * 60 * 60; // 24 hours, 60 minutes, 60 seconds
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;

            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        // If it's already a string in time format
        return $value;
    }
}
