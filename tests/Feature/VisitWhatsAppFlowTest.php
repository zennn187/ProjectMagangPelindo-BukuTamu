<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VisitWhatsAppFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_kiosk_submission_accepts_meeting_invitation_and_notifies_employee(): void
    {
        Storage::fake('public');
        Http::fake([
            'https://wa.example.test/*' => Http::response(['ok' => true]),
        ]);
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://wa.example.test/send',
            'services.whatsapp.api_token' => 'secret-token',
            'services.whatsapp.sender_name' => 'Buku Tamu Pelindo',
        ]);

        $employee = Employee::create([
            'name' => 'Andi Irawan',
            'department' => 'SDM',
            'position' => 'Manager',
            'phone_number' => '081234567890',
            'is_active' => true,
        ]);

        $response = $this->postJson(route('kiosk.store'), [
            'visitor_name' => 'Nafisa',
            'visitor_phone' => '081299998888',
            'visitor_institution' => 'PT Contoh',
            'employee_id' => $employee->id,
            'purpose' => 'Mengantar undangan rapat koordinasi.',
            'visit_type' => Visit::TYPE_MEETING_INVITATION,
            'photo' => 'data:image/jpeg;base64,'.base64_encode('fake-image'),
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('visits', [
            'visitor_name' => 'Nafisa',
            'visit_type' => Visit::TYPE_MEETING_INVITATION,
            'delivery_pref' => null,
            'status' => Visit::STATUS_PENDING,
        ]);
        $visit = Visit::where('visitor_name', 'Nafisa')->firstOrFail();
        $this->assertNotNull($visit->photo_path);
        Storage::disk('public')->assertExists($visit->photo_path);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://wa.example.test/send'
                && $request['to'] === '6281234567890'
                && str_contains($request['message'], 'Andi Irawan')
                && str_contains($request['message'], 'undangan rapat / kegiatan rapat')
                && str_contains($request['message'], 'Nafisa');
        });
    }

    public function test_check_in_notifies_visitor_without_blocking_status_update(): void
    {
        Http::fake([
            'https://wa.example.test/*' => Http::response(['error' => true], 500),
        ]);
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://wa.example.test/send',
            'services.whatsapp.api_token' => 'secret-token',
        ]);

        $user = User::create([
            'name' => 'Admin SDM',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();
        $employee = Employee::create([
            'name' => 'Andi Irawan',
            'department' => 'SDM',
            'position' => 'Manager',
            'phone_number' => '081234567890',
            'is_active' => true,
        ]);
        $visit = Visit::create([
            'visitor_name' => 'Nafisa',
            'visitor_phone' => '081299998888',
            'visitor_institution' => 'PT Contoh',
            'employee_id' => $employee->id,
            'purpose' => 'Kunjungan koordinasi',
            'qr_code_token' => 'token-check-in',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);

        $response = $this->actingAs($user)->post(route('visits.check-in', $visit));

        $response->assertRedirect();
        $this->assertDatabaseHas('visits', [
            'id' => $visit->id,
            'status' => Visit::STATUS_ACTIVE,
        ]);

        Http::assertSent(function ($request) {
            return $request['to'] === '6281299998888'
                && str_contains($request['message'], 'permohonan kunjungan')
                && str_contains($request['message'], 'sudah diterima');
        });
    }
}
