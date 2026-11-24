<?php

namespace Tests\Feature;

use App\Exports\VisitsExport;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Str;
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
        $day = now()->toDateString();
        DB::table('visits')->where('id', $visit->id)->update([
            'created_at' => now()->startOfDay()->addHours(9),
            'updated_at' => now()->startOfDay()->addHours(9),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index', [
            'from' => $day,
            'to' => $day,
        ]));

        $response->assertOk()
            ->assertSee('Jumlah kunjungan per tanggal', false)
            ->assertSee($day.': 1 kunjungan', false)
            ->assertSee('<svg', false);
    }

    public function test_report_period_cards_filter_database_data_and_export_the_selected_range(): void
    {
        $this->travelTo(Carbon::create(2026, 11, 24, 12));
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $photoVisit = null;
        foreach ([
            'hari ini' => '2026-11-24',
            'awal minggu' => '2026-11-18',
            'di luar minggu' => '2026-11-17',
            'awal bulan' => '2026-10-24',
            'di luar bulan' => '2026-10-23',
            'awal enam bulan' => '2026-05-24',
            'di luar enam bulan' => '2026-05-23',
            'awal tahun' => '2025-11-24',
            'di luar tahun' => '2025-11-23',
        ] as $name => $date) {
            $visit = $this->createVisitAt($name, Carbon::parse($date)->setTime(12, 0));
            if ($name === 'hari ini') {
                $photoVisit = $visit;
                $visit->photo()->create([
                    'contents' => 'report-photo',
                    'mime_type' => 'image/jpeg',
                ]);
            }
        }

        $this->assertNotNull($photoVisit);

        $response = $this->actingAs($admin)->get(route('admin.reports.index', [
            'period' => 'week',
        ]));

        $response->assertOk()
            ->assertViewHas('from', fn (Carbon $from) => $from->toDateString() === '2026-11-18')
            ->assertViewHas('to', fn (Carbon $to) => $to->toDateString() === '2026-11-24')
            ->assertViewHas('summary', fn (array $summary) => $summary['total'] === 2)
            ->assertViewHas('chartData', fn ($chartData) => $chartData->count() === 7 && $chartData->sum('value') === 2)
            ->assertViewHas('periodCards', fn ($cards) => [
                $cards['week']['total'],
                $cards['month']['total'],
                $cards['six_months']['total'],
                $cards['year']['total'],
            ] === [2, 4, 6, 8])
            ->assertSee('data-report-period="week"', false)
            ->assertSee('Data Identitas Kunjungan')
            ->assertSee('ID-hari ini')
            ->assertSee('href="tel:081234567890"', false)
            ->assertSee(route('visits.photo', $photoVisit, false), false);

        Excel::fake();
        $filename = 'laporan-kunjungan-2026-11-18-sampai-2026-11-24-20261124-120000.xlsx';

        $this->get(route('admin.reports.export', ['period' => 'week']))
            ->assertOk();

        Excel::assertDownloaded(
            $filename,
            fn ($export) => $export instanceof VisitsExport
                && $export->query()->pluck('visitor_name')->sort()->values()->all() === ['awal minggu', 'hari ini']
                && $export->headings()[3] === 'No. Identitas'
                && $export->query()->where('visitor_name', 'hari ini')->value('identity_number') === 'ID-hari ini'
        );
    }

    private function createVisitAt(string $name, Carbon $createdAt): Visit
    {
        $visit = Visit::create([
            'visitor_name' => $name,
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'identity_number' => 'ID-'.$name,
            'purpose' => 'Uji periode laporan',
            'qr_code_token' => Str::random(32),
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);

        DB::table('visits')->where('id', $visit->id)->update([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        return $visit;
    }
}
