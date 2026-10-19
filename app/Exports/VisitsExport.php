<?php

namespace App\Exports;

use App\Models\Visit;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VisitsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected ?Carbon $from;

    protected ?Carbon $to;

    public function __construct(?Carbon $from = null, ?Carbon $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Query the visits, filtering by the requested date range (all history is visible to admin).
     * Dicicil 500 baris per chunk agar export ribuan data tidak timeout di Vercel free.
     */
    public function query()
    {
        return Visit::with('employee')
            ->when($this->from, fn ($q) => $q->whereDate('created_at', '>=', $this->from->toDateString()))
            ->when($this->to, fn ($q) => $q->whereDate('created_at', '<=', $this->to->toDateString()))
            ->orderBy('created_at', 'desc');
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Tamu',
            'Telepon',
            'Instansi',
            'Pegawai Tujuan',
            'Jenis Kunjungan',
            'Keperluan',
            'Status',
            'Check-in',
            'Check-out',
        ];
    }

    public function map($visit): array
    {
        return [
            $visit->created_at?->format('d/m/Y H:i'),
            $visit->visitor_name,
            $visit->visitor_phone,
            $visit->visitor_institution,
            $visit->employee?->name ?? '-',
            match ($visit->visit_type) {
                Visit::TYPE_DELIVER => 'Pengantaran Surat',
                Visit::TYPE_MEETING_INVITATION => 'Undangan Rapat / Kegiatan',
                default => 'Kunjungan',
            },
            $visit->purpose,
            strtoupper($visit->status),
            $visit->check_in_time?->format('d/m/Y H:i') ?? '-',
            $visit->check_out_time?->format('d/m/Y H:i') ?? '-',
        ];
    }
}
