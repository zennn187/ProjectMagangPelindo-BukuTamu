<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'department',
        'position',
        'phone_number',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * A single employee can be the destination of many visits.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Scope to only active employees (the ones shown on the kiosk).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}