<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skill extends Model
{
    use HasFactory;

    protected $table = 'skills';

    protected $fillable = [
        'skill',
        'qualification',
        'employee_id',
    ];


    public function employees()
    {
        return $this->belongsTo(Employee::class);
    }




}
