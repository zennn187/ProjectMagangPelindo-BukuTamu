@extends('layouts.operator')

@section('page_title', $service->exists ? 'Edit Layanan' : 'Tambah Layanan')

@section('content')
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm max-w-2xl">
        <h2 class="font-semibold text-bank-navy mb-1">{{ $service->exists ? 'Edit Layanan' : 'Layanan Baru' }}</h2>
        <p class="text-sm text-bank-gray mb-6">Lengkapi detail layanan, termasuk catatan bila perlu.</p>

        <form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}">
            @csrf
            @if($service->exists)
                @method('PUT')
            @endif

            <div class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-bank-navy mb-1.5">Nama Layanan <span class="text-bank-red">*</span></label>
                    <input id="name" name="name" type="text" required value="{{ old('name', $service->name) }}"
                           class="w-full rounded-xl border border-bank-light bg-white px-4 py-2.5 text-sm text-bank-navy placeholder-bank-gray focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 focus:outline-none"
                           placeholder="cth: Pengantaran Surat">
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="icon" class="block text-sm font-medium text-bank-navy mb-1.5">Ikon (emoji)</label>
                        <input id="icon" name="icon" type="text" maxlength="8" value="{{ old('icon', $service->icon ?? '✨') }}"
                               class="w-full rounded-xl border border-bank-light bg-white px-4 py-2.5 text-sm focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 focus:outline-none"
                               placeholder="📄">
                    </div>
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-bank-navy mb-1.5">Status</label>
                        <label class="flex items-center gap-2 rounded-xl border border-bank-light bg-white px-4 py-2.5 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $service->exists ? $service->is_active : true)) class="h-4 w-4 rounded accent-[#1814F3]">
                            <span class="text-sm text-bank-navy">Layanan aktif</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-bank-navy mb-1.5">Deskripsi</label>
                    <input id="description" name="description" type="text" value="{{ old('description', $service->description) }}"
                           class="w-full rounded-xl border border-bank-light bg-white px-4 py-2.5 text-sm focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 focus:outline-none"
                           placeholder="Deskripsi singkat layanan">
                </div>

                <div>
                    <label for="note" class="block text-sm font-medium text-bank-navy mb-1.5">Catatan</label>
                    <textarea id="note" name="note" rows="3"
                              class="w-full rounded-xl border border-bank-light bg-white px-4 py-2.5 text-sm focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 focus:outline-none"
                              placeholder="Catatan tambahan, mis. prosedur atau ketentuan layanan">{{ old('note', $service->note) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8">
                <button type="submit" class="rounded-xl bg-bank-blue px-6 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 transition-colors">
                    {{ $service->exists ? 'Simpan Perubahan' : 'Tambah Layanan' }}
                </button>
                <a href="{{ route('services.index') }}" class="rounded-xl bg-bank-light px-6 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/70">Batal</a>
            </div>
        </form>
    </div>
@endsection