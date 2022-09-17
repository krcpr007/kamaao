<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Add_project extends FormRequest
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
            'project_title'     => 'required',
            'pr_sub_title'      => 'required',
            'project_type'      => 'required',
            'total_openings'    => 'required',
            'amount'            => 'required|integer',
            'state'             => 'required',
            'city'              => 'required',
            'area'              => 'required',
            'number_of_task'    => 'required',
            'term_condition'    => 'required',
            'mini_edu_req'      => 'required',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date',
            "experience_req"    =>  'required',
            'skill_req'         => 'required',
            'doc_req'           => 'required',
            'add_req'           => 'required'
        ];
    }
}
