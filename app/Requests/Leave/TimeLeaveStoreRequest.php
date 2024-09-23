<?php

namespace App\Requests\Leave;

use App\Helpers\AppHelper;
use App\Models\LeaveRequestMaster;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TimeLeaveStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $convertedIssueDate = AppHelper::getEnglishDate($this->input('issue_date'));

        $issueDate = $convertedIssueDate ? Carbon::parse($convertedIssueDate) : now();
        $currentTime = now()->format('H:i:s');

        $rules = [
            'issue_date' => 'required|date|after_or_equal:' . now()->format('Y-m-d'),
            'reasons' => 'required|string|min:10',
        ];

        if ($issueDate->isAfter(now())) {
            $rules['leave_from'] = 'required';
            $rules['leave_to'] = 'required|after:leave_from';
        } else {
            $rules['leave_from'] = 'nullable|after_or_equal:' . $currentTime;
            $rules['leave_to'] = 'nullable|after:leave_from';
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'issue_date.required' => 'leave issue date field is required.',
            'issue_date.date' => 'leave issue date must be a valid date.',
            'issue_date.after_or_equal' => 'Leave issue date cannot be in past days.',
            'leave_from.required' => 'The leave from field is required when leave issue date is after today.',
            'leave_to.required' => 'The leave to field is required when leave issue date is after today.',
            'leave_from.after_or_equal' => 'The leave from time must be after or equal to the current time.',
            'leave_to.after' => 'The leave to date must be after the leave from date.',
            'reasons.required' => 'The reasons field is required.',
            'reasons.string' => 'The reasons must be a string.',
            'reasons.min' => 'The reasons must be at least :min characters.',
        ];
    }



}
