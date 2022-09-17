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
            'task_title'        => 'required',
            'type'              => 'required',
            'company_id'        => 'required',
            'price'             => 'required',
            'expiry_date'       => 'required',
            'number_of_steps'   => 'required',
            'tnc'               => 'required',
            'belong_to_project' => 'required',
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
