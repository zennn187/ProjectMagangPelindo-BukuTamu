<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Seed sample layanan/pelayanan yang tersedia di kantor.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Kunjungan Tamu',
                'icon' => '🤝',
                'description' => 'Pertemuan dengan pegawai terkait kepentingan resmi.',
                'note' => 'Tamu wajib melakukan registrasi melalui kiosk sebelum masuk.',
            ],
            [
                'name' => 'Pengantaran Surat/Dokumen',
                'icon' => '📄',
                'description' => 'Pengiriman surat, dokumen, atau paket ke kantor.',
                'note' => 'Dokumen dapat dititipkan ke resepsionis bila yang dituju tidak berada di kantor.',
            ],
            [
                'name' => 'Koordinasi Meeting',
                'icon' => '📊',
                'description' => 'Reservasi dan koordinasi ruang rapat.',
                'note' => 'Pengajuan ruang rapat minimal 1 hari sebelumnya.',
            ],
            [
                'name' => 'Undangan Rapat / Kegiatan',
                'icon' => 'RPT',
                'description' => 'Permohonan kedatangan untuk undangan rapat atau kegiatan resmi.',
                'note' => 'Tamu memilih pegawai atau unit yang menjadi tujuan undangan.',
            ],
            [
                'name' => 'Layanan Keamanan',
                'icon' => '🛡️',
                'description' => 'Bantuan keamanan dan laporan insiden.',
                'note' => 'Hubungi pos keamanan untuk keadaan darurat.',
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                $service + ['is_active' => true]
            );
        }
    }
}
