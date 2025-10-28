<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory;

    const ID = 'id';

    const USER_ID = 'user_id';

    const NAME = 'name';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::USER_ID,
        self::NAME,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }
}
