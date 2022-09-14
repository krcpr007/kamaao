<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Job extends Model
{
    use SoftDeletes;
    use HasFactory;
    
    protected $fillable = [
                        'call_action',
                        "call_action1",
                        "job_title",
                        "sub_title",
                        "job_type",
                        "job_category",
                        "expiry_date",
                        "total_openings",
                        "salary_min",
                        "salary_max",
                        "state",
                        "city",
                        "area",
                        "descri_video",
                        "roles_responsibilities",
                        "mini_edu_req",
                        "year_req",
                        "month_req",
                        "skill_req",
                        "doc_req",
                        "add_req",
                        "status"
                    ];
}
