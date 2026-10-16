<?php

namespace App\Services;

use App\Models\Visit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function notifyEmployeeNewVisit(Visit $visit): void
    {
        $visit->loadMissing('employee');

        if (! $visit->employee?->phone_number) {
            return;
        }

        $message = sprintf(
            'Halo selamat %s Bapak/Ibu %s, saat ini ada 1 permohonan %s dari %s yang menunggu persetujuan melalui web/aplikasi Buku Tamu Pelindo.',
            $this->dayPart(),
            $visit->employee->name,
            $this->visitTypeLabel($visit),
            $visit->visitor_name
        );

        $this->send($visit->employee->phone_number, $message);
    }

    public function notifyVisitorAccepted(Visit $visit): void
    {
        $visit->loadMissing('employee');

        if (! $visit->visitor_phone) {
            return;
        }

        $employeeName = $visit->employee?->name ?? 'petugas kami';

        $message = sprintf(
            'Halo selamat %s Bapak/Ibu %s, permohonan %s yang Anda ajukan kepada Bapak/Ibu %s sudah diterima. Mohon tunjukkan pesan ini kepada resepsionis untuk pengarahan selanjutnya.',
            $this->dayPart(),
            $visit->visitor_name,
            $this->visitTypeLabel($visit),
            $employeeName
        );

        $this->send($visit->visitor_phone, $message);
    }

    /**
     * Send the one-time checkout barcode (as a link to the gate pass page)
     * to the visitor's WhatsApp right after they register at the gate.
     */
    public function notifyVisitorCheckoutBarcode(Visit $visit): void
    {
        if (! $visit->visitor_phone || ! $visit->checkout_token) {
            return;
        }

        $passUrl = route('gate.pass', $visit->checkout_token);

        $message = sprintf(
            "Halo Bapak/Ibu %s, pendaftaran kedatangan Anda di Pelabuhan Dumai sudah tercatat pukul %s.\n\n".
            "Barcode CHECKOUT Anda (sekali pakai) tersedia pada tautan berikut:\n%s\n\n".
            "Tunjukkan barcode tersebut kepada petugas security saat Anda keluar area. ".
            "Barcode hanya dapat digunakan satu kali dan otomatis tercatat sebagai checkout di sistem.\n\n".
            'Terima kasih. - Buku Tamu Digital Pelindo Regional 1 Dumai',
            $visit->visitor_name,
            now('Asia/Jakarta')->format('H:i'),
            $passUrl
        );

        $this->send($visit->visitor_phone, $message);
    }

    public function send(string $phoneNumber, string $message): bool
    {
        if (! config('services.whatsapp.enabled')) {
            Log::info('WhatsApp notification skipped: service is disabled (set WHATSAPP_ENABLED=true).');

            return false;
        }

        $apiUrl = config('services.whatsapp.api_url');
        $apiToken = config('services.whatsapp.api_token');

        if (! $apiUrl || ! $apiToken) {
            Log::warning('WhatsApp notification skipped because API configuration is incomplete.');

            return false;
        }

        try {
            $response = Http::withToken($apiToken)
                ->acceptJson()
                ->timeout(5)
                ->connectTimeout(3)
                ->retry(1, 200)
                ->post($apiUrl, [
                    'to' => $this->normalizePhone($phoneNumber),
                    'message' => $message,
                    'sender_name' => config('services.whatsapp.sender_name'),
                ]);

            if ($response->failed()) {
                Log::warning('WhatsApp notification failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $exception) {
            Log::warning('WhatsApp notification error.', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function visitTypeLabel(Visit $visit): string
    {
        return match ($visit->visit_type) {
            Visit::TYPE_DELIVER => 'surat / dokumen',
            Visit::TYPE_MEETING_INVITATION => 'undangan rapat / kegiatan rapat',
            default => 'kunjungan',
        };
    }

    protected function dayPart(): string
    {
        $hour = Carbon::now('Asia/Jakarta')->hour;

        return match (true) {
            $hour >= 4 && $hour < 11 => 'pagi',
            $hour >= 11 && $hour < 15 => 'siang',
            $hour >= 15 && $hour < 18 => 'sore',
            default => 'malam',
        };
    }

    protected function normalizePhone(string $phoneNumber): string
    {
        return \App\Support\PhoneHelper::normalize($phoneNumber);
    }
}
