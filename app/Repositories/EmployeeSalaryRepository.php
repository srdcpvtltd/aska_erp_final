<?php

namespace App\Repositories;


use App\Models\EmployeeSalary;

class EmployeeSalaryRepository
{
    const STATUS = 1;

    public function getAll($select = ['*'])
    {
        return EmployeeSalary::select($select)->get();

    }

    public function getEmployeeSalaryByEmployeeId($employeeId, $select=['*'])
    {
        return EmployeeSalary::select($select)->where('employee_id', $employeeId)->first();
    }

    public function find($id)
    {
        return EmployeeSalary::where('id',$id)->first();
    }


    public function store($validatedData)
    {
        return EmployeeSalary::create($validatedData)->fresh();
    }

    public function update($employeeSalaryDetail,$validatedData)
    {
         return $employeeSalaryDetail->update($validatedData);
    }

    public function delete($employeeSalaryDetail)
    {
        return $employeeSalaryDetail->delete();
    }





}
