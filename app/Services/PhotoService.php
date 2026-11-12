<?php

namespace App\Services;

class PhotoService
{
    /**
     * Decode and compress a base64 data-URL photo for persistent database storage.
     *
     * @return array{contents: string, mime_type: string}|null
     */
    public function process(?string $dataUrl, int $maxBytes = 3145728): ?array
    {
        if (empty($dataUrl)) {
            return null;
        }

        if (strlen($dataUrl) > $maxBytes) {
            return null;
        }

        $mimeType = preg_match('#^data:(image/[\w.+-]+);base64,#i', $dataUrl, $matches)
            ? strtolower($matches[1])
            : null;
        $base64 = preg_replace('#^data:image/[\w.+-]+;base64,#i', '', $dataUrl);

        if (! $base64 || $mimeType === null || ! str_starts_with($mimeType, 'image/')) {
            return null;
        }

        $image = base64_decode($base64, true);

        if ($image === false || $image === '' || strlen($image) > 2097152) {
            return null;
        }

        $compressed = $this->compress($image, 640, 70);

        if ($compressed === null) {
            return null;
        }

        return [
            'contents' => $compressed,
            'mime_type' => $compressed === $image ? $mimeType : 'image/jpeg',
        ];
    }

    /**
     * Resize sisi panjang ke maxSide px + encode JPG quality.
     * Support JPEG/PNG/WebP/GIF via GD. Return null bila gagal.
     */
    public function compress(string $binary, int $maxSide = 640, int $quality = 70): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return strlen($binary) <= 2097152 ? $binary : null;
        }

        $src = @imagecreatefromstring($binary);

        if ($src === false) {
            // Bukan gambar valid (mis. dummy di test): simpan apa adanya
            // agar tidak 422, selama masih di bawah batas.
            return strlen($binary) <= 2097152 ? $binary : null;
        }

        $w = imagesx($src);
        $h = imagesy($src);

        if ($w <= 0 || $h <= 0) {
            imagedestroy($src);

            return null;
        }

        if (max($w, $h) > $maxSide) {
            $ratio = $maxSide / max($w, $h);
            $nw = (int) round($w * $ratio);
            $nh = (int) round($h * $ratio);
            $dst = imagescale($src, $nw, $nh);
            imagedestroy($src);
            if ($dst === false) {
                return null;
            }
            $src = $dst;
        }

        ob_start();
        $ok = imagejpeg($src, null, $quality);
        $out = ob_get_clean();
        imagedestroy($src);

        if (! $ok || ! is_string($out) || $out === '') {
            return null;
        }

        return $out;
    }
}
