<?php

namespace Tests\Feature;

use App\Models\RetentionSetting;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitPhoto;
use App\Services\VisitBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminDataManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_manage_retention_and_visit_history(): void
    {
        $operator = User::factory()->create(['role' => 'receptionist', 'email_verified_at' => now()]);

        $this->actingAs($operator)->get(route('admin.data.index'))->assertForbidden();
        $this->actingAs($operator)->get(route('admin.status-history.index'))->assertForbidden();
    }

    public function test_backup_can_restore_deleted_visit_without_overwriting_existing_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $visit = Visit::create([
            'visitor_name' => 'Tamu Backup',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Pemulihan data',
            'qr_code_token' => 'backup-restore-test',
            'status' => Visit::STATUS_PENDING,
            'visit_type' => Visit::TYPE_MEET,
        ]);
        $photoContents = file_get_contents(public_path('icons/icon-192.png'));
        $visit->photo()->create(['contents' => $photoContents, 'mime_type' => 'image/png']);

        $backupPath = app(VisitBackupService::class)->create(now()->startOfDay(), now()->endOfDay());

        try {
            $visit->delete();
            $file = new UploadedFile($backupPath, 'backup.zip', 'application/zip', null, true);

            $this->actingAs($admin)
                ->post(route('admin.data.restore'), ['backup' => $file])
                ->assertRedirect()
                ->assertSessionHas('success');

            $restored = Visit::query()->where('qr_code_token', 'backup-restore-test')->firstOrFail();
            $this->assertSame($photoContents, $restored->photo()->firstOrFail()->contents);
            $restored->update(['visitor_name' => 'Nama yang sudah ada']);
            VisitPhoto::query()->whereKey($restored->id)->delete();

            $file = new UploadedFile($backupPath, 'backup.zip', 'application/zip', null, true);
            $this->actingAs($admin)
                ->post(route('admin.data.restore'), ['backup' => $file])
                ->assertRedirect();

            $this->assertSame('Nama yang sudah ada', $restored->fresh()->visitor_name);
            $this->assertSame($photoContents, $restored->photo()->firstOrFail()->contents);
        } finally {
            @unlink($backupPath);
        }
    }

    public function test_expired_data_requires_explicit_confirmation_before_deletion(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        RetentionSetting::current()->update([
            'visit_retention_days' => 60,
            'photo_retention_days' => 30,
        ]);
        $visit = Visit::create([
            'visitor_name' => 'Tamu Kedaluwarsa',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Uji retensi',
            'qr_code_token' => 'retention-delete-test',
            'status' => Visit::STATUS_COMPLETED,
            'visit_type' => Visit::TYPE_MEET,
        ]);
        $visit->forceFill(['created_at' => now()->subDays(61), 'updated_at' => now()->subDays(61)])->save();
        $photoOnlyVisit = Visit::create([
            'visitor_name' => 'Foto Kedaluwarsa',
            'visitor_phone' => '081234567890',
            'visitor_institution' => 'PT Contoh',
            'purpose' => 'Retensi foto',
            'qr_code_token' => 'retention-photo-test',
            'status' => Visit::STATUS_COMPLETED,
            'visit_type' => Visit::TYPE_MEET,
        ]);
        $photoOnlyVisit->forceFill(['created_at' => now()->subDays(31), 'updated_at' => now()->subDays(31)])->save();
        $photoOnlyVisit->photo()->create(['contents' => 'old-photo-data']);

        $this->actingAs($admin)->get(route('admin.data.index'))
            ->assertOk()
            ->assertSee('Kunjungan kedaluwarsa')
            ->assertSee('Foto kedaluwarsa');
        $this->actingAs($admin)
            ->post(route('admin.data.delete-expired'), ['confirmation' => 'HAPUS-TIDAK'])
            ->assertSessionHasErrors('confirmation');
        $this->assertDatabaseHas('visits', ['id' => $visit->id]);

        $this->actingAs($admin)
            ->post(route('admin.data.delete-expired'), ['confirmation' => 'HAPUS'])
            ->assertRedirect();
        $this->assertDatabaseMissing('visits', ['id' => $visit->id]);
        $this->assertDatabaseHas('visits', ['id' => $photoOnlyVisit->id]);
        $this->assertDatabaseMissing('visit_photos', ['visit_id' => $photoOnlyVisit->id]);
    }
}
