<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class application_remarks extends Model
{
    use HasFactory;
    protected $fillable = ['remarks','application_id', 'remarked_by'];

    public function application()
    {
        return $this->hasOne(Application::class);
    }
    
}
