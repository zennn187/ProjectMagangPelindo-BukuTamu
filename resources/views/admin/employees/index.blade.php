@extends('layouts.operator')

@section('page_title', 'Pegawai')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <p class="text-sm text-bank-gray">Master data pegawai Pelindo yang dapat menjadi tujuan kunjungan.</p>
        <a href="{{ route('admin.employees.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-xl hover:shadow-bank-blue/30">
            + Tambah Pegawai
        </a>
    </div>

    @php
        $totalEmployees = $employees->total();
        $activeEmployees = \App\Models\Employee::where('is_active', true)->count();
        $departmentOptions = ['IT', 'Operasional', 'Umum', 'Keuangan', 'HRD', 'Administrasi', 'Logistik', 'Teknik'];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 mb-6">
        <div class="bg-white rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-center h-11 w-11 rounded-full bg-bank-light text-bank-blue text-lg">👥</div>
            <p class="mt-4 text-sm font-medium text-bank-gray">Total Pegawai</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy">{{ $totalEmployees }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-center h-11 w-11 rounded-full bg-emerald-50 text-emerald-600 text-lg">✅</div>
            <p class="mt-4 text-sm font-medium text-bank-gray">Pegawai Aktif</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy">{{ $activeEmployees }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-center h-11 w-11 rounded-full bg-amber-50 text-amber-600 text-lg">🌴</div>
            <p class="mt-4 text-sm font-medium text-bank-gray">Non-aktif / Cuti</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy">{{ max(0, $totalEmployees - $activeEmployees) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl overflow-hidden shadow-sm ring-1 ring-bank-light/70">
        <div class="px-5 py-4 border-b border-bank-light">
            <form method="GET" action="{{ route('admin.employees.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div class="md:col-span-2">
                    <label class="block text-xs uppercase tracking-wide text-bank-gray mb-1">Cari Nama / Divisi / Jabatan</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bank-gray" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari pegawai..." class="soft-search pl-9">
                    </div>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wide text-bank-gray mb-1">Divisi</label>
                    <select name="department" class="soft-select">
                        <option value="" {{ empty($department) ? 'selected' : '' }}>Semua</option>
                        @foreach($departmentOptions as $option)
                            <option value="{{ $option }}" {{ ($department ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wide text-bank-gray mb-1">Status</label>
                    <select name="status" class="soft-select">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Non-aktif / Cuti</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex justify-end gap-2">
                    <button type="submit" class="rounded-xl bg-bank-blue px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">Filter</button>
                    <a href="{{ route('admin.employees.index') }}" class="rounded-xl bg-bank-light px-4 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/80">Reset</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bank-light text-sm">
                <thead class="bg-bank-bg text-left text-xs uppercase text-bank-gray">
                    <tr>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Divisi</th>
                        <th class="px-5 py-3">Jabatan</th>
                        <th class="px-5 py-3">Telepon</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bank-light">
                    @forelse($employees as $employee)
                        <tr class="transition-colors duration-200 hover:bg-bank-bg/70">
                            <td class="px-5 py-3 font-medium text-bank-navy">{{ $employee->name }}</td>
                            <td class="px-5 py-3 text-bank-navy">{{ $employee->department }}</td>
                            <td class="px-5 py-3 text-bank-navy">{{ $employee->position }}</td>
                            <td class="px-5 py-3 text-bank-navy">{{ $employee->phone_number ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $employee->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-bank-light text-bank-gray' }}">
                                    {{ $employee->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.employees.edit', $employee) }}" class="rounded-lg bg-bank-light px-3 py-1.5 text-xs font-medium text-bank-navy transition-colors hover:bg-bank-light/80">Edit</a>
                                    <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" data-confirm="Hapus pegawai ini? Data akan dihapus secara permanen dari sistem." data-confirm-title="Hapus pegawai" data-confirm-action="Ya, hapus" data-confirm-variant="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-bank-red/10 px-3 py-1.5 text-xs font-medium text-bank-red transition-colors hover:bg-bank-red/20">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-bank-gray">Belum ada data pegawai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="px-5 py-3 border-t border-bank-light">{{ $employees->links() }}</div>
        @endif
    </div>
@endsection
