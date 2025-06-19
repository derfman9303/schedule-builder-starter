<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    const NAME = 'name';
    const START_DATE = 'start_date';
    const END_DATE = 'end_date';

    protected $fillable = [
        self::NAME,
        self::START_DATE,
        self::END_DATE,
    ];

    public function workWeeks()
    {
        return $this->hasMany(WorkWeek::class);
    }
}
