<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetentionSetting extends Model
{
    protected $fillable = ['id', 'visit_retention_days', 'photo_retention_days'];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'visit_retention_days' => 365,
            'photo_retention_days' => 365,
        ]);
    }
}
