<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTask extends Model
{
     protected $fillable = [
        'employee_name', 'task_name', 'priority',
        'completed', 'pending', 'overdue', 'in_progress', 'delayed',
        'task_date'
    ];
}
