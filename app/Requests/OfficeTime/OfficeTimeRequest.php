<?php

namespace App\Requests\OfficeTime;

use App\Models\OfficeTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class OfficeTimeRequest extends FormRequest
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
    public function rules()
    {
        return [
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'shift' => ['required',Rule::in(OfficeTime::SHIFT)],
            'category' => ['required',Rule::in(OfficeTime::CATEGORY)],
            'is_early_check_in' => ['nullable'],
            'checkin_before' => ['nullable','required_if:is_early_check_in,1'],
            'is_early_check_out' => ['nullable'],
            'checkout_before' => ['nullable','required_if:is_early_check_out,1'],
            'is_late_check_in' => ['nullable'],
            'checkin_after' => ['nullable','required_if:is_late_check_in,1'],
            'is_late_check_out' => ['nullable'],
            'checkout_after' => ['nullable','required_if:is_late_check_out,1'],
        ];

    }

}












