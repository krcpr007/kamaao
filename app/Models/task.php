<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class task extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_title',
        'type',
        'company_id',
        'price',
        'expiry_date',
        'number_of_steps',
        'tnc',
        'belong_to_project',
        'is_enabled'
    ];

    public function task_steps()
    {
        return $this->hasMany(task_steps::class);
    } 
}
