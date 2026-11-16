<?php

namespace App\Services;

use App\Models\Visit;
use App\Models\VisitStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VisitStatusService
{
    public function transition(
        Visit $visit,
        array $allowedFrom,
        string $toStatus,
        string $action,
        array $attributes = [],
        ?string $note = null,
    ): Visit {
        return DB::transaction(function () use ($visit, $allowedFrom, $toStatus, $action, $attributes, $note) {
            $lockedVisit = Visit::query()->lockForUpdate()->findOrFail($visit->id);

            if (! in_array($lockedVisit->status, $allowedFrom, true)) {
                throw ValidationException::withMessages([
                    'status' => 'Status kunjungan telah berubah. Muat ulang data sebelum mencoba lagi.',
                ]);
            }

            $fromStatus = $lockedVisit->status;
            $lockedVisit->update(array_merge($attributes, ['status' => $toStatus]));

            VisitStatusHistory::create([
                'visit_id' => $lockedVisit->id,
                'user_id' => auth()->id(),
                'action' => $action,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'note' => $note,
                'created_at' => now(),
            ]);

            return $lockedVisit->fresh();
        });
    }

    public function recordInitial(Visit $visit, string $action): void
    {
        VisitStatusHistory::create([
            'visit_id' => $visit->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'from_status' => null,
            'to_status' => $visit->status,
            'created_at' => now(),
        ]);
    }
}
