<?php

namespace App\Exports;

use App\Models\Visit;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VisitsExport implements FromCollection, WithHeadings, WithMapping
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
     */
    public function collection(): \Illuminate\Support\Collection
    {
        return Visit::with('employee')
            ->when($this->from, fn ($q) => $q->whereDate('created_at', '>=', $this->from->toDateString()))
            ->when($this->to, fn ($q) => $q->whereDate('created_at', '<=', $this->to->toDateString()))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Visit $visit) {
                return [
                    'tanggal' => $visit->created_at?->format('d/m/Y H:i'),
                    'nama' => $visit->visitor_name,
                    'telepon' => $visit->visitor_phone,
                    'instansi' => $visit->visitor_institution,
                    'pegawai_tujuan' => $visit->employee?->name ?? '-',
                    'jenis' => match ($visit->visit_type) {
                        Visit::TYPE_DELIVER => 'Pengantaran Surat',
                        Visit::TYPE_MEETING_INVITATION => 'Undangan Rapat / Kegiatan',
                        default => 'Kunjungan',
                    },
                    'keperluan' => $visit->purpose,
                    'status' => strtoupper($visit->status),
                    'check_in' => $visit->check_in_time?->format('d/m/Y H:i') ?? '-',
                    'check_out' => $visit->check_out_time?->format('d/m/Y H:i') ?? '-',
                ];
            });
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

    public function map($row): array
    {
        return $row;
    }
}
