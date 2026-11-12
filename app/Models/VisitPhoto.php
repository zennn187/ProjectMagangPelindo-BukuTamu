<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitPhoto extends Model
{
    protected $primaryKey = 'visit_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'visit_id',
        'contents',
        'mime_type',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
