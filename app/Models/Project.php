<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['company_legal_name','company_popular_name','company_url','company_logo','about_company','call_action',
    "call_action1",
    "project_title",
    "sub_title",
    "project_type",
    "expiry_date",
    "total_openings",
    "amt",
    "task",
    "term_condition",
    "add_rewa",
    "mini_edu_req",
    "experience_req",
    "skill_req",
    "doc_req",
    "add_req",
    "status"
];

    public function task()
    {
        return $this->hasMany(task::class, 'belong_to_project');
        // return task::with('task_steps');
    }

    public function steps()
    {
        return $this->hasManyThrough(task_steps::class, task::class, 'belong_to_project' );
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}