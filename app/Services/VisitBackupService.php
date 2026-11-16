<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\GateLog;
use App\Models\Visit;
use App\Models\VisitPhoto;
use App\Models\VisitStatusHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use JsonException;
use ZipArchive;

class VisitBackupService
{
    private const MAX_ARCHIVE_BYTES = 4_194_304;

    private const MAX_RECORDS = 1000;

    public function create(Carbon $from, Carbon $to): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new InvalidArgumentException('Fitur arsip ZIP tidak tersedia pada server ini.');
        }

        $query = Visit::query()
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->orderBy('id');

        if ((clone $query)->count() > self::MAX_RECORDS) {
            throw new InvalidArgumentException('Rentang arsip berisi lebih dari 1.000 kunjungan. Bagi rentang tanggal menjadi beberapa file backup.');
        }

        $visits = $query->get();
        $visitIds = $visits->modelKeys();
        $photoFiles = [];
        $photoMetadata = [];
        $databasePhotos = VisitPhoto::query()->whereIn('visit_id', $visitIds)->get()->keyBy('visit_id');

        foreach ($visits as $visit) {
            $photo = $databasePhotos->get($visit->id);
            if ($photo) {
                $contents = $photo->contents;
                $mimeType = $photo->mime_type;
            } elseif ($visit->photo_path && Storage::disk('public')->exists($visit->photo_path)) {
                $contents = Storage::disk('public')->get($visit->photo_path);
                $mimeType = Storage::disk('public')->mimeType($visit->photo_path) ?: 'image/jpeg';
            } else {
                continue;
            }

            if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                continue;
            }

            $extension = match ($mimeType) {
                'image/png' => 'png',
                'image/webp' => 'webp',
                default => 'jpg',
            };
            $path = "photos/{$visit->id}.{$extension}";
            $photoFiles[$path] = $contents;
            $photoMetadata[(string) $visit->id] = [
                'path' => $path,
                'mime_type' => $mimeType,
                'sha256' => hash('sha256', $contents),
            ];
        }

        $payload = [
            'format' => 'bukutamu-visit-backup',
            'version' => 1,
            'visits' => $visits->map(fn (Visit $visit) => $visit->getAttributes())->all(),
            'gate_logs' => GateLog::query()->whereIn('visit_id', $visitIds)->get()->map(fn (GateLog $row) => $row->getAttributes())->all(),
            'status_histories' => VisitStatusHistory::query()->whereIn('visit_id', $visitIds)->get()->map(fn (VisitStatusHistory $row) => $row->getAttributes())->all(),
        ];
        $data = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $dataHash = hash('sha256', $data);
        ksort($photoMetadata);

        $archivePath = tempnam(sys_get_temp_dir(), 'bukutamu-backup-');
        if ($archivePath === false) {
            throw new InvalidArgumentException('Server tidak dapat menyiapkan file backup sementara.');
        }

        $zip = new ZipArchive;
        if ($zip->open($archivePath, ZipArchive::OVERWRITE) !== true) {
            @unlink($archivePath);
            throw new InvalidArgumentException('Server gagal membuat file backup ZIP.');
        }

        $zip->addFromString('data.json', $data);
        foreach ($photoFiles as $path => $contents) {
            $zip->addFromString($path, $contents);
        }
        $zip->addFromString('manifest.json', json_encode([
            'format' => 'bukutamu-visit-backup',
            'version' => 1,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'data_sha256' => $dataHash,
            'photos' => $photoMetadata,
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
        $zip->close();

        if (filesize($archivePath) > self::MAX_ARCHIVE_BYTES) {
            @unlink($archivePath);
            throw new InvalidArgumentException('File backup melebihi batas unduhan aman 4 MB. Bagi rentang tanggal menjadi beberapa file backup.');
        }

        return $archivePath;
    }

    public function restore(string $archivePath): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new InvalidArgumentException('Fitur pemulihan ZIP tidak tersedia pada server ini.');
        }

        $zip = new ZipArchive;
        if ($zip->open($archivePath) !== true) {
            throw new InvalidArgumentException('File bukan arsip ZIP yang dapat dibuka.');
        }

        try {
            if ($zip->numFiles < 2 || $zip->numFiles > self::MAX_RECORDS + 2) {
                throw new InvalidArgumentException('Isi arsip tidak sesuai format backup Buku Tamu.');
            }

            $manifestContents = $zip->getFromName('manifest.json');
            $dataContents = $zip->getFromName('data.json');
            if (! is_string($manifestContents) || ! is_string($dataContents) || strlen($dataContents) > 16_777_216) {
                throw new InvalidArgumentException('Manifest atau data utama backup tidak valid atau terlalu besar.');
            }

            try {
                $manifest = json_decode($manifestContents, true, 32, JSON_THROW_ON_ERROR);
                $payload = json_decode($dataContents, true, 64, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new InvalidArgumentException('File backup berisi JSON yang rusak atau tidak valid.', previous: $exception);
            }
            if (($manifest['format'] ?? null) !== 'bukutamu-visit-backup'
                || ($manifest['version'] ?? null) !== 1
                || ($payload['format'] ?? null) !== 'bukutamu-visit-backup'
                || ($payload['version'] ?? null) !== 1
                || ! is_array($payload['visits'] ?? null)
                || ! is_array($payload['gate_logs'] ?? null)
                || ! is_array($payload['status_histories'] ?? null)
                || count($payload['visits']) > self::MAX_RECORDS
                || ! is_array($manifest['photos'] ?? null)
                || count($manifest['photos']) > count($payload['visits'])) {
                throw new InvalidArgumentException('Versi atau format backup tidak didukung.');
            }

            $dataHash = hash('sha256', $dataContents);
            $photos = $manifest['photos'] ?? [];
            ksort($photos);
            if (! preg_match('/^[a-f0-9]{64}$/', (string) ($manifest['data_sha256'] ?? ''))
                || ! hash_equals($manifest['data_sha256'], $dataHash)) {
                throw new InvalidArgumentException('Pemeriksaan integritas backup gagal. Pastikan file berasal dari fitur Unduh Backup aplikasi ini.');
            }

            $this->validatePayload($payload, $photos);
            $this->validateEntries($zip, $photos);

            return DB::transaction(function () use ($payload, $photos, $zip) {
                $visitColumns = Schema::getColumnListing('visits');
                $visitIds = [];
                $insertedVisits = 0;
                $skippedVisits = 0;

                foreach ($payload['visits'] as $row) {
                    if (! is_array($row) || ! isset($row['id'], $row['qr_code_token'])) {
                        throw new InvalidArgumentException('Ada data kunjungan yang tidak lengkap di dalam backup.');
                    }

                    $existingById = Visit::query()->find($row['id']);
                    if ($existingById) {
                        if (hash_equals($existingById->qr_code_token, $row['qr_code_token'])) {
                            $visitIds[(string) $row['id']] = (int) $existingById->id;
                        }
                        $skippedVisits++;

                        continue;
                    }

                    $existingByToken = Visit::query()->where('qr_code_token', $row['qr_code_token'])->first();
                    if ($existingByToken) {
                        $visitIds[(string) $row['id']] = (int) $existingByToken->id;
                        $skippedVisits++;

                        continue;
                    }

                    if (! empty($row['employee_id']) && ! Employee::query()->whereKey($row['employee_id'])->exists()) {
                        $row['employee_id'] = null;
                    }

                    DB::table('visits')->insert($this->onlyColumns($row, $visitColumns));
                    $visitIds[(string) $row['id']] = (int) $row['id'];
                    $insertedVisits++;
                }

                $restoredPhotos = 0;
                foreach ($photos as $oldVisitId => $photo) {
                    $currentVisitId = $visitIds[(string) $oldVisitId] ?? null;
                    if (! $currentVisitId || VisitPhoto::query()->whereKey($currentVisitId)->exists()) {
                        continue;
                    }

                    $contents = $zip->getFromName($photo['path']);
                    $image = is_string($contents) ? @getimagesizefromstring($contents) : false;
                    if (! is_array($image) || ($image['mime'] ?? null) !== ($photo['mime_type'] ?? null)
                        || ! hash_equals((string) ($photo['sha256'] ?? ''), hash('sha256', $contents))) {
                        throw new InvalidArgumentException('Foto dalam backup tidak lolos pemeriksaan integritas.');
                    }

                    VisitPhoto::query()->create([
                        'visit_id' => $currentVisitId,
                        'contents' => $contents,
                        'mime_type' => $photo['mime_type'],
                    ]);
                    $restoredPhotos++;
                }

                $restoredGateLogs = $this->restoreRelatedRows(
                    $payload['gate_logs'] ?? [],
                    'gate_logs',
                    $visitIds,
                    Schema::getColumnListing('gate_logs'),
                );
                $restoredHistories = $this->restoreRelatedRows(
                    $payload['status_histories'] ?? [],
                    'visit_status_histories',
                    $visitIds,
                    Schema::getColumnListing('visit_status_histories'),
                );

                return compact('insertedVisits', 'skippedVisits', 'restoredPhotos', 'restoredGateLogs', 'restoredHistories');
            });
        } finally {
            $zip->close();
        }
    }

    private function validateEntries(ZipArchive $zip, array $photos): void
    {
        $uncompressedBytes = 0;
        $allowed = ['manifest.json' => true, 'data.json' => true];
        $seen = [];

        foreach ($photos as $visitId => $photo) {
            if (! is_array($photo)
                || ! preg_match('/^[1-9][0-9]*$/', (string) $visitId)
                || ! preg_match('/^photos\/[1-9][0-9]*\.(jpg|png|webp)$/', (string) ($photo['path'] ?? ''))
                || ! in_array($photo['mime_type'] ?? null, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                throw new InvalidArgumentException('Daftar foto pada backup tidak valid.');
            }
            $allowed[$photo['path']] = true;
        }

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);
            $stats = $zip->statIndex($index);
            if (! isset($allowed[$name]) || isset($seen[$name]) || ! is_array($stats)) {
                throw new InvalidArgumentException('Arsip berisi file tambahan atau path yang tidak diizinkan.');
            }
            $seen[$name] = true;
            $uncompressedBytes += (int) $stats['size'];
            if ($uncompressedBytes > 16_777_216) {
                throw new InvalidArgumentException('Isi backup terlalu besar setelah dibuka.');
            }
        }

        if (! isset($seen['manifest.json'], $seen['data.json'])) {
            throw new InvalidArgumentException('Arsip tidak memiliki file utama backup.');
        }

        foreach ($photos as $photo) {
            if (! isset($seen[$photo['path']])) {
                throw new InvalidArgumentException('Ada foto backup yang tidak ditemukan di dalam arsip.');
            }
        }
    }

    private function validatePayload(array $payload, array $photos): void
    {
        $visitIds = [];
        foreach ($payload['visits'] as $row) {
            if (! is_array($row)
                || filter_var($row['id'] ?? null, FILTER_VALIDATE_INT) === false
                || (int) $row['id'] < 1
                || ! is_string($row['qr_code_token'] ?? null)
                || $row['qr_code_token'] === ''
                || ! in_array($row['status'] ?? null, [
                    Visit::STATUS_PENDING,
                    Visit::STATUS_WAITING,
                    Visit::STATUS_ACTIVE,
                    Visit::STATUS_COMPLETED,
                    Visit::STATUS_REJECTED,
                ], true)
                || ! in_array($row['visit_type'] ?? null, [
                    Visit::TYPE_MEET,
                    Visit::TYPE_DELIVER,
                    Visit::TYPE_MEETING_INVITATION,
                ], true)) {
                throw new InvalidArgumentException('Data kunjungan di dalam backup tidak valid.');
            }
            foreach (['visitor_name', 'visitor_phone', 'visitor_institution', 'purpose'] as $requiredField) {
                if (! is_string($row[$requiredField] ?? null) || $row[$requiredField] === '') {
                    throw new InvalidArgumentException('Data kunjungan di dalam backup tidak lengkap.');
                }
            }
            $visitIds[(string) $row['id']] = true;
        }

        foreach ($photos as $visitId => $photo) {
            if (! isset($visitIds[(string) $visitId])
                || ! is_string($photo['sha256'] ?? null)
                || ! preg_match('/^[a-f0-9]{64}$/', $photo['sha256'])) {
                throw new InvalidArgumentException('Informasi foto backup tidak valid.');
            }
        }
    }

    private function restoreRelatedRows(array $rows, string $table, array $visitIds, array $columns): int
    {
        $restored = 0;
        foreach ($rows as $row) {
            if (! is_array($row) || ! isset($row['id'], $row['visit_id'])) {
                throw new InvalidArgumentException('Log terkait dalam backup tidak valid.');
            }

            $visitId = $visitIds[(string) $row['visit_id']] ?? null;
            if (! $visitId || DB::table($table)->where('id', $row['id'])->exists()) {
                continue;
            }

            $row['visit_id'] = $visitId;
            if (isset($row['user_id']) && ! DB::table('users')->where('id', $row['user_id'])->exists()) {
                $row['user_id'] = null;
            }
            DB::table($table)->insert($this->onlyColumns($row, $columns));
            $restored++;
        }

        return $restored;
    }

    private function onlyColumns(array $row, array $columns): array
    {
        return array_intersect_key($row, array_flip($columns));
    }
}
