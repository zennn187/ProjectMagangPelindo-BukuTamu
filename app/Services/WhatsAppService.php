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

    public function send(string $phoneNumber, string $message): bool
    {
        if (! config('services.whatsapp.enabled')) {
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
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber) ?: '';

        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);
        }

        return $phone;
    }
}
