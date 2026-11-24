<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GateLog;
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

    public function test_scanner_renders_five_latest_database_activities_and_endpoint_paginates_today(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
            'email_verified_at' => now(),
        ]);

        for ($number = 1; $number <= 30; $number++) {
            $visit = $this->createVisit(Visit::SOURCE_GATE, "Gate Tamu {$number}");
            $log = GateLog::create([
                'visit_id' => $visit->id,
                'direction' => GateLog::DIRECTION_IN,
            ]);
            $log->forceFill(['created_at' => now()->subSeconds(30 - $number)])->save();
        }

        $yesterdayVisit = $this->createVisit(Visit::SOURCE_GATE, 'Gate Tamu Kemarin');
        $yesterdayLog = GateLog::create([
            'visit_id' => $yesterdayVisit->id,
            'direction' => GateLog::DIRECTION_IN,
        ]);
        $yesterdayLog->forceFill(['created_at' => now()->subDay()])->save();

        $this->actingAs($user)
            ->get(route('gate.scanner'))
            ->assertOk()
            ->assertSee('Gate Tamu 30')
            ->assertSee('Gate Tamu 26')
            ->assertDontSee('Gate Tamu 25');

        $this->actingAs($user)
            ->getJson(route('gate.activities', ['day' => now()->toDateString(), 'page' => 1]))
            ->assertOk()
            ->assertJsonPath('total', 30)
            ->assertJsonCount(25, 'items')
            ->assertJsonPath('items.0.name', 'Gate Tamu 30');
    }

    public function test_latest_feed_includes_phone_and_private_photo_url(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
            'email_verified_at' => now(),
        ]);
        $visit = $this->createVisit(Visit::SOURCE_GATE, 'Tamu dengan Foto');
        $visit->photo()->create([
            'contents' => 'test-image-bytes',
            'mime_type' => 'image/jpeg',
        ]);

        $this->actingAs($user)
            ->getJson(route('gate-reports.latest', ['day' => now()->toDateString()]))
            ->assertOk()
            ->assertJsonPath('visits.0.phone', '081234567890')
            ->assertJsonPath('visits.0.photo_url', route('visits.photo', ['visit' => $visit], false));
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
