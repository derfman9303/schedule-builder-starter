<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    const USER_ID = 'user_id';
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
