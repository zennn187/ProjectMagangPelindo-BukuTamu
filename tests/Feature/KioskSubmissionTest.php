<?php

namespace Tests\Feature;

use App\Services\PhotoService;
use Illuminate\Support\Facades\Log;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class KioskSubmissionTest extends TestCase
{
    public function test_failed_submission_logs_a_short_redacted_exception_with_the_error_id(): void
    {
        $visitorName = 'Visitor Private Name';
        $visitorPhone = '081234567890';
        $photo = 'private-photo-data';

        $photos = Mockery::mock(PhotoService::class);
        $photos->shouldReceive('process')
            ->once()
            ->andThrow(new RuntimeException("Failure for {$visitorName} {$visitorPhone} {$photo}"));
        $this->app->instance(PhotoService::class, $photos);

        Log::shouldReceive('error')
            ->once()
            ->with('Kiosk submission failed', Mockery::on(function (array $context) use ($visitorName, $visitorPhone, $photo): bool {
                $this->assertSame(RuntimeException::class, $context['exception_class']);
                $this->assertArrayHasKey('error_id', $context);
                $this->assertStringContainsString('[redacted]', $context['message']);
                $this->assertStringNotContainsString($visitorName, $context['message']);
                $this->assertStringNotContainsString($visitorPhone, $context['message']);
                $this->assertStringNotContainsString($photo, $context['message']);
                $this->assertArrayHasKey('file', $context);
                $this->assertArrayHasKey('line', $context);

                return true;
            }));

        $response = $this->postJson(route('kiosk.store'), [
            'visitor_name' => $visitorName,
            'visitor_phone' => $visitorPhone,
            'visitor_institution' => 'Private Institution',
            'purpose' => 'Private Purpose',
            'visit_type' => 'meet',
            'photo' => $photo,
            'consent' => '1',
        ]);

        $response->assertStatus(500)
            ->assertJsonStructure(['message', 'error_id']);
        $this->assertStringContainsString('Server gagal menyimpan formulir.', $response->json('message'));
    }
}
