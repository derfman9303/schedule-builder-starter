<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkWeek extends Model
{
    const SCHEDULE_ID = 'schedule_id';
    const EMPLOYEE_ID = 'employee_id';
    const EMPLOYEE_NAME = 'employee_name';

    protected $fillable = [
        self::SCHEDULE_ID,
        self::EMPLOYEE_ID,
        self::EMPLOYEE_NAME,
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, self::SCHEDULE_ID);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, self::EMPLOYEE_ID);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
