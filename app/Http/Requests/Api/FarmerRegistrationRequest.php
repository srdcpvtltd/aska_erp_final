<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FarmerRegistrationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'father_name' => 'required',
            'mobile' => 'digits:10|required|numeric',
            'country_id' => 'required',
            'state_id' => 'required',
            'district_id' => 'required',
            'block_id' => 'required',
            'gram_panchyat_id' => 'required',
            'village_id' => 'required',
            'age' => 'required',
            'gender' => 'required',
            'qualification' => 'required',
            'offered_area' => 'required|numeric',
            'adhaarno' => 'digits:12|required|numeric',
            'language' => 'required',
            'sms_mode' => 'required',
            'created_by' => 'required',
            'zone_id' => 'required',
            'center_id' => 'required',
            // 'farmer_id_2' => 'required',
            'farmer_category' => 'required',
            'irregation_mode' => 'required',
            'irregation' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation Error',
            'errors' => $validator->errors(),
            'code' => 422
        ], 422));
    }
}
