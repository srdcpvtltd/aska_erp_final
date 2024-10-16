<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GuarantorRequest extends FormRequest
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
            'farming_id' => 'required',
            'name' => 'required',
            'father_name' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'district_id' => 'required',
            'block_id' => 'required',
            'gram_panchyat_id' => 'required',
            'village_id' => 'required',
            'post_office' => 'required',
            'police_station' => 'required',
            'age' => 'required'
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
