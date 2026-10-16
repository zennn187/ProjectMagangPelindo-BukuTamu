<?php

namespace App\Jobs;

use App\Models\Visit;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 20;

    public function __construct(
        public int $visitId,
        public string $kind = 'new-visit',
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $visit = Visit::with('employee')->find($this->visitId);

        if (! $visit) {
            return;
        }

        match ($this->kind) {
            'accepted' => $whatsApp->notifyVisitorAccepted($visit),
            'checkout-barcode' => $whatsApp->notifyVisitorCheckoutBarcode($visit),
            default => $whatsApp->notifyEmployeeNewVisit($visit),
        };
    }
}
