<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class task_steps extends Model
{
    use HasFactory;


    public function task()
    {
        return $this->belongsTo(task::class);
    }


}
