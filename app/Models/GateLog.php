<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GateLog extends Model
{
    public const DIRECTION_IN = 'in';

    public const DIRECTION_OUT = 'out';

    protected $fillable = [
        'visit_id',
        'direction',
        'token',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
