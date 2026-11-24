<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GateReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_latest_gate_report_reads_database_rows_and_excludes_kiosk_visits(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
            'email_verified_at' => now(),
        ]);

        $gateVisit = $this->createVisit(Visit::SOURCE_GATE, 'Tamu Gate');
        $this->createVisit(Visit::SOURCE_KIOSK, 'Tamu Gate Kiosk');

        $response = $this->actingAs($user)->getJson(route('gate-reports.latest', [
            'day' => now()->toDateString(),
            'search' => 'Gate',
        ]));

        $response->assertOk()
            ->assertJsonPath('counts.total', 1)
            ->assertJsonPath('counts.inside', 1)
            ->assertJsonPath('visits.0.name', 'Tamu Gate')
            ->assertJsonPath('visits.0.identity', 'NIK-12345')
            ->assertJsonPath('visits.0.chat_url', route('gate.chat', $gateVisit));
    }

    public function test_sidebar_orders_requested_pages_and_keeps_gate_report_accessible(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertOk()
            ->assertSee('id="open-visits"', false)
            ->assertSee('Antrean Kunjungan')
            ->assertSee('Kedatangan Tamu');

        $content = $response->getContent();
        $hrefs = [
            route('dashboard'),
            route('dashboard', ['section' => 'antrean']).'#open-visits',
            route('gate.scanner'),
            route('gate-reports.index'),
            route('admin.users.index'),
            route('admin.status-history.index'),
            route('admin.reports.index'),
            route('admin.data.index'),
            route('admin.employees.index'),
            route('admin.blacklists.index'),
            route('profile.edit'),
        ];
        $positions = array_map(
            fn (string $href) => strpos($content, 'href="'.$href.'"'),
            $hrefs
        );

        $this->assertNotContains(false, $positions);
        $sortedPositions = $positions;
        sort($sortedPositions);
        $this->assertSame($sortedPositions, $positions);
    }

    private function createVisit(string $source, string $name): Visit
    {
        return Visit::create([
            'visitor_name' => $name,
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Kunjungan pengujian laporan',
            'qr_code_token' => Str::random(32),
            'status' => Visit::STATUS_ACTIVE,
            'visit_type' => Visit::TYPE_MEET,
            'source' => $source,
            'identity_number' => 'NIK-12345',
            'person_to_meet' => 'Pegawai Uji',
            'check_in_time' => now(),
        ]);
    }
}
