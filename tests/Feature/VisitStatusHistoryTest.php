<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visit;
use App\Models\VisitStatusHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitStatusHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_receptionist_status_change_records_actor_time_and_note(): void
    {
        $user = User::factory()->create(['role' => 'receptionist']);
        $visit = Visit::create([
            'visitor_name' => 'Tamu Uji',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Pertemuan',
            'qr_code_token' => 'audit-test-token',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);

        $response = $this->actingAs($user)->postJson(route('visits.wait', $visit), [
            'note' => 'Pegawai sedang rapat',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('visit_status_histories', [
            'visit_id' => $visit->id,
            'user_id' => $user->id,
            'action' => 'waiting',
            'from_status' => Visit::STATUS_PENDING,
            'to_status' => Visit::STATUS_WAITING,
            'note' => 'Pegawai sedang rapat',
        ]);
        $this->assertNotNull(VisitStatusHistory::query()->where('visit_id', $visit->id)->value('created_at'));
    }

    public function test_gate_scanner_status_change_is_audited_with_the_operator(): void
    {
        $user = User::factory()->create(['role' => 'receptionist', 'email_verified_at' => now()]);
        $visit = Visit::create([
            'visitor_name' => 'Tamu Gate',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Kunjungan',
            'qr_code_token' => '12345678901234567890123456789012',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);

        $response = $this->actingAs($user)->postJson(route('gate.scan'), [
            'code' => $visit->qr_code_token,
        ]);

        $response->assertOk()->assertJsonPath('status', 'success');
        $this->assertDatabaseHas('visit_status_histories', [
            'visit_id' => $visit->id,
            'user_id' => $user->id,
            'action' => 'gate-check-in',
            'from_status' => Visit::STATUS_PENDING,
            'to_status' => Visit::STATUS_ACTIVE,
        ]);
        $this->assertDatabaseHas('gate_logs', [
            'visit_id' => $visit->id,
            'direction' => 'in',
        ]);
    }
}
