<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const EMPLOYEE_ID = 'employee_id';
    const DATE = 'date';
    const START_TIME = 'start_time';
    const END_TIME = 'end_time';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::USER_ID,
        self::EMPLOYEE_ID,
        self::DATE,
        self::START_TIME,
        self::END_TIME,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, self::EMPLOYEE_ID);
    }
}
