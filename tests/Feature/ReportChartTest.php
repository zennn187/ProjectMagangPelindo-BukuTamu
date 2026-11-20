<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ReportChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_renders_visit_data_in_the_accessible_svg_chart(): void
    {
        Cache::flush();

        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        $visit = Visit::create([
            'visitor_name' => 'Pengunjung Chart',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Uji grafik laporan',
            'qr_code_token' => 'report-chart-visit',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);
        $visit->forceFill([
            'created_at' => '2026-11-17 09:00:00',
            'updated_at' => '2026-11-17 09:00:00',
        ])->save();

        $response = $this->actingAs($admin)->get(route('admin.reports.index', [
            'from' => '2026-11-17',
            'to' => '2026-11-17',
        ]));

        $response->assertOk()
            ->assertSee('Jumlah kunjungan per tanggal', false)
            ->assertSee('2026-11-17: 1 kunjungan', false)
            ->assertSee('<svg', false);
    }
}
