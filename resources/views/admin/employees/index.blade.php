@extends('layouts.operator')

@section('page_title', 'Pegawai')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <p class="text-sm text-bank-gray">Master data pegawai Pelindo yang dapat menjadi tujuan kunjungan.</p>
        <a href="{{ route('admin.employees.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 transition-colors">
            + Tambah Pegawai
        </a>
    </div>

    {{-- Kartu ringkasan --}}
    @php
        $totalEmployees = $employees->total();
        $activeEmployees = \App\Models\Employee::where('is_active', true)->count();
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

    <div class="bg-white rounded-3xl overflow-hidden shadow-sm">
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
                        <tr class="hover:bg-bank-bg/60">
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
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('admin.employees.edit', $employee) }}" class="rounded-lg bg-bank-light px-3 py-1.5 text-xs font-medium text-bank-navy hover:bg-bank-light/70">Edit</a>
                                    <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Hapus pegawai ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg bg-bank-red/10 px-3 py-1.5 text-xs font-medium text-bank-red hover:bg-bank-red/20">Hapus</button>
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