<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoService
{
    /**
     * Decode base64 data-URL photo and persist it under the public disk.
     * Returns relative path or null on failure / oversize.
     */
    public function store(?string $dataUrl, int $maxBytes = 3145728): ?string
    {
        if (empty($dataUrl)) {
            return null;
        }

        if (strlen($dataUrl) > $maxBytes) {
            return null;
        }

        $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $dataUrl);

        if (! $base64 || ! Str::startsWith($dataUrl, 'data:image/')) {
            return null;
        }

        $image = base64_decode($base64, true);

        if ($image === false || $image === '' || strlen($image) > 2097152) {
            return null;
        }

        $filename = 'visits/'.Str::uuid().'.jpg';

        if (! Storage::disk('public')->put($filename, $image)) {
            return null;
        }

        return $filename;
    }
}
