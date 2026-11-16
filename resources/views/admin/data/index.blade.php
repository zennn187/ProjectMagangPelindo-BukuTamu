@extends('layouts.operator')

@section('page_title', 'Pengelolaan Data')

@section('content')
    @if(session('success'))
        <div role="status" class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <p class="mb-5 text-sm text-bank-gray">Backup dan pemulihan hanya dapat dilakukan admin. Arsip berisi informasi pribadi dan foto tamu; simpan di lokasi yang aman.</p>

    <section class="mb-6 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-lg font-semibold text-bank-navy">Masa simpan</h2>
        <p class="mt-1 text-sm text-bank-gray">Masa simpan foto dan catatan kunjungan diatur terpisah. Mengubah nilainya tidak langsung menghapus data.</p>
        <form method="POST" action="{{ route('admin.data.retention') }}" class="mt-5 grid gap-4 sm:grid-cols-3 sm:items-end">
            @csrf
            <div>
                <label for="visit_retention_days" class="mb-1 block text-sm font-medium text-bank-navy">Catatan kunjungan (hari)</label>
                <input id="visit_retention_days" name="visit_retention_days" type="number" min="30" max="3650" required value="{{ old('visit_retention_days', $settings->visit_retention_days) }}" class="soft-field">
            </div>
            <div>
                <label for="photo_retention_days" class="mb-1 block text-sm font-medium text-bank-navy">Foto (hari)</label>
                <input id="photo_retention_days" name="photo_retention_days" type="number" min="30" max="3650" required value="{{ old('photo_retention_days', $settings->photo_retention_days) }}" class="soft-field">
            </div>
            <button type="submit" class="min-h-11 rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Simpan masa simpan</button>
        </form>
    </section>

    <section class="mb-6 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-lg font-semibold text-bank-navy">Backup dan pemulihan</h2>
        <p class="mt-1 text-sm text-bank-gray">Pilih rentang tanggal untuk mengunduh ZIP berisi catatan, foto, log gate, dan riwayat status. Ekspor Excel tahunan tersedia di menu Laporan.</p>
        <form method="GET" action="{{ route('admin.data.backup') }}" target="_blank" rel="noopener" class="mt-5 grid gap-4 sm:grid-cols-3 sm:items-end">
            <div>
                <label for="from" class="mb-1 block text-sm font-medium text-bank-navy">Dari tanggal</label>
                <input id="from" type="date" name="from" required value="{{ old('from', $from) }}" class="soft-date">
            </div>
            <div>
                <label for="to" class="mb-1 block text-sm font-medium text-bank-navy">Sampai tanggal</label>
                <input id="to" type="date" name="to" required value="{{ old('to', $to) }}" class="soft-date">
            </div>
            <button type="submit" class="min-h-11 rounded-xl bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Unduh backup ZIP</button>
        </form>

        <div class="mt-6 border-t border-bank-light pt-5">
            <h3 class="font-semibold text-bank-navy">Pulihkan dari backup</h3>
            <p class="mt-1 text-sm text-bank-gray">Pemulihan hanya menambahkan data yang hilang; data yang sudah ada tidak ditimpa. Upload hanya file ZIP hasil fitur backup aplikasi ini.</p>
            <form method="POST" action="{{ route('admin.data.restore') }}" enctype="multipart/form-data" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <div class="min-w-0 flex-1">
                    <label for="backup" class="mb-1 block text-sm font-medium text-bank-navy">File backup (.zip, maksimal 4 MB)</label>
                    <input id="backup" name="backup" type="file" accept=".zip,application/zip" required class="block min-h-11 w-full rounded-xl border border-bank-light bg-white px-3 py-2 text-sm text-bank-navy">
                </div>
                <button type="submit" class="min-h-11 rounded-xl bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800" data-confirm="Arsip akan digabungkan. Data yang sudah ada tidak ditimpa. Pastikan Anda memilih file backup tepercaya." data-confirm-title="Pulihkan backup" data-confirm-action="Pulihkan">Pulihkan backup</button>
            </form>
        </div>
    </section>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
        <h2 class="text-lg font-semibold text-amber-950">Data yang melewati masa simpan</h2>
        <p class="mt-1 text-sm text-amber-900">Penghapusan tidak berjalan otomatis. Unduh dan simpan backup terlebih dahulu, lalu konfirmasi secara manual di bawah. Saat kunjungan dihapus, foto dan riwayat status terkait ikut terhapus.</p>
        <dl class="mt-4 grid grid-cols-2 gap-3 text-sm sm:max-w-lg">
            <div class="rounded-xl bg-white/70 p-3"><dt class="text-bank-gray">Kunjungan kedaluwarsa</dt><dd class="mt-1 text-xl font-bold text-bank-navy">{{ number_format($expiredVisits) }}</dd></div>
            <div class="rounded-xl bg-white/70 p-3"><dt class="text-bank-gray">Foto kedaluwarsa</dt><dd class="mt-1 text-xl font-bold text-bank-navy">{{ number_format($expiredPhotos) }}</dd></div>
        </dl>
        <form method="POST" action="{{ route('admin.data.delete-expired') }}" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-end" data-confirm="Tindakan ini menghapus kunjungan dan foto yang sudah melewati masa simpan. Data hanya dapat dipulihkan dari file backup." data-confirm-title="Hapus data kedaluwarsa?" data-confirm-action="Lanjutkan hapus" data-confirm-variant="danger">
            @csrf
            <div>
                <label for="delete-confirmation" class="mb-1 block text-sm font-medium text-amber-950">Ketik HAPUS untuk mengaktifkan penghapusan</label>
                <input id="delete-confirmation" name="confirmation" type="text" required pattern="HAPUS" autocomplete="off" class="soft-field">
            </div>
            <button type="submit" class="min-h-11 rounded-xl bg-red-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-800">Hapus data kedaluwarsa</button>
        </form>
    </section>
@endsection
