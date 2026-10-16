<?php

namespace App\Support;

class PhoneHelper
{
    public static function normalize(?string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $phone) ?? '';

        if ($digits !== '' && str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        return $digits;
    }

    public static function mask(?string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $phone) ?? '';

        if (strlen($digits) <= 6) {
            return $digits;
        }

        return substr($digits, 0, 4).str_repeat('x', strlen($digits) - 7).substr($digits, -3);
    }

    public static function waLink(?string $phone, string $message = ''): ?string
    {
        $digits = self::normalize($phone);

        if ($digits === '') {
            return null;
        }

        return 'https://wa.me/'.$digits.($message !== '' ? '?text='.rawurlencode($message) : '');
    }
}
