<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Visit extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    public const STATUS_PENDING = 'pending';

    public const STATUS_WAITING = 'waiting';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_REJECTED = 'rejected';

    // Visit types
    public const TYPE_MEET = 'meet';

    public const TYPE_DELIVER = 'deliver';

    public const TYPE_MEETING_INVITATION = 'meeting_invitation';

    // Delivery preference when type is 'deliver'
    public const DELIVERY_HAND_IN = 'hand_in';

    public const DELIVERY_LEAVE = 'leave';

    // Where the visit was registered
    public const SOURCE_KIOSK = 'kiosk';

    public const SOURCE_GATE = 'gate';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'visitor_name',
        'visitor_phone',
        'visitor_institution',
        'employee_id',
        'purpose',
        'photo_path',
        'qr_code_token',
        'status',
        'visit_type',
        'delivery_pref',
        'received_by_name',
        'status_note',
        'check_in_time',
        'check_out_time',
        'source',
        'identity_number',
        'plate_number',
        'person_to_meet',
        'checkout_token',
        'checkout_used_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'check_in_time' => 'datetime',
            'check_out_time' => 'datetime',
            'checkout_used_at' => 'datetime',
        ];
    }

    /**
     * Human readable duration between check-in and check-out, e.g. "4 jam 49 menit".
     */
    public function durationText(): ?string
    {
        if (! $this->check_in_time || ! $this->check_out_time) {
            return null;
        }

        $totalMinutes = (int) round($this->check_in_time->diffInMinutes($this->check_out_time));
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} jam {$minutes} menit";
        }

        if ($hours > 0) {
            return "{$hours} jam";
        }

        return "{$minutes} menit";
    }


    /**
     * Helper: build a wa.me link to contact the visitor on WhatsApp.
     */
    public function whatsappUrl(string $message = ''): ?string
    {
        return \App\Support\PhoneHelper::waLink($this->visitor_phone, $message);
    }

    /**
     * Search scope shared by dashboard index + realtime feed (avoids duplication).
     */
    public function scopeSearch($query, string $search)
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function ($inner) use ($search) {
            $inner->where('visitor_name', 'like', "%{$search}%")
                ->orWhere('visitor_institution', 'like', "%{$search}%")
                ->orWhere('visitor_phone', 'like', "%{$search}%")
                ->orWhere('purpose', 'like', "%{$search}%")
                ->orWhereHas('employee', fn ($eq) => $eq->where('name', 'like', "%{$search}%"));
        });
    }

    public function scopeForDay($query, ?string $day, string $column = 'created_at')
    {
        if ($day) {
            try {
                $day = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $day)->format('Y-m-d');
            } catch (\Throwable) {
                $day = now()->toDateString();
            }

            return $query->whereDate($column, $day);
        }

        return $query;
    }

    /**
     * The destination employee for this visit (if still linked).
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Render the visitor QR code as an inline SVG pointing to the badge URL.
     */
    public function qrCodeSvg(int $size = 180): string
    {
        return QrCode::size($size)
            ->color(4, 120, 87)
            ->generate(route('badge', $this->qr_code_token));
    }
}
