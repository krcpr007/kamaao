<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

use Illuminate\Http\Exceptions\HttpResponseException;


class add_task extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(auth('sanctum')->user()->user_type==1)
        {
            return true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'task_title'        => 'required|min:10',
            'type'              => 'required',
            'company_id'        => 'required|integer',
            'price'             => 'required|integer',
            'expiry_date'       => 'required|date',
            'number_of_steps'   => 'required|integer',
            'tnc'               => 'required',
            // 'belong_to_project' => 'required|integer',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 422,
            'meaasge' => "The given data was invalid to process with",
            'errors' => $validator->errors()
            
        ], 422));
    }
}
