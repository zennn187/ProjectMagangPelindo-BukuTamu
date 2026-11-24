<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VisitLatestTest extends TestCase
{
    use RefreshDatabase;

    public function test_latest_visits_endpoint_returns_recent_visits(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);

        $employee = Employee::factory()->create();

        Visit::create([
            'visitor_name' => 'Budi Santoso',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'employee_id' => $employee->id,
            'purpose' => 'Meeting',
            'photo_path' => null,
            'qr_code_token' => 'token-123',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
            'delivery_pref' => Visit::DELIVERY_HAND_IN,
        ]);

        $response = $this->actingAs($user)->getJson('/dashboard/latest');

        $response->assertOk();
        $response->assertJsonPath('visits.0.visitor_name', 'Budi Santoso');
        $response->assertJsonPath('visits.0.status', Visit::STATUS_PENDING);
        $response->assertJsonPath('counts.pending', 1);
    }

    public function test_latest_visits_endpoint_includes_database_photo_url(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);
        $visit = Visit::create([
            'visitor_name' => 'Budi Santoso',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Meeting',
            'qr_code_token' => 'token-with-photo',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);
        $visit->photo()->create(['contents' => 'photo-binary']);

        $response = $this->actingAs($user)->getJson('/dashboard/latest');

        $response->assertOk()
            ->assertJsonPath('visits.0.photo_url', route('visits.photo', $visit, false));
    }

    public function test_photo_endpoint_streams_the_private_database_photo_to_authenticated_users(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
            'email_verified_at' => now(),
        ]);
        $visit = Visit::create([
            'visitor_name' => 'Budi Santoso',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Meeting',
            'qr_code_token' => 'token-photo-endpoint',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);
        $visit->photo()->create(['contents' => 'photo-binary']);

        $response = $this->actingAs($user)->get(route('visits.photo', $visit));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertSee('photo-binary', false);
    }

    public function test_users_without_operator_role_cannot_view_dashboard_or_visitor_photos(): void
    {
        $user = User::make(['role' => 'guest']);
        $visit = Visit::create([
            'visitor_name' => 'Tamu Privat',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Meeting',
            'qr_code_token' => 'private-photo-access',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);
        $visit->photo()->create(['contents' => 'photo-binary']);

        $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('visits.photo', $visit))->assertForbidden();
    }

    public function test_dashboard_date_input_uses_valid_date_format(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);

        $response = $this->actingAs($user)->get('/dashboard?day=2026-08-30+00%3A00%3A00');

        $response->assertOk();
        $response->assertSee('value="2026-08-30"', false);
        $response->assertDontSee('value="2026-08-30 00:00:00"', false);
    }

    public function test_dashboard_initial_rows_use_the_same_compact_actions_as_live_rows(): void
    {
        $this->travelTo(Carbon::create(2026, 11, 24, 12));
        Cache::forget('dashboard.monthly.2026-11');

        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);
        Visit::create([
            'visitor_name' => 'Tamu Uji',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Tes tampilan aksi',
            'qr_code_token' => 'initial-actions-test',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Jumlah kunjungan per hari pada minggu bulan ini', false);
        $response->assertSee('data-week="1"', false);
        $response->assertSee('data-week="2"', false);
        $response->assertSee('data-week="3"', false);
        $response->assertSee('data-week="4"', false);
        $response->assertSee('data-week="5"', false);
        $response->assertSee('<circle', false);
        preg_match('/<tbody\b[^>]*>(.*?)<\/tbody>/s', $response->getContent(), $rows);
        $this->assertArrayHasKey(1, $rows);
        $this->assertStringContainsString('detail-visit-btn', $rows[1]);
        $this->assertStringNotContainsString('Check-in</button>', $rows[1]);
        $this->assertStringNotContainsString('Suruh Tunggu</button>', $rows[1]);
        $this->assertStringNotContainsString('Tolak</button>', $rows[1]);
    }

    public function test_dashboard_unresolved_queue_includes_older_open_visits_but_excludes_terminal_statuses(): void
    {
        $this->travelTo(Carbon::create(2026, 11, 24, 12));
        Cache::forget('dashboard.monthly.2026-11');

        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);

        foreach ([
            Visit::STATUS_PENDING => 'Belum Dijawab',
            Visit::STATUS_WAITING => 'Diminta Menunggu',
            Visit::STATUS_ACTIVE => 'Sudah Checkin',
            Visit::STATUS_COMPLETED => 'Sudah Checkout',
            Visit::STATUS_REJECTED => 'Sudah Ditolak',
        ] as $status => $visitorName) {
            $visit = Visit::create([
                'visitor_name' => $visitorName,
                'visitor_phone' => '081234567890',
                'visitor_institution' => 'PT Contoh',
                'purpose' => 'Tes antrean lintas hari',
                'qr_code_token' => 'open-queue-'.$status,
                'status' => $status,
                'visit_type' => Visit::TYPE_MEET,
            ]);
            DB::table('visits')->where('id', $visit->id)->update([
                'created_at' => now()->subDay(),
            ]);
        }

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Antrean Kunjungan Belum Selesai')
            ->assertSee('Belum Dijawab')
            ->assertSee('Diminta Menunggu')
            ->assertSee('Sudah Checkin')
            ->assertDontSee('Sudah Checkout')
            ->assertDontSee('Sudah Ditolak')
            ->assertSee('id="open-visit-total" data-count="3"', false)
            ->assertSee('data-open-status="pending"', false)
            ->assertSee('data-open-status="waiting"', false)
            ->assertSee('data-open-status="active"', false);
    }

    public function test_dashboard_chart_adds_a_fifth_week_for_month_end_days(): void
    {
        $this->travelTo(Carbon::create(2026, 11, 28, 12));
        Cache::forget('dashboard.monthly.2026-11');

        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);

        foreach ([3 => 1, 10 => 2, 19 => 3, 28 => 4] as $day => $expectedWeek) {
            $visit = Visit::create([
                'visitor_name' => "Tamu W{$expectedWeek}",
                'visitor_phone' => '081234567890',
                'visitor_institution' => 'PT Contoh',
                'purpose' => 'Tes chart mingguan',
                'qr_code_token' => "weekly-chart-{$expectedWeek}",
                'status' => Visit::STATUS_PENDING,
                'visit_type' => Visit::TYPE_MEET,
            ]);
            DB::table('visits')->where('id', $visit->id)->update(['created_at' => now()->setDay($day)]);
        }
        foreach ([10 => 1, 19 => 2, 28 => 3, 30 => 1] as $day => $additionalVisits) {
            for ($index = 1; $index <= $additionalVisits; $index++) {
                $visit = Visit::create([
                    'visitor_name' => "Tamu tambahan {$day}-{$index}",
                    'visitor_phone' => '081234567890',
                    'visitor_institution' => 'PT Contoh',
                    'purpose' => 'Tes chart mingguan',
                    'qr_code_token' => "weekly-chart-extra-{$day}-{$index}",
                    'status' => Visit::STATUS_PENDING,
                    'visit_type' => Visit::TYPE_MEET,
                ]);
                DB::table('visits')->where('id', $visit->id)->update([
                    'created_at' => now()->setDay($day)->addMinutes($index),
                ]);
            }
        }

        $response = $this->actingAs($user)->get(route('dashboard', ['chart_week' => 3]));

        $response->assertOk()
            ->assertSee('data-active-week="3"', false)
            ->assertSee('data-month="2026-11"', false)
            ->assertSee('bukutamu-dashboard-chart-week', false)
            ->assertSee('fill-opacity="1"', false)
            ->assertSee('data-date="2026-11-03" data-value="1"', false)
            ->assertSee('data-date="2026-11-10" data-value="2"', false)
            ->assertSee('data-date="2026-11-19" data-value="3"', false)
            ->assertSee('data-date="2026-11-28" data-value="4"', false)
            ->assertSee('data-date="2026-11-30" data-value="1"', false)
            ->assertSee('data-total="2"', false)
            ->assertSee('data-total="4"', false)
            ->assertSee('aria-pressed="true"', false);

        $content = $response->getContent();
        preg_match('/<button\b(?=[^>]*data-week="5")(?=[^>]*data-total="1")[^>]*>/s', $content, $weekFiveButton);
        $this->assertNotEmpty($weekFiveButton);
        $weekFiveStart = strpos($content, '<g class="visit-particle-week-data" data-week="5"');
        $svgEnd = strpos($content, '</svg>', $weekFiveStart);
        $this->assertNotFalse($weekFiveStart);
        $this->assertNotFalse($svgEnd);
        $this->assertStringContainsString(
            'data-date="2026-11-30"',
            substr($content, $weekFiveStart, $svgEnd - $weekFiveStart)
        );

        foreach ([
            '2026-11-03' => 5,
            '2026-11-10' => 10,
            '2026-11-19' => 15,
            '2026-11-28' => 20,
            '2026-11-30' => 5,
        ] as $date => $expectedActiveDots) {
            preg_match('/<g data-date="'.$date.'" data-value="[0-9]+">(.*?)<\/g>/s', $content, $dayMarkup);
            $this->assertArrayHasKey(1, $dayMarkup);
            preg_match_all('/class="visit-particle-dot is-active"/', $dayMarkup[1], $activeDots);
            $this->assertCount($expectedActiveDots, $activeDots[0]);
        }

        preg_match('/<g data-date="2026-11-19" data-value="3">(.*?)<\/g>/s', $content, $wednesdayMarkup);
        preg_match('/class="visit-particle-dot is-active"[^>]*cy="([^"]+)"/', $wednesdayMarkup[1], $firstActiveDot);
        $this->assertSame('170', $firstActiveDot[1]);
    }

    public function test_dashboard_chart_keeps_four_weeks_when_month_has_exactly_28_days(): void
    {
        $this->travelTo(Carbon::create(2026, 2, 24, 12));
        Cache::forget('dashboard.monthly.2026-02');

        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('data-week="4"', false)
            ->assertDontSee('data-week="5"', false);
    }

    public function test_dashboard_chart_uses_actual_weekdays_for_january_month_blocks(): void
    {
        $this->travelTo(Carbon::create(2026, 1, 24, 12));
        Cache::forget('dashboard.monthly.2026-01');

        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);

        foreach ([1, 7] as $day) {
            $visit = Visit::create([
                'visitor_name' => "Tamu Januari {$day}",
                'visitor_phone' => '081234567890',
                'visitor_institution' => 'PT Contoh',
                'purpose' => 'Tes tanggal kalender',
                'qr_code_token' => "january-chart-{$day}",
                'status' => Visit::STATUS_PENDING,
                'visit_type' => Visit::TYPE_MEET,
            ]);
            DB::table('visits')->where('id', $visit->id)->update([
                'created_at' => now()->setDay($day),
            ]);
        }

        $response = $this->actingAs($user)->get(route('dashboard', ['chart_week' => 1]));
        $firstDayLabel = Carbon::create(2026, 1, 1)->translatedFormat('D');
        $seventhDayLabel = Carbon::create(2026, 1, 7)->translatedFormat('D');

        $response->assertOk()
            ->assertSee('data-active-week="1"', false)
            ->assertSee('data-month="2026-01"', false)
            ->assertSee("1 Jan, {$firstDayLabel}: 1 kunjungan", false)
            ->assertSee("7 Jan, {$seventhDayLabel}: 1 kunjungan", false);
    }

    public function test_checkout_redirect_contains_the_correct_visitor_for_toast(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);

        $employee = Employee::factory()->create();
        $visit = Visit::create([
            'visitor_name' => 'Oza Okta',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'employee_id' => $employee->id,
            'purpose' => 'Meeting',
            'photo_path' => null,
            'qr_code_token' => 'token-checkout',
            'status' => Visit::STATUS_ACTIVE,
            'visit_type' => Visit::TYPE_MEET,
            'delivery_pref' => Visit::DELIVERY_HAND_IN,
        ]);

        $response = $this->actingAs($user)->post(route('visits.check-out', $visit));

        $response->assertRedirect();
        $response->assertSessionHas('visit_action.type', 'check-out');
        $response->assertSessionHas('visit_action.visit_id', $visit->id);
        $response->assertSessionHas('visit_action.visitor_name', 'Oza Okta');
        $this->assertSame(Visit::STATUS_COMPLETED, $visit->fresh()->status);
    }

    public function test_dashboard_status_actions_return_json_without_a_page_redirect(): void
    {
        $user = User::factory()->create([
            'role' => 'receptionist',
        ]);
        $visit = Visit::create([
            'visitor_name' => 'Budi Santoso',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Meeting',
            'qr_code_token' => 'token-json-action',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);

        $response = $this->actingAs($user)->postJson(route('visits.check-in', $visit));

        $response->assertOk()
            ->assertJsonPath('visit_id', $visit->id)
            ->assertJsonPath('status', Visit::STATUS_ACTIVE);
        $this->assertSame(Visit::STATUS_ACTIVE, $visit->fresh()->status);

        $this->actingAs($user)->getJson('/dashboard/latest')
            ->assertJsonPath('counts.pending', 0)
            ->assertJsonPath('counts.active', 1);
    }
}
