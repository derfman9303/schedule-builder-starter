<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    const WORK_WEEK_ID = 'work_week_id';
    const WEEK_DAY = 'week_day';
    const START_TIME = 'start_time';
    const END_TIME = 'end_time';
    const DATE = 'date';
    const DAY_OFFSET = 'day_offset';

    protected $fillable = [
        self::WORK_WEEK_ID,
        self::START_TIME,
        self::END_TIME,
        self::WEEK_DAY,
        self::DATE,
        self::DAY_OFFSET,
    ];

    public function workWeek()
    {
        return $this->belongsTo(WorkWeek::class, self::WORK_WEEK_ID);
    }
}
