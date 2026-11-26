<?php

namespace Tests\Unit;

use App\Services\PhotoService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhotoServiceTest extends TestCase
{
    #[Test]
    public function it_rejects_svg_and_mismatched_image_payloads(): void
    {
        $service = new PhotoService;
        $svg = base64_encode('<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

        self::assertNull($service->process('data:image/svg+xml;base64,'.$svg));
        self::assertNull($service->process('data:image/jpeg;base64,'.base64_encode('<svg></svg>')));
        self::assertNull($service->process('data:image/png;base64,'.base64_encode("\xFF\xD8\xFF")));
    }

    #[Test]
    public function it_accepts_a_png_payload_with_a_matching_signature(): void
    {
        $contents = file_get_contents(public_path('icons/icon-192.png'));
        self::assertIsString($contents);

        $photo = (new PhotoService)->process('data:image/png;base64,'.base64_encode($contents));

        self::assertNotNull($photo);
        self::assertContains($photo['mime_type'], ['image/png', 'image/jpeg']);
    }
}
