<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RetentionSetting;
use App\Models\Visit;
use App\Models\VisitPhoto;
use App\Services\VisitBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DataManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('is-admin');

        $settings = RetentionSetting::current();
        $visitCutoff = now()->subDays($settings->visit_retention_days);
        $photoCutoff = now()->subDays($settings->photo_retention_days);
        $expiredVisits = Visit::query()->where('created_at', '<', $visitCutoff)->count();
        $expiredPhotos = Visit::query()
            ->where('created_at', '<', $photoCutoff)
            ->where(function ($query) {
                $query->whereHas('photo')->orWhereNotNull('photo_path');
            })
            ->count();

        return view('admin.data.index', [
            'settings' => $settings,
            'expiredVisits' => $expiredVisits,
            'expiredPhotos' => $expiredPhotos,
            'from' => now()->startOfYear()->toDateString(),
            'to' => now()->toDateString(),
        ]);
    }

    public function updateRetention(Request $request): RedirectResponse
    {
        Gate::authorize('is-admin');

        $data = $request->validate([
            'visit_retention_days' => ['required', 'integer', 'between:30,3650'],
            'photo_retention_days' => ['required', 'integer', 'between:30,3650'],
        ]);

        $settings = RetentionSetting::current();
        $previousSettings = $settings->only(['visit_retention_days', 'photo_retention_days']);
        $settings->update($data);
        Log::info('Visitor data retention settings updated', [
            'user_id' => auth()->id(),
            'previous' => $previousSettings,
            'updated' => $data,
        ]);

        return redirect()->route('admin.data.index')
            ->with('success', 'Masa simpan berhasil diperbarui. Data tidak dihapus sampai admin menekan tombol penghapusan.');
    }

    public function downloadBackup(Request $request, VisitBackupService $backups): BinaryFileResponse|RedirectResponse
    {
        Gate::authorize('is-admin');

        $data = $request->validate([
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        try {
            $from = Carbon::createFromFormat('Y-m-d', $data['from']);
            $to = Carbon::createFromFormat('Y-m-d', $data['to']);
            $path = $backups->create($from, $to);
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['backup' => $exception->getMessage()])->withInput();
        }

        return response()->download(
            $path,
            'backup-bukutamu-'.$from->format('Ymd').'-'.$to->format('Ymd').'.zip',
            ['Content-Type' => 'application/zip'],
        )->deleteFileAfterSend(true);
    }

    public function restoreBackup(Request $request, VisitBackupService $backups): RedirectResponse
    {
        Gate::authorize('is-admin');

        $data = $request->validate([
            'backup' => ['required', 'file', 'mimes:zip', 'max:4096'],
        ]);

        try {
            $restored = $backups->restore($data['backup']->getRealPath());
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['backup' => $exception->getMessage()]);
        }

        Log::info('Visitor data restored by admin', [
            'user_id' => auth()->id(),
            'inserted_visits' => $restored['insertedVisits'],
            'skipped_visits' => $restored['skippedVisits'],
            'restored_photos' => $restored['restoredPhotos'],
            'restored_gate_logs' => $restored['restoredGateLogs'],
            'restored_status_histories' => $restored['restoredHistories'],
        ]);

        return back()->with('success', sprintf(
            'Pemulihan selesai: %d kunjungan ditambahkan, %d duplikat dilewati, %d foto, %d log gate, dan %d riwayat status dipulihkan.',
            $restored['insertedVisits'],
            $restored['skippedVisits'],
            $restored['restoredPhotos'],
            $restored['restoredGateLogs'],
            $restored['restoredHistories'],
        ));
    }

    public function deleteExpired(Request $request): RedirectResponse
    {
        Gate::authorize('is-admin');

        $request->validate(['confirmation' => ['required', 'in:HAPUS']]);
        $settings = RetentionSetting::current();
        $visitCutoff = now()->subDays($settings->visit_retention_days);
        $photoCutoff = now()->subDays($settings->photo_retention_days);

        $visitIdsToDelete = Visit::query()
            ->where('created_at', '<', $visitCutoff)
            ->pluck('id');
        $expiredPhotoVisitIds = Visit::query()
            ->where('created_at', '<', $photoCutoff)
            ->where(function ($query) {
                $query->whereHas('photo')->orWhereNotNull('photo_path');
            })
            ->pluck('id');
        $affectedVisitIds = $expiredPhotoVisitIds->merge($visitIdsToDelete)->unique()->values();
        $legacyPhotos = Visit::query()
            ->whereIn('id', $affectedVisitIds)
            ->whereNotNull('photo_path')
            ->pluck('photo_path', 'id');
        $disk = Storage::disk('public');
        $failedLegacyPaths = [];
        $deletedLegacyPhotoCount = 0;

        foreach ($legacyPhotos as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
                if ($disk->exists($path)) {
                    $failedLegacyPaths[] = $path;
                } else {
                    $deletedLegacyPhotoCount++;
                }
            }
        }

        $successfulLegacyVisitIds = $legacyPhotos->keys()->reject(function ($visitId) use ($legacyPhotos, $failedLegacyPaths) {
            return in_array($legacyPhotos[$visitId], $failedLegacyPaths, true);
        });
        if ($successfulLegacyVisitIds->isNotEmpty()) {
            Visit::query()->whereIn('id', $successfulLegacyVisitIds)->update(['photo_path' => null]);
        }

        if ($failedLegacyPaths !== []) {
            Log::error('Legacy visitor photos could not be removed during retention cleanup', [
                'user_id' => auth()->id(),
                'failed_photo_count' => count($failedLegacyPaths),
            ]);

            return back()->withErrors([
                'retention' => 'Sebagian file foto lama tidak dapat dihapus. Data kunjungan tidak dihapus; periksa izin storage dan coba lagi.',
            ]);
        }

        [$deletedVisits, $databasePhotosDeleted] = DB::transaction(function () use ($visitIdsToDelete, $affectedVisitIds) {
            $databasePhotosDeleted = VisitPhoto::query()->whereIn('visit_id', $affectedVisitIds)->delete();
            $deletedVisits = Visit::query()->whereIn('id', $visitIdsToDelete)->delete();

            return [$deletedVisits, $databasePhotosDeleted];
        });
        $deletedPhotos = $databasePhotosDeleted + $deletedLegacyPhotoCount;

        Log::info('Expired visitor data deleted by admin', [
            'user_id' => auth()->id(),
            'visit_count' => $deletedVisits,
            'photo_count' => $deletedPhotos,
        ]);

        return redirect()->route('admin.data.index')->with('success', sprintf(
            'Penghapusan selesai: %d kunjungan dan %d foto terhapus. Data ini tidak dapat dipulihkan tanpa file backup.',
            $deletedVisits,
            $deletedPhotos,
        ));
    }
}
