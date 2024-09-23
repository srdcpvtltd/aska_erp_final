<?php

namespace App\Requests\Payroll\ReviseSalary;

use App\Repositories\UserRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviseSalaryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id' => ['required', Rule::exists('users', 'id')
                ->where('is_active', UserRepository::IS_ACTIVE)
                ->where('status', UserRepository::STATUS_VERIFIED)
            ],
            'increment_percent' => ['required', 'numeric','between:0,100'],
            'increment_amount' => ['required', 'numeric', 'gt:0'],
            'remark' => ['nullable', 'string'],
            'revised_salary' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
