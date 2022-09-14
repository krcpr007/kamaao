<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

use Illuminate\Http\Exceptions\HttpResponseException;
class post_job extends FormRequest
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
        return 
            [ 
                'descri_video'=> 'bail|mimes:mp4,mov,ogg,qt | max:10240',//10mb restrict in front end
                'job_title'=> 'required',
                'sub_title'=> 'required',
                'job_type'=> 'required',
                'job_category'=> 'required',
                'expiry_date'=> 'required|date',
                'total_openings'=> 'required|integer',
                'salary_min'=> 'required|integer',
                'salary_max'=> 'required|integer',
                'state'=> 'required',
                'city'=> 'required',
                'area'=> 'required',
                'roles_responsibilities'=> 'required',
                'mini_edu_req'=> 'required',
                'year_req'=> 'required',
                'month_req'=> 'required',
                'skill_req'=> 'required',
                'doc_req'=> 'required',
                'company_id'=> 'required',
                'add_req'=> 'string|nullable',
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
